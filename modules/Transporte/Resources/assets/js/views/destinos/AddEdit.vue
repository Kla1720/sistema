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
                            <label for="nombre">Nombre</label>
                            <input type="text" id="nombre" class="form-control" v-model="form.nombre" :class="{ 'is-invalid': errors.nombre }"/>
                            <div v-if="errors.nombre" class="invalid-feedback">{{ errors.nombre[0] }}</div>
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
            destino: {
                type: Object,
                required: false,
                default: {},
            },
        },
        data() {
            return {
                form: {},
                title: "",
                errors: {},
                loading: false,
                resource:'transportes/destinos',
                loading_search:false
            };
        },
        mounted(){

        },
        methods: {
            onUpdate() {
                this.loading = true;
                this.$http
                    .put(`/transportes/destinos/${this.destino.id}/update`, this.form)
                    .then((response) => {
                        this.$emit("onUpdateItem", response.data.data);
                        this.onClose();
                    })
                    .finally(() => {
                        this.loading = false;
                        this.errors = {};
                    })
                    .catch((error) => {
                        this.axiosError(error);
                    });
            },
            onStore() {
                this.loading = true;
                this.$http
                    .post("/transportes/destinos/store", this.form)
                    .then((response) => {
                        this.$emit("onAddItem", response.data.data);
                        this.onClose();
                    })
                    .finally(() => {
                        this.loading = false;
                        this.errors = {};
                    })
                    .catch((error) => {
                        this.axiosError(error);
                    });
            },
            onSubmit() {
                if (this.destino) {
                    this.onUpdate();
                } else {
                    this.onStore();
                }
            },
            onClose() {
                this.$emit("update:visible", false);
            },
            onCreate() {
                if (this.destino) {
                    this.form = this.destino;
                    this.title = "Editar destino";
                } else {
                    this.title = "Crear destino";
                    this.form = {
                        active: true,
                    };
                }
            }
        },
    };
</script>
