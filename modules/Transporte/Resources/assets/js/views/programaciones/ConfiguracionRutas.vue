<template>
    <el-dialog :title="title" :visible="visible" @close="onClose" @open="onCreate" width="700px">

        <div v-if="programacion" class="form-body">
            <template v-for="terminal in terminales">
                <div v-if="(terminal.id != programacion.terminal_origen_id && terminal.id != programacion.terminal_destino_id)"  class="form-group row" :key="terminal.id">
                    <!-- <label for="">{{ terminal.nombre }}</label> -->
                    <el-checkbox v-model="rutas" :label="terminal.id">{{ terminal.nombre }} </el-checkbox>
                </div>
            </template>
        </div>
        <div class="row">
            <div class="col-6">
                <el-button
                    native-type="submit"
                    :disabled="loading"
                    type="primary"
                    class="btn-block"
                    :loading="loading"
                    @click="guardarConfiguracion"
                >Guardar</el-button>
            </div>
            <div class="col-6">
                <el-button class="btn-block" @click="onClose">Cancelar</el-button>
            </div>
        </div>
    
    </el-dialog>
    
</template>
<script>
export default {
    props:{
        visible: {
            type: Boolean,
            required: true,
            default: false,
        },
        programacion: {
            type: Object,
            required: false,
            default: () => {},
        },
        terminales: {
            type: Array,
            required: false,
            default: () => [],
        },
    },
    data(){
        return ({
            form:{},
            title:'Configuración de rutas',
            rutas:[],
            loading:false,
        });
    },
    methods:{
        onClose() {
            this.$emit("update:visible", false);
        },
        onCreate() {
            this.rutas = [];
            if (this.programacion) {
                this.form = this.programacion;
                this.rutas = this.programacion.rutas.map( ruta => ruta.terminal_id);
            }
        },
        async guardarConfiguracion(evt){
            this.loading = true;
            try{

                const { data } = await this.$http.put(`/transportes/programaciones/${this.programacion.id}/configuracion-rutas`,{ rutas: this.rutas });
                this.$emit("onUpdateItem", data.data);
                this.onClose();
            } catch(error){
                this.axiosError(error);
            }finally{
                this.loading = false;
                this.errors = {};
            }
        }
    }
}
</script>