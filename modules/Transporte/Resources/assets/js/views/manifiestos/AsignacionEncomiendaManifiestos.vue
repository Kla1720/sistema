<template>
    <div>

        <div class="page-header pr-0">
            <h2>
                <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
            </h2>
            <h2>
                <a href="/transportes/manifiestos"><i class="fas fa-arrow-left"></i></a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>ARMA EL MANIFIESTO DE ENCOMIENDAS</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <div class="btn-group flex-wrap">
                    <!-- <button
                        type="button"
                        class="btn btn-custom btn-sm mt-2 mr-2"
                        @click="onCreate"
                    >
                        <i class="fa fa-plus-circle"></i> Nuevo
                    </button> -->
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Origen</label>
                            <el-input v-model="programacion.origen.nombre" type="text" disabled></el-input>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Destino</label>
                            <el-input v-model="programacion.destino.nombre" type="text" disabled></el-input>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Hora de salida</label>
                            <el-input v-model="programacion.hora_salida" type="time" disabled></el-input>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="">Fecha de salida</label>
                        <el-date-picker
                        disabled
                        v-model="manifiesto.fecha"
                        type="date"
                        value-format="yyyy-MM-dd"
                        placeholder="Fecha">
                        </el-date-picker>
                    </div>
                </div>
                <div class="row mt-2">

                    <div v-loading="loadingEncomiendas" class="col-md-12 table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Cliente</th>
                                    <th>Documento</th>
                                    <th>Articulos</th>
                                    <th>Cantidad</th>
                                    <th>Importe</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="encomienda.documents" v-for="encomienda in listEncomiendasDocuments" :key="encomienda.id">
                                    <td>{{ encomienda.fecha_salida }}</td>
                                    <td>{{ encomienda.programacion.hora_salida }}</td>
                                    <td>{{ encomienda.remitente.name }}</td>
                                    <td>{{ encomienda.document.series }}-{{ encomienda.document.number }}</td>
                                    <td>
                                        <div v-for="(item,index) in encomienda.document.items" :key="index">
                                            {{ item.item.description }}
                                        </div>
                                    </td>
                                    <td>{{ encomienda.document.items.length }}</td>
                                    <td>{{ encomienda.document.total }}</td>
                                    <th>
                                        <el-button type="primary" @click="desasignar(encomienda)">
                                            <i class="fa fa-arrow-left"></i>
                                        </el-button>
                                    </th>
                                </tr>

                                <tr v-if="encomienda.sale_note" v-for="encomienda in listEncomiendasNotes" :key="encomienda.id">
                                    <td>{{ encomienda.fecha_salida }}</td>
                                    <td>{{ encomienda.programacion.hora_salida }}</td>
                                    <td>{{ encomienda.remitente.name }}</td>
                                    <td>{{ encomienda.sale_note.series }}-{{ encomienda.sale_note.number }}</td>
                                    <td>
                                        <div v-for="(item,index) in encomienda.sale_note.items" :key="index">
                                            {{ item.item.description }}
                                        </div>
                                    </td>
                                    <td>{{ encomienda.sale_note.items.length }}</td>
                                    <td>{{ encomienda.sale_note.total }}</td>
                                    <th>
                                        <el-button type="primary" @click="desasignar(encomienda)">
                                            <i class="fa fa-arrow-left"></i>
                                        </el-button>
                                    </th>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="row">
                     <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Fecha inicio</label>
                            <el-date-picker
                            v-model="fechaInicio"
                            type="date"
                            value-format="yyyy-MM-dd"
                            @change="getEncomiendasSinAsignar"
                            >
                            </el-date-picker>
                        </div>


                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Fecha final</label>
                            <el-date-picker
                            v-model="fechaFinal"
                            type="date"
                            value-format="yyyy-MM-dd"
                            @change="getEncomiendasSinAsignar"
                            >
                            </el-date-picker>
                        </div>
                    </div>

                </div>
                <div class="row mt-2">
                    <div v-loading="loadingTable" class="col-md-12 table-responsive">
                        <table class="table table-striped table-bordered">
                           <thead>
                                <tr>
                                    <th></th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Cliente</th>
                                    <th>Documento</th>
                                    <th>Articulos</th>
                                    <th>Cantidad</th>
                                    <th>Importe</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="encomienda.documents" v-for="encomienda in encomiendasSinAsignarDocuments" :key="encomienda.id">
                                    <th>
                                        <el-button type="primary" @click="asignar(encomienda)">
                                            <i class="fa fa-arrow-right"></i>
                                        </el-button>
                                    </th>
                                    <td>{{ encomienda.fecha_salida }}</td>
                                    <td>{{ programacion.hora_salida }}</td>
                                    <td>{{ encomienda.remitente.name }}</td>
                                    <td>{{ encomienda.document.series }}-{{ encomienda.document.number }}</td>
                                    <td>
                                        <div v-for="(item,index) in encomienda.document.items" :key="index">
                                            {{ item.item.description }}
                                        </div>
                                    </td>
                                    <td>{{ encomienda.document.items.length }}</td>
                                    <td>{{ encomienda.document.total }}</td>
                                </tr>
                                <tr v-if="encomienda.sale_note" v-for="encomienda in encomiendasSinAsignarNotes" :key="encomienda.id">
                                    <th>
                                        <el-button type="primary" @click="asignar(encomienda)">
                                            <i class="fa fa-arrow-right"></i>
                                        </el-button>
                                    </th>
                                    <td>{{ encomienda.fecha_salida }}</td>
                                    <td>{{ programacion.hora_salida }}</td>
                                    <td>{{ encomienda.remitente.name }}</td>
                                    <td>
                                        {{ encomienda.sale_note.series }}-{{ encomienda.sale_note.number }}
                                    </td>
                                    <td>
                                        <div v-for="(item,index) in encomienda.sale_note.items" :key="index">
                                            {{ item.item.description }}
                                        </div>
                                    </td>
                                    <td>
                                        {{ encomienda.sale_note.items.length }}
                                    </td>
                                    <td>
                                        {{ encomienda.sale_note.total }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>

    </div>

</template>
<script>
import moment from "moment";
export default {

    props:{
        manifiesto:{
            type:Object,
            required:true
        }
    },
    created(){
        this.fechaInicio = this.fechaFinal = moment().format('YYYY-MM-DD');
        this.programacion = this.manifiesto.programacion;
        this.getEncomiendas();
        this.getEncomiendasSinAsignar();

    },

    data:() => ({
        programacion:null,
        loadingEncomiendas:false,
        loadingTable:false,

        fechaInicio:null,
        fechaFinal:null,
        listEncomiendasDocuments:[],
        listEncomiendasNotes:[],
        encomiendasSinAsignarDocuments:[],
        encomiendasSinAsignarNotes:[]
    }),
    methods:{

        async getEncomiendas(){
            try{
                let form = new FormData();
                form.append('fecha',this.manifiesto.fecha);
                form.append('programacion',this.programacion.id);
                this.loadingEncomiendas = true;
                const { data } = await this.$http.post(`/transportes/manifiestos/get-encomiendas`,form);
                this.loadingEncomiendas = false;
                console.log('arriba')
                console.log(data)

                this.listEncomiendasDocuments = data.documents;
                this.listEncomiendasNotes = data.sale_notes;


            }catch(error){
                this.loadingEncomiendas = false;
                if(error.response) this.axiosError(error);

            }
        },
        async getEncomiendasSinAsignar(){
            try{
                let form = new FormData();
                form.append('fecha_inicio',this.fechaInicio);
                form.append('fecha_final',this.fechaFinal);
                this.loadingTable = true;
                const { data } = await axios.post('/transportes/manifiestos/get-encomiendas-sin-asignar',form);
                this.loadingTable = false;

                console.log('abajo')
                console.log(data)
                this.encomiendasSinAsignarDocuments = data.documents;
                this.encomiendasSinAsignarNotes = data.sale_notes;

            }catch(error){
                this.loadingTable = false;
                if(error.response) this.axiosError(error);

            }
        },
        async asignar(encomienda){
            try{
                let form = new FormData();
                form.append('programacion',this.programacion.id);
                form.append('encomienda',encomienda.id);
                form.append('manifiesto',this.manifiesto.id);
                const { data } = await this.$http.post('/transportes/manifiestos/asignacion-encomienda',form);

                this.$message({
                    type: 'success',
                    message: data.message
                });

                this.getEncomiendas();
                this.getEncomiendasSinAsignar();


            }catch(error){
                if(error.response) this.axiosError(error);
            }
        },
        async desasignar(encomienda){
            try{
                let form = new FormData();
                form.append('encomienda',encomienda.id);
                const { data } = await this.$http.post('/transportes/manifiestos/desasignar-encomienda',form);

                this.$message({
                    type: 'success',
                    message: data.message
                });

                this.getEncomiendas();
                this.getEncomiendasSinAsignar();


            }catch(error){
                if(error.response) this.axiosError(error);
            }


        }

    }

}
</script>
