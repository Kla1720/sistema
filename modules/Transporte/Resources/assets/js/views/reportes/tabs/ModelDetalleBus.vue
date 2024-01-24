<template>
    <el-dialog title="" :visible="visible" @close="onClose" @open="onCreate" :close-on-click-modal="false" fullscreen>
        <el-card title="Detalle">
            <div slot="header" class="clearfix">
                <span>Detalle</span>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div v-if="vehiculo" class="row justify-content-center">
                        <div class="form-group">
                            <label for="" class="pl-3 text-right pr-2 control-label" style="width:40%;float: left">Piso</label>
                            <div style="width: 60%;float: left">
                                <el-select v-model="piso" placeholder="Piso" :disabled="vehiculo.pisos == 1">
                                    <el-option v-for="floor in vehiculo.pisos" :label="floor" :key="floor" :value="floor" >
                                    </el-option>
                                </el-select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div v-if="asientos.length > 0 && vehiculo" style="width: 100%">
                    <Bus v-if="piso == 1" :change-color="changeColor" :image-front="vehiculo.img_front" :image-back="vehiculo.img_back" :seats.sync="asientosPisoUno" :ancho-vehiculo="vehiculo.ancho_vehiculo" @dbclick="dbClick"  />
                    <Bus v-if="piso == 2" :change-color="changeColor" :image-front="vehiculo.img_front" :image-back="vehiculo.img_back"  :seats.sync="asientosPisoDos" :ancho-vehiculo="vehiculo.ancho_vehiculo" @dbclick="dbClick"  />
                    
                </div>
            </div>
            <div v-if="listaOcupados.length > 0" class="row border border-solid p-2 mt-2 radius radius-30">
                <div v-for="(ocupado, index) in listaOcupados" class="col-md-12 mt-2" :key="index">
                    Viaje de <el-tag style="color:white" :color="ocupado.origen.color"> {{ ocupado.origen.nombre }} </el-tag>  a <el-tag style="color:white" :color="ocupado.destino.color"> {{ ocupado.destino.nombre }} </el-tag>
                </div>
            </div>
        </el-card>
        
    </el-dialog>
</template>
<script>

import Bus from '../../bus/Bus.vue';
import { Loading } from 'element-ui';
export default {
    components:{ Bus },
    props:{
        value:{
            type:Boolean,
            default: false
        },
        transporteProgramacion:{
            type:Object,
            default: () => ({})
        },
        fecha:{
            type:String,
            default: null,
        }

    },
    data(){
        return {
            asientos:[],
            vehiculo:null,
            ocupados: [],
            piso:1,
            listaOcupados: []
        }
    },
    computed:{
        visible:{
            get(){
                return this.value
            },
            set(value){
                this.$emit('input',value)
            }
        },
        asientosPisoUno:function(){
            return this.asientos.filter(  asiento => asiento.piso == 1);
        },
        asientosPisoDos:function(){
            return this.asientos.filter(  asiento => asiento.piso == 2);
        },
    },
    methods:{

        changeColor(asiento,config){

            const existePasaje = this.ocupados.find(  item => item.asiento_id == asiento.id );

            if(existePasaje) {
                if(existePasaje.estado_asiento_id == 2){
                    if(config.isBelt){ //Hay un path que se pinta de azul
                        return{
                            fill:'#fff'
                        };
                    }

                    return {
                        fill: existePasaje.origen.color || '#ff0000', // '#ff0000'
                        animation:'none',
                        color: '#fff'
                    }
                }

                if(existePasaje.estado_asiento_id == 3){
                    if(config.isSeat){
                        return {
                            fill:'#fff',
                            animation:'reservado 1s infinite'
                        }
                    }

                    return {
                        fill:'#fff',
                        animation:'none'
                    }
                }
            }

            if(this.asiento) {
                if(asiento.id == this.asiento.id){
                    if(config.isSeat){
                        return {
                            fill:'#ff6600',
                            animation:'reservado 1s infinite'
                        }
                    }
                    return {
                        fill:'#ff6600',
                        animation:'none'
                    }

                }
            }

            return {
                fill:'#fff',
                animation:'none'
            }
        },

        dbClick(asiento){

            this.listaOcupados = this.ocupados.filter( item => item.asiento_id == asiento.id  );

            // console.log(asiento)
        },

        async onCreate(){

            if(!this.transporteProgramacion) return;


            this.vehiculo = this.transporteProgramacion.vehiculo;
            this.asientos = this.vehiculo.seats;

            this.ocupados = this.transporteProgramacion.ocupados;

        },
        onClose(){
            this.vehiculo = null;
            this.asientos = [];
            this.ocupados = [];
            this.listaOcupados = [];

            this.visible = false;

        }

    }

}
</script>
<style >

.overlay-spinner{

    z-index: 2005 !important;

}

</style>