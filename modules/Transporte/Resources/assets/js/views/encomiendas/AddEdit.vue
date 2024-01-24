<template>
    <div>
    <el-dialog
        :title="title"
        :visible="visible"
        @close="onClose"
        @open="onCreate"
        width="800px"
        :close-on-click-modal="false"

    >
        <form v-loading="load" autocomplete="off">
            <div class="form-body">
                <div class="row">
                    <div class="col-6">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">

                                    <label for="">Tipo de comprobante</label>
                                    <el-select
                                        v-model="document.document_type_id"
                                        @change="changeDocumentType"
                                        popper-class="el-select-document_type"
                                        :disabled="edit"
                                        class="border-left rounded-left border-info"
                                    >
                                        <el-option
                                            v-for="option in documentTypesInvoice"
                                            :key="option.id"
                                            :value="option.id"
                                            :label="option.description"
                                        ></el-option>

                                        <el-option key="nv" value="nv" label="NOTA DE VENTA"></el-option>
                                    </el-select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Serie</label>
                                    <el-select v-model="document.series_id" :disabled="edit">
                                        <el-option
                                            v-for="option in series"
                                            :key="option.id"
                                            :value="option.id"
                                            :label="option.number"
                                        ></el-option>
                                    </el-select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="">Fecha salida</label>
                                        <el-date-picker
                                            :class="{'is-invalid':errors.fecha_salida}"
                                            v-model="encomienda.fecha_salida"
                                            type="date"
                                            value-format="yyyy-MM-dd"
                                            placeholder="Fecha salida" @change="seleccionarFecha">
                                        </el-date-picker>
                                        <div v-if="errors.fecha_salida" class="invalid-feedback">{{ errors.fecha_salida[0] }}</div>
                                    </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="">Estado Envio</label>
                                    <el-select v-model="encomienda.estado_envio_id"  popper-class="el-select-customers" :class="{'is-invalid':errors.estado_envio_id}" placeholder="Estado encomienda" >
                                        <el-option v-for="estadoEnvio in estadosEnvio" :key="estadoEnvio.id" :value="estadoEnvio.id" :label="`${estadoEnvio.nombre}`">
                                        </el-option>
                                    </el-select>
                                    <div v-if="errors.estado_envio_id" class="invalid-feedback">{{ errors.estado_envio_id[0] }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mt-4">
                        <div class="form-group">
                            <label for="dni">
                                Remitente
                                <span class="text-danger">*</span>
                            </label>

                            <div class="row">
                                <div class="col-md-4">
                                    <el-select v-model="persona.identity_document_type_id">
                                        <el-option value="1" label="DNI"></el-option>
                                        <el-option value="6" label="RUC"></el-option>
                                    </el-select>
                                </div>
                                <div class="col-md-8">
                                    <input type="number" class="form-control" v-model="encomienda.remitente_numero" placeholder="ingrese dni y presione enter" v-on:keyup.enter="buscar_remitente" :maxlength="persona.identity_document_type_id==1 ? 8 : 12 " oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"></input>
                                    <span v-if="errors.remitente_id" class="text-danger">{{ errors.remitente_id[0] }}</span>
                                </div>
                            </div>


                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" v-model="encomienda.remitente_nombre"></input>
                        </div>
                    </div>
                    <div class="col-6 mt-4">
                        <div class="form-group">
                            <label for="nombre">
                                Destinatario

                            </label>

                            <input type="number" class="form-control" v-model="encomienda.destinatario_numero" placeholder="ingrese dni y presione enter" v-on:keyup.enter="buscar_destinatario" :loading="loading" ></input>
                            <span v-if="errors.destinatario_id" class="text-danger">{{ errors.destinatario_id[0] }}</span>
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control" v-model="encomienda.destinatario_nombre"></input>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 offset-4 mt-3">
                        <p class="text-center text-blue font-bold">CLAVE
                            <input type="text" class="form-control font-large font-bold text-center text-blue" v-model="encomienda.clave" maxlength="4"></input>
                        </p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-4">
                        <div class="form-group">
                            <label for="">Origen</label>
                            <el-select v-model="terminalId" filterable remote disabled
                                placeholder="Buscar origen"
                                :remote-method="searchTerminales"
                                :loading="loadingTerminales"
                                @change="searchDestinos"
                                >
                                <el-option v-for="terminal in terminales" :key="terminal.id" :value="terminal.id" :label="terminal.nombre">
                                </el-option>
                            </el-select>

                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label for="">Destino</label>
                            <el-select v-model="encomienda.destino_id" :loading="loadingDestinos"  placeholder="Destino" @change="seleccionarFecha">
                                <el-option v-for="destino in destinos" :key="destino.id" :value="destino.id" :label="`${destino.nombre}`">
                                </el-option>
                            </el-select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label for="">Pago</label>
                            <el-select v-model="encomienda.estado_pago_id"  popper-class="el-select-customers" :class="{'is-invalid':errors.estado_pago_id}" placeholder="Pago">
                                <el-option v-for="estadoPago in estadosPago" :key="estadoPago.id" :value="estadoPago.id" :label="`${estadoPago.nombre}`">
                                </el-option>
                            </el-select>
                            <div v-if="errors.estado_pago_id" class="invalid-feedback">{{ errors.estado_pago_id[0] }}</div>
                        </div>
                    </div>
                </div>

                <!-- <el-divider></el-divider> -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-info text-center">
                                Lista de productos
                            </div>
                            <div class="card-body">

                                <div v-if="!edit" class="row">
                                    <div v-if="!addNewItem" class="col-4">
                                        <div class="form-group">
                                            <label for="">
                                                Buscar producto
                                                <a href="#" @click.prevent="addNewItem = true,nuevoProducto()">[+ Nuevo]</a>
                                            </label>
                                            <el-select v-model="selectItem" filterable remote value-key="id"  popper-class="el-select-customers"
                                                placeholder="Buscar producto"
                                                :remote-method="searchProducto"
                                                :loading="loadingSProducto"
                                                clearable

                                                >
                                                <el-option v-for="item in items" :key="item.id" :value="item" :label="item.name">
                                                </el-option>
                                            </el-select>
                                        </div>
                                    </div>

                                    <div v-else class="col-4">

                                        <div class="form-group">
                                            <label for="">
                                                Descripción <span class="text-danger">*</span>
                                                <a href="#" @click.prevent="addNewItem = false">[Buscar]</a>
                                            </label>
                                            <el-input v-model="producto.item.description" placeholder="Descripción"></el-input>
                                        </div>



                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label for="">Cant. <span class="text-danger">*</span></label>
                                            <el-input type="number" v-model="producto.quantity" placeholder="1"></el-input>
                                        </div>

                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label for="">Precio unitario <span class="text-danger">*</span></label>
                                            <el-input type="number" v-model="producto.unit_price" placeholder="Precio"></el-input>
                                        </div>

                                    </div>
                                    <div class="col-3">
                                        <el-button :style="{marginTop:'1.82rem'}" :loading="loadingProducto" type="primary" @click="agregarProducto">Agregar</el-button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Descripcion</th>
                                                    <th>Precio</th>
                                                    <th>Cantidad</th>
                                                    <th>Importe</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <tr v-for="(producto,index) in document.items" :key="index">
                                                    <td>{{ producto.item.description }}</td>
                                                    <td>{{ producto.unit_price | toDecimals }}</td>
                                                    <td>{{ producto.quantity }}</td>
                                                    <td>{{ producto.total_value }}</td>
                                                    <th>
                                                        <el-button v-if="!edit" type="danger" @click="eliminarProducto(index)">
                                                            <i class="fa fa-trash"></i>
                                                        </el-button>
                                                    </th>
                                                </tr>

                                                <tr>
                                                    <td><strong>Total: </strong></td>
                                                    <td class="text-right" colspan="3"> {{ total | toDecimals }} </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <el-collapse v-model="activePanel" accordion>
                            <el-collapse-item name="1" >
                                <template slot="title">
                                    <i class="fa fa-plus text-info"></i> &nbsp;Programaciones<i class="header-icon el-icon-information"></i>
                                </template>

                                <div class="row">
                                    <div class="col-12 table-responsive">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th>Vehículo</th>
                                                <th>Origen</th>
                                                <th>Destino</th>
                                                <th>Hr. Salida</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody v-loading="loadingTable">
                                            <tr v-for="programacion in programaciones" :key="programacion.id">
                                                <td>{{ programacion.vehiculo.placa }}</td>
                                                <td>{{ programacion.origen.nombre }}</td>
                                                <td>{{ programacion.destino.nombre }}</td>
                                                <td>{{ programacion.hora_salida }}</td>
                                                <td >
                                                    <div class="text-center">

                                                        <el-button v-if="encomienda.programacion_id == programacion.id" type="danger" @click="encomienda.programacion = null">
                                                            <i class="fa fa-trash"></i>
                                                        </el-button>
                                                        <el-button v-else type="success" @click="guardarEncomienda(programacion)">
                                                            <i class="fa fa-check"></i>
                                                        </el-button>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>

                                        </table>
                                    </div>
                                </div>
                            </el-collapse-item>
                        </el-collapse>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <el-collapse v-model="activePanel2" accordion>
                            <el-collapse-item name="1" >
                                <template slot="title">
                                    <i class="fa fa-plus text-info"></i> &nbsp;Pagos<i class="header-icon el-icon-information"></i>
                                </template>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <table class="table table-bordered table-stripped">
                                            <thead>
                                                <th>M. Pago</th>
                                                <th>Destino</th>
                                                <th>Referencia</th>
                                                <!-- <th>Monto</th> -->
                                            </thead>
                                            <tbody>

                                                <tr>
                                                    <td>
                                                        <div class="form-group mb-2 mr-2">
                                                            <el-select v-model="payment.payment_method_type_id">
                                                                <el-option
                                                                v-for="option in paymentMethodTypes"
                                                                :key="option.id"
                                                                :value="option.id"
                                                                :label="option.description"
                                                                ></el-option>
                                                            </el-select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group mb-2 mr-2">
                                                            <el-select
                                                                v-model="payment.payment_destination_id"
                                                                filterable
                                                                :disabled="payment.payment_destination_disabled"
                                                            >
                                                                <el-option
                                                                v-for="option in paymentDestinations"
                                                                :key="option.id"
                                                                :value="option.id"
                                                                :label="option.description"
                                                                ></el-option>
                                                            </el-select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group mb-2 mr-2">
                                                            <el-input v-model="payment.reference"></el-input>
                                                        </div>
                                                    </td>
                                                    <!-- <td>
                                                        <div class="form-group mb-2 mr-2">
                                                            <el-input v-model="row.payment"></el-input>
                                                        </div>
                                                    </td> -->
                                                </tr>

                                            </tbody>

                                        </table>
                                    </div>

                                </div>
                            </el-collapse-item>
                        </el-collapse>
                    </div>
                </div>
                <div class="row text-center mt-4">
                    <div class="col-6">
                        <el-button
                            v-if="!edit"
                            :disabled="loading"
                            type="primary"
                            class="btn-block"
                            :loading="loading"
                            @click="onGoToInvoice"
                        >Guardar</el-button>
                        <el-button
                            v-else
                            :disabled="loading"
                            type="primary"
                            class="btn-block"
                            :loading="loading"
                            @click="updateEncomienda"
                        >Guardar</el-button>
                    </div>
                    <div class="col-6">
                        <el-button class="btn-block" @click="onClose">Cancelar</el-button>
                    </div>
                </div>
            </div>
        </form>
    </el-dialog>
    <sale-note-options
                    :showDialog.sync="showDialogSaleNoteOptions"
                    :recordId="documentId"
                    :showClose="true"
                    :configuration="configuration"
                >
    </sale-note-options>
    <document-options
                    :showDialog.sync="showDialogDocumentOptions"
                    :recordId="documentId"
                    :isContingency="false"
                    :showClose="true"
                    :configuration="configuration"
                >
    </document-options>
    </div>
</template>

<script>
import moment from "moment";
import { exchangeRate } from '../../../../../../../resources/js/mixins/functions';
import DocumentOptions from "@views/documents/partials/options.vue";
//import SaleNoteOptions from "@views/sale_notes/partials/options.vue";
import SaleNoteOptions from '@views/sale_notes/partials/option_aqp.vue'
import PersonForm from "@views/persons/form2.vue";

export default {
    props: {
        document_type_03_filter:{
            type:Boolean,
            required:true
        },
        visible: {
            type: Boolean,
            required: true,
            default: false,
        },
        itemEncomienda:{
            type:Object,
            required:false,
            default:null
        },
        estadosPago:{
            type:Array,
            required:true,
        },
        estadosEnvio:{
            type:Array,
            required:true,
        },
        edit:{
            type:Boolean,
            required:true,
            default:false,
        },
        allSeries:{
            type:Array,
            default:() => []
        },
        establishment:{
            type:Object,
            required:true
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
        }
    },
    components:{
        DocumentOptions,
        PersonForm,
        SaleNoteOptions
    },
    mixins: [exchangeRate],
    data() {
        return {
            resource: "documents",
            loadingProducto:false,
            input_person:{},
            showDialogNewPerson:false,
            title: "Encomienda",
            total: 0,
            loading: false,
            totalPaid: 0,
            totalDebt: 0,
            response: {},
            documentId:null,
            //buscar_destinatario:false,
            buscar_pasajero:false,
            document: {
                payments: [],
            },
            errors: {},
            all_document_types: [],
            resource_documents: "documents",
            showDialogDocumentOptions: false,
            showDialogSaleNoteOptions: false,
            documentNewId: null,
            form_cash_document: {},
            loadingRemitente:false,
            loadingDestinatario:false,
            loadingTerminales:false,
            loadingDestinos:false,
            loadingTable:false,
            remitentes:[],
            destinatarios:[],
            destinos:[],
            terminales:[],
            terminalId:null,
            destinoId:null,
            programaciones:[],
            programacion:null,
            load:false,
            productos:[],
            producto:{
                item:{}

            },
            encomienda: {
                document_id:null,
                remitente_id:null,
                remitente_numero:null,
                remitente_nombre:'null',
                destinatario_id:null,
                destinatario_numero:'',
                destinatario_nombre:'',
                estado_pago_id:null,
                estado_envio_id:null,
                programacion_id:null,
                fecha_salida:moment().format("YYYY-MM-DD"),
                origen_id:null,
                destino_id:null,
                clave:''
            },
            payment:{},
            document_types: [],
            series:[],
            is_contingency: 0,
            activePanel: 0,
            activePanel2:0,
            items:[],
            loadingSProducto:false,
            addNewItem:false,
            selectItem:null,
            tempRemitentes:[],
            selva:null,
            is_document_type_invoice: true,

            persona: {
                id: null,
                country_id:"PE",
                type: "customers",
                identity_document_type_id: '1',
                edad: null,
                number: '',
                name: null,
                addresses: []
            },
            transporte_configuration:[]
        };
    },
    async mounted() {

    },
    computed: {
        isAutoPrint: function () {

            if(this.configuration)
            {
                return this.configuration.auto_print
            }

            return false

        },
    },
   async created(){
        this.initDocument();
        this.initForm();
        this.all_document_types = this.documentTypesInvoice;
        this.document.document_type_id = (this.documentTypesInvoice.length > 0)?this.documentTypesInvoice[0].id:null;
        this.series  = this.allSeries;
        this.document.establishment_id = this.establishment.id;
        this.changeDocumentType();
        this.startConnectionQzTray()




    },
    watch:{
        terminalId(newVal){
            if(newVal)this.searchDestinos();
        },
        total(newVal){
            if(newVal) this.payment.payment = parseFloat(newVal);
        },
        selectItem(value){
            if(value){
                let precio = parseFloat(this.selectItem.sale_unit_price);
                this.producto.item.id = this.selectItem.id;
                this.producto.item.name = this.selectItem.name;
                this.producto.item.second_name = this.selectItem.second_name;
                this.producto.item.amount_plastic_bag_taxes = this.selectItem.amount_plastic_bag_taxes;
                this.producto.item.attributes = this.selectItem.attributes;
                this.producto.item.barcode = this.selectItem.barcode;
                this.producto.item.brand = this.selectItem.brand;
                this.producto.item.calculate_quantity = this.selectItem.calculate_quantity;
                this.producto.item.category = this.selectItem.category;
                this.producto.item.currency_type_id = this.selectItem.currency_type_id;
                this.producto.item.currency_type_symbol = this.selectItem.currency_type_symbol;
                this.producto.item.description = this.selectItem.description;
                this.producto.item.full_description = this.selectItem.full_description;
                this.producto.item.has_igv = this.selectItem.has_igv;
                this.producto.item.has_plastic_bag_taxes = this.selectItem.has_plastic_bag_taxes;
                this.producto.item.internal_id = this.selectItem.internal_id;
                this.producto.item.item_unit_types = this.selectItem.item_unit_types
                this.producto.item.lots = this.selectItem.lots
                this.producto.item.lots_enabled = this.selectItem.lots_enabled
                this.producto.item.lots_group = this.selectItem.lots_group
                this.producto.item.presentation = this.selectItem.presentation
                this.producto.item.purchase_affectation_igv_type_id = this.selectItem.purchase_affectation_igv_type_id
                this.producto.item.purchase_unit_price = this.selectItem.purchase_unit_price
                this.producto.item.sale_affectation_igv_type_id = this.selectItem.sale_affectation_igv_type_id
                this.producto.item.sale_unit_price = this.selectItem.sale_unit_price
                this.producto.item.series_enabled = this.selectItem.series_enabled
                this.producto.item.stock = this.selectItem.stock;
                this.producto.item.stock_min = this.selectItem.stock_min;
                this.producto.item.unit_type_id = this.selectItem.unit_type_id;
                this.producto.unit_price = precio
            }else {
                if( this.transporte_configuration.encomienda_afecto_igv) { //jala la configuración de afectacion de igv de la nueva opcion de configuracion des módulo transporte
                    this.initProductoGrabado();
                }else{
                    this.initProductoExonerado();
                }
            }
        }
    },
    methods: {
        startConnectionQzTray(){

            if (!qz.websocket.isActive() && this.isAutoPrint)
            {
                startConnection();
            }

        },
        modalNote(){
            this.$eventHub.$emit('reloadDataNotes')
            this.showDialogSaleNoteOptions= true
        },
        validateIdentityDocumentType() {
            let identity_document_types = ["0", "1"];
            let customer = this.document.customer;
            if (
                identity_document_types.includes(customer.identity_document_type_id)
            ) {
                this.document_types = _.filter(this.all_document_types, { id: "03" });
            } else {
                this.document_types = this.all_document_types;
            }

            this.document.document_type_id = this.document_types.length > 0 ? this.document_types[0].id : null;
        },
        changeDateOfIssue() {
        this.document.date_of_due = this.document.date_of_issue;
        },
        changeDocumentType() {
            this.document.is_receivable = false;

            this.document.customer_id=null
            this.encomienda.remitente_numero=""
            this.encomienda.remitente_nombre=""
            this.persona.number= null
            this.persona.name=  null



            this.series = [];
            if (this.document.document_type_id !== "nv") {
                this.filterSeries();
                this.is_document_type_invoice = true;
            } else {
                this.series = _.filter(this.allSeries, {
                    document_type_id: "80",
                });
                this.document.series_id =
                    this.series.length > 0 ? this.series[0].id : null;

                this.is_document_type_invoice = false;
                this.encomienda.estado_pago_id=2;//2 = pago en destino
                //this.controlPago();
            }

            this.cleanCustomer();
            this.filterCustomers();
        },
        cleanCustomer(){
            this.document.customer_id = null
            this.pasajeros = []
        },
        clickAddPayment() {
            const payment =
                this.document.payments.length == 0 ? this.document.total : 0;

                this.payment = {
                    id: null,
                    document_id: null,
                    date_of_payment: moment().format("YYYY-MM-DD"),
                    payment_method_type_id: "01",
                    payment_destination_id: null,
                    reference: null,
                    payment: payment,
                }

                this.payment.payment_destination_id = this.paymentDestinations.length > 0 ? this.paymentDestinations[0].id : null;;

        },
        onExitPage() {
        window.location.href = "/hotels/reception";
        },
        validatePaymentDestination() {
        let error_by_item = 0;

        this.document.payments.forEach((item) => {
            if (item.payment_destination_id == null) error_by_item++;
        });

        return {
            error_by_item: error_by_item,
        };
        },
        initForm() {
        this.form_cash_document = {
            document_id: null,
            sale_note_id: null,
        };
        },
        async seleccionarFecha(){
            this.loadingTable = true;
            this.programaciones = [];
            this.encomienda.programacion_id = null;
            let data = {
                origen_id:this.terminalId,
                destino_id:this.encomienda.destino_id,
                fecha_salida:this.encomienda.fecha_salida
            }
            const { data:programaciones } = await this.$http.post(`/transportes/encomiendas/programaciones-disponibles`,data);
            this.loadingTable = false;
            this.programaciones = programaciones.programaciones;

        },
        async searchProducto(q=''){

            this.loadingSProducto = true;
            const { data } = await this.$http.get(`/transportes/encomiendas/get-productos?search=${q}`);
            this.loadingSProducto = false;
            this.items = data;
        },

        async searchTerminales(input = ''){
            this.loadingTerminales = true;
            const { data } = await this.$http.get(`/transportes/encomiendas/get-terminales?search=${input}`);
            this.loadingTerminales = false;
            this.terminales = data.terminales;
        },

        async searchDestinos(){
            this.loadingDestinos = true;
            const { data } = await this.$http.get(`/transportes/encomiendas/${this.terminalId}/get-destinos`);
            this.loadingDestinos = false;
            //this.destinos = data.programaciones;
            this.destinos = data.destinos;
        },
        onUpdate() {
            this.loading = true;
            this.$http
                .put(`/transportes/choferes/${this.chofer.id}/update`, this.form)
                .then((response) => {

                    this.$emit("onUpdateItem", response.data.data);
                    this.onClose();
                })
                .finally(() => {
                    this.loading = false;
                    this.errors = {};
                })
                .catch((error) => {
                    this.axiosError(error);
                });
        },
        async onStore() {
            this.loading = true;
            this.errors = {};

            await this.$http.post('/transportes/encomiendas/store',this.encomienda)
                .then( ({data}) => {
                    this.loading = false;
                    this.$emit('onAddItem',data.encomienda);

                    if (this.document.document_type_id === "nv") {
                        this.modalNote();
                    } else {
                        this.$eventHub.$emit('reloadDataInvoices')
                        this.$emit('onSuccessVenta',this.documentId);
                    }

                    this.$message({
                        type: 'success',
                        message: 'Encomienda registrada.'
                    });
                    this.onClose()
                }).catch( error => {
                this.axiosError(error);
            }).finally(() => {
                this.loading = false;
            });
        },
        onSubmit() {
            if (this.chofer) {
                this.onUpdate();
            } else {
                this.onStore();
            }
        },
        onClose() {
            this.programaciones = [];
            this.terminalId = null;
            this.destinoId = null;
            // this.onEdit = false;
            this.$emit("update:visible", false);
        },
        guardarEncomienda(programacion){
            this.encomienda.programacion_id = programacion.id;
        },
        async agregarProducto(evt){
            if(this.producto.item.description && this.producto.unit_price){
                let precio = parseFloat(this.producto.unit_price);
                let cant = parseFloat(this.producto.quantity);
                let valorventa = parseFloat(precio/1.18);
                let igv = parseFloat(precio-valorventa);

                let total = parseFloat(cant*precio);

                if( this.transporte_configuration.encomienda_afecto_igv) { //encomienda grabada
                    console.log("grabado")
                    this.producto.input_unit_price_value=precio;
                    this.producto.cant=cant;
                    this.producto.item.name = this.producto.item.description;
                    this.producto.item.sale_unit_price =precio;
                    this.producto.total=total;
                    this.producto.total_base_igv=valorventa*cant;
                    this.producto.total_value=valorventa*cant;
                    this.producto.unit_price=precio;
                    this.producto.unit_value=valorventa;
                    this.producto.total_igv= igv*cant;
                    this.producto.total_taxes=igv*cant;
                }else{ // encomienda exonerada
                    console.log("exonerado")
                    this.producto.input_unit_price_value=precio;
                    this.producto.cant=cant;
                    this.producto.item.name = this.producto.item.description;
                    this.producto.item.sale_unit_price = precio;
                    this.producto.item.unit_price=precio;
                    this.producto.total=total;
                    this.producto.total_base_igv=precio*cant;
                    this.producto.total_value=precio*cant;
                    this.producto.unit_price=precio;
                    this.producto.unit_value=precio*cant;
                }

                this.loadingProducto = true;
                let id = await this.createItem(this.producto.item);
                await this.searchProducto(id);
                this.loadingProducto = false;
                if(!id) return this.$message.error('Lo sentimos no se pudo agregar el producto');
                this.producto.item_id = this.producto.item.id = id;

                let p =  JSON.parse(JSON.stringify(this.producto));

                this.document.items.push( p );

                this.total += parseFloat(this.producto.total);

                if( this.transporte_configuration.encomienda_afecto_igv) { //jala la configuración de afectacion de igv de la nueva opcion de configuracion des módulo transporte
                    this.initProductoGrabado();
                }else{
                    this.initProductoExonerado();
                }
                this.selectItem = null;
            }
        },

        eliminarProducto(index){
            this.document.items.splice(index,1);
            let total = 0;
            this.document.items.forEach(item => {
                total += parseFloat(item.unit_price);
            })
            this.total = total;
        },
        async onCreate() {
            await this.cargar_transporte_configuracion()
            this.setTime()

            this.total = 0;

            this.terminalId = this.userTerminal.terminal_id;
            if(this.edit){
                this.encomienda = {...this.itemEncomienda};
                let programacion = this.encomienda.programacion;

                // this.encomienda.destino_id = programacion  ?  programacion.terminal_destino_id : null;
                if(programacion){
                    this.programaciones.push(this.encomienda.programacion);
                }
                this.document = this.encomienda.document;
                this.document.items.forEach( item => {
                    this.total += parseFloat(item.total);
                });
            }else {
                this.comprobante = null;
                this.encomienda = {
                    document_id: null,
                    note_id: null,
                    destino_id:null,
                    descripcion:null,
                    remitente_id:null,
                    destinatario_id:null,
                    estado_pago_id:1,
                    estado_envio_id:1,
                    programacion_id:null,
                    fecha_salida:moment().format("YYYY-MM-DD"),
                    clave :''
                }
                this.initDocument();
                this.document.document_type_id = (this.documentTypesInvoice.length > 0)?this.documentTypesInvoice[0].id:null;
                this.document.establishment_id = this.establishment.id;
                this.clickAddPayment();
            }
            this.changeDocumentType();
            this.document.document_type_id = '03';
            this.filterSeries();
            this.filterCustomers();
            this.onCalculateTotals();

            this.load = true;

            //await this.initializeSelects();
            await this.searchTerminales();
            await this.searchProducto();

            this.load = false;

            const date = moment().format("YYYY-MM-DD");
            await this.searchExchangeRateByDate(date).then((res) => {
                this.document.exchange_rate_sale = res;
            });

            this.tempRemitentes = this.remitentes =this.destinatarios = this.persons;

        },
        onUpdateItemsWithExtras(){
            this.document.items = this.document.items.map((it) => {
                // it.item.description = name;
                // it.item.full_description = name;
                // it.name_product_pdf = name;
                it.quantity = 1;
                const newTotal =
                    parseFloat(it.total) + parseFloat(this.arrears);
                it.input_unit_price_value = parseFloat(newTotal);
                it.item.unit_price = parseFloat(newTotal);
                it.unit_value = parseFloat(newTotal);
                const newItem = this.calculateRowItem(it, "PEN", 3);
                return newItem;
                // return it;
            });
        },
        async onGoToInvoice() {
            // this.onUpdateItemsWithExtras();
            this.onCalculateTotals();
            let validate_payment_destination = this.validatePaymentDestination();

            if (validate_payment_destination.error_by_item > 0) {
                return this.$message.error("El destino del pago es obligatorio");
            }

            if(!this.encomienda.remitente_id){
                return this.$message.info('Debe seleccionar remitente.');
            }

            if(!this.isCashOpen) return this.$message.info('La caja no esta abierta');

            if(!this.encomienda.destino_id) return this.$message.info('Seleccione un destino de la encomienda');

            if(this.document.items.length <= 0) return this.$message.info('Agregue detalles a su encomienda');

            if(this.encomienda.estado_pago_id==1){//pagado
                this.document.payments.push(this.payment);
            }
            else{
                this.document.payments=[];
            }

            //if(1==1) return this.$message.info('prueba');
            this.loading = true;

            if (this.document.document_type_id === "nv") {
            this.document.prefix = "NV";
            this.resource_documents = "sale-notes";
            } else {
                this.document.prefix = null;
                this.resource_documents = "documents";
            }
            this.$http
                .post(`/${this.resource_documents}`, this.document)
                .then(async (response) => {
                    if (response.data.success) {
                        this.documentId = response.data.data.id;
                        this.documentNewId = response.data.data.id;


                        if (this.document.document_type_id === "nv"){
                            this.form_cash_document.sale_note_id = response.data.data.id;
                            this.encomienda.note_id = response.data.data.id;
                        }
                        else{
                            this.encomienda.document_id = response.data.data.id;
                            this.form_cash_document.document_id = response.data.data.id;
                        }


                        this.$emit("update:showDialog", false);
                        await this.onStore();// guardando encomienda
                        await this.saveCashDocument();
                        this.autoPrintDocument();

                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch((error) => {
                    if (error.response) {
                        this.$message.error(error.response.data.message);
                    }
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        saveCashDocument() {
            this.$http
                .post(`/cash/cash_document`, this.form_cash_document)
                .then((response) => {
                    if (!response.data.success) {
                        this.$message.error(response.data.message);
                    }

                    this.form_cash_document.document_id=null;
                    this.form_cash_document.sale_note_id=null;
                })
                .catch((error) => {
                    this.axiosError(error);
                });
        },
        onCalculatePaidAndDebts() {
            this.totalPaid = this.rent.items
                .map((i) => {
                    if (i.payment_status === "PAID") {
                        return i.item.total;
                    }
                    return 0;
                })
                .reduce((a, b) => a + b, 0);
            const totalDebt = this.rent.items
                .map((i) => {
                    if (i.payment_status === "DEBT") {
                        return i.item.total;
                    }
                    return 0;
                })
                .reduce((a, b) => a + b, 0);
            this.totalDebt = totalDebt + parseFloat(this.arrears);
        },
        initProductoGrabado(){
            this.producto = {
                IdLoteSelected: null,
                affectation_igv_type: {
                    active: 1,
                    description: "Grabado - Operación Onerosa",
                    exportation: 0,
                    free: 0,
                    id: "10"
                },
                affectation_igv_type_id: "10",
                attributes: [],
                charges: [],
                currency_type_id: "PEN",
                discounts: [],
                document_item_id: null,
                input_unit_price_value: "100",//cambiado
                item: {
                    id: null,
                    name:null,
                    second_name:null,
                    amount_plastic_bag_taxes: "0.10",
                    attributes: [],
                    barcode: "",
                    brand: "",
                    calculate_quantity: false,
                    category: "",
                    currency_type_id: "PEN",
                    currency_type_symbol: "S/",
                    description: null, //cambiado
                    full_description: "",
                    has_igv: false,
                    internal_id: null,
                    item_unit_types: [],
                    lots: [],
                    lots_enabled: false,
                    lots_group: [],
                    presentation: [],
                    purchase_affectation_igv_type_id: "10",
                    purchase_unit_price: "0.000000",
                    sale_affectation_igv_type_id: "10",
                    sale_unit_price: 0,
                    stock: 1,
                    stock_min:1,
                    unit_price: "0", //cambiado
                    unit_type_id: "ZZ",
                    is_set: false,
                    series_enabled: false,
                    purchase_has_igv: true,
                    web_platform_id:null,
                    has_plastic_bag_taxes: false,
                    item_warehouse_prices: [],
                },
                item_id: 1,
                percentage_igv: 18,
                percentage_isc: 0,
                percentage_other_taxes: 0,
                price_type_id: "01",
                quantity: 1,
                system_isc_type_id: null,
                total: 0,//cambiado
                total_base_igv: 0,//cambiado
                total_base_isc: 0,
                total_base_other_taxes: 0,
                total_charge: 0,
                total_discount: 0,
                total_igv: 0,
                total_isc: 0,
                total_other_taxes: 0,
                total_plastic_bag_taxes: 0,
                total_taxes: 0,
                total_value: 0,//cambiado
                unit_price: 0,//cambiado
                unit_value: 0,//cambiado
                warehouse_id: null
            };
        },
        initProductoExonerado(){
            this.producto = {
                IdLoteSelected: null,
                affectation_igv_type: {
                    active: 1,
                    description: "Exonerado - Operación Onerosa",
                    exportation: 0,
                    free: 0,
                    id: "20"
                },
                affectation_igv_type_id: "20",
                attributes: [],
                charges: [],
                currency_type_id: "PEN",
                discounts: [],
                document_item_id: null,
                input_unit_price_value: "100",//cambiado
                item: {
                    id:null,
                    name:null,
                    second_name:null,
                    amount_plastic_bag_taxes: "0.10",
                    attributes: [],
                    barcode: "",
                    brand: "",
                    calculate_quantity: false,
                    category: "",
                    currency_type_id: "PEN",
                    currency_type_symbol: "S/",
                    description: null,
                    full_description: "",
                    has_igv: false,
                    item_type_id:'02',
                    internal_id: null,
                    item_unit_types: [],
                    lots: [],
                    lots_enabled: false,
                    lots_group: [],
                    presentation: [],
                    purchase_affectation_igv_type_id: "20",
                    purchase_unit_price: "0.000000",
                    sale_affectation_igv_type_id: "20",
                    sale_unit_price: 0,
                    stock: 1,
                    stock_min:1,
                    unit_price: 0, //cambiado
                    unit_type_id: "ZZ",
                    is_set: false,
                    series_enabled: false,
                    purchase_has_igv: true,
                    web_platform_id:null,
                    has_plastic_bag_taxes: false,
                    item_warehouse_prices: [],
                },
                item_id: null,
                percentage_igv: 18,
                percentage_isc: 0,
                percentage_other_taxes: 0,
                price_type_id: "01",
                quantity: 1,
                system_isc_type_id: null,
                total: 0,//cambiado
                total_base_igv: 0,//cambiado
                total_base_isc: 0,
                total_base_other_taxes: 0,
                total_charge: 0,
                total_discount: 0,
                total_igv: 0,
                total_isc: 0,
                total_other_taxes: 0,
                total_plastic_bag_taxes: 0,
                total_taxes: 0,
                total_value: 0,//cambiado
                unit_price: 0,//cambiado
                unit_value: 0,//cambiado
                warehouse_id: null
            };
        },
        initDocument() {
            this.document = {
                note_credit_or_debit_type_id:null,
                note_description:null,
                affected_document_id:null,
                group_id:null,
                customer_id: null,
                customer: {},
                document_type_id: null,
                series_id: null,
                establishment_id: null,
                number: "#",
                date_of_issue: null,
                time_of_issue: null,
                currency_type_id: "PEN",
                purchase_order: null,
                exchange_rate_sale: 0,
                total_prepayment: 0,
                total_charge: 0,
                total_discount: 0,
                total_exportation: 0,
                total_free: 0,
                total_taxed: 0,
                total_unaffected: 0,
                total_exonerated: 0,
                total_igv: 0,
                total_base_isc: 0,
                total_isc: 0,
                total_base_other_taxes: 0,
                total_other_taxes: 0,
                total_taxes: 0,
                total_value: 0,
                total: 0,
                operation_type_id: "0101",
                date_of_due: null,
                delivery_date: null,
                items: [],
                charges: [],
                discounts: [],
                attributes: [],
                guides: [],
                additional_information: null,
                actions: {
                    format_pdf: "a4",
                },
                dispatch_id: null,
                dispatch: null,
                is_receivable: false,
                payments: [],
                hotel: {},
            };
        },
        onCalculateTotals() {
            let total_discount = 0
            let total_charge = 0
            let total_exportation = 0
            let total_taxed = 0
            let total_exonerated = 0
            let total_unaffected = 0
            let total_free = 0
            let total_igv = 0
            let total_value = 0
            let total = 0
            let total_plastic_bag_taxes = 0
            this.document.items.forEach((row) => {
                total_discount += parseFloat(row.total_discount)
                total_charge += parseFloat(row.total_charge)

                if (row.affectation_igv_type_id === '10') {
                    total_taxed += parseFloat(row.total_value)
                }
                if (row.affectation_igv_type_id === '20') {
                    total_exonerated += parseFloat(row.total_value)
                }
                if (row.affectation_igv_type_id === '30') {
                    total_unaffected += parseFloat(row.total_value)
                }
                if (row.affectation_igv_type_id === '40') {
                    total_exportation += parseFloat(row.total_value)
                }
                if (['10', '20', '30', '40'].indexOf(row.affectation_igv_type_id) < 0) {
                    total_free += parseFloat(row.total_value)
                }
                if (['10', '20', '30', '40'].indexOf(row.affectation_igv_type_id) > -1) {
                    total_igv += parseFloat(row.total_igv)
                    total += parseFloat(row.total)
                }
                total_value += parseFloat(row.total_value)
                total_plastic_bag_taxes += parseFloat(row.total_plastic_bag_taxes)

                if (['13', '14', '15'].includes(row.affectation_igv_type_id)) {

                    let unit_value = (row.total_value/row.quantity) / (1 + row.percentage_igv / 100)
                    let total_value_partial = unit_value * row.quantity
                    row.total_taxes = row.total_value - total_value_partial
                    row.total_igv = row.total_value - total_value_partial
                    row.total_base_igv = total_value_partial
                    total_value -= row.total_value

                }
            });

            this.document.total_exportation = _.round(total_exportation, 2)
            this.document.total_taxed = _.round(total_taxed, 2)
            this.document.total_exonerated = _.round(total_exonerated, 2)
            this.document.total_unaffected = _.round(total_unaffected, 2)
            this.document.total_free = _.round(total_free, 2)
            this.document.total_igv = _.round(total_igv, 2)
            this.document.total_value = _.round(total_value, 2)
            this.document.total_taxes = _.round(total_igv, 2)
            this.document.total_plastic_bag_taxes = _.round(total_plastic_bag_taxes, 2)
            // this.form.total = _.round(total, 2)
            this.document.total = _.round(total + this.document.total_plastic_bag_taxes, 2)

            if(this.enabled_discount_global)
                this.discountGlobal()

            if(this.prepayment_deduction)
                this.discountGlobalPrepayment()

            if(['1001', '1004'].includes(this.document.operation_type_id))
                this.changeDetractionType()

            //this.setTotalDefaultPayment()
            //this.setPendingAmount()
        },
        async updateEncomienda(evt){
            this.loading = true;
            await this.$http.put(`/transportes/encomiendas/${this.encomienda.id}/update`,this.encomienda)
            .then(({ data }) => {
                this.$message.success(data.message);
                this.$emit('onUpdateItem',data.encomienda);
                this.onClose();
            }).finally(() => {
                this.loading = false;
                this.errors = {};
            }).catch((error) => {
                this.axiosError(error);
            });
        },
        filterSeries() {
            this.document.series_id = null
            this.series = _.filter( this.allSeries, {
                'establishment_id': this.document.establishment_id,
                'document_type_id': this.document.document_type_id,
                'contingency': this.is_contingency});
            this.document.series_id = (this.series.length > 0)?this.series[0].id:null
        },
        filterCustomers() {
            if (['0101', '1001', '1004'].includes(this.document.operation_type_id)) {

                if(this.document.document_type_id === '01') {
                    this.tempRemitentes = _.filter(this.remitentes, {'identity_document_type_id': '6'})
                } else {
                    this.tempRemitentes = _.filter(this.remitentes, (c) => { return c.identity_document_type_id !== '6' })
                    // if(this.document_type_03_filter) {
                    //     this.pasajeros = _.filter(this.pasajeros, (c) => { return c.identity_document_type_id !== '6' })
                    // } else {
                    //     this.pasajeros = this.pasajeros
                    // }
                }

            } else {
                this.tempRemitentes = this.remitentes
            }

        },
        async createItem(item){
            try{
                const { data } = await this.$http.post('/items',item);
                return data.id;

            }catch(error){
                return null;
            }
        },
        nuevoProducto(){
            if( this.transporte_configuration.encomienda_afecto_igv) { //jala la configuración de afectacion de igv de la nueva opcion de configuracion des módulo transporte
                this.initProductoGrabado();
            }else{
                this.initProductoExonerado();
            }
        },
        setTime(){

            //setInterval(() => {
                this.$http
                    .get(`/documents/fecha-actual`)
                    .then((response) => {
                        this.document.date_of_issue= response.data.fecha
                        this.document.time_of_issue= response.data.hora
                        this.document.date_of_due= response.data.fecha
                        this.document.delivery_date= response.data.fecha


                    });
            //}, 5000)
        },
        autoPrintDocument(){

            if(this.isAutoPrint)
            {
                var route = `/printticket/document/${this.documentNewId}/ticket`;
                if(this.resource_documents!=='documents'){
                    route = `/sale-notes/ticket/${this.documentNewId}/ticket`;
                }

                this.$http.get(route)
                    .then(response => {
                        this.printTicket(response.data)
                    })
                    .catch(error => {
                        console.log(error)
                    })
            }

        },
        printTicket(html_pdf){

            if (html_pdf.length > 0)
            {
                const config = getUpdatedConfig()
                const opts = getUpdatedConfig()

                const printData = [
                    {
                        type: 'html',
                        format: 'plain',
                        data: html_pdf,
                        options: opts
                    }
                ]

                qz.print(config, printData)
                    .then(()=>{

                        this.$notify({
                            title: '',
                            message: 'Impresión en proceso...',
                            type: 'success'
                        })

                    })
                    .catch(displayError)
            }

        },
        async buscar_remitente(){
            let type = this.persona.identity_document_type_id
            this.encomienda.remitente_nombre = ""
            this.loading = true

            let response_local =  await this.$http.get(`/transportes/encomiendas/get-pasajero/${type}/${this.encomienda.remitente_numero}`)
            if(response_local.data.success){
                this.encomienda.remitente_id   = response_local.data.data.id
                this.encomienda.remitente_nombre= response_local.data.data.name
                this.document.customer_id = response_local.data.data.id
            }else{
                this.encomienda.remitente_nombre= response_local.data.name.name
            }
            this.loading = false

        },

        limpiarPersona(){
            this.persona.id=""
            this.persona.number= ""
            this.persona.name=  ""
            this.persona.edad=""
        },
        async buscar_destinatario(){
            //let type = this.persona.identity_document_type_id
            let type = 1 //solo dni
            this.encomienda.destinatario_nombre = ""
            this.loading = true

            let response_local = await this.$http.get(`/transportes/encomiendas/get-pasajero/${type}/${this.encomienda.destinatario_numero}`)
            if(response_local.data.success){
                this.encomienda.destinatario_id   = response_local.data.data.id
                this.encomienda.destinatario_nombre= response_local.data.data.name
            }else{
                this.encomienda.destinatario_nombre= response_local.data.name.name
            }
            this.loading = false

        },
        async cargar_transporte_configuracion(){
            await this.$http.get(`/transportes/configuration/record`)
                .then(response => {
                    this.transporte_configuration = response.data.data
                })

            if( this.transporte_configuration.encomienda_afecto_igv) { //jala la configuración de afectacion de igv de la nueva opcion de configuracion des módulo transporte
                this.initProductoGrabado();
            }else{
                this.initProductoExonerado();
            }
        }
    }
};
</script>
