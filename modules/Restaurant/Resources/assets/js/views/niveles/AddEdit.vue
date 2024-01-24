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
                        <div class="form-group">
                            <label for="nombre">Estado</label>
                            <el-checkbox  v-model="form.activo">

                            </el-checkbox>
                            <div v-if="errors.activo" class="invalid-feedback">{{ errors.activo[0] }}</div>
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
            nivel: {
                type: Object,
                required: false,
                default: {},
            }
        },
        data() {
            return {
                form: {},
                title: "",
                errors: {},
                loading: false,
                resource:'restaurant/niveles',
                loading_search:false
            };
        },
        mounted(){

        },
        methods: {
            onUpdate() {
                this.loading = true;
                this.$http
                    .put(`/${this.resource}/${this.nivel.id}/update`, this.form)
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
                    .post(`/${this.resource}/store`, this.form)
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
                if (this.nivel) {
                    this.onUpdate();
                } else {
                    this.onStore();
                }
            },
            onClose() {
                this.$emit("update:visible", false);
            },
            onCreate() {
                if (this.nivel) {
                    this.form = this.nivel;
                    this.title = "Editar nivel";
                } else {
                    this.title = "Crear nivel";
                    this.form = {
                        activo: true,
                    };
                }
            }
        },
    };
</script>
