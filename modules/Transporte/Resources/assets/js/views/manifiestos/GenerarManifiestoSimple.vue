<template>
    <el-dialog :title="title" :visible="visible" @close="onClose" @open="onCreate" width="750px">
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <label for="">Serie</label>
                    <el-select v-model="manifiesto.serie">
                        <el-option
                        v-for="serie in all_series"
                        :key="serie.id"
                        :label="serie.number"
                        :value="serie.number">
                        </el-option>
                    </el-select>
                    <span v-if="errors.serie" class="invalid-feedback" :style="{display:'block'}">{{ errors.serie[0] }}</span>
                </div>

            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="">Fecha</label>
                    <el-date-picker
                    v-model="manifiesto.fecha"
                    type="date"
                    value-format="yyyy-MM-dd"
                    placeholder="Fecha">
                    </el-date-picker>
                    <span v-if="errors.fecha" class="invalid-feedback" :style="{display:'block'}">{{ errors.fecha[0] }}</span>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="">Hora</label>
                    <el-input v-model="manifiesto.hora" type="text" disabled></el-input>
                    <span v-if="errors.hora" class="invalid-feedback" :style="{display:'block'}">{{ errors.hora[0] }}</span>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Conductor</label>
                     <select v-model="manifiesto.chofer_id" class="form-control">
                        <option
                        v-for="chofer in choferes"
                        :key="chofer.id"
                        :label="chofer.nombre"
                        :value="chofer.id">
                        </option>
                    </select>
                    <span v-if="errors.chofer_id" class="invalid-feedback" :style="{display:'block'}">{{ errors.chofer_id[0] }}</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">Copiloto</label>
                     <select v-model="manifiesto.copiloto_id" class="form-control">
                        <template  v-for="copiloto in choferes">
                            <option
                            v-if="manifiesto.chofer_id != copiloto.id"
                            :key="copiloto.id"
                            :label="copiloto.nombre"
                            :value="copiloto.id">
                            </option>

                        </template>

                    </select>
                    <span v-if="errors.copiloto_id" class="invalid-feedback" :style="{display:'block'}">{{ errors.copiloto_id[0] }}</span>
                </div>
            </div>

            <div class="col-6">
                <label for="">Observaciones</label>
                <div class="form-group">
                    <el-input rows="10" v-model="manifiesto.observaciones" type="textarea"></el-input>
                </div>
            </div>
        </div>
        <div class="row">

            <div class="col-md-4">
                <div class="form-group" v-if="origen">
                    <label for="">Origen</label>
                    <el-input v-model="origen.nombre"></el-input>
                </div>

            </div>
            <div class="col-md-4">
                 <div class="form-group" v-if="destino">
                    <label for="">Destino</label>
                    <el-input v-model="destino.nombre">
                    </el-input>
                </div>
            </div>

        </div>
        <div v-if="programacion" class="row mt-2">
            <div class="col-12 text-center">
                <el-button type="primary" @click="onStore">
                    <i class="fa fa-save"></i>
                    Generar manifiesto
                </el-button>
            </div>

        </div>
    </el-dialog>
</template>
<script>
import moment from 'moment'
export default {
    props:{
        visible: {
            type: Boolean,
            required: true,
            default: false,
        },
        series:{
            type:Array,
            default:() => []
        },
        origen:{
            type:Object|null,
            required:true,
            default:null
        },
        destino:{
            type:Object|null,
            required:true,
            default:null


        },
        programacion: {
            type: Object|null,
            required: true,
            default: () => ({}),
        },
        choferes:{
            type:Object|null,
            required:true,
            default:null
        },
        fecha:{
            type:String,
            default:null
        },
        existe_manifiesto:{
            type: Boolean,
            required: true,
            default: false,
        }
    },
    emit:['update:existe_manifiesto'],
    data(){
        return ({
            loadingOrigen:false,
            loadingDestino:false,
            title:'Crear nuevo manifiesto',
            manifiesto:{
                id:null,
                tipo:2,
                serie:null,
                fecha:null,
                hora:null,
                observaciones:null,
                programacion_id:null
            },
            buscando:false,
            loading:false,
            all_series:{},
            errors:{}
        });
    },
    methods:{
        onClose(){
            this.$emit('update:visible',false);
        },
        onCreate(){
            this.initForm();
            this.manifiesto.fecha= this.fecha
            this.manifiesto.hora= this.programacion.hora_salida
            this.manifiesto.programacion_id= (this.programacion) ? this.programacion.programacion_id : 0

            // this.$emit('update:visible',false);
        },
        onStore(){
            this.loading = true;
            this.$http.post('/transportes/manifiestos/guardar-manifiesto',this.manifiesto)
            .then( async response => {
                this.loading = false;

                await this.$message({
                    type: 'success',
                    message: response.data.message
                });
                this.$emit('onAddUpdateManifiesto');
                this.$emit('update:existe_manifiesto', true)
                window.open(`/transportes/manifiestos/${response.data.manifiesto.id}/imprimir-manifiesto`);
                this.onClose();
            }).catch(error => {
                this.loading = false;
                this.axiosError(error);
            });
        },
        initForm(){

            this.manifiesto.fecha= moment().format('YYYY-MM-DD');
            if(this.manifiesto.tipo==1){//encomienda
                this.all_series = _.filter(this.series, {'document_type_id': '100'});
            }
            else{// pasajes
               this.all_series = _.filter(this.series, {'document_type_id': '33'});
            }
            this.manifiesto.serie = (this.all_series.length > 0)?this.all_series[0].number:null


        },
        cleanForm(){
            this.manifiesto.id = null;
            this.manifiesto.tipo = null;
            this.manifiesto.serie = null;
            this.manifiesto.fecha = null;
            this.manifiesto.hora = null;
            this.manifiesto.chofer_id = null;
            this.manifiesto.copiloto_id  = null;
            this.manifiesto.observaciones = null;
            this.manifiesto.programacion_id = null;
            this.origen = null;
            this.destino = null;
        },
        async onUpdate(){

            try{

                this.loading = true;
                const { data } = await this.$http.put(`/transportes/manifiestos/${this.manifiesto.id}/actualizar-manifiesto`,this.manifiesto);
                this.loading = false;

                await this.$message({
                    type: 'success',
                    message: data.message
                });
                this.$emit('update:visible',false);
                this.$emit('onAddUpdateManifiesto');

            }catch(error){
                this.loading = false;
                if(error.response) this.axiosError(error);

            }

        }
    }

}
</script>
