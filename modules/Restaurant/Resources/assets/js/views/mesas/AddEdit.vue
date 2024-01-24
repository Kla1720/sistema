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
                            <label for="nombre">Numero</label>
                            <input type="text" id="nombre" class="form-control" v-model="form.numero" :class="{ 'is-invalid': errors.numero }"/>
                            <div v-if="errors.numero" class="invalid-feedback">{{ errors.numero[0] }}</div>
                        </div>

                        <div class="form-group">
                            <label for="nombre">Nivel</label>
                            <el-select placeholder="Seleccionar nivel" v-model="form.nivel_id" :class="{ 'el-form-item is-error': errors.nivel_id}">
                                <el-option
                                    v-for="nivel in niveles"
                                    :key="nivel.id"
                                    :value="nivel.id"
                                    :label="nivel.nombre"
                                ></el-option>
                            </el-select>
                            <div v-if="errors.nivel_id" class="invalid-feedback">{{ errors.nivel_id[0] }}</div>
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
            mesa: {
                type: Object,
                required: false,
                default: {},
            },
            niveles: {
                type: Array,
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
                resource:'restaurant/mesas',
                loading_search:false
            };
        },
        mounted(){

        },
        methods: {
            onUpdate() {
                this.loading = true;
                this.$http
                    .put(`/restaurant/mesas/${this.mesa.id}/update`, this.form)
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
                console.log(this.form);
                this.$http
                    .post("/restaurant/mesas/store", this.form)
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
                if (this.mesa) {
                    this.onUpdate();
                } else {
                    this.onStore();
                }
            },
            onClose() {
                this.$emit("update:visible", false);
            },
            onCreate() {
                if (this.mesa) {
                    this.form = this.mesa;
                    this.title = "Editar mesa";
                } else {
                    this.title = "Crear mesa";
                    this.form = {
                        activo: true,
                    };
                }
            }
        },
    };
</script>
