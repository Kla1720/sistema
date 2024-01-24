<template>
    <el-dialog
        :title="title"
        :visible="visible"
        @close="onClose"
        @open="onCreate"
        width="400px"
    >
        <form autocomplete="off" @submit.prevent="onSubmit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-7 col-lg-7 col-xl-7 col-sm-7">

                        <div class="form-group">
                            <label for="nombre">Usuarios</label>
                            <el-select v-model="form.user_id" id="user_id" name="user_id">
                                    <el-option v-for="usuario in usuarios" :key="usuario.id" :value="usuario.id" :label="usuario.name">
                                    </el-option>
                            </el-select>
                        </div>
                        <div class="form-group">
                            <label for="nombre">Terminales</label>
                            <el-select v-model="form.terminal_id" id="terminal_id" name="terminal_id">
                                    <el-option v-for="terminal in terminales" :key="terminal.id" :value="terminal.id" :label="terminal.nombre">
                                    </el-option>
                            </el-select>
                        </div>
                        

                        <div class="row text-center">
                            <div class="col-6">
                                <el-button native-type="submit" :disabled="loading" type="primary" class="btn-block" :loading="loading">Guardar</el-button>
                            </div>
                            <div class="col-6">
                                <el-button class="btn-block" @click="onClose">Cancelar</el-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </el-dialog>
</template>

<script>
    export default {
        props: {
            visible: {
                type: Boolean,
                required: true,
                default: false,
            },
            usuario_terminal: {
                type: Object,
                required: false,
                default: {},
            }
        },
        data() {
            return {
                form: {
                    terminal_id:null,
                    user_id:null
                },
                title: "",
                errors: {},
                loading: false,
                resource:'transportes/usuarios-terminales',
                loading_search:false,
                terminales:[],
                usuarios:[]
            };
        },
        mounted(){

        },
        methods: {
           async onUpdate() {
                this.loading = true;
                try{
                    const { data } =  await this.$http.put(`/transportes/usuarios-terminales/${this.usuario_terminal.id}/update`,this.form);
                    this.$emit("onUpdateItem", data.data);
                    this.onClose();
                }catch(error){
                    this.axiosError(error);
                }finally{
                    this.loading = false;
                    this.errors = {};

                    }
            },
            onStore() {
                this.loading = true;
                this.$http
                    .post("/transportes/usuarios-terminales/store", this.form)
                    .then((response) => {
                        this.$emit("onAddItem", response.data.data);
                        this.onClose();
                    })
                    .finally(() => {
                        this.loading = false;
                        this.errors = {};
                    })
                    .catch((error) => {
                        let response = error.response;
                        this.$message({
                            type: 'error',
                            message: response.data.message
                        });
                    });
            },
            onSubmit() {
                if (this.usuario_terminal) {
                    this.onUpdate();
                } else {
                    this.onStore();
                }
            },
            onClose() {
                this.$emit("update:visible", false);
            },
            onCreate() {
                if (this.usuario_terminal) {
                    this.form = this.usuario_terminal;
                    this.title = "Editar Usuarios Terminales";
                } else {
                    this.title = "Crear Usuarios Terminales";
                    this.form = {
                        active: true,
                    };
                }
                this.getTables();
            },
             async getTables(){
                const { data } = await this.$http.get("/transportes/usuarios-terminales/get-tables");
                this.terminales = data.terminales;
                this.usuarios = data.usuarios;
             }
        },
    };
</script>
