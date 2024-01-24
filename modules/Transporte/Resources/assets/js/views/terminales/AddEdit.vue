<template>
    <el-dialog
        :title="title"
        :visible="visible"
        @close="onClose"
        @open="onCreate"
        width="500px"
    >
        <form autocomplete="off" @submit.prevent="onSubmit">

            
            <div class="form-body">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" name="nombre" id="nombre" class="form-control" v-model="form.nombre" :class="{ 'is-invalid': errors.nombre }"/>
                    <div v-if="errors.nombre" class="invalid-feedback">{{ errors.nombre[0] }}</div>
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <textarea type="text" name="direccion" id="direccion" class="form-control" v-model="form.direccion" :class="{ 'is-invalid': errors.direccion }"/>
                    <div v-if="errors.direccion" class="invalid-feedback">{{ errors.direccion[0] }}</div>
                </div>
                <div class="form-group">
                    <label for="nombre">Ciudad</label>
                    <el-select placeholder="Seleccionar ciudad" v-model="form.destino_id" :class="{ 'el-form-item is-error': errors.destino_id}">
                        <!-- <el-option :value="null">Seleccionar ciudad</el-option> -->
                        <el-option
                            v-for="ciudad in destinos"
                            :key="ciudad.id"
                            :value="ciudad.id"
                            :label="ciudad.nombre"
                        ></el-option>
                    </el-select>
                    <!-- <select name="destino" id="destino" class="form-control" v-model="form.destino_id" :class="{ 'is-invalid': errors.destino_id}"  >
                        <option :value="null" hidden>Seleccionar ciudad</option>
                        
                        <option  :value="ciudad.id" :key="ciudad.id">
                            {{ ciudad.nombre }}
                        </option>
                    </select> -->
                    <!-- <input type="text" id="nombre" class="form-control" v-model="form.nombre" :class="{ 'is-invalid': errors.nombre }"/> -->
                    <span v-if="errors.destino_id" :style="{marginTop: '0.25rem',fontSize: '80%',color: '#dc3545'}">El destino es obligatorio</span>
                </div>
                <div class="form-group">
                    <label for="color">Color Asientos</label>
                    <el-input type="color" name="color" id="color" class="form-control" v-model="form.color" :class="{ 'is-invalid': errors.color }"/>
                    <div v-if="errors.color" class="invalid-feedback">{{ errors.color[0] }}</div>
                </div>
                <!-- <div class="form-group">
                    <label for="licencia">Licencia</label>
                    <input type="text" id="licencia" class="form-control" v-model="form.licencia" :class="{ 'is-invalid': errors.licencia }"/>
                    <div v-if="errors.licencia" class="invalid-feedback">{{ errors.licencia[0] }}</div>
                </div>
                <div class="form-group">
                    <label for="categoria">Categoria</label>
                    <input type="text" id="categoria" class="form-control" v-model="form.categoria" :class="{ 'is-invalid': errors.categoria }"/>
                    <div v-if="errors.categoria" class="invalid-feedback">{{ errors.categoria[0] }}</div>
                </div> -->

                <div class="row text-center">
                    <div class="col-6">
                        <el-button
                            native-type="submit"
                            :disabled="loading"
                            type="primary"
                            class="btn-block"
                            :loading="loading"
                        >Guardar</el-button
                        >
                    </div>
                    <div class="col-6">
                        <el-button class="btn-block" @click="onClose">Cancelar</el-button>
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
        terminal: {
            type: Object,
            required: false,
            default: {},
        },
        destinos:{
            type:Array,
            default:() => []
        },
    },
    data() {
        return {
            form: {
                nombre:null,
                direccion:null,
                destino_id:null,
                color:null,
            },
            title: "",
            errors: {},
            loading: false,
        };
    },
    methods: {
        async onUpdate() {
            this.loading = true;
            try{
                const { data } =  await this.$http.put(`/transportes/terminales/${this.terminal.id}/update`,this.form);
                this.$emit("onUpdateItem", data.data);
                this.onClose();
            }catch(error){
                this.axiosError(error);
            }finally{
                this.loading = false;
                this.errors = {};

            }

            // this.$http
            //     .put(`/transportes/terminal/${this.chofer.id}/update`, this.form)
            //     .then((response) => {
            //         this.$emit("onUpdateItem", response.data.data);
            //         this.onClose();
            //     })
            //     .finally(() => {
            //         this.loading = false;
            //         this.errors = {};
            //     })
            //     .catch((error) => {
            //         this.axiosError(error);
            //     });
        },
        async onStore() {
            this.loading = true;
            try{
                const { data } = await this.$http.post("/transportes/terminales/store",this.form);
                this.$emit("onAddItem", data.data);
                this.onClose();
            } catch(error){
                if(error.response) this.axiosError(error);
                
            }finally{
                this.loading = false;
            }
            
            // this.$http
            //     .post("/transportes/terminales/store", this.form)
            //     .then((response) => {
            //         this.$emit("onAddItem", response.data.data);
            //         this.onClose();
            //     })
            //     .finally(() => {
            //         this.loading = false;
            //         this.errors = {};
            //     })
            //     .catch((error) => {
            //         this.axiosError(error);
            //     });
        },
        onSubmit() {
            if (this.terminal) {
                this.onUpdate();
            } else {
                this.onStore();
            }
        },
        onClose() {
            this.errors = {};
            this.$emit("update:visible", false);
        },
        onCreate() {
            if (this.terminal) {
                this.form = this.terminal;
                this.title = "Editar terminal";
            } else {
                this.title = "Crear terminal";
                this.form = {
                    active: true,
                    destino_id:null,
                    direccion:null
                };
            }
        },
    },
};
</script>
