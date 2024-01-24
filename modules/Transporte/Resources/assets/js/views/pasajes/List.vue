<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>LISTADO DE PASAJES VENDIDOS</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <div class="btn-group flex-wrap">
                    <button
                        type="button"
                        class="btn btn-custom btn-sm mt-2 mr-2"
                        @click="onCreate"
                    >
                        <i class="fa fa-plus-circle"></i> Nuevo
                    </button>
                </div>
            </div>
        </div>
        <div v-if="!sale"  class="card mb-0">
            <div class="card-header bg-info text-center">

                <h3 class="my-0">Listado de pasajes</h3>
            </div>
            <div  class="card-body">
                <div v-loading="loading" class="table-responsive">
                    <el-tabs v-model="tab" >
                        <el-tab-pane label="Facturas - Boletas" name="invoice">
                            <div class="row">
                                <div v-loading="loading" class="col-md-12 mt-2">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Número</th>
                                                <th>Origen - Destino</th>
                                                <th>Fecha y hora salida </th>
                                                <th># Asiento</th>
                                                <th>Cliente</th>
                                                <th>T.Gravado</th>
                                                <th>T.Igv</th>
                                                <th>Total</th>
                                                <th>Estado Sunat</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <template v-if="listPasajes.length > 0">
                                                    <tr v-for="pasaje in listPasajes" :key="pasaje.id" :class="{'text-danger': (pasaje.document.state_type_id === '11'),
                                                        'text-warning': (pasaje.document.state_type_id === '13'),
                                                        'border-light': (pasaje.document.state_type_id === '01'),
                                                        'border-left border-info': (pasaje.document.state_type_id === '03'),
                                                        'border-left border-success': (pasaje.document.state_type_id === '05'),
                                                        'border-left border-secondary': (pasaje.document.state_type_id === '07'),
                                                        'border-left border-dark': (pasaje.document.state_type_id === '09'),
                                                        'border-left border-danger': (pasaje.document.state_type_id === '11'),
                                                        'border-left border-warning': (pasaje.document.state_type_id === '13')}">
                                                        <td class="text-right">{{ pasaje.id }}</td>
                                                        <td>{{ pasaje.document.series }}-{{pasaje.document.number}}</td>
                                                        <td>
                                                            <p>
                                                                <span  class="badge bg-secondary text-white bg-success">
                                                                {{ pasaje.origen.nombre }}
                                                            </span>
                                                                ->

                                                                <span v-if="pasaje.programacion_id >0" class="badge bg-secondary text-white bg-success">
                                                                {{ pasaje.destino.nombre }}
                                                            </span>

                                                                <span v-else class="badge bg-secondary text-white bg-info">
                                                                {{ pasaje.destino.nombre }}
                                                            </span>
                                                            </p>

                                                        </td>
                                                        <td>{{ pasaje.fecha_salida }} {{ pasaje.hora_salida }}</td>
                                                        <td class="text-center"> <div style="background: limegreen;color:white"><b>{{ pasaje.numero_asiento }}</b></div></td>
                                                        <td>{{ pasaje.pasajero.name }}</td>
                                                        <td>{{ pasaje.document.total_taxed }}</td>
                                                        <td>{{ pasaje.document.total_igv }}</td>
                                                        <td>{{ pasaje.document.total }}</td>
                                                        <td>
                                                            <el-tooltip v-if="tooltip(pasaje.document, false)"  class="item" effect="dark" placement="bottom">
                                                                <div slot="content">{{tooltip(pasaje.document)}}</div>
                                                                <span class="badge bg-secondary text-white" :class="{'bg-danger': (pasaje.document.state_type_id === '11'), 'bg-warning': (pasaje.document.state_type_id === '13'), 'bg-secondary': (pasaje.document.state_type_id === '01'), 'bg-info': (pasaje.document.state_type_id === '03'), 'bg-success': (pasaje.document.state_type_id === '05'), 'bg-secondary': (pasaje.document.state_type_id === '07'), 'bg-dark': (pasaje.document.state_type_id === '09')}">
                                                                    {{pasaje.document.state_type.description}}
                                                                </span>
                                                            </el-tooltip>
                                                            <span v-else class="badge bg-secondary text-white" :class="{'bg-danger': (pasaje.document.state_type_id === '11'), 'bg-warning': (pasaje.document.state_type_id === '13'), 'bg-secondary': (pasaje.document.state_type_id === '01'), 'bg-info': (pasaje.document.state_type_id === '03'), 'bg-success': (pasaje.document.state_type_id === '05'), 'bg-secondary': (pasaje.document.state_type_id === '07'), 'bg-dark': (pasaje.document.state_type_id === '09')}">
                                                                {{pasaje.document.state_type.description}}
                                                            </span>
                                                            <template v-if="pasaje.document.regularize_shipping && pasaje.document.state_type_id === '01'">
                                                                <el-tooltip class="item" effect="dark" :content="pasaje.document.message_regularize_shipping" placement="top-start">
                                                                    <i class="fas fa-exclamation-triangle fa-lg" style="color: #d2322d !important"></i>
                                                                </el-tooltip>
                                                            </template>
                                                        </td>
                                                        <td class="text-center">
                                                            <!-- <el-tooltip class="item" effect="dark" content="Editar" placement="top-start">
                                                                <el-button type="success" @click="onEdit(pasaje)">
                                                                    <i class="fa fa-edit"></i>
                                                                </el-button>
                                                            </el-tooltip> -->

                                                            <el-tooltip class="item" effect="dark" content="Imprimir comprobante" placement="top-start">
                                                                <el-button type="primary" @click="verComprobante(pasaje)">
                                                                    <i class="fa fa-file-alt"></i>
                                                                </el-button>
                                                            </el-tooltip>

<!--                                                            <el-tooltip class="item" effect="dark" content="Anular" placement="top-start">-->
<!--                                                                <el-button :disabled="user.type != 'admin'" type="danger" @click="anular(pasaje)">-->
<!--                                                                    <i class="fa fa-trash"></i>-->
<!--                                                                </el-button>-->
<!--                                                            </el-tooltip>-->

                                                        </td>
                                                    </tr>
                                                </template>
                                                <template v-else>
                                                    <tr>
                                                        <td colspan="9">
                                                            <el-alert type="info" title="No hay pasajes"  center/>
                                                        </td>
                                                    </tr>
                                                </template>

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row justify-content-center">

                                        <el-pagination
                                            v-if="total > 0"
                                            :page-size="limit"
                                            @current-change="changePage"
                                            :current-page.sync="page"
                                            :total="total"
                                            layout="prev, pager, next">
                                        </el-pagination>
                                    </div>
                                </div>
                            </div>
                        </el-tab-pane>
                        <el-tab-pane label="Notas de venta" name="notes">
                            <div class="row">
                                <div v-loading="loadingNotes" class="col-md-12 mt-2">
                                    <table class="table table-bordered table-striped">
                                        <template v-if="listNotes.length > 0">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Número</th>
                                                <th>Origen - Destino</th>
                                                <th>Fecha y hora salida </th>
                                                <th># Asiento</th>
                                                <th>Cliente</th>
                                                <th>T.Gravado</th>
                                                <th>T.Igv</th>
                                                <th>Total</th>
                                                <th>Estado Sunat</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr v-for="(note, index) in listNotes" :key="note.id">
                                                <td class="text-right">{{ index+1 }}</td>
                                                <td>{{ note.sale_note.series + '-' + note.sale_note.number}}</td>
                                                <td>
                                                    <p>
                                                        <span  class="badge bg-secondary text-white bg-success">
                                                                {{ note.origen.nombre }}
                                                        </span>
                                                        ->

<!--                                                        <span v-if="note.programacion_id >0" class="badge bg-secondary text-white bg-success">-->
<!--                                                                {{ note.destino.nombre }}-->
<!--                                                        </span>-->

                                                        <span class="badge bg-secondary text-white bg-info">
                                                                {{ note.destino.nombre }}
                                                        </span>
                                                    </p>

                                                </td>
                                                <td>{{ note.fecha_salida }} {{ note.hora_salida }}</td>
                                                <td class="text-center"> <div style="background: limegreen;color:white"><b>{{ note.numero_asiento }}</b></div></td>
                                                <td>{{ note.pasajero.name }}</td>
                                                <td>{{ note.sale_note.total_taxed }}</td>
                                                <td>{{ note.sale_note.total_igv }}</td>
                                                <td>{{ note.sale_note.total }}</td>
                                                <td class="text-center">
                                                    <el-button type="primary" @click="verNota(note)">
                                                        <i class="fa fa-file-alt"></i>
                                                    </el-button>
<!--                                                    <button :disabled="user.type != 'admin'" data-toggle="tooltip" data-placement="top" title="Anular"  type="button" class="btn waves-effect waves-light btn-xs btn-danger"-->
<!--                                                            @click="anularNota(note)"><i class="fas fa-trash"></i></button>-->
                                                </td>
                                            </tr>

                                            </tbody>
                                        </template>
                                        <template v-else>
                                            <tr>
                                                <td class="text-center" colspan="8">
                                                    <el-alert
                                                        center
                                                        title="No hay comprobantes registrados"
                                                        type="info"
                                                        :closable="false">
                                                    </el-alert>
                                                </td>
                                            </tr>
                                        </template>

                                    </table>
                                </div>
                            </div>

                        </el-tab-pane>
                    </el-tabs>
                </div>
            </div>

        </div>
        <!-- <sales v-else
            :item-pasajero="pasaje"
            :list-programaciones="listProgramaciones"
            :terminal="userTerminal"
            :estado-asientos="estadoAsientos"
            :establishment="establishment"
            :series="series"
            :document-types-invoice="documentTypesInvoice"
            :payment-method-types="paymentMethodTypes"
            :payment-destinations="paymentDestinations"
            :configuration="configuration"
            :sale.sync="sale"
            @onSuccessVenta="onSuccessVenta"
        /> -->
        <document-options
            :showDialog.sync="showDialogDocumentOptions"
            :recordId="documentNewId"
            :isContingency="false"
            :showClose="true"
            :configuration="configuration"
        ></document-options>

        <sale-note-options
            :showDialog.sync="showDialogSaleNoteOptions"
            :recordId="documentNewId"
            :showClose="true"
            :configuration="configuration"
        >
        </sale-note-options>

        <documents-voided
            :showDialog.sync="showDialogVoided"
            :recordId="recordId">
        </documents-voided>
    </div>
</template>

<script>
import DocumentOptions from "@views/documents/partials/options.vue";
// import SaleNoteOptions from "@views/sale_notes/partials/options.vue";
import SaleNoteOptions from '@views/sale_notes/partials/option_aqp.vue'
import DocumentsVoided from '@views/documents/partials/voided.vue'
import Sales from './Sales.vue';
export default {
    props: {
        listProgramaciones:{
            type:Array,
            default:() => []
        },
        pasajes:{
            type:Array,
            // required:true,
            default:() => []
        },
        establishment:{
            type:Object,
            required:true
        },
        series:{
            type:Array,
            default:() => []
        },
        documentTypesInvoice:{
            type:Array,
            default:() => []
        },
        paymentMethodTypes: {
            type: Array,
            required: true,
        },
        paymentDestinations: {
            type: Array,
            required: true,
        },
        userTerminal:{
            type:Object,
            default:{}
        },
        configuration:{
            type: Object,
            required: true,
        },
        estadoAsientos:{
            type:Array,
            required:true,
            default:() => []
        },
        user:{
            type:Object,
            required:true,
        }
    },
    components: {
        DocumentOptions,
        DocumentsVoided,
        SaleNoteOptions,
        Sales
    },
    created(){
        //this.listPasajes = this.pasajes;
        this.$eventHub.$on('reloadData',async() => {
            this.cancelarBoleto();
        });
        this.getPasajes();
        this.getEncomiendasNotes();
    },
    data() {
        return {

            //paginacion
            page:1,
            total:0,
            limit:25,


            sale:false,
            recordId:null,
            pasajeId:null,
            showDialogVoided:false,
            tab:'invoice',
            listPasajes: [],
            listNotes: [],
            openModalAddEdit: false,
            pasaje:null,
            loading: false,
            loadingNotes:false,
            edit:false,
            documentNewId:null,
            showDialogDocumentOptions:false,
            showDialogSaleNoteOptions: false,
        };
    },
    mounted() {
    },
    methods: {

        async getPasajes(){
            try{
                this.loading = true;
                const { data } = await this.$http.get(`/transportes/pasajes/get-pasajes?page=${this.page}&limit=${this.limit}`);
                this.loading = false;
                this.listPasajes = data.data;
                this.total = data.count;


            }catch(error){
                this.loading = false;
                if(error.response) this.axiosError(error);

            }

        },
        async getEncomiendasNotes(){
            try{
                this.loading = true;
                const { data } = await this.$http.get('/transportes/pasajes/get-pasajes-notes');
                this.listNotes = data;
                this.loading = false;
            }catch(error){
                this.loading = false;
                if(error.response) this.axiosError(error);
            }
        },
        async onSuccessVenta(documentId){
            this.documentId = documentId;
            this.showDialogDocumentOptions = true;
            this.getPasajes();
        },
        onDelete(item) {
            this.$confirm(`¿estás seguro de eliminar al elemento ${item.nombre}?`, 'Atención', {
                confirmButtonText: 'Si, continuar',
                cancelButtonText: 'No, cerrar',
                type: 'warning'
            }).then(() => {
                this.$http.delete(`/transportes/choferes/${item.id}/delete`).then(response => {
                    this.$message({
                        type: 'success',
                        message: response.data.message
                    });
                    this.items = this.items.filter(i => i.id !== item.id);
                }).catch(error => {
                    this.axiosError(error)
                });
            }).catch();
        },
        onEdit(pasaje) {
            window.location.href = `/transportes/sales/${pasaje.id}`;
        },
        onUpdateItem(pasaje) {

            this.items = this.listPasajes.map((i) => {
                if (i.id === pasaje.id) {
                    return pasaje;
                }
                return i;
            });
            this.edit = false;
        },
        onAddItem(pasaje) {
            this.documentNewId = pasaje.document_id;
            this.showDialogDocumentOptions = true;
            this.listPasajes.unshift(pasaje);
        },
        onCreate() {
            // this.sale = true;
            window.location.href="/transportes/sales";
        },
        verComprobante(pasaje){
            this.documentNewId = pasaje.document_id;
            this.showDialogDocumentOptions = true;
        },
        verNota(pasaje){
            this.documentNewId = pasaje.note_id;
            this.showDialogSaleNoteOptions = true;
        },
        tooltip(row, message = true) {
            if (message) {
                if (row.shipping_status) return row.shipping_status.message;

                if (row.sunat_shipping_status) return row.sunat_shipping_status.message;

                if (row.query_status) return row.query_status.message;
            }

            if ((row.shipping_status) || (row.sunat_shipping_status) || (row.query_status)) return true;

            return false;
        },

        anular(pasaje) {

            this.$confirm(`¿Estás seguro de anular el boleto ?`, 'Atención', {
                confirmButtonText: 'Si, continuar',
                cancelButtonText: 'No, cerrar',
                type: 'warning'
            }).then(() => {

                this.pasajeId = pasaje.id;
                this.recordId = pasaje.document.id;
                this.showDialogVoided = true;


            });

        },
        anularNota(note) {
            var url = `/sale-notes/anulate/${note.sale_note.id}`
            return new Promise((resolve) => {
                this.$confirm('¿Desea anular el registro?', 'Anular', {
                    confirmButtonText: 'Anular',
                    cancelButtonText: 'Cancelar',
                    type: 'warning'
                }).then(() => {
                    this.$http.get(url)
                        .then(res => {
                            if (res.data.success) {
                                this.$message.success('Se anuló correctamente el registro')
                                this.cancelarBoleto(note.id)
                                resolve()
                            } else {
                                const {message = 'Error al intentar anular'} = res.data
                                this.$message.error(message)
                            }
                        })
                        .catch(error => {
                            if (error.response.status === 500) {
                                this.$message.error('Error al intentar anular');
                            } else {
                                console.log(error.response.data.message)
                            }
                        })
                }).catch(error => {
                    console.log(error)
                });
            })
        },

        async cancelarBoleto(pasaje_id){
            try{
                const { data } = await axios.delete(`/transportes/pasajes/${pasaje_id}/delete`);

                if(!data.success){
                    this.$message({
                        type: 'error',
                        message: data.message
                    });
                }

                this.$message({
                    type: 'success',
                    message: data.message
                });

                this.listPasajes = this.listPasajes.filter(i => i.id !== this.pasajeId);

            }catch(error){

                this.$message({
                    type: 'error',
                    message: 'Lo sentimos ha ocurrido un error'
                });

            }
        },
        changePage(val) {
            this.page = val;
            this.getPasajes();
        }
    },
};
</script>
