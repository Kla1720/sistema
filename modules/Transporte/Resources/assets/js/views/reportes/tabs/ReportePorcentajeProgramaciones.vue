<template>
    <div>
        <div class="row">
            <div class="col-3">
                <div class="form-group">
                    <label for="">Fecha</label>
                    <el-date-picker
                    v-model="fecha"
                    type="date"
                    value-format="yyyy-MM-dd"
                    placeholder="Fecha">
<!--                    :picker-options="pickerOptions"-->

                    </el-date-picker>
                    <span v-if="errors.fecha" class="invalid-feedback" :style="{display:'block'}">{{ errors.fecha[0] }}</span>
                </div>
            </div>
            <div class="col-3 d-flex">
                <el-button v-if="total > 0" type="primary" @click="imprimirReporte" style="align-self:center">Imprimir</el-button>
            </div>

        </div>
        <div v-loading="loading" class="row mt-2">
            <div class="col-md-12">
                <table class="table table-bordered table-striped table-responsive">

                    <thead>
                        <tr>
                            <td>Placa</td>
                            <td>Nombre</td>
                            <td>Hora de Partida y Ruta</td>
                            <td></td>
                            <td>Asientos vendidos</td>
                            <td>Asientos disponibles</td>
                            <td>Porcentaje vendido</td>
                            <td>Efectivo</td>
                        </tr>
                    </thead>
                    <tbody>

                        <tr v-for="(row, index) in records" :key="index">
                            <td>{{ row.vehiculo.placa }}</td>
                            <td>{{ row.vehiculo.nombre }}</td>
                            <td>
                                {{ row.hora_salida }}  {{ row.origen.nombre }} - {{ row.destino.nombre }}
                            </td>
                            <td>
                                <el-button type="success" size="mini" @click="recien(row)" >
                                    <i class="fa fa-check"></i>
                                </el-button>
                            </td>
                            <td>{{ row.asientos_ocupados }}</td>
                            <td>{{ row.asientos_disponibles }}</td>
                            <td>


                                <progress :value="row.asientos_ocupados" :max="row.vehiculo.asientos"> </progress> {{ row.porcentaje }} %
                            </td>
                            <td>{{ row.total_vendido }}</td>
                        </tr>

                    </tbody>


                </table>
            </div>
            <div class="col-md-12 d-flex justify-content-center">
                <el-pagination
                @current-change="changePage"
                :total="total"
                layout="prev, pager, next">
                </el-pagination>
            </div>



        </div>

        <ModalDetalleBus v-model="showDetails" :transporte-programacion="transporteProgramacion" :fecha="fecha" />

    </div>
</template>
<script>
import moment from 'moment';

import ModalDetalleBus from './ModelDetalleBus.vue'

export default {
    components:{ ModalDetalleBus },
    created(){
        this.fecha = this.now = moment().format('YYYY-MM-DD');
    },
    data(){
        return {
            fecha:null,
            now:null,
            errors:{},
            page:1,
            total:1000,
            records:[],
            loading:false,
            showDetails:false,

            transporteProgramacion:null,

        }
    },
    watch:{
        fecha(newVal){
            if(newVal){
                this.page = 1;
                this.getData();
            }
        }
    },
    computed:{
        pickerOptions(){
            return {
                disabledDate:(time) => {
                    return time.getTime() < moment(this.now);
                },

            }

        }
    },
    methods:{
        async getData(){
            this.loading = true;

            const { data } = await axios.post(`/transportes/reportes/reporte-porcentaje-programaciones/preview`,{
                page: this.page,
                limit: 50,
                oficina: this.oficina,
                fecha: this.fecha
            });

            this.total = data.total;
            this.records = data.data;

            console.log(this.records)

            this.loading = false;
        },
        changePage(page){
            this.page = page;
        },
        imprimirReporte(){
            let tok = $('meta[name=csrf-token]').attr('content');
            let form = $("<form>").attr({id:"form1",target:"_blank",method:"POST",action:"/transportes/reportes/reporte-porcentaje-programaciones"});
            let fecha = $("<input>").attr({type:"text",name:"fecha",value:this.fecha});

            let token = $("<input>").attr({type:"text",name:"_token",value:tok});
            form.append(token,fecha);
            $(document.body).append(form);
            form.submit();
            $("#form1").remove();

        },
        recien(row){
            this.transporteProgramacion = row;

            this.$nextTick(() =>  this.showDetails = true)
           
            // alert('En proceso de implementación')
        }
    },


}
</script>
