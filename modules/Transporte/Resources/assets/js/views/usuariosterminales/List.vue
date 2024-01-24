<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>REGISTRO DE USUARIOS</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <div class="btn-group flex-wrap">
                    <button
                        type="button"
                        class="btn btn-custom btn-sm mt-2 mr-2"
                        @click="onCreateUserTerminal"
                    >
                        <i class="fa fa-plus-circle"></i> Agregar permisos
                    </button>
                </div>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-header bg-info">
                <h3 class="my-0">Listado de usuarios</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th></th>
                            <th class="text-center">Nombre</th>
                            <th class="text-center">Terminal Asignado</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="usuario_terminal in users_terminals" :key="usuario_terminal.id">
                            <td class="text-right">{{ usuario_terminal.id }}</td>
                            <td class="text-center">{{ usuario_terminal.user.name}}</td>
                            <td class="text-center">{{ usuario_terminal.terminal.nombre}}</td>
                            <td class="text-center">
                                 <el-tooltip class="item" effect="dark" content="Editar" placement="top-start">
                                    <el-button type="success" @click="onEdit(usuario_terminal)">
                                        <i class="fa fa-edit"></i>
                                    </el-button>
                                </el-tooltip>

                                <el-tooltip class="item" effect="dark" content="Anular" placement="top-start">
                                    <el-button type="danger" @click="onDelete(usuario_terminal)">
                                        <i class="fa fa-trash"></i>
                                    </el-button>
                                </el-tooltip>  
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
            :usuario_terminal="usuario_terminal"
        ></ModalAddEdit>
    </div>
</template>

<script>
    import ModalAddEdit from "./AddEdit";

    export default {
        props: {
            usuarios_terminales: {
                type: Array,
                required: true,
            }
        },
        components: {
            ModalAddEdit,
        },

        data() {
            return {
                users_terminals: null,
                usuario_terminal:null,
                openModalAddEdit: false,
                loading: false,
            };
        },
        mounted() {
            this.users_terminals = this.usuarios_terminales;
        },
        methods: {
           onDelete(usuario_terminal) {
                this.$confirm(`¿estás seguro de eliminar al elemento ${usuario_terminal.user.name}?`, 'Atención', {
                    confirmButtonText: 'Si, continuar',
                    cancelButtonText: 'No, cerrar',
                    type: 'warning'
                }).then(() => {
                    this.$http.delete(`/transportes/usuarios-terminales/${usuario_terminal.id}/delete`).then(response => {
                        this.$message({
                            type: 'success',
                            message: response.data.message
                        });
                        this.users_terminals = this.users_terminals.filter(i => i.id !== usuario_terminal.id);
                    }).catch(error => {
                        let response = error.response;
                        this.$message({
                            type: 'error',
                            message: response.data.message
                        });
                    });
                }).catch();
            },
            onEdit(usuario_terminal) {
                this.usuario_terminal = { ...usuario_terminal };
                this.openModalAddEdit = true;
            },
            onUpdateItem(data) {
                this.users_terminals = this.users_terminals.map((i) => {
                    if (i.id === data.id) {
                        return data;
                    }
                    return i;
                });
            },
            onAddItem(data) {
                this.users_terminals.unshift(data);
            },
            onCreateUserTerminal() {
                this.usuario_terminal = null;
                this.openModalAddEdit = true;
            },
        },
    };
</script>
