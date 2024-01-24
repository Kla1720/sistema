<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>REGISTRO DE PROGRAMACIONES</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <div class="btn-group flex-wrap">
                    <button
                        type="button"
                        class="btn btn-custom btn-sm mt-2 mr-2"
                        @click="onCreate"
                    >
                        <i class="fa fa-plus-circle"></i> Nuevo
                    </button>
                </div>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-header bg-info">
                <h3 class="my-0">Listado de programaciones</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <el-table v-loading="loading" :data="programaciones" border stripe>
                        <el-table-column prop="origen" label="Origen" v-slot="{ row }">
                            <template>
                                <span :class="{'text-danger': (row.active === 0)}">{{row.origen.nombre}}</span>
                            </template>
                        </el-table-column>

                        <el-table-column prop="destino" label="Destino" v-slot="{ row }">
                            <template>
                                <span :class="{'text-danger': (row.active === 0)}">{{ row.destino.nombre}}</span>
                            </template>
                        </el-table-column>

                        <el-table-column prop="vehiculo" label="Vehiculo" v-slot="{ row }">
                            <template>
                                <span :class="{'text-danger': (row.active === 0)}">{{ row.vehiculo.placa }}</span>
                            </template>
                        </el-table-column>

                        <el-table-column prop="hora_salida" label="Hora salida" />
                        <el-table-column prop="actions" label="" v-slot="{ row:programacion }">
                            <el-button type="success" @click="onEdit(programacion)">
                                <i class="fa fa-edit"></i>
                            </el-button>
                            <el-button type="danger" @click="onDelete(programacion)">
                                <i class="fa fa-trash"></i>
                            </el-button>

                            <el-button v-if="programacion.active==1" type="disable" @click="desactivar(programacion)" title="Desactivar">
                                <i class="fa fa-eye-slash"></i>
                            </el-button>
                            <el-button  v-else type="success" @click="activar(programacion)" title="Activar">
                                <i class="fa fa-eye"></i>
                            </el-button>
                        </el-table-column>
                    </el-table>

                </div>
            </div>
        </div>



        <ModalAddEdit
            :visible.sync="openModalAddEdit"
            @onAddItem="onAddItem"
            @onUpdateItem="onUpdateItem"
            :programacion="programacion"
            :terminales="terminales"
            :vehiculos="vehiculos"
            :user-terminal="userTerminal"
        ></ModalAddEdit>
        <!-- Modal para la configuracion de rutas de cada terminal -->
        <configuracion-rutas
        :terminales="terminales"
        :programacion="programacion"
        :visible.sync="openModalConfigRutas"
        @onUpdateItem="onUpdateItem" />

        <generar-manifiesto
        :visible.sync="visible"
        :series="series"
        :programacion="programacion"
        :choferes="choferes"
        />
    </div>
</template>

<script>
import ModalAddEdit from "./AddEdit";
import ConfiguracionRutas from './ConfiguracionRutas';
import GenerarManifiesto from './GenerarManifiesto.vue';


export default {
    props: {
        terminales: {
            type: Array,
            required: true,
        },
        vehiculos:{
            type:Array,
            required:true
        },
        series:{
            type:Array,
            default:() => []
        },
        choferes:{
            type:Array,
            default:() => []
        },
        userTerminal:{
            type:Object,
            default:{}
        },
    },
    components: {
        ModalAddEdit,
        ConfiguracionRutas,
        GenerarManifiesto
    },
    created(){
        this.listVehiculos = this.vehiculos;
        this.getProgramaciones();
    },
    data() {
        return {
            listVehiculos:[],
            programacion: null,
            openModalAddEdit: false,
            openModalConfigRutas:false,
            loading: false,
            visible:false,
            programaciones: []
        };
    },
    mounted() {
        // this.listTerminales = this.terminales;
    },
    methods: {

        async getProgramaciones(){

            try{
                this.loading = true;
                const {data} = await this.$http.get(`/transportes/programaciones/programaciones/list`);
                this.programaciones = data;
            }catch(error){
                this.axiosError(error);
            }finally{
                this.loading = false;
            }

        },
        onDelete(item) {
            this.$confirm(`¿Estás seguro de eliminar la programación ?`, 'Atención', {
                confirmButtonText: 'Si, continuar',
                cancelButtonText: 'No, cerrar',
                type: 'warning'
            }).then(() => {
                this.$http.delete(`/transportes/programaciones/${item.id}/delete`).then(response => {
                this.$message({
                    type: 'success',
                    message: response.data.message
                });
                this.listProgramaciones = this.listProgramaciones.filter(i => i.id !== item.id);
            }).catch(error => {
                let response = error.response;
                this.$message({
                    type: 'error',
                    message: response.data.message
                });
            });
            })
        },
        onEdit(programacion) {
            this.programacion = { ...programacion };
            this.openModalAddEdit = true;
        },
        onUpdateItem(programacion) {
            this.getProgramaciones();
        },
        onAddItem(programacion) {
            this.getProgramaciones();
        },
        onCreate() {
            this.programacion = null;
            this.openModalAddEdit = true;
        },
        onConfigRutas(programacion){
            this.programacion = programacion;
            this.openModalConfigRutas = true;
        },
        openModalGenerarManifiesto(programacion){
            this.programacion = programacion;
            this.visible = true;
        },
        desactivar(programacion){
            this.$http.put(`/transportes/programacion/${programacion.id}/desactivar`).then(response => {
                this.$message({
                    type: 'success',
                    message: response.data.message
                });
                location.reload();
            }).catch(error => {
                let response = error.response;
                this.$message({
                    type: 'error',
                    message: response.data.message
                });
            });
        },
        activar(programacion){
            this.$http.put(`/transportes/programacion/${programacion.id}/activar`).then(response => {
                this.$message({
                    type: 'success',
                    message: response.data.message
                });
                location.reload();
            }).catch(error => {
                let response = error.response;
                this.$message({
                    type: 'error',
                    message: response.data.message
                });
            });
        }
    },
};
</script>
