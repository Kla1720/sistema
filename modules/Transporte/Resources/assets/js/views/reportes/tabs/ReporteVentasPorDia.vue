<template>
    <div>
        <div class="row">
            <div class="col-5">
                <div class="form-group">
                    <label for="">Oficina</label>
                    <el-select v-model="oficina" value-key="id">
                        <el-option
                        v-for="oficina in oficinas"
                        :key="oficina.id"
                        :label="oficina.nombre"

                        :value="oficina.id">
                        </el-option>
                    </el-select>
                    <span v-if="errors.serie" class="invalid-feedback" :style="{display:'block'}">{{ errors.oficina[0] }}</span>
                </div>

            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="">Fecha</label>
                    <el-date-picker
                    v-model="fecha"
                    type="date"
                    value-format="yyyy-MM-dd"
                    placeholder="Fecha">
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
                            <td>Nombre</td>
                            <td>Total vendido</td>
                        </tr>
                    </thead>
                    <tbody>

                        <tr v-for="(row, index) in records" :key="index">
                            <td>{{ row.name }}</td>
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

    </div>
</template>
<script>
import moment from 'moment'
export default {
    props:{
        oficinas:{
            type:Array,
            default: () => ([])
        }
    },
    created(){
        this.fecha = moment().format('YYYY-MM-DD');
        let oficinas = [...this.oficinas];
        let oficina = oficinas.shift();
        this.oficina = oficina ? oficina.id : null;
    },
    data(){
        return {
            fecha:null,
            oficina:null,
            errors:{},
            page:1,
            total:1000,
            records:[],
            loading:false,

        }
    },
    watch:{
        oficina(newVal){
            if(newVal){
                this.page = 1;
                this.getData();
            }
        },
        fecha(newVal){
            if(newVal) {
                this.page = 1;
                this.getData();
            }
        }
    },
    methods:{

        async getData(){
            this.loading = true;

            const { data } = await axios.post(`/transportes/reportes/reporte-venta-por-dia/preview`,{
                page: this.page,
                limit: 50,
                oficina: this.oficina,
                fecha: this.fecha
            });

            this.total = data.total;
            this.records = data.data;

            this.loading = false;
        },
        changePage(page){
            this.page = page;
        },
        imprimirReporte(){
            let tok = $('meta[name=csrf-token]').attr('content');
            let form = $("<form>").attr({id:"form1",target:"_blank",method:"POST",action:"/transportes/reportes/reporte-venta-por-dia"});
            let oficina = $("<input>").attr({type:"text",name:"oficina",value:this.oficina});
            let fecha = $("<input>").attr({type:"text",name:"fecha",value:this.fecha});

            let token = $("<input>").attr({type:"text",name:"_token",value:tok});
            form.append(token,oficina,fecha);
            $(document.body).append(form);
            form.submit();
            $("#form1").remove();
        }

    }

}
</script>
