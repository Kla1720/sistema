<template>
    <el-dialog :title="title" :visible="visible" @close="onClose" @open="onCreate" width="750px">
        <div class="row">
            <div class="col-md-12">
                <h3 class="p-0 text-center">
                    <b> Trasbordo de <span style="color: #f37914">{{ (this.programacion.vehiculo) ?  this.programacion.vehiculo.placa : '' }}</span> de la fecha <span style="color: #1496f3">{{this.fecha}}  {{this.programacion.hora_salida}}</span></b>
                    <p>Los asientos que no aparecen en el vehículo de destino, estan ocupados para la fecha seleccionada.</p>
                </h3>
            </div>
        </div>
        <div class="row" v-if="programacion">
            <div class="col-6">
                <div class="form-group">
                    <b>Vehículo de origen</b>
                    <p>{{(programacion.vehiculo) ?  programacion.vehiculo.placa :''}}</p>
                </div>

                <div class="form-group">
                    <label for="">Fecha origen</label>
                    <el-date-picker
                        v-model="fecha"
                        type="date"
                        value-format="yyyy-MM-dd"
                        placeholder="Fecha">
                    </el-date-picker>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <b>Seleccione vehículo de destino</b>
                    <el-select v-model="traspaso.vehiculo_destino_id" @change="getAsientos()">
                        <el-option
                            v-for="vehiculo in vehiculos"
                            :key="vehiculo.id"
                            :label="vehiculo.placa"
                            :value="vehiculo.id">
                        </el-option>
                    </el-select>
                </div>

                <div class="form-group">
                    <label for="">Fecha postergada</label>
                    <el-date-picker
                        v-model="traspaso.fecha_viaje"
                        type="date"
                        value-format="yyyy-MM-dd"
                        placeholder="Fecha">
                    </el-date-picker>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <draggable  group="people" @start="drag=true" @end="drag=false">
                    <transition-group type="transition">
                        <div class="list-group-item2 text-white" style="box-shadow: 2px 2px 2px grey;background: #f37914;margin-bottom: 5px" v-for="element in traspaso.asientos_origen" :key="element.asiento_id">
                            <i class="fas fa-chair"></i> Asiento # : {{element.numero_asiento}}
                            <div class="flecha"><i class="fa fa-arrow-right" aria-hidden="true"></i></div>
                        </div>
                    </transition-group>
                </draggable>
            </div>
            <div class="col-md-6">
                <draggable  group="people" @start="drag=true" @end="drag=false">
                    <transition-group type="transition">
                        <div class="list-group-item2 text-white" style="box-shadow: 2px 2px 2px grey;background: #0fc567;margin-bottom: 5px" v-for="element in traspaso.asientos_destino" :key="element.id">
                            <i class="fas fa-chair"></i> Asiento # : {{element.numero_asiento}}
                            <i class="fa fa-arrows" aria-hidden="true"></i>
                        </div>
                    </transition-group>
                </draggable>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12">
                <label for="">Observaciones</label>
                <div class="form-group">
                    <el-input rows="10" v-model="programacion.observaciones" type="textarea"></el-input>
                </div>
            </div>
        </div>

        <div v-if="programacion" class="row mt-2">
            <div class="col-12 text-center">
                <el-button type="primary" @click="onStore">
                    <i class="fa fa-save"></i>
                    Guardar cambios
                </el-button>
            </div>

        </div>
    </el-dialog>
</template>
<script>
import moment from 'moment'
import draggable from 'vuedraggable'
export default {
    components: {
        draggable,
    },
    props:{
        visible: {
            type: Boolean,
            required: true,
            default: false,
        },
        programacion: {
            type: Object|null,
            required: true,
            default: () => ({}),
        },
        fecha:{
            type:String,
            default:null
        },
        asientos_ocupados: {
            type: Object|null,
            required: true,
            default: () => ({}),
        },

    },
    emit:['update:existe_manifiesto'],
    watch: {
        isDragging(newValue) {
            if (newValue) {
                this.delayedDragging = true;
                return;
            }
            this.$nextTick(() => {
                this.delayedDragging = false;
            });
        }
    },
    computed: {
        dragOptions() {
            return {
                animation: 0,
                group: "description",
                disabled: !this.editable,
                ghostClass: "ghost"
            };
        }
    },
    data(){
        return ({
            loadingOrigen:false,
            loadingDestino:false,
            title:'Trasbordo...  acomode los asientos con el mouse.',
            vehiculos:{},
            traspaso:{
                tipo:2,
                observaciones:null,
                programacion_id:null,
                vehiculo_origen_id:null,
                vehiculo_destino_id:null,
                fecha_viaje:null,
                asientos_origen:[],
                asientos_destino:[],
            },
            buscando:false,
            loading:false,
            errors:{},
            isDragging: false,
            delayedDragging: false
        });
    },
    methods:{
        onClose(){
            this.$emit('update:visible',false);
        },
       async onCreate(){
           this.initForm();
           this.getVehiculos()

           this.traspaso.asientos_origen= this.asientos_ocupados

           console.log(this.traspaso.asientos_origen)
        },
        onStore(){
            this.loading = true;
            this.$http.post('/transportes/manifiestos/guardar-traspaso',this.traspaso)
            .then( async response => {
                this.loading = false;

                await this.$message({
                    type: 'success',
                    message: response.data.message
                });
                this.$emit('onAddUpdateManifiesto');
                this.onClose();
            }).catch(error => {
                this.loading = false;
                this.axiosError(error);
            });
        },
        initForm(){


        },
        cleanForm(){
            this.traspaso = null
        },
        async getVehiculos(){
            let param = {}
            const { data } = await this.$http.post(`/transportes/vehiculos/get-all`,param);
            //this.loadingProgramaciones = false;
            this.vehiculos = data;
        },
        async getAsientos(){
            let id = this.traspaso.vehiculo_destino_id
            const { data } = await this.$http.get(`/transportes/vehiculos/${id}/get-asientos`);
            //this.loadingProgramaciones = false;
            this.traspaso.asientos_destino = data;

            console.log(data)
            //console.log(this.traspaso.asientos_destino)
        },
        orderList() {
            this.traspaso.asientos_destino =  this.traspaso.asientos_destino.sort((one, two) => {
                return one.order - two.order;
            });
        },
        onMove({ relatedContext, draggedContext }) {
            const relatedElement = relatedContext.element;
            const draggedElement = draggedContext.element;
            return (
                (!relatedElement || !relatedElement.fixed) && !draggedElement.fixed
            );
        }
    }

}
</script>
<style>
.flip-list-move {
    transition: transform 0.5s;
}
.no-move {
    transition: transform 0s;
}
.ghost {
    opacity: 0.5;
    background: #c8ebfb;
}
.list-group {
    min-height: 20px;
}
.list-group-item2 {
    cursor: move;
    padding: 5px;
}
.list-group-item i {
    cursor: pointer;
}
.flecha{
    color: #0fc567;
    font-size: 23px;
    bottom: unset;
    float: right;
    margin-right: -30px;
}
</style>
