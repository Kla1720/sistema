<template>
    <div>
        <el-dialog width="80%" :visible="visible" @close="onClose" @open="onCreate"  :close-on-click-modal="false">
            <div class="row mt-4">
                <div class="col-md-12">
                    <div id="tabs" >
                        <ul class="nav nav-tabs">
                            <li class="nav-item active">
                                <a class="nav-link active" href="#boleto" data-toggle="tab"><i class="fas fa-star"></i> Boleto</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div id="boleto" class="tab-pane active">
                                <div class="row mt-2">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <label for="dni">Estado de asiento</label>
                                            <el-select v-model="estadoAsiento"  popper-class="el-select-customers"
                                                       placeholder="Estado asiento"
                                                       :disabled=" (transportePasaje) ? true : false"
                                            >
                                                <el-option v-for="estado in tempEstadosAsientos" :key="estado.id" :value="estado.id" :label="estado.nombre">

                                                </el-option>
                                            </el-select>
                                        </div>
                                    </div>
                                    <div v-if="!isReserva" class="col-5">
                                        <div class="form-group">
                                            <label for="">Tipo de comprobante</label>
                                            <el-select
                                                v-model="document.document_type_id"
                                                @change="changeDocumentType"
                                                popper-class="el-select-document_type"
                                                dusk="document_type_id"
                                                class="border-left rounded-left border-info"
                                                :disabled="transportePasaje ? true : false"
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
                                    <div v-if="!isReserva" class="col-3">
                                        <div class="form-group">
                                            <label for="">Serie</label>
                                            <!-- <el-input v-model="document.serie" disabled></el-input> -->
                                            <el-select v-model="document.series_id" :disabled="transportePasaje ? true : false">
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
                                <div class="row mt-4" v-if="(!isReserva && document.document_type_id === '01')">
                                    <div class="col-md-3">
                                        <label for="ruc">
                                            Ruc
                                        </label>
                                        <input placeholder="Ingrese el Ruc y presione enter" name="ruc" id="ruc" class="form-control" v-model="empresa.number" v-on:keyup.enter="buscar_rapida_ruc" type="number"   maxlength="11" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"> </input>

                                    </div>
                                    <div class="col-md-5">
                                        <label  id="cliente">Razón Social</label><input name="nombre" class="form-control" v-model="empresa.name" type="text"></input>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Direccion</label><input name="edad" class="form-control" v-model="empresa.address" type="text"></input>
                                    </div>

                                </div>

                                <div class="row mt-4" v-if="isReserva">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label for="dni">
                                                        Dni
                                                    </label>
                                                    <input ref="dniPasajero" id="dniPasajero"  class="form-control" v-model="dniPasajero" type="number" placeholder="Dni y enter" v-on:keyup.enter="buscar_rapida_dni_reserva"></input>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="dni">
                                                        Cliente
                                                    </label>
                                                    <el-input ref="nombrePasajero" id="nombrePasajero"  v-model="nombrePasajero" type="text" placeholder="Nombre del cliente"></el-input>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="dni">
                                                        Teléfono
                                                    </label>
                                                    <el-input ref="telPasajero" id="telPasajero"  v-model="telPasajero" type="number" placeholder="Teléfono" ></el-input>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="!isReserva" class="row mt-4">
                                    <div class="col-md-3">
                                            <label class="ml-2 mr-2" for="edad">
                                                <el-select v-model="persona.identity_document_type_id"
                                                    popper-class="el-select-identity_document_type"
                                                    @change="changeIdentityDocType">
                                                <el-option v-for="option in identity_document_types"
                                                   :key="option.id"
                                                   :label="option.description"
                                                   :value="option.id"></el-option>
                                            </el-select>
                                            </label>
                                            <input :loading="loading_search" placeholder="Ingrese el Dni y presione enter" name="dni" ref="pasajero" id="pasajero" class="form-control" v-model="persona.number" v-on:keyup.enter="buscar_rapida_dni" :type="persona.identity_document_type_id==1 ? 'number' : 'text' "  :maxlength="persona.identity_document_type_id==1 ? 8 : 12 " oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);" required> </input>
                                    </div>
                                    <div class="col-md-4">
                                            <label  for="nombre" >Nombre</label><input name="nombre" class="form-control" v-model="persona.name" type="text" :loading="loading_search"></input>
                                    </div>
                                    <div class="col-md-2">
                                        <label  for="edad" >Edad</label><input name="edad" class="form-control" v-model="persona.edad" type="text" ></input>
                                    </div>
                                    <div class="col-md-3">
                                        <label  for="telfono">Tel:</label><input name="telefono" class="form-control" v-model="persona.telephone" type="text" ></input>
                                    </div>

                                </div>
                                <div class="row mt-4">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="dni">Precio</label>
                                            <el-input ref="precioBoleto" v-model="precio" type="number" id="precio-boleto" required></el-input>
                                        </div>

                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="dni">Asiento</label>
                                            <el-input :disabled="tipoVenta == 2 ? true : false" v-model="numeroAsiento" type="text" min="1"  id="numero-asiento"></el-input>
                                        </div>
                                    </div>
                                </div>
<!--                                <div v-if="transportePasaje" class="row justify-content-center">-->

<!--                                    <el-button type="primary" @click="viewComprobante" :style="{marginTop:'1.90rem'}">-->
<!--                                        Comprobante-->
<!--                                        <i class="fa fa-file-alt"></i>-->
<!--                                    </el-button>-->
<!--                                    <el-button type="danger" @click="anularBoleto" :style="{marginTop:'1.90rem'}">-->
<!--                                        Anular-->
<!--                                        <i class="fa fa-trash"></i>-->
<!--                                    </el-button>-->

<!--                                </div>-->

                                <div v-if="!transportePasaje && !isReserva" class="row mt-2">
                                    <div class="col-md-12">
                                        <el-collapse v-model="activePanel" accordion>
                                            <el-collapse-item name="2" >
                                                <template slot="title">
                                                    <i class="fa fa-plus text-info"></i> &nbsp;Menor de edad<i class="header-icon el-icon-information"></i>
                                                </template>
                                                <div  class="row mt-2">
                                                    <div class="col-12">
                                                        <div class="row mt-2">
                                                            <div class="col-12">
                                                                <el-button type="info"  icon="el-icon-plus" @click="agregarLineaNino">Agregar linea </el-button>
                                                                <table class="table table-bordered table-stripped">
                                                                    <thead>
                                                                    <th>Dni</th>
                                                                    <th>Nombre apellidos</th>
                                                                    <th>Edad</th>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr v-for="menor in menores">
                                                                            <td>
                                                                                <div class="form-group mb-2 mr-2">
                                                                                    <el-input v-model="menor.dni"></el-input>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="form-group mb-2 mr-2">
                                                                                    <el-input v-model="menor.nombres"></el-input>
                                                                                </div>
                                                                            </td>
                                                                            <td>
                                                                                <div class="form-group mb-2 mr-2">
                                                                                    <el-input v-model="menor.edad"></el-input>
                                                                                </div>
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </el-collapse-item>
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
                                            <el-collapse-item name="3" >
                                                <template slot="title">
                                                    <i class="fa fa-plus text-info"></i> &nbsp;Exceso de Equipaje<i class="header-icon el-icon-information"></i>
                                                </template>
                                                <div  class="row mt-2">
                                                    <div class="col-12">
                                                        <div class="row mt-2">
                                                            <div class="col-12">
                                                                <table class="table table-bordered table-stripped">
                                                                    <thead>
                                                                    <th>Descripción</th>
                                                                    <th>Importe</th>
                                                                    </thead>
                                                                    <tbody>
                                                                    <tr>
                                                                        <td>
                                                                            <div class="form-group mb-2 mr-2">
                                                                                <el-input v-model="sobre_equipajes.descripcion"></el-input>
                                                                            </div>
                                                                        </td>
                                                                        <td>
                                                                            <div class="form-group mb-2 mr-2">
                                                                                <el-input v-model="sobre_equipajes.importe"></el-input>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            </el-collapse-item>
                                        </el-collapse>

<!--                                        <h1>Total: {{ total_vista}}</h1>-->
                                    </div>
                                </div>
                            </div>
                            <div id="manifiesto" class="tab-pane">
                                <div>Vista de manifiestos</div>
                                <a href="/transportes/manifiestos" target="_blank">Abrir en otra ventana</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row mt-4">
            <div class="col-12 d-flex justify-content-center">
<!--                    <el-button v-if="transportePasaje && destino" :loading="loading" type="primary" @click="actualizarPasaje">Actualizar</el-button>-->
                    <el-button v-if="!transportePasaje" :loading="loading" type="primary" @click="guardarComprobante">Guardar</el-button>
                <p v-else class="bg-danger text-white p-2">Este asiento fué vendido en otro punto</p>
                </div>
            </div>
        </el-dialog>


        <sale-note-options
            :showDialog.sync="showDialogSaleNoteOptions"
            :recordId="documentId"
            :configuration="configuration"
            :showClose="true"
        >
        </sale-note-options>

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
        :buscar_destinatario="buscar_destinatario"
        :buscar_pasajero="buscar_pasajero"
        :input_person="input_person"
        :document_type_id="document.document_type_id"></person-form>



    </div>

</template>

<script>
import { exchangeRate } from '../../../../../../../resources/js/mixins/functions';
import DocumentOptions from "@views/documents/partials/options.vue";
//import SaleNoteOptions from "@views/sale_notes/partials/options.vue";
import SaleNoteOptions from '@views/sale_notes/partials/option_aqp.vue'
import PersonForm from "@views/persons/form2.vue";

export default {
    mixins: [exchangeRate],
    components:{
        DocumentOptions,
        PersonForm,
        SaleNoteOptions
    },
    props:{
        visible:{
            type:Boolean,
            default:false
        },
        document_type_03_filter:{
            type:Boolean,
            required:true,
        },
        asiento:{
            type:Object|null,
            default:null,
        },
        programacion: {
            type: Object|null,
            required: true,
            default: () => ({}),
        },
        estadosAsientos:{
            type:Array,
            required:true,
            default:() => []
        },
        fechaSalida: {
            type: String | null,
            required: true,
            default: '',
        },
        establishment:{
            type:Object,
            required:true
        },
        allSeries:{
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
        transportePasaje:{
            type:Object|null,
            default:() => null
        },
        tipoVenta:{
            type:Number,
            default:null
        },
        isCashOpen:{
            type:Boolean,
            required:true,
            default:false,
        },
        horaSalida:{
            type:String|null,
            required:true,
            default:null,
        },
        origen:{
            type:Object|null,
            required:true,
            default:null
        },
        destino:{
            type:Object|null,
            required:true,
            default:null


        },
        reloj:{
            type:String|null,
            required:true,
            default:null
        },
        fecha:{
            type:String|null,
            required:true,
            default:null
        },
        hora:{
            type:String|null,
            required:true,
            default:null
        }
    },
    mounted() {

    },
    created(){
        this.initDocument();
        setInterval(() => {
            this.document.date_of_issue= this.fecha
            this.document.time_of_issue= this.hora
            this.document.date_of_due= this.fecha
            this.document.delivery_date= this.fecha
        }, 5000)
        this.initForm();
        this.all_document_types = this.documentTypesInvoice;
        this.series  = this.allSeries;
        this.document.establishment_id = this.establishment.id;
        this.changeDocumentType();
        this.startConnectionQzTray()
    },
    watch:{
        precio:function(newVal){
            if(this.document.payments.length > 0){
                this.document.payments[0].payment = newVal ? newVal : 0;
            }
        },
        asiento(value){
            this.numeroAsiento = value ? value.numero_asiento : null;
        },
        tipoVenta(newVal){
            if(newVal == 1) {

                this.estadoAsiento = 2;
                this.tempEstadosAsientos = this.estadosAsientos.filter(  estado => estado.id != 3 )
            }
            else this.tempEstadosAsientos = this.estadosAsientos;
        },
        total_vista(){
            this.total_vista = this.precio+ this.sobre_equipajes.importe
        }
    },
    computed:{
        nameItem(){
            if(this.tipoVenta === 2) return `${this.programacion.origen.nombre}-${this.programacion.destino.nombre}`
            else if(this.tipoVenta === 1) return `${this.origen.nombre}-${this.destino.nombre}`;
        },
        isReserva(){
            return this.estadoAsiento === 3;
        },

        isAutoPrint: function () {

            if(this.configuration)
            {
                return this.configuration.auto_print
            }

            return false

        },
    },
    data(){
        return ({
            resources:"transportes",
            loading_search:false,
            dniPasajero:null,
            nombrePasajero:null,
            telPasajero:null,
            tabs:'venta',
            resource_documents: "documents",
            input_person:{},
            showDialogNewPerson:false,
            documentNewId: null,
            clientes:[],
            pasajeros:[],
            pasajero:null,
            loadingPasajero:false,
            loadingCliente:false,

            estadoAsiento:null,
            clienteId:null,
            pasajeroId:null,
            precio:null,
            numeroAsiento:null,
            buscar_destinatario:false,
            buscar_pasajero:false,
            loading:false,
            tempEstadosAsientos:this.estadosAsientos.filter(  estado => estado.id != 3 || estado.id != 4 ),
            //document
            documentId:null,
            sale_note_id:null,
            document: {
                payments: [],
            },
            producto:{},
            all_document_types: [],
            document_types: [],
            series:[],
            showDialogDocumentOptions:false,
            showDialogSaleNoteOptions: false,
            payment:{
                payment_method_type_id:null,
                payment_destination_id:null,
                reference:null,
                payment:null
            },
            is_contingency: 0,
            activePanel: 0,
            activeNames:[1],
            tempPasajeros:[],
            tempClientes:[],
            is_document_type_invoice: true,

            empresa: {
                id: null,
                country_id:"PE",
                type: "customers",
                identity_document_type_id: '6',
                number: '',
                name: null,
                address:null,
                condition:null,
                state:null,
                addresses: [],

                department_id: null,
                district_id: null,
                province_id: null,
                trade_name: null
            },
            persona: {
                id: null,
                country_id:"PE",
                type: "customers",
                identity_document_type_id: '1',
                edad: '',
                number: null,
                name: null,
                addresses: []
            },
            identity_document_types: [],
            menores:[
                {
                    dni: "",
                    nombres: "",
                    edad:null
                },
            ],
            sobre_equipajes: {
                    descripcion: "",
                    importe: 0
            },
            total_vista:0,
            transporte_configuration:[]
        });
    },
    methods:{
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
        modalPerson(buscar_pasajero){
            this.showDialogNewPerson=true;
            this.buscar_pasajero = buscar_pasajero

        },

        async onCreate(){
            await this.$http.get(`/persons/tables`)
                .then(response => {
                    this.identity_document_types = response.data.identity_document_types.filter(  doc => doc.description == "DNI" || doc.description == "CE" || doc.description == "Pasaporte" );
                })
            await this.$http.get(`/${this.resources}/configuration/record`)
                .then(response => {
                    this.transporte_configuration = response.data.data
                })

            this.estadoAsiento = 2;

            if( this.transporte_configuration.pasaje_afecto_igv) { //jala la configuración de afectacion de igv de la nueva opcion de configuracion des módulo transporte
                this.initProductoGrabado();
            }else{
                this.initProductoExonerado();
            }
            this.clickAddPayment();
            this.onCalculateTotals();
            const date = moment().format("YYYY-MM-DD");
            await this.searchExchangeRateByDate(date).then((res) => {
                this.document.exchange_rate_sale = res;
            });

            if(this.transportePasaje){
                this.pasajero = this.transportePasaje.pasajero || {};
                this.precio = this.transportePasaje.precio;
                this.pasajeroId = this.pasajero.id;
                this.clienteId = this.transportePasaje.cliente_id;
                this.estadoAsiento = this.transportePasaje.estado_asiento_id;
                this.documentId = this.transportePasaje.document_id;
                this.sale_note_id=this.transportePasaje.note_id;
                this.numeroAsiento = this.transportePasaje.numero_asiento;
            }

            this.document.document_type_id = (this.documentTypesInvoice.length > 0)?this.documentTypesInvoice[0].id:null;
            this.document.document_type_id = '03';
            this.changeDocumentType();
            this.filterSeries();
        },

        async guardarComprobante(){
            console.log('programa')
            console.log(this.programacion)

            let validator = this.validate();

            if(validator.fails){
                this.loading = false;
                return this.$message.info(validator.first);
            }

            if(!this.pasajeroId && !this.isReserva) {
                this.loading = true;

               await this.$http
                    .post("/persons", this.persona)
                    .then((response) => {

                        this.pasajeroId   = response.data.id
                        if(this.document.document_type_id == '03'){
                            this.clienteId   = response.data.id
                            this.document.customer_id=response.data.id
                        }

                    })
                    .finally(() => {
                        this.errors = {};
                        this.loading = false;
                    })
                    .catch((error) => {
                        console.log(error);
                        this.loading = false;
                    });

            }
            if(!this.clienteId && this.document.document_type_id == '01' && !this.isReserva) {
                this.loading = true;
                await this.$http
                    .post("/persons", this.empresa)
                    .then((response) => {

                        this.clienteId   = response.data.id
                        this.document.customer_id=response.data.id
                    })
                    .finally(() => {
                        this.errors = {};
                        this.loading = false;
                    })
                    .catch((error) => {
                        console.log(error);
                        this.loading = false;
                    });
            }
            else{
                console.log("Ningun contenido")
            }
            this.document.customer_id= (this.document.document_type_id ==='01') ? this.clienteId:this.pasajeroId
            this.saveDocument()
        },

        async saveDocument(){
            this.loading = true;
            let precio = parseFloat(this.precio);
            let cant = 1;
            let valorventa = parseFloat(precio/1.18);
            let igv = parseFloat(precio-valorventa);
            let total = parseFloat(cant*precio);

            if(this.isReserva) return this.guardarPasaje()

            if(this.transporte_configuration.pasaje_afecto_igv){// pasaje grabado con igv

               this.document.items.length=0;
               this.producto.input_unit_price_value=precio;
               this.producto.item.description = 'Pasaje -'+this.nameItem;
               this.producto.item.name = 'Pasaje -'+this.nameItem;
               this.producto.item.sale_unit_price = precio;//
               this.producto.item.unit_price=precio;
               this.producto.total=total;//
               this.producto.total_base_igv=valorventa*cant;//
               this.producto.total_value=valorventa*cant;//
               this.producto.unit_price=precio;//
               this.producto.unit_value=valorventa;//
                this.producto.total_igv= igv*cant;
                this.producto.total_taxes=igv*cant;

            }else{ //pasaje exonerado del igv
                this.document.items.length=0;
                this.producto.input_unit_price_value=precio;
                this.producto.item.description = 'Pasaje -'+this.nameItem;
                this.producto.item.name = 'Pasaje -'+this.nameItem;
                this.producto.item.sale_unit_price = precio;
                this.producto.item.unit_price=precio;
                this.producto.total=precio;
                this.producto.total_base_igv=precio;
                this.producto.total_value=precio;
                this.producto.unit_price=precio;
                this.producto.unit_value=precio;
            }
               const id = await this.createItem(this.producto.item);
               if(!id) return this.$message.error('Lo sentimos ha ocurrido un error');
               this.producto.item_id = this.producto.item.id = id;
               this.document.items.push(this.producto);

            let total_sobreequipaje = 0
                if(this.sobre_equipajes.importe > 0){

                    this.initProductoGrabado()
                    let precio = parseFloat(this.sobre_equipajes.importe);
                    let cant = 1;
                    let valorventa = parseFloat(precio/1.18);
                    let igv = parseFloat(precio-valorventa);
                    let total = parseFloat(cant*precio);

                    this.producto.input_unit_price_value=precio;
                    this.producto.cant=1;
                    this.producto.item.description = this.sobre_equipajes.descripcion;
                    this.producto.item.name = this.sobre_equipajes.descripcion;
                    this.producto.item.sale_unit_price =precio;
                    this.producto.total=total;
                    this.producto.total_base_igv=valorventa*cant;
                    this.producto.total_value=valorventa*cant;
                    this.producto.unit_price=precio;
                    this.producto.unit_value=valorventa;
                    this.producto.total_igv= igv*cant;
                    this.producto.total_taxes=igv*cant;

                    let id = await this.createItem(this.producto.item);
                    if(!id) return this.$message.error('Lo sentimos no se pudo agregar el producto');
                    this.producto.item_id = this.producto.item.id = id;

                    let p =  JSON.parse(JSON.stringify(this.producto));

                    this.document.items.push( p );

                    total_sobreequipaje = total
                }


               this.payment.payment= precio+total_sobreequipaje;
               this.document.payments.push(this.payment);

               this.onCalculateTotals();

               let validate_payment_destination = this.validatePaymentDestination();
               if (validate_payment_destination.error_by_item > 0) {
                   return this.$message.error("El destino del pago es obligatorio");
               }
               if (this.document.document_type_id === "nv") {
                   this.document.prefix = "NV";
                   this.resource_documents = "sale-notes";
               } else {
                   this.document.prefix = null;
                   this.resource_documents = "documents";
               }

               await this.$http
                   .post(`/${this.resource_documents}`, this.document)
                   .then(async (response) => {
                       if (response.data.success) {
                           this.documentId = response.data.data.id;
                           this.documentNewId = response.data.data.id;

                           if (this.document.document_type_id === "nv"){
                               this.form_cash_document.sale_note_id = response.data.data.id;
                               this.sale_note_id = response.data.data.id;
                           }
                           else{
                               this.form_cash_document.document_id = response.data.data.id;
                           }
                           await this.guardarPasaje();

                           this.autoPrintDocument()
                       } else {
                           this.$message.error(response.data.message);
                       }
                   })
                   .catch((error) => {
                       this.loading = false;
                       if (error.response) {
                           this.$message.error(error.response.data.message);
                       }

                   }).then(() => {
                       this.loading = false;
                   });

        },
        async guardarPasaje(){
            this.loading = true;
            let doc = null;
            let note = null;
            let client = null;
            if(!this.isReserva){
                doc = (this.document.document_type_id==='nv')? null: this.documentId;
                note = (this.document.document_type_id==='nv') ? this.sale_note_id : null
                client = (this.document.document_type_id==='03' || this.document.document_type_id==='nv') ?  this.pasajeroId: this.clienteId;

            }

            let data = {
                document_id: doc,
                note_id: note,
                cliente_id: this.estadoAsiento == 2 ? client: null,
                nombre_pasajero: this.nombrePasajero,
                pasajero_id: this.estadoAsiento == 2 ? this.pasajeroId : null,
                asiento_id:this.tipoVenta == 2 ? this.asiento.id : null,
                numero_asiento:this.numeroAsiento,
                estado_asiento_id:this.estadoAsiento,
                fecha_salida:this.fechaSalida,
                precio:this.precio,
                tipo_venta:this.tipoVenta, //venta asiento libre
                destino_id: this.destino.id,
                hora_salida: this.tipoVenta == 2 ?  this.programacion.hora_salida : this.horaSalida,
                programacion_id: this.tipoVenta == 2 ? this.programacion.id : null,
                ninios: this.menores,
                form_cash_document: this.form_cash_document,
                persona:this.persona,

                dniPasajero:this.dniPasajero,
                nombrePasajero:this.nombrePasajero,
                telefono: this.telPasajero ? this.telPasajero : this.persona.telephone


            };

            this.$http.post('/transportes/sales/realizar-venta-boleto',data)
            .then( ({data}) => {

                if(!data.success){
                    this.$emit('update:visible',false);
                    this.$emit('onUpdateItem');
                    return this.$message({
                        type: 'error',
                        message: data.message
                    });

                    this.limpiar_datos()
                }

                if (this.document.document_type_id === "nv") {
                    this.modalNote();
                } else {
                    if(!this.isReserva) this.$emit('onSuccessVenta',this.documentId);
                }


                this.limpiar_datos()

                this.$emit('onUpdateItem');
                this.$emit('notificationAll'); //sirve para notificar que se ha hecho una venta de un pasaje
                this.$message({
                    type: 'success',
                    message: data.message
                });

                this.$emit('update:visible',false);

                this.loading = false;

            }).catch( error => {
                this.limpiar_datos()
                 alert(error);
                this.axiosError(error);
                this.loading = false;
            }).finally(() => {
                this.loading = false;
            });
        },
        limpiar_datos(){
            this.estadoAsiento = 2;

            //this.precio = null;
            this.clienteId=null;
            this.pasajeroId = null;
            this.numeroAsiento = null;
            this.form_cash_document.document_id=null;
            this.form_cash_document.sale_note_id=null;
            this.document.document_type_id = '03';
            this.nombrePasajero = null;
            this.filterSeries();

            if( this.transporte_configuration.pasaje_afecto_igv) { //jala la configuración de afectacion de igv de la nueva opcion de configuracion des módulo transporte
                this.initProductoGrabado();
            }else{
                this.initProductoExonerado();
            }
            this.document.payments= [];

            this.empresa.id    = null
            this.empresa.number = null
            this.empresa.name  = null
            this.empresa.address= null
            this.empresa.condition= null
            this.empresa.state = null
            this.empresa.department_id= null
            this.empresa.district_id= null
            this.empresa.province_id= null
            this.empresa.trade_name= null
            //
            this.persona.id  = null
            this.persona.number = null
            this.persona.name = null
            this.persona.edad  = ''
            this.persona.telephone= ''
            this.menores = [
                {
                    dni: "",
                    nombres: "",
                    edad:null
                },
            ]

            this.sobre_equipajes = {
                descripcion: "",
                importe: 0
            }

            this.dniPasajero=null
            this.nombrePasajero=null
            this.telPasajero=null
        },
        async actualizarPasaje(){
            let data = {
                asiento_id:this.tipoVenta == 2 ? this.asiento.id : null,
                numero_asiento:this.numeroAsiento,
                programacion_id:this.tipoVenta == 2 ? this.programacion.id : null,
                fecha_salida:this.fechaSalida,
                origen_id:this.origen.id,
                destino_id:this.destino.id,
                precio:this.precio,
                hora_salida:this.horaSalida
            };
            this.loading = true;

            this.$http.put(`/transportes/sales/${this.transportePasaje.id}/actualizar-boleto`,data)
            .then( ({data}) => {
                this.loading = false;
                this.$emit('onUpdateItem');
                this.$message({
                    type: 'success',
                    message: data.message
                });
            }).catch( error => {
                this.axiosError(error);
            }).finally(() => {
                this.loading = false;
            });
        },
        //document and payment
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
                    description:null,
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
        initDocument() {
            this.document = {
                establishment_id:null,
                customer_id: null,
                customer: {},
                document_type_id: null,
                series_id: null,
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
        changeDocumentType() {
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
            }
        },
        cleanCustomer(){
            this.document.customer_id = null
            this.clienteId = null;
            this.tempPasajeros = []
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

            this.document.document_type_id = this.document_types.length > 0 ? this.document_types[0].id : null;
            this.changeDocumentType();
        },
        viewComprobante(){
            this.showDialogDocumentOptions = true;
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
                    this.tempClientes = _.filter(this.clientes, {'identity_document_type_id': '6'})
                } else {
                    this.tempClientes = _.filter(this.clientes, (c) => { return c.identity_document_type_id !== '6' })
                }
             this.tempPasajeros = _.filter(this.pasajeros, (c) => { return c.identity_document_type_id !== '6' })
            } else {
                this.tempPasajeros = [...this.pasajeros]
            }
        },
        anularBoleto(){
            this.$confirm(`¿Estás seguro de anular el boleto ?`, 'Atención', {
                confirmButtonText: 'Si, continuar',
                cancelButtonText: 'No, cerrar',
                type: 'warning'
            }).then(() => {

                setTimeout(() => {
                    this.$emit('anularBoleto',this.transportePasaje);
                }, 500);
            });


        },
        async searchProducto(q=''){

            this.loadingSProducto = true;
            const { data } = await this.$http.get(`/transportes/encomiendas/get-productos?search=${q}`);
            this.loadingSProducto = false;
            this.items = data;
        },
        async createItem(item){
            try{
                const { data } = await this.$http.post('/items',item);
                return data.id;

            }catch(error){
                return null;
            }
        },


        validate(){
            let valid = true;
            let errors = [];
            if(!this.isCashOpen) {
                valid = false;
                errors.push('La caja no esta abierta');
                // this.$message.info('La caja no esta abierta');
            }
            if(!this.destino){
                valid = false;
                errors.push('Debe seleccionar un destino');
            }
            if(this.isReserva){

                if(!this.nombrePasajero || this.nombrePasajero == ''){
                    valid = false;
                    //this.$refs.nombrePasajero.focus();
                    errors.push('Debe poner el nombre del pasajero');
                }
            }


            if(!this.programacion && !this.isReserva) {
                valid = false;
                errors.push('Debe seleccionar una programación.');
            }
            if(this.precio){
                    let p = parseFloat(this.precio);
                    if(p <= 0){
                        valid = false;
                        errors.push('El precio debe ser mayor a 0');
                    }
            }

            if(this.tipoVenta == 1){//venta libre

                if(this.destino && !this.horaSalida){
                    valid = false;
                    errors.push('Debe ingresar hora de salida');

                    let element = document.getElementById('hora-salida');
                    //element.focus();
                }

                if(!this.precio){
                    valid = false;
                    errors.push('Debe poner un precio');

                    let element = document.getElementById('precio-boleto');
                    //element.focus();
                }

                if(!this.clienteId && this.document.document_type_id=='01' && !this.isReserva){

                    valid = false;
                    errors.push('Debe seleccionar un cliente o pasajero');
                    let element = document.getElementById('cliente');
                    //element.focus();
                }

                if(!this.numeroAsiento){
                    valid = false;
                    errors.push('Debe ingresar un asiento');

                    let element = document.getElementById('numero-asiento');
                    //element.focus();
                }
            }

            else if(this.tipoVenta == 2){//venta con programación

                if(!this.programacion){
                    valid = false;
                    errors.push('Debe seleccionar una programación');
                }

                if(!this.precio){
                    valid = false;
                    errors.push('Debe poner un precio');

                    let element = document.getElementById('precio-boleto');
                    //element.focus();
                }
                if(!this.pasajeroId && this.document.document_type_id=='03' && !this.isReserva && !this.persona.name){

                    valid = false;
                    errors.push('Debe seleccionar un pasajero');
                    let element = document.getElementById('pasajero');
                    //element.focus();
                }
                if(!this.asiento) {
                    valid = false;
                    errors.push('Debe seleccionar un asiento');
                }
            }

            return {
                fails:!valid,
                errors:errors,
                first:errors.length > 0 ? errors[0] : null,
            }
        },
        async buscar_rapida_dni(){
            this.persona.name=""
            this.loading_search = true
            let response_local = await this.$http.get(`/transportes/encomiendas/get-pasajero/1/${this.persona.number}`)

            if(response_local.data.success){
                this.pasajeroId   = response_local.data.data.id

                if(this.document.document_type_id == '03'){
                    this.clienteId   = response_local.data.id
                    this.document.customer_id = response_local.data.data.id
                }

                this.persona.id  = response_local.data.data.id
                this.persona.number = response_local.data.data.number
                this.persona.name = response_local.data.data.name
                this.persona.edad = response_local.data.data.edad
                this.persona.telephone= response_local.data.data.telephone
            }else{
                this.persona.name = response_local.data.data.name
            }
            this.loading_search = false
        },
        async buscar_rapida_ruc(){

            this.loading_search = true
            let response_local = await this.$http.get(`/transportes/encomiendas/get-pasajero/6/${this.empresa.number}`)

            if(response_local.data.success){
                this.clienteId     = response_local.data.data.id
                this.document.customer_id=response_local.data.id
                this.empresa.id    = response_local.data.data.id
                this.empresa.name  = response_local.data.data.name
                this.empresa.address= response_local.data.data.address
                this.empresa.condition= response_local.data.data.condition
                this.empresa.state= response_local.data.data.state

                this.empresa.department_id= response_local.data.data.department_id
                this.empresa.district_id= response_local.data.data.district_id
                this.empresa.province_id= response_local.data.data.province_id
                this.empresa.trade_name= response_local.data.data.trade_name

            }else{
                this.empresa.name= response_local.data.data.name
            }
            this.loading_search = false
        },

        onClose(){
            this.$emit('update:visible',false);
            this.$emit('onCancel');
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

        changeIdentityDocType() {
            if(this.persona.identity_document_type_id ===4 ){ //cedula

            }
            else if(this.persona.identity_document_type_id ===7 ){ //pasaporte


            }
        },
        agregarLineaNino(){
            this.menores.push({dni: '', nombres: '' , edad: ''});
        },

        agregarLineaEquipaje(){
            this.sobre_equipajes.push({cant: '', descripcion: '' , precio_unitario: '', importe:''});
        },
        async buscar_rapida_dni_reserva(){

            this.nombrePasajero=""
            this.loading_search = true
            let response_local = await this.$http.get(`/transportes/encomiendas/get-pasajero/1/${this.dniPasajero}`)

            if(response_local.data.success){
                this.pasajeroId   = response_local.data.data.id

                this.nombrePasajero=response_local.data.data.name
                this.telPasajero=response_local.data.data.telephone

            }else{
                this.nombrePasajero = response_local.data.data.name
            }
            this.loading_search = false
        }
    }
}
</script>
