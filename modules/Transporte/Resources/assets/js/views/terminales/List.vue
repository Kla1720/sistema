<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>REGISTRO DE TERMINALES</span></li>
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
                <h3 class="my-0">Listado de terminales</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th></th>
                            <th>Nombre</th>
                            <th>Dirección</th>
                            <th>Ciudad</th>
                            <th>Color de asiento</th>
                            <th></th>
                            <!-- <th>Licencia</th>
                            <th>Categoría</th> -->
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="terminal in listTerminales" :key="terminal.id">
                            <td class="text-right">{{ terminal.id }}</td>
                            <td>{{ terminal.nombre }}</td>
                            <td>{{ terminal.direccion }}</td>
                            <td>{{ terminal.destino.nombre }}</td>
                            <td class="text-center">
                                <el-tag class="p-3" :color="terminal.color || '#000000'"></el-tag>
                                <!-- <el-switch :active-color="terminal.color || '#000000'" :value="item.color"></el-switch> -->
                            </td>
                            <!-- <td>{{ item.licencia }}</td>
                            <td>{{ item.categoria }}</td> -->
                            <td class="text-center">
                                <el-button type="success" @click="onEdit(terminal)">
                                    <i class="fa fa-edit"></i>
                                </el-button>
                                <el-button type="danger" @click="onDelete(terminal)">
                                    <i class="fa fa-trash"></i>
                                </el-button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>



        <ModalAddEdit
            :visible.sync="openModalAddEdit"
            @onAddItem="onAddItem"
            @onUpdateItem="onUpdateItem"
            :terminal="terminal"
            :destinos="destinos"
        ></ModalAddEdit>
       
    </div>
</template>

<script>
import ModalAddEdit from "./AddEdit";


export default {
    props: {
        terminales: {
            type: Array,
            required: true,
        },
        destinos:{
            type:Array,
            required:true
        }
    },
    components: {
        ModalAddEdit,
        // ConfiguracionRutas
    },
    data() {
        return {
            listTerminales: [],
            terminal: null,
            openModalAddEdit: false,
            loading: false,
        };
    },
    mounted() {
        this.listTerminales = this.terminales;
    },
    methods: {
        onDelete(item) {
            this.$confirm(`¿estás seguro de eliminar al elemento ${item.nombre}?`, 'Atención', {
                confirmButtonText: 'Si, continuar',
                cancelButtonText: 'No, cerrar',
                type: 'warning'
            }).then(() => {
                this.$http.delete(`/transportes/terminales/${item.id}/delete`).then(response => {
                    this.$message({
                        type: 'success',
                        message: response.data.message
                    });
                    this.items = this.items.filter(i => i.id !== item.id);
                }).catch(error => {
                    let response = error.response;
                    this.$message({
                        type: 'error',
                        message: response.data.message
                    });
                });
            }).catch();
        },
        onEdit(terminal) {
            this.terminal = { ...terminal };
            this.openModalAddEdit = true;
        },
        onUpdateItem(terminal) {
            let terminales = this.listTerminales.map((i) => {
                if (i.id === terminal.id) {
                    return terminal;
                }
                return i;
            });

            this.listTerminales = terminales.slice()
        },
        onAddItem(terminal) {
            this.listTerminales.unshift(terminal);
        },
        onCreate() {
            this.terminal = null;
            this.openModalAddEdit = true;
        },
    },
};
</script>
