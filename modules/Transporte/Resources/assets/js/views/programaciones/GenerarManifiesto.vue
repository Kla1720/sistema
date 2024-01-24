<template>
    <el-dialog :title="title" :visible="visible" @close="onClose" @open="onCreate" width="750px">
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <label for="">Serie</label>
                    <el-select v-model="manifiesto.serie" placeholder="Serie">
                        <el-option
                        v-for="serie in series"
                        :key="serie.id"
                        :label="serie.number"
                        :value="serie.id">
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
                     <el-select v-model="manifiesto.tipo" placeholder="Tipo">
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
            <div class="col-3">
                <div class="form-group">
                    <label for="">Chofer</label>
                     <el-select v-model="manifiesto.chofer_id" placeholder="Chofer">
                        <el-option
                        v-for="chofer in choferes"
                        :key="chofer.id"
                        :label="chofer.nombre"
                        :value="chofer.id">
                        </el-option>
                    </el-select>
                    <span v-if="errors.chofer_id" class="invalid-feedback" :style="{display:'block'}">{{ errors.chofer_id[0] }}</span>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="">Copiloto</label>
                     <el-select v-model="manifiesto.copiloto_id" placeholder="Chofer">
                        <template  v-for="copiloto in choferes">
                            <el-option
                            v-if="manifiesto.chofer_id != copiloto.id"
                            :key="copiloto.id"
                            :label="copiloto.nombre"
                            :value="copiloto.id">
                            </el-option>

                        </template>

                    </el-select>
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
        <!-- <div v-loading="buscando" class="row mt-2">
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
        </div> -->
        <div class="row mt-2">
            <div class="col-12 text-center">
                <el-button :loading="loading" type="primary" @click="onStore">
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
        },
        programacion:{
            type:Object|null,
            default:null
        },
        choferes:{
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
            errors:{}
        });
    },
    methods:{
        onClose(){
            this.$emit('update:visible',false);
        },
        onCreate(){
            if(this.programacion){
                this.manifiesto.programacion_id = this.programacion.id;
                this.manifiesto.hora = this.programacion.hora_salida;
            }
        },
        onStore(){
            alert('hula');
            this.loading = true;
            this.$http.post('/transportes/manifiestos/guardar-manifiesto',this.manifiesto)
            .then( async response => {
                this.loading = false;

                await this.$message({
                    type: 'success',
                    message: response.data.message
                });
                window.open(`/transportes/manifiestos/${response.data.manifiesto.id}/imprimir-manifiesto`);
                this.onClose();
            }).catch(error => {
                this.loading = false;
                this.axiosError(error);
            });
        },
    }

}
</script>
