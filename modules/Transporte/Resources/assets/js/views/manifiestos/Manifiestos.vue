<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>REGISTRO DE MANIFIESTOS</span></li>
            </ol>
        </div>
        <div class="card mb-0">
            <!-- <div class="card-header bg-info">
                <h3 class="my-0">Listado de manifiestos</h3>
            </div> -->
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <el-tabs v-model="tab" >
                            <el-tab-pane label="Pasajes" name="pasajes">

                                <div class="row">
                                    <div class="col-md-12 d-flex justify-content-end">
                                        <el-button type="primary" @click="onCreate(2)"> Nuevo </el-button>
                                    </div>
                                    <div v-loading="loadingPasajes" class="col-md-12 mt-2">
                                        <table class="table table-bordered table-striped">
                                            <template v-if="listPasajes.length > 0">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Número</th>
                                                    <th>Origen - Destino</th>
                                                    <th>Chofer</th>
                                                    <th>Copiloto</th>
                                                    <th>Fecha salida</th>
                                                    <th>Hora salida</th>
                                                    <th>Observaciones</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(manifiesto, index) in listPasajes" :key="manifiesto.id">
                                                    <td>{{index+1}}</td>
                                                    <td>{{ manifiesto.serie }}-{{ manifiesto.numero }}</td>
                                                    <td>{{ manifiesto.programacion.origen.nombre }} - {{ manifiesto.programacion.destino.nombre }}</td>
                                                    <td>{{ manifiesto.chofer.nombre }}</td>
                                                    <td>
                                                        <span v-if="manifiesto.copiloto">{{ manifiesto.copiloto.nombre }}</span>
                                                    </td>
                                                    <td>{{ manifiesto.fecha }}</td>
                                                    <td>{{ manifiesto.hora }}</td>
                                                    <td>{{ manifiesto.observaciones }}</td>
                                                    <td class="text-center">


                                                        <el-tooltip class="item" effect="dark" content="Editar" placement="top-start">
                                                            <el-button type="default" @click="editar(manifiesto)">
                                                                <i class="fa fa-edit"></i>
                                                            </el-button>
                                                        </el-tooltip>
                                                        <el-tooltip class="item" effect="dark" content="Imprimir" placement="top-start">
                                                            <el-button type="primary" @click="imprimir(manifiesto)">
                                                                <i class="fa fa-file-alt"></i>
                                                            </el-button>
                                                        </el-tooltip>
                                                        <el-tooltip class="item" effect="dark" content="Registrar asistencia" placement="top-start">
                                                            <el-button type="primary" @click="registrarAsistencia(manifiesto)">
                                                                <i class="fa fa-bus"></i>
                                                            </el-button>
                                                        </el-tooltip>


                                                        <!-- <el-button type="danger" @click="onDelete(encomienda)">
                                                            <i class="fa fa-trash"></i>
                                                        </el-button> -->
                                                    </td>
                                                </tr>

                                                </tbody>
                                            </template>
                                            <template v-else>
                                                <tr>
                                                    <td class="text-center" colspan="8">
                                                        <el-alert
                                                            center
                                                            title="No hay manifiestos registrados"
                                                            type="info"
                                                            :closable="false">
                                                        </el-alert>
                                                    </td>
                                                </tr>
                                            </template>

                                        </table>
                                    </div>
                                </div>

                            </el-tab-pane>
                            <el-tab-pane label="Encomiendas" name="encomiendas">
                                <div class="row">
                                    <div class="col-md-12 d-flex justify-content-end">
                                        <el-button type="primary" @click="onCreate(1)" >Nuevo</el-button>
                                        <!-- <el-button type="primary" >Nuevo</el-button> -->
                                    </div>
                                    <div v-loading="loadingEncomiendas" class="col-md-12 mt-2">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <template v-if="listManifiestos.length > 0">
                                                    <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Serie</th>
                                                        <th>Número</th>
                                                        <th>Conductor</th>
                                                        <th>Copiloto</th>
                                                        <th>Fecha salida</th>
                                                        <th>Hora salida</th>
                                                        <th>Observaciones</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(manifiesto, index) in listManifiestos" :key="manifiesto.id">
                                                        <td>{{index+1}}</td>
                                                        <td>{{ manifiesto.serie }}</td>
                                                        <td>{{ manifiesto.numero }}</td>
                                                        <td>{{ manifiesto.chofer.nombre }}</td>
                                                        <td>
                                                            <span v-if="manifiesto.copiloto">{{ manifiesto.copiloto.nombre }}</span>
                                                        </td>
                                                        <td>{{ manifiesto.fecha }}</td>
                                                        <td>{{ manifiesto.hora }}</td>
                                                        <td>{{ manifiesto.observaciones }}</td>
                                                        <td class="text-center">


                                                            <!-- <el-button type="success" @click="onEdit(encomienda)">
                                                                <i class="fa fa-edit"></i>
                                                            </el-button> -->
                                                            <el-tooltip class="item" effect="dark" content="Editar" placement="top-start">
                                                                <el-button type="default" @click="editar(manifiesto)">
                                                                    <i class="fa fa-edit"></i>
                                                                </el-button>
                                                            </el-tooltip>
                                                            <el-tooltip class="item" effect="dark" content="Imprimir" placement="top-start">
                                                                <el-button type="primary" @click="imprimir(manifiesto)">
                                                                    <i class="fa fa-file-alt"></i>
                                                                </el-button>
                                                            </el-tooltip>
                                                            <el-tooltip class="item" effect="dark" content="Asignar encomiendas" placement="top-start">
                                                                <el-button @click="asignarEncomiendas(manifiesto)">
                                                                    <i class="fa fa-arrow-right"></i>
                                                                </el-button>
                                                            </el-tooltip>

                                                            <!-- <el-button type="danger" @click="onDelete(encomienda)">
                                                                <i class="fa fa-trash"></i>
                                                            </el-button> -->
                                                        </td>
                                                    </tr>

                                                </tbody>
                                                </template>
                                                <template v-else>
                                                    <tr>
                                                        <td class="text-center" colspan="8">
                                                        <el-alert
                                                            center
                                                            title="No hay manifiestos registrados"
                                                            type="info"
                                                            :closable="false">
                                                            </el-alert>
                                                        </td>
                                                    </tr>
                                                </template>

                                            </table>
                                        </div>
                                    </div>
                                </div>


                            </el-tab-pane>
                        </el-tabs>
                    </div>

                </div>

            </div>
        </div>

        <generar-manifiesto
        :tipo="tipo"
        :visible.sync="visible"
        :series="series"
        :programacion="programacion"
        :choferes="choferes"
        @onAddUpdateManifiesto="onAddUpdateManifiesto"
        :item-manifiesto="manifiesto"
        />

        <tenant-transporte-asistencia-modal
            :visible.sync="visibleAsistencia"
            :programacion="programacion"
            :manifiesto="manifiesto"
        />

    </div>
</template>
<script>
import AddEdit from './AddEdit';
import GenerarManifiesto from './GenerarManifiesto.vue';
import AsistenciaModal from './AsistenciaModal.vue';
export default{
    components: {
        AddEdit,
        GenerarManifiesto,
        AsistenciaModal
    },
    props:{
        series:{
            type:Array,
            default:() => []
        },
        choferes:{
            type:Array,
            default:() => []
        }
    },
    created(){
        this.getManifiestosEncomiendas();
        this.getManifiestosPasajes();
    },
    data(){
        return({
            loadingEncomiendas:false,
            loadingPasajes:false,
            tipo:null,
            tab:'pasajes',
            visible:false,
            visibleAsistencia:false,
            programacion:null,
            manifiesto:null
        });
    },
    methods:{

        onCreate(tipo){
            this.tipo = tipo;
            this.visible = true;
        },
        imprimir(manifiesto){
            window.open(`/transportes/manifiestos/${manifiesto.id}/imprimir-manifiesto`);
        },
        onAddUpdateManifiesto(){
            this.manifiesto = null;
            this.getManifiestosEncomiendas();
            this.getManifiestosPasajes();
        },

        async getManifiestosEncomiendas(){
            try {
                this.listManifiestos = [];

                this.loadingEncomiendas = true;
                const { data } = await this.$http.get('/transportes/manifiestos/get-manifiestos?tipo=1');
                this.loadingEncomiendas = false;
                this.listManifiestos = data;

            } catch (error) {
                this.loadingEncomiendas = false;
                if(error.response) this.axiosError(error);
            }
        },
        async getManifiestosPasajes(){
            try {
                this.listPasajes = [];
                this.loadingPasajes = true;
                const { data } = await this.$http.get('/transportes/manifiestos/get-manifiestos?tipo=2');
                this.loadingPasajes = false;
                this.listPasajes = data;

            } catch (error) {
                this.loadingPasajes = false;
                if(error.response) this.axiosError(error);
            }
        },

        asignarEncomiendas(manifiesto){
            window.location.replace(`/transportes/manifiestos/${manifiesto.id}/asignar-encomiendas`);
        },
        editar(manifiesto){
            this.manifiesto = manifiesto;
            this.visible = true;
        },
        registrarAsistencia(manifiesto){
            this.manifiesto = manifiesto
            this.programacion = manifiesto.programacion
            this.visibleAsistencia = true;
        }
    }

}
</script>
