<template>
    <el-dialog :title="title" :visible="visible" @close="onClose" @open="onCreate" width="750px">
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <label for="">Serie:</label>
                    <el-select v-model="manifiesto.serie" placeholder="Serie">
                        <el-option
                        v-for="serie in series"
                        :key="serie.id"
                        :label="serie.number"
                        :value="serie.id">
                        </el-option>
                    </el-select>
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
                </div>
            </div>
             <div class="col-3">
               <el-button :style="{marginTop:'1.84rem'}" type="secondary" @click="getProgramaciones">
                   <i class="fa fa-search"></i>
                   Buscar
                </el-button>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-3">
                <label for="">Hora</label>
                <div class="form-group">
                    <el-input v-model="manifiesto.hora" type="text" disabled></el-input>
                </div>
            </div>
            <div class="col-6">
                <label for="">Observaciones</label>
                <div class="form-group">
                    <el-input rows="10" v-model="manifiesto.observaciones" type="textarea"></el-input>
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
                                    
                                    <el-button v-if="manifiesto.programacion_id == programacion.id"  type="danger" @click="deseleccionar">
                                        <i class="fa fa-trash"></i>
                                    </el-button>
                                    <el-button v-else type="success" @click="select(programacion)">
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
        <div v-if="manifiesto.programacion_id != null" class="row mt-2">
            <div class="col-12 text-center">
                <el-button :loading="loading" type="primary">
                    <i class="fa fa-save"></i>
                    Generar manifiesto
                </el-button>
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
        series:{
            type:Array,
            default:() => []
        }
    },
    data(){
        return ({
            title:'Crear nuevo manifiesto',
            manifiesto:{
                id:null,
                serie:null,
                fecha:null,
                hora:null,
                observaciones:null,
                programacion_id:null
            },
            listProgramaciones:[],
            buscando:false,
            loading:false,
        });
    },
    methods:{
        async getProgramaciones(evt){
            try {
                this.manifiesto.programacion_id = null;
                this.manifiesto.hora = null;
                let form = {
                    fecha_salida:this.manifiesto.fecha
                }
                this.buscando = true;
                const { data } = await this.$http.post('/transportes/manifiestos/get-programaciones',form);
                this.buscando = false;
                this.listProgramaciones = data;
                
            } catch (error) {
                this.buscando = false;
                this.listProgramaciones = [];
            }
        },
        select(programacion){
            this.manifiesto.programacion_id = programacion.id;
            this.manifiesto.hora = programacion.hora_salida;

        },
        deseleccionar(evt){
            this.manifiesto.programacion_id = null;
            this.manifiesto.hora = null;
        },
        onClose(){

        },
        onCreate(){

        }
    }
    
}
</script>