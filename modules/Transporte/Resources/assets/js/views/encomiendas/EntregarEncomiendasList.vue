<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>ENTREGAR ENCOMIENDAS</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <div class="btn-group flex-wrap">
<!--                    <button-->
<!--                        type="button"-->
<!--                        class="btn btn-custom btn-sm mt-2 mr-2"-->
<!--                        @click="onCreate"-->
<!--                    >-->
<!--                        <i class="fa fa-plus-circle"></i> Nuevo-->
<!--                    </button>-->
                </div>
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-header bg-info">
                <h3 class="my-0">Entregar encomiendas</h3>
            </div>

            <div v-loading="loading" class="card-body">
                <div class="row">
                    <div class="col-lg-12 col-md-12 table-responsive">
                        <div class="col-lg-5 col-md-5 col-sm-12 pb-2">
                            <input class="form-control" placeholder="Escriba el DNI y presione ENTER"
                                      v-model="numero_dni"
                                      style="width: 100%;"
                                      prefix-icon="el-icon-search"
                                      v-on:keyup.enter="buscarEncomienda" type="number" >
                            </input>
                        </div>
                    </div>

                </div>
                            <div class="row">
                                <div v-loading="loadingInvoices" class="col-md-12 mt-2">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <template v-if="listInvoices.length > 0">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Numero</th>
                                                    <th>Origen - Destino</th>
                                                    <th>Remitente</th>
                                                    <th>Destinatario</th>
                                                    <th>Fecha salida</th>
                                                    <th>Hora salida</th>
                                                    <th>Estado envio</th>
                                                    <th>Estado Sunat</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>

                                                <tr v-for="(invoice, index) in listInvoices" :key="invoice.id" :class="{'text-danger': (invoice.document.state_type_id === '11'),
                                                    'text-warning': (invoice.document.state_type_id === '13'),
                                                    'border-light': (invoice.document.state_type_id === '01'),
                                                    'border-left border-info': (invoice.document.state_type_id === '03'),
                                                    'border-left border-success': (invoice.document.state_type_id === '05'),
                                                    'border-left border-secondary': (invoice.document.state_type_id === '07'),
                                                    'border-left border-dark': (invoice.document.state_type_id === '09'),
                                                    'border-left border-danger': (invoice.document.state_type_id === '11'),
                                                    'border-left border-warning': (invoice.document.state_type_id === '13')}">
                                                    <td class="text-right">{{ index+1 }}</td>
                                                    <td>{{ invoice.document.series + '-' +invoice.document.number  }}</td>
                                                    <td>
                                                        <p>
                                                                <span  class="badge bg-secondary text-white bg-success">
                                                                 {{ (invoice.terminal) ? invoice.terminal.nombre :'' }}
                                                            </span>
                                                            ->

                                                            <span v-if="invoice.programacion_id >0" class="badge bg-secondary text-white bg-success">
                                                                {{ (invoice.destino) ? invoice.destino.nombre :'' }}
                                                            </span>

                                                            <span v-else class="badge bg-secondary text-white bg-info">
                                                                {{ (invoice.destino) ? invoice.destino.nombre :''  }}
                                                            </span>
                                                        </p>

                                                    </td>
                                                    <td>{{ invoice.remitente.name }}</td>
                                                    <td>{{ (invoice.destinatario) ? invoice.destinatario.name : invoice.destinatario_nombre  }}</td>
                                                    <td>{{ invoice.fecha_salida }}</td>
                                                    <td>{{ invoice.programacion ? invoice.programacion.hora_salida : 'Sin programación' }}</td>
                                                    <td>
                                                                <span class="badge bg-secondary text-white" :class="{'bg-danger': (invoice.estado_envio_id === 5), 'bg-warning': (invoice.estado_envio_id === 2), 'bg-secondary': (invoice.estado_envio_id === 1), 'bg-info': (invoice.estado_envio_id === 3), 'bg-success': (invoice.estado_envio_id === 4)}">
                                                                        {{invoice.estado_envio.nombre}}
                                                                </span>

                                                    </td>
                                                    <td>
                                                        <el-tooltip v-if="tooltip(invoice, false)" class="item" effect="dark" placement="bottom">
                                                            <div slot="content">{{tooltip(invoice)}}</div>
                                                            <span class="badge bg-secondary text-white" :class="{'bg-danger': (invoice.document.state_type_id === '11'), 'bg-warning': (invoice.document.state_type_id === '13'), 'bg-secondary': (invoice.document.state_type_id === '01'), 'bg-info': (invoice.document.state_type_id === '03'), 'bg-success': (invoice.document.state_type_id === '05'), 'bg-secondary': (invoice.document.state_type_id === '07'), 'bg-dark': (invoice.document.state_type_id === '09')}">
                                                                    {{invoice.document.state_type.description}}
                                                                </span>
                                                        </el-tooltip>
                                                        <span v-else class="badge bg-secondary text-white" :class="{'bg-danger': (invoice.document.state_type_id === '11'), 'bg-warning': (invoice.document.state_type_id === '13'), 'bg-secondary': (invoice.document.state_type_id === '01'), 'bg-info': (invoice.document.state_type_id === '03'), 'bg-success': (invoice.document.state_type_id === '05'), 'bg-secondary': (invoice.document.state_type_id === '07'), 'bg-dark': (invoice.document.state_type_id === '09')}">
                                                                {{invoice.document.state_type.description}}
                                                            </span>
                                                        <template v-if="invoice.document.regularize_shipping && invoice.document.state_type_id === '01'">
                                                            <el-tooltip class="item" effect="dark" :content="invoice.document.message_regularize_shipping" placement="top-start">
                                                                <i class="fas fa-exclamation-triangle fa-lg" style="color: #d2322d !important"></i>
                                                            </el-tooltip>
                                                        </template>
                                                    </td>
                                                    <td class="text-center">
                                                        <el-button type="primary" @click="entregarEncomienda(invoice,invoice.id)">
                                                            <i class="fa fa-file-file"></i>
                                                            Entregar
                                                        </el-button>
                                                        <el-button type="primary" @click="verComprobante(invoice)">
                                                            <i class="fa fa-file-alt"></i>
                                                        </el-button>
                                                        <button :disabled="user.type != 'admin'" data-toggle="tooltip" data-placement="top" title="Anular" v-if="invoice.state_type_id != '11'" type="button" class="btn waves-effect waves-light btn-xs btn-danger"
                                                                @click.prevent="anularDocument(invoice)"><i class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>

                                                </tbody>
                                            </template>
                                            <template v-else>
                                                <tr>
                                                    <td class="text-center" colspan="8">
                                                        <el-alert
                                                            center
                                                            title="No hay facturas o boletas registrados"
                                                            type="info"
                                                            :closable="false">
                                                        </el-alert>
                                                    </td>
                                                </tr>
                                            </template>

                                        </table>

                                        <table class="table table-bordered table-striped">
                                            <template v-if="listNotes.length > 0">
                                                <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Numero</th>
                                                    <th>Origen - Destino</th>
                                                    <th>Remitente</th>
                                                    <th>Destinatario</th>
                                                    <th>Fecha salida</th>
                                                    <th>Hora salida</th>
                                                    <th>Estado envio</th>
                                                    <th></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr v-for="(note, index) in listNotes" :key="note.id">
                                                    <td class="text-right">{{ index+1 }}</td>
                                                    <td>{{ note.sale_note ? note.sale_note.series + '-' + note.sale_note.number :'' }}</td>
                                                    <td>
                                                        <p>
                                                                <span  class="badge bg-secondary text-white bg-success">
                                                                {{ (note.terminal) ? note.terminal.nombre :'' }}
                                                            </span>
                                                            ->

                                                            <span v-if="note.programacion_id >0" class="badge bg-secondary text-white bg-success">
                                                                {{ (note.destino)? note.destino.nombre :'' }}
                                                            </span>

                                                            <span v-else class="badge bg-secondary text-white bg-info">
                                                                {{ (note.destino)? note.destino.nombre :'' }}
                                                            </span>
                                                        </p>

                                                    </td>
                                                    <td>{{ note.remitente.name }}</td>
                                                    <td>{{ (note.destinatario) ? note.destinatario.name : note.destinatario_nombre  }}</td>
                                                    <td>{{ note.fecha_salida }}</td>
                                                    <td>{{ note.programacion ? note.programacion.hora_salida : 'Sin programación' }}</td>
                                                    <td>
                                                                <span class="badge bg-secondary text-white" :class="{'bg-danger': (note.estado_envio_id === 5), 'bg-warning': (note.estado_envio_id === 2), 'bg-secondary': (note.estado_envio_id === 1), 'bg-info': (note.estado_envio_id === 3), 'bg-success': (note.estado_envio_id === 4)}">
                                                                        {{note.estado_envio.nombre}}
                                                                </span>

                                                    </td>
                                                    <td class="text-center">
                                                        <el-button type="primary" @click="verNota(note)">
                                                            <i class="fa fa-file-alt"></i>
                                                        </el-button>
                                                        <el-button type="primary" @click="entregarEncomienda(note, note.id)">
                                                            <i class="fa fa-file-file"></i>
                                                            Entregar
                                                        </el-button>
                                                        <button :disabled="user.type != 'admin'" data-toggle="tooltip" data-placement="top" title="Anular" v-if="note.state_type_id != '11'" type="button" class="btn waves-effect waves-light btn-xs btn-danger"
                                                                @click.prevent="anularNota(note.id)"><i class="fas fa-trash"></i></button>


                                                    </td>
                                                </tr>

                                                </tbody>
                                            </template>
                                            <template v-else>
                                                <tr>
                                                    <td class="text-center" colspan="8">
                                                        <el-alert
                                                            center
                                                            title="No hay notas de venta registrados"
                                                            type="info"
                                                            :closable="false">
                                                        </el-alert>
                                                    </td>
                                                </tr>
                                            </template>

                                        </table>
                                    </div>
                                    <div class="row justify-content-center">

                                        <el-pagination
                                            v-if="total > 0"
                                            :page-size="25"
                                            @current-change="changePage"
                                            :current-page.sync="page"
                                            :total="total"
                                            layout="prev, pager, next">
                                        </el-pagination>
                                    </div>
                                </div>
                            </div>
            </div>
        </div>
        <ModalAddEdit
            :visible.sync="openModalAddEdit"
            @onAddItem="onAddItem"
            @onUpdateItem="onUpdateItem"
            :item-encomienda="encomienda"
            :estados-envio="estadosEnvio"
            :estados-pago="estadosPago"
            :establishment="establishment"
            :all-series="series"
            :document-types-invoice="documentTypesInvoice"
            :payment-method-types="paymentMethodTypes"
            :payment-destinations="paymentDestinations"
            :edit="edit"
            :user-terminal="userTerminal"
            :configuration="configuration"
            :document_type_03_filter="document_type_03_filter"
            @onSuccessVenta="onSuccessVenta"
            :is-cash-open="isCashOpen"
            :persons="persons"
        ></ModalAddEdit>
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

        <ModalEntregaForm
            :showDialogEntrega.sync="showDialogEntrega"
            @onUpdateItem="onUpdateItem"
            @onUpdateItemNote="onUpdateItemNote"
            :encomienda = "encomienda"
            :recordId="recordId">
        </ModalEntregaForm>



    </div>
</template>

<script>
import ModalAddEdit from "./AddEdit";
import DocumentOptions from "@views/documents/partials/options.vue";
//import SaleNoteOptions from "@views/sale_notes/partials/options.vue";
import SaleNoteOptions from '@views/sale_notes/partials/option_aqp.vue'
import DocumentsVoided from '@views/documents/partials/voided.vue'
import ModalEntregaForm from './EntregaForm'

export default {
    props: {

        document_type_03_filter:{
            type:Boolean,
            required:true
        },
        estadosPago:{
            type:Array,
            required:true,
        },
        estadosEnvio:{
            type:Array,
            required:true,
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
        isCashOpen:{
            type:Boolean,
            required:true,
            default:false,
        },
        persons:{
            type:Array,
            default:() => []
        },
        user:{
            type:Object,
            required:true,
        }
    },
    components: {
        ModalAddEdit,
        DocumentOptions,
        SaleNoteOptions,
        DocumentsVoided,
        ModalEntregaForm
    },
    created(){
        //this.getEncomiendas();
        //this.getEncomiendasNotes();
        this.$eventHub.$on('reloadDataNotes', () => {
            this.getEncomiendasNotes();
        })
        this.$eventHub.$on('reloadDataInvoices', () => {
            this.getEncomiendas();
        })
    },
    data() {
        return {
            //pagination
            page:1,
            limit:25,
            total:0,

            listInvoices: [],
            listNotes: [],
            openModalAddEdit: false,
            encomienda:null,
            loading: false,
            edit:false,
            documentNewId:null,
            showDialogDocumentOptions:false,
            showDialogSaleNoteOptions: false,
            showDialogVoided:false,
            showDialogEntrega:false,
            loadingInvoices:false,
            loadingNotes:false,
            recordId:null,
            encomiendaId:null,
            search: '',
            lots_: [],
            numero_dni:null,
            isNote:false,
        };
    },
    mounted() {
        //silent
    },
    watch:{
        lots(val)
        {
            this.lots_ = val
        }
    },
    methods: {
        filter()
        {
            if(this.search)
            {
                this.lots_ = _.filter(this.listInvoices, x => x.listInvoices.toUpperCase().includes(this.search.toUpperCase()))
            }
            else{
                this.lots_ = this.listInvoices
            }
        },

        onSuccessVenta(documentId){
            this.documentNewId = documentId;
            this.showDialogDocumentOptions = true;
        },
        entregarEncomienda(encomienda, documentId){
            this.encomienda = { ...encomienda };
            this.recordId = documentId;
            this.showDialogEntrega = true;
        },

        async getEncomiendas(){
            try{
                this.loading = true;
                const { data } = await this.$http.get(`/transportes/encomiendas/get-encomiendas?page=${this.page}&limit=${this.limit}`);
                this.listInvoices = data.data;
                this.total = data.count;
                this.loading = false;

            }catch(error){
                this.loading = false;
                if(error.response) this.axiosError(error);
            }
        },
        async getEncomiendasNotes(){
            try{
                this.loading = true;
                const { data } = await this.$http.get('/transportes/encomiendas/get-encomiendas-notes');
                this.listNotes = data;
                this.loading = false;
            }catch(error){
                this.loading = false;
                if(error.response) this.axiosError(error);
            }
        },
        async buscarEncomienda(){
            try{
                this.loading = true;
                const { data } = await this.$http.get(`/transportes/encomiendas/buscar/${this.numero_dni}`);
                this.listInvoices = data.data;
                this.loading = false;
            }catch(error){
                this.loading = false;
                if(error.response) this.axiosError(error);
            }

            this.buscarEncomiendaNota()
        },
        async buscarEncomiendaNota(){
            try{
                this.loading = true;
                const { data } = await this.$http.get(`/transportes/encomiendas/buscar-nota/${this.numero_dni}`);
                this.listNotes = data.data;
                this.loading = false;
            }catch(error){
                this.loading = false;
                if(error.response) this.axiosError(error);
            }
        },
        onDelete(item) {
            this.$confirm(`¿estás seguro de eliminar la encomienda?`, 'Atención', {
                confirmButtonText: 'Si, continuar',
                cancelButtonText: 'No, cerrar',
                type: 'warning'
            }).then(() => {
                this.$http.delete(`/transportes/encomiendas/${item.id}/delete`).then(response => {
                    this.$message({
                        type: 'success',
                        message: response.data.message
                    });
                    this.listInvoices = this.listInvoices.filter(i => i.id !== item.id);
                }).catch(error => {
                    if(error.response){
                        this.axiosError(error)
                    }

                });
            }).catch();
        },
        onEdit(encomienda) {
            this.edit = true;
            this.encomienda = { ...encomienda };
            this.openModalAddEdit = true;
        },
        onUpdateItem(encomienda) {
            //this.getEncomiendas();

            this.listInvoices = this.listInvoices.map((i) => {
                if (i.id === encomienda.id) {
                    return encomienda;
                }
                return i;
            });
        },
        onUpdateItemNote(){
            this.getEncomiendasNotes();
        },
        onAddItem(encomienda) {
            this.getEncomiendas();
            this.documentNewId = encomienda.document_id;
        },
        onCreate() {
            this.edit = false;
            this.encomienda = null;
            this.openModalAddEdit = true;
        },
        verComprobante(encomienda){
            this.documentNewId = encomienda.document_id;
            this.showDialogDocumentOptions = true;
        },
        verNota(encomienda){
            this.documentNewId = encomienda.note_id;
            this.showDialogSaleNoteOptions = true;
        },
        anularDocument(encomienda) {
            this.recordId = encomienda.document_id;
            this.showDialogVoided = true;
        },
        anularNota(id) {
            this.$confirm(`¿Estás seguro de anular este registro ?`, 'Atención', {
                confirmButtonText: 'Si, continuar',
                cancelButtonText: 'No, cerrar',
                type: 'warning'
            }).then(() => {

                this.$http
                    .get(`/sale-notes/anulate/${id}`)
                    .then(response => {
                        this.getEncomiendasNotes();
                    });

            });

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
        changePage(val) {
            this.page = val;
            this.getEncomiendas();
        }
    },
};
</script>
