<template>
    <div>
    <el-dialog title="Venta" :visible="visible" @close="onClose" @open="onCreate" :close-on-click-modal="false" width="800px">
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="">Tipo de comprobante</label>
                    <el-select v-if="transportePasaje"
                        v-model="document.document_type_id"
                        @change="changeDocumentType"
                        popper-class="el-select-document_type"
                        dusk="document_type_id"
                        class="border-left rounded-left border-info"
                        :disabled="(transportePasaje && !isReserva) ? true : false"
                    >
                        <el-option
                            v-for="option in documentTypesInvoice"
                            :key="option.id"
                            :value="option.id"
                            :label="option.description"
                        ></el-option>
                        <el-option key="nv" value="nv" label="NOTA DE VENTA"></el-option>
                    </el-select>
<!--                    <input v-else  class="form-control" value="Sin comprobante" disabled="disabled" />-->
                </div>
            </div>
            <div class="col-6" v-if="(transportePasaje)">
                <div class="form-group">
                    <label for="">Serie</label>
                    <!-- <el-input v-model="document.serie" disabled></el-input> -->
                    <el-select v-model="document.series_id" :disabled="(transportePasaje && !isReserva) ? true : false">
                        <el-option
                            v-for="option in allSeries"
                            :key="option.id"
                            :value="option.id"
                            :label="option.number"
                        ></el-option>
                    </el-select>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label for="dni">Estado de asiento</label>
                    <el-select v-model="estadoAsiento"  popper-class="el-select-customers"
                               placeholder="Estado asiento"
                               :disabled="transportePasaje ? true : false"
                    >
                        <el-option v-for="estado in tempEstadosAsientos" :key="estado.id" :value="estado.id" :label="estado.nombre">

                        </el-option>
                    </el-select>
                </div>
            </div>
        </div>
        <div class="row pt-2">
            <div  class="col-5">
                <div class="form-group">
                    <label for="dni">
                        Cliente
                        <a href="#" @click.prevent="modalPerson(true,false)">[+ Nuevo]</a>
                    </label>
                    <el-select v-if="transportePasaje && isReserva" v-model="clienteId" filterable remote  popper-class="el-select-customers"
                        dusk="clienteId"
                        placeholder="Buscar cliente"
                        :remote-method="searchCliente"
                        :loading="loadingCliente"
                        :disabled="(transportePasaje && !isReserva) ? true : false"
                        >
                        <el-option v-for="pasaje in tempClientes" :key="pasaje.id" :value="pasaje.id" :label="pasaje.name">

                        </el-option>
                    </el-select>

                    <el-input v-if="transportePasaje && !isReserva && transportePasaje.pasajero"  v-model="transportePasaje.pasajero.name" disabled type="text">   ></el-input>
                </div>
            </div>

            <div class="col-3">
                <div class="form-group">
                    <label for="dni">Precio</label>
                    <el-input v-model="precio" type="number" :disabled=" (transportePasaje) ? true : false"></el-input>
                </div>

            </div>

            <div v-if="asiento" class="col-3">
                <div class="form-group">
                    <label for="dni">Asiento</label>
                    <el-input disabled :value="asiento.numero_asiento"></el-input>
                </div>
            </div>
            <div v-if="isReserva" class="col-5">
                <div class="form-group">
                    <label for="dni">
                        Nombre del pasajero

                    </label>
                    <el-input disabled v-model="nombrePasajero" type="text" placeholder="Nombre del pasajero" ></el-input>
                    <p class="text-center text-green-light text-blue"><b>{{telPasajero}}</b></p>
                </div>
            </div>
            <div v-if="isReserva && document.document_type_id == '01'" class="col-5">
                <div class="form-group">
                    <label for="dni">
                        Pasajero
                        <a href="#" @click.prevent="modalPerson(false,true)">[+ Nuevo]</a>
                    </label>
                    <el-select v-model="pasajeroId" filterable remote  popper-class="el-select-customers"
                               dusk="pasajeroId"
                               placeholder="Buscar pasajero"
                               :remote-method="searchPasajero"
                               :loading="loadingPasajero"
                    >
                        <el-option v-for="persona in tempPasajeros" :key="persona.id" :value="persona.id" :label="persona.name">

                        </el-option>
                    </el-select>
                </div>
            </div>




        </div>
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <label for="dni">
                        Vendido por:

                    </label>
                    <el-input disabled v-model="usuario" type="text" placeholder="Nombre del vendedor" ></el-input>

                </div>
            </div>
            <div v-if="transportePasaje" class="col-6 d-flex align-items-center" style="font-size: 15px">
                Viaje de <el-tag style="color:white" :color="transportePasaje.origen.color"> {{ transportePasaje.origen.nombre }} </el-tag>  a <el-tag style="color:white" :color="transportePasaje.destino.color"> {{ transportePasaje.destino.nombre }} </el-tag>
            </div>
        </div>
        <div v-if="((this.documentId || this.sale_note_id) && !isReserva)" class="row justify-content-center">

            <el-button type="primary" @click="viewComprobante" :style="{marginTop:'1.90rem'}">
                Comprobante
                <i class="fa fa-file-alt"></i>
            </el-button>
            <el-button :disabled="user.type != 'admin'" type="danger" @click="anularBoleto" :style="{marginTop:'1.90rem'}">
                Anular
                <i class="fa fa-trash"></i>
            </el-button>

        </div>
        <div v-if="(isReserva)" class="row justify-content-center">

            <el-button type="danger" @click="eliminarReserva(transportePasaje.id)" :style="{marginTop:'1.90rem'}">
                Eliminar reserva
                <i class="fa fa-trash"></i>
            </el-button>

        </div>
        <div v-if="!transportePasaje || isReserva" class="row mt-2">
            <div class="col-md-12">
                <el-collapse v-model="activePanel" accordion>
                    <el-collapse-item name="1" >
                        <template slot="title">
                            <i class="fa fa-plus text-info"></i> &nbsp;Pagos<i class="header-icon el-icon-information"></i>
                        </template>
                        <div  class="row mt-2">
                            <div class="col-12">
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
                                                        <el-input disabled v-model="payment.payment"></el-input>
                                                    </div>
                                                </td> -->
                                            </tr>

                                            </tbody>

                                        </table>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </el-collapse-item>
                </el-collapse>
            </div>
        </div>



        <document-options
        :showDialog.sync="showDialogDocumentOptions"
        :recordId="documentId"
        :isContingency="false"
        :showClose="true"
        :configuration="configuration"
        ></document-options>

        <person-form :showDialog.sync="showDialogNewPerson"
        type="customers"
        :external="true"
        :input_person="input_person"
        :buscar_cliente="buscar_cliente"
        :buscar_pasajero="buscar_pasajero"
        :document_type_id="document.document_type_id"></person-form>

        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-center">
                <el-button v-if="transportePasaje && isReserva" :loading="loading" type="primary" @click="saveDocument">Guardar</el-button>
                <el-button type="secondary" @click="onClose">Cancelar</el-button>
            </div>
        </div>
    </el-dialog>
    <sale-note-options
        :showDialog.sync="showDialogSaleNoteOptions"
        :recordId="sale_note_id"
        :configuration="configuration"
        :showClose="true"
    >
    </sale-note-options>

    </div>
</template>
<script>
import { exchangeRate } from '../../../../../../../resources/js/mixins/functions';
import DocumentOptions from "@views/documents/partials/options.vue";
import SaleNoteOptions from "@views/sale_notes/partials/options.vue";
import PersonForm from "@views/persons/form2.vue";
export default {
    mixins: [exchangeRate],
    components:{
        DocumentOptions,
        PersonForm,
        SaleNoteOptions

    },
    props:{
        visible: {
            type: Boolean,
            required: true,
            default: false,
        },
        asiento: {
            type: Object|null,
            required: true,
            default: {},
        },
        programacion: {
            type: Object|null,
            required: true,
            default: {},
        },
        estadosAsientos:{
            type:Array,
            required:true,
            default:() => []
        },
        fechaSalida: {
            type: String|null,
            required: true,
            default: null,
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
        configuration:{
            type: Object,
            required: true,
        },
        tipoVenta:{
            type:Number,
            default:null
        },
        user:{
            type:Object,
            required:true,
        },
        pasaje:{
            type:Object,
            required:false,
            default: () => ({})
        },
    },
    created(){

        this.initDocument();
        this.initForm();
        this.all_document_types = this.documentTypesInvoice;
        this.document.document_type_id = (this.documentTypesInvoice.length > 0)?this.documentTypesInvoice[0].id:null;
        this.allSeries = this.series;
        this.document.establishment_id = this.establishment.id;
        this.changeDocumentType();

        this.onCreate()

    },
    watch:{
        precio:function(newVal){
            // if(this.document.payments.length > 0){
            //     this.document.payments[0].payment = newVal ? newVal : 0;
            // }
        },
    },
    mounted(){

        this.$eventHub.$on('reloadDataCustomers', (clienteId) => {
            this.reloadDataCustomers(clienteId)
        })

        this.$eventHub.$on('reloadDataPasajeros', (pasajeroId) => {
            this.reloadDataPasajeros(pasajeroId)
        });
    },
    data(){
        return ({
            buscar_cliente:false,
            input_person:{},
            showDialogNewPerson:false,
            pasajeros:[],
            pasajero:null,
            loadingPasajero:false,
            estadoAsiento:null,
            pasajeroId:null,
            precio:null,
            loading:false,
            transportePasaje:null,
            //document
            documentId:null,
            document: {
                payments: [],
            },
            producto:{},
            all_document_types: [],
            document_types: [],
            allSeries:[],
            showDialogDocumentOptions:false,
            payment:{
                payment_method_type_id:null,
                payment_destination_id:null,
                reference:null,
                payment:null
            },
            is_contingency: 0,
            activePanel: 0,
            tempPasajeros:[],
            clientes:[],
            tempClientes:[],
            loadingCliente:false,
            buscar_pasajero:false,
            clienteId:null,
            sale_note_id:null,
            showDialogSaleNoteOptions:false,
            nombrePasajero:null,
            telPasajero:null,
            tempEstadosAsientos: this.estadosAsientos,
            usuario: null
        });
    },
    computed:{
        isReserva(){
            return this.asiento && this.asiento.estado_asiento_id === 3;
        },
        nameItem(){
            if(this.transportePasaje.tipo_venta == 2) return `${this.programacion.origen.nombre}-${this.programacion.destino.nombre}`
            else if(this.transportePasaje.tipo_venta == 1) return `${this.origen.nombre}-${this.destino.nombre}`;
        },
    },
    methods:{

        async reloadDataCustomers(clienteId){

            await this.searchCliente();
            this.clienteId = clienteId;

        },
        async reloadDataPasajeros(pasajeroId){

            await this.searchPasajero();
            this.pasajeroId = pasajeroId;
        },
        onClose(){
            this.$emit("update:visible", false);
            this.pasajeroId = null;
            this.clienteId= null;
            this.estadoAsiento = null;
            this.precio = null;
            //this.transportePasaje.id=null;
            this.documentId=null;
            //this.sale_note_id=null;

            this.changeDocumentType();
        },
        async searchPasajero(input=''){
            this.loadingPasajero = true;
            const { data } = await this.$http.get(`/transportes/encomiendas/get-clientes?search=${input}`);
            this.loadingPasajero = false;
            this.tempPasajeros = this.pasajeros  = data.clientes;
        },
        async searchCliente(input=''){
            this.loadingCliente = true;
            const { data } = await this.$http.get(`/transportes/encomiendas/get-clientes?search=${input}`);
            this.loadingCliente = false;
            this.tempClientes = this.clientes  = data.clientes;
        },
        async onCreate(){
            this.transportePasaje = this.pasaje || null;
            this.initProducto();
            //this.initDocument();
            this.clickAddPayment();
            this.onCalculateTotals();

            if(this.transportePasaje){
                this.nombrePasajero = this.transportePasaje.nombre_pasajero;
                this.telPasajero = this.transportePasaje.telefono;
                this.pasajero = this.transportePasaje.pasajero;
                this.precio = this.transportePasaje.precio;
                this.pasajeroId = this.pasajero ? this.pasajero.id : null;
                this.estadoAsiento = 2;
                this.documentId = this.transportePasaje.document_id
                this.sale_note_id = this.transportePasaje.note_id ;
                this.usuario = this.transportePasaje.user_name;

                if(this.transportePasaje.document_id){
                    this.document.document_type_id= this.transportePasaje.document.document_type_id;
                }
                if(this.transportePasaje.note_id){
                    this.document.document_type_id= "nv";
                }
                // this.document.document_type_id = this.transportePasaje.document
                // ? this.transportePasaje.document.document_type_id
                // : (this.documentTypesInvoice.length > 1) ? this.documentTypesInvoice[1].id : null
            }

            const date = moment().format("YYYY-MM-DD");
            await this.searchExchangeRateByDate(date).then((res) => {
                this.document.exchange_rate_sale = res;
            });

            await this.searchPasajero();
            await this.searchCliente();

            this.changeDocumentType();
        },
        async saveDocument(){

            if(this.document.document_type_id == '01'){
                if(!this.clienteId && !this.pasajeroId ){
                    return this.$message.error('Debe llenar el cliente y el pasajero');
                }
            }

            if(this.document.document_type_id == '03' || this.document.document_type_id == 'nv'){
                if(!this.clienteId ){
                    return this.$message.error('Debe llenar el cliente');
                }

            }






            this.loading = true;
            this.document.items.length=0;
            let precio = parseFloat(this.precio);
            if(!precio) return;
            this.producto.input_unit_price_value=precio;
            this.producto.item.unit_price=precio;
            this.producto.item.description = 'Pasaje -'+this.nameItem;
            this.producto.item.name = 'Pasaje -'+this.nameItem;
            this.producto.total=precio;
            this.producto.total_base_igv=precio;
            this.producto.total_value=precio;
            this.producto.unit_price=precio;
            this.producto.unit_value=precio;
            this.document.items.push(this.producto);
            //this.document.payments.push(this.payment);
            this.document.customer_id=this.pasajeroId;

            const id = await this.createItem(this.producto.item);
            if(!id) return this.$message.error('Lo sentimos ha ocurrido un error');
            this.producto.item_id = this.producto.item.id = id;

            //this.document.items.push(this.producto);
            this.payment.payment= precio;
            this.document.payments.push(this.payment);

            this.document.customer_id=this.clienteId;
            this.onCalculateTotals();
            let validate_payment_destination = this.validatePaymentDestination();
            if (validate_payment_destination.error_by_item > 0) {
                return this.$message.error("El destino del pago es obligatorio");
            }

            let resourceDocuments;

            if (this.document.document_type_id === "nv") {
                this.document.prefix = "NV";
                resourceDocuments = "sale-notes";
            } else {
                this.document.prefix = null;
                resourceDocuments = "documents";
            }



            await this.$http
                .post(`/${resourceDocuments}`, this.document)
                .then(async (response) => {
                    if (response.data.success) {
                        if (this.document.document_type_id === "nv"){
                            this.form_cash_document.sale_note_id = response.data.data.id;
                            this.sale_note_id = response.data.data.id;
                        }
                        else{
                            this.documentId = response.data.data.id;
                            this.form_cash_document.document_id = response.data.data.id;
                        }

                        await this.saveCashDocument();
                        await this.guardarPasaje();
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch((error) => {
                    if (error.response) {
                        this.$message.error(error.response.data.message);
                    }
                }).then(() => {
                    this.loading_submit = false;
                });
        },
        async guardarPasaje(){
            let doc = (this.document.document_type_id==='nv')? null: this.documentId;
            let note = (this.document.document_type_id==='nv') ? this.sale_note_id : null
            let client = (this.document.document_type_id==='03' || this.document.document_type_id==='nv') ? this.clienteId : this.pasajeroId;


            let data = {
                document_id: doc,
                note_id: note,
                cliente_id:this.clienteId,
                pasajero_id:client,
                // asiento_id:this.asiento.id,
                estado_asiento_id:this.estadoAsiento,
                programacion_id:this.programacion.id,
                fecha_salida:this.fechaSalida,
                precio:this.precio
            };
            this.$http.put(`/transportes/sales/${this.transportePasaje.id}/venta-boleto-reservado`,data)
            .then( ({data}) => {
                this.loading = false;

                if (this.document.document_type_id === "nv") {
                    this.showDialogSaleNoteOptions= true
                } else {
                    this.$emit('onSuccessVenta',this.documentId);
                }

                this.precio = null;
                this.clienteId=null;
                this.pasajeroId = null;
                this.numeroAsiento = null;
                this.form_cash_document.document_id=null;
                this.form_cash_document.sale_note_id=null;
                this.document.document_type_id = '03';

                this.filterSeries();
                this.filterCustomers();
                this.initProducto();
                this.document.payments= [];
                this.$emit('onUpdateItem');
                this.$emit('notificationAll');
                this.$message({
                    type: 'success',
                    message: data.message
                });
                this.onClose()
            }).catch( error => {
                this.axiosError(error);
            }).finally(() => {
                this.loading = false;
            });
        },
        //document and payment
        initProducto(){
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
                    internal_id: null,
                    item_unit_types: [],
                    lots: [],
                    lots_enabled: false,
                    lots_group: [],
                    presentation: [],
                    purchase_affectation_igv_type_id: "20",
                    purchase_unit_price: "0.000000",
                    sale_affectation_igv_type_id: "20",
                    sale_unit_price: 35,
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
                item_id: 2,
                percentage_igv: 18,
                percentage_isc: 0,
                percentage_other_taxes: 0,
                price_type_id: "01",
                quantity: 1,
                system_isc_type_id: null,
                total: 100,//cambiado
                total_base_igv: 100,//cambiado
                total_base_isc: 0,
                total_base_other_taxes: 0,
                total_charge: 0,
                total_discount: 0,
                total_igv: 0,
                total_isc: 0,
                total_other_taxes: 0,
                total_plastic_bag_taxes: 0,
                total_taxes: 0,
                total_value: 100,//cambiado
                unit_price: 100,//cambiado
                unit_value: 100,//cambiado
                warehouse_id: null
            };
        },
        initDocument() {
            this.document = {
                establishment_id:null,
                customer_id: null,
                customer: {},
                document_type_id: null,
                series_id: null,
                number: "#",
                date_of_issue: moment().format("YYYY-MM-DD"),
                time_of_issue: moment().format("HH:mm:ss"),
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
                date_of_due: moment().format("YYYY-MM-DD"),
                delivery_date: moment().format("YYYY-MM-DD"),
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
        saveCashDocument() {
            this.$http
                .post(`/cash/cash_document`, this.form_cash_document)
                .then((response) => {
                    if (!response.data.success) {
                        this.$message.error(response.data.message);
                    }
                })
                .catch((error) => {
                    this.axiosError(error);
                });
        },
        changeDocumentType() {

            this.allSeries = [];
            if (this.document.document_type_id !== "nv") {
                this.filterSeries();
                this.is_document_type_invoice = true;
            } else {
                this.allSeries = _.filter(this.series, {
                    document_type_id: "80",
                });
                this.document.series_id =
                    this.allSeries.length > 0 ? this.allSeries[0].id : null;

                this.is_document_type_invoice = false;
            }
            this.cleanCustomer();
            this.filterCustomers();
        },
        cleanCustomer(){
            this.document.customer_id = null
            this.tempPasajeros = [];
        },
        clickAddPayment() {
            const payment = this.document.payments.length == 0 ? this.document.total : 0;
            const paymentDestinationId = this.paymentDestinations.length > 0 ? this.paymentDestinations[0].id : null;
            this.payment = {
                id: null,
                document_id: null,
                date_of_payment: moment().format("YYYY-MM-DD"),
                payment_method_type_id: "01",
                payment_destination_id: paymentDestinationId,
                reference: null,
                payment: payment,
            };
        },
        validateIdentityDocumentType() {
            let identity_document_types = ["0", "1"];
            let pasajeros = this.pasajeros;
            if (
                identity_document_types.includes(pasajeros.identity_document_type_id)
            ) {
                this.document_types = _.filter(this.all_document_types, { id: "03" });
            } else {
                this.document_types = this.all_document_types;
            }
            //this.document.document_type_id = this.document_types.length > 0 ? this.document_types[0].id : null;
            this.changeDocumentType();
        },
        viewComprobante(){
            if(this.transportePasaje.document_id){
                this.showDialogDocumentOptions = true;
            }else{
                this.showDialogSaleNoteOptions= true
            }
        },
        filterSeries() {
            this.document.series_id = null
            this.allSeries = _.filter(this.series, {
                'establishment_id': this.document.establishment_id,
                'document_type_id': this.document.document_type_id,
                'contingency': this.is_contingency});
            this.document.series_id = (this.allSeries.length > 0)?this.allSeries[0].id:null
        },
        filterCustomers() {
            if (['0101', '1001', '1004'].includes(this.document.operation_type_id)) {
                if(this.document.document_type_id === '01') {
                    this.tempClientes = _.filter(this.pasajeros, {'identity_document_type_id': '6'})
                } else {

                    this.tempClientes = _.filter(this.clientes, (c) => { return c.identity_document_type_id !== '6' })

                }
                this.tempPasajeros = _.filter(this.pasajeros, (c) => { return c.identity_document_type_id !== '6' })
            } else {
                this.tempPasajeros = this.pasajeros
            }
        },
        anularBoleto(){
            console.log(this.transportePasaje)
            if(this.transportePasaje.document_id){
                this.$emit('anularBoleto',this.transportePasaje);
            }else{
                this.anularNota(`/sale-notes/anulate/${this.sale_note_id}`)
            }

        },
        anularNota(url) {
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
                                this.cancelarBoleto(this.transportePasaje.id)
                                resolve()
                            }
                            else{
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
        async cancelarBoleto(id){
            try{
                const { data } = await axios.delete(`/transportes/pasajes/${id}/delete`);

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

                this.$emit('onUpdateItem');
                this.$emit('notificationAll');

            }catch(error){

                this.$message({
                    type: 'error',
                    message: 'Lo sentimos ha ocurrido un error'
                });

            }
        },
        modalPerson(buscar_cliente,buscar_pasajero){
            this.showDialogNewPerson=true;
            this.buscar_cliente = buscar_cliente
            this.buscar_pasajero = buscar_pasajero

        },
        async createItem(item){
            try{
                const { data } = await this.$http.post('/items',item);
                return data.id;

            }catch(error){
                return null;
            }
        },
        eliminarReserva(id){
            this.$http
                .get(`/transportes/sales/borrar-reserva/${id}`)
                .then((response) => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.$emit('onUpdateItem');
                        this.onClose()
                    }
                })
                .catch((error) => {
                    this.axiosError(error);
                });
        }
    }
}
</script>
