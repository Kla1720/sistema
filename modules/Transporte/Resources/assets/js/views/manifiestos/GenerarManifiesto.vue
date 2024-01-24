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
            <div class="col-3">
                <div class="form-group">
                    <label for="">Tipo</label>
                     <el-select v-model="manifiesto.tipo" placeholder="Tipo" disabled>
                        <el-option
                        v-for="tipo in tipos"
                        :key="tipo.id"
                        :label="tipo.descripcion"
                        :value="tipo.id">
                        </el-option>
                    </el-select>
                    <span v-if="errors.tipo" class="invalid-feedback" :style="{display:'block'}">{{ errors.tipo[0] }}</span>
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
                <div class="form-group">
                    <label for="">Origen</label>
                    <el-select v-model="origen" filterable remote  popper-class="el-select-customers"
                        placeholder="Buscar origen"
                        :remote-method="searchOrigen"
                        :loading="loadingOrigen"
                        @change="getProgramaciones">
                        <el-option v-for="origen in origenes" :key="origen.id" :value="origen.id" :label="origen.nombre">

                        </el-option>
                    </el-select>
                </div>

            </div>
            <div class="col-md-4">
                 <div class="form-group">
                    <label for="">Destino</label>
                    <el-select v-model="destino" filterable remote  popper-class="el-select-customers"
                        placeholder="Buscar destino"
                        :remote-method="searchDestino"
                        :loading="loadingDestino"
                        @change="getProgramaciones"
                    >
                        <el-option v-for="destino in destinos" :key="destino.id" :value="destino.id" :label="destino.nombre">

                        </el-option>
                    </el-select>
                </div>
            </div>

        </div>
        <div v-loading="buscando" class="row mt-2">
            <div class="col-md-12 table-responsive">
                <table class="table table-bordered table-striped">
                    <template v-if="listProgramaciones.length > 0">
                        <thead>
                            <tr>
                                <th>Vehiculo</th>
                                <th>Origen</th>
                                <th>Destino</th>
                                <th>Hora Salida</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="programacion in listProgramaciones" :key="programacion.id">
                                <td>{{ programacion.vehiculo.placa }}</td>
                                <td>{{ programacion.origen.nombre }}</td>
                                <td>{{ programacion.destino.nombre }}</td>
                                <td>{{ programacion.hora_salida }}</td>
                                <td class="text-center">

                                    <el-button v-if="manifiesto.programacion_id == programacion.id"  type="danger" @click="manifiesto.programacion_id = null">
                                        <i class="fa fa-trash"></i>
                                    </el-button>
                                    <el-button v-else type="success" @click="selectProgramacion(programacion)">
                                        <i class="fa fa-check"></i>
                                    </el-button>
                                </td>
                            </tr>

                        </tbody>
                    </template>
                    <template v-else>
                        <tr>
                            <td class="text-center" colspan="8">
                                <el-alert
                                center
                                title="Sin Programaciones"
                                type="info"
                                :closable="false">
                                </el-alert>
                            </td>
                        </tr>
                    </template>



                </table>
            </div>
        </div>
        <div v-if="manifiesto.programacion_id" class="row mt-2">
            <div class="col-12 text-center">
                <el-button v-if="!itemManifiesto" :loading="loading" type="primary" @click="onStore">
                    <i class="fa fa-save"></i>
                    Generar manifiesto
                </el-button>
                <el-button v-else :loading="loading" type="primary" @click="onUpdate">
                    <i class="fa fa-save"></i>
                    Actualizar
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
        programacion:{
            type:Object|null,
            default:null
        },
        choferes:{
            type:Array,
            default:() => []
        },
        tipo:{
            type:Number|null,
            default:1
        },
        itemManifiesto:{
            type:Object|null,
            default:null
        }
    },
    data(){
        return ({
            loadingOrigen:false,
            loadingDestino:false,
            title:'Crear nuevo manifiesto',
            origen:null,
            destino:null,
            manifiesto:{
                id:null,
                tipo:null,
                serie:null,
                fecha:null,
                hora:null,
                observaciones:null,
                programacion_id:null
            },
            listProgramaciones:[],
            origenes:[],
            destinos:[],
            buscando:false,
            loading:false,
            tipos:[
                {
                    id:1,
                    descripcion:'Encomiendas'
                },
                {
                    id:2,
                    descripcion:'Pasajes'
                },
            ],
            all_series:{},
            errors:{}
        });
    },
    methods:{

        selectProgramacion(programacion){
            this.manifiesto.hora = programacion.hora_salida;
            this.manifiesto.programacion_id = programacion.id;
        },
        onClose(){
            this.$emit('update:visible',false);
        },
        onCreate(){
            this.cleanForm();
            this.listProgramaciones = [];
            this.manifiesto.tipo = this.tipo;
            this.searchOrigen();
            this.searchDestino();

            if(this.itemManifiesto){
                this.manifiesto.id = this.itemManifiesto.id;
                this.manifiesto.tipo = parseInt(this.itemManifiesto.tipo);
                this.manifiesto.serie = this.itemManifiesto.serie.id;
                this.manifiesto.fecha = this.itemManifiesto.fecha;
                this.manifiesto.hora = this.itemManifiesto.hora;
                this.manifiesto.observaciones = this.itemManifiesto.observaciones;
                this.manifiesto.chofer_id = this.itemManifiesto.chofer_id;
                this.manifiesto.copiloto_id = this.itemManifiesto.copiloto_id;
                this.manifiesto.programacion_id = this.itemManifiesto.programacion_id;
                this.listProgramaciones.push(this.itemManifiesto.programacion);
            }
            this.initForm();
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
        async searchOrigen(param){
            this.loadingOrigen = true;
            let search = param || '';
            const data = await this.getTerminales(search);
            this.loadingOrigen = false;
            this.origenes = data;
        },
        async searchDestino(param){
            this.loadingDestino = true;
            let search = param || '';
            const data = await this.getTerminales(search);
            this.loadingDestino = false;
            this.destinos = data;
        },
        async getTerminales(param){
            const { data } = await axios.get(`/transportes/encomiendas/get-terminales?search=${param}`);
            return data.terminales;
        },
        async getProgramaciones(){
            if(this.destino && this.origen && this.manifiesto.fecha){
                this.buscando = true;
                 let data = {
                    origen_id:this.origen,
                    destino_id:this.destino,
                    fecha_salida:this.manifiesto.fecha
                }
                const { data:programaiones } = await this.$http.post(`/transportes/manifiestos/get-programaciones`,data);
                this.buscando = false;
                this.listProgramaciones = programaiones.programaciones;
            }

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
