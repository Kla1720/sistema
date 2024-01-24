<template>
<div>
    <el-dialog :title="titleDialog" :visible="showDialogOptions" @open="create" width="30%" :close-on-click-modal="false" :close-on-press-escape="false" :show-close="false">
        <div class="row" v-show="!showGenerate">
            <div class="col text-center font-weight-bold">
                <p>Imprimir A4</p>
                <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickToPrint('a4')">
                    <i class="fa fa-file-alt"></i>
                </button>
            </div>
            <div class="col text-center font-weight-bold">
                <p>Imprimir A5</p>
                <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickToPrint('a5')">
                    <i class="fa fa-file-alt"></i>
                </button>
            </div>
            <div class="col text-center font-weight-bold">
                <p>Imprimir Ticket</p>
                <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickToPrint('ticket')">
                    <i class="fa fa-receipt"></i>
                </button>
            </div>
            <template v-if="configuration">
                <div class="col text-center font-weight-bold" v-if="configuration.ticket_58">
                    <p>Imprimir Ticket 58MM</p>
                    <button type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickToPrint('ticket_58')">
                        <i class="fa fa-receipt"></i>
                    </button>
                </div>
            </template>

        </div>

        <div class="row" v-show="!showGenerate">
            <div class="col-md-12">
                <el-input v-model="customer_email">
                    <el-button slot="append" icon="el-icon-message" @click="clickSendEmail" :loading="loading">Enviar</el-button>
                </el-input>
                <!--<small class="form-control-feedback" v-if="errors.customer_email" v-text="errors.customer_email[0]"></small> -->
            </div>
        </div>

        <div class="row" v-if="typeUser == 'admin'">
            <div class="col-md-9" v-show="!showGenerate">
                <div class="form-group">
                    <el-checkbox v-model="generate">Generar comprobante electrónico</el-checkbox>
                </div>
            </div>
        </div>
        <div class="row" v-if="generate">
            <div class="col-12 mt-4">
                <div class="form-group">
                    <label for="dni">
                        Cliente
                        <span class="text-danger">*</span>
                    </label>

                    <div class="row">
                        <div class="col-md-4">
                            <el-select v-model="tipo_doc" @change="cambiar_tipo_dosc_cliente">
                                <el-option value="1" label="DNI"></el-option>
                                <el-option value="6" label="RUC"></el-option>
                                <el-option value="0" label="CLIENTES VARIOS"></el-option>
                            </el-select>
                        </div>
                        <div class="col-md-8">
                            <input type="number" class="form-control" v-model="cliente_numero" placeholder="ingrese dni y presione enter"   v-on:keyup.enter="buscar_cliente" :maxlength="tipo_doc==1 ? 8 : 12 " oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"></input>
                            <span v-if="errors.cliente_numero" class="text-danger">{{ errors.cliente_numero[0] }}</span>
                        </div>
                    </div>


                </div>

                <div class="form-group">
                    <input type="text" class="form-control" v-model="cliente_nombre"></input>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="form-group" :class="{'has-danger': errors.document_type_id}">
                    <label class="control-label">Tipo comprobante</label>
                    <el-select v-model="document.document_type_id" @change="changeDocumentType" popper-class="el-select-document_type" dusk="document_type_id" class="border-left rounded-left border-info">
                        <el-option v-for="option in document_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                        <el-option key="nv" value="80" label="NOTA DE VENTA"></el-option>
                    </el-select>
                    <small class="form-control-feedback" v-if="errors.document_type_id" v-text="errors.document_type_id[0]"></small>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group" :class="{'has-danger': errors.series_id}">
                    <label class="control-label">Serie</label>
                    <el-select v-model="document.series_id">
                        <el-option v-for="option in series" :key="option.id" :value="option.id" :label="option.number"></el-option>
                    </el-select>
                    <small class="form-control-feedback" v-if="errors.series_id" v-text="errors.series_id[0]"></small>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                    <label class="control-label">Fecha de emisión</label>
                    <!-- <el-date-picker
              readonly
              v-model="document.date_of_issue"
              type="date"
              value-format="yyyy-MM-dd"
              :clearable="false"
              @change="changeDateOfIssue"
            ></el-date-picker> -->
                    <el-date-picker v-model="document.date_of_issue" type="date" value-format="yyyy-MM-dd" :clearable="false" @change="changeDateOfIssue"></el-date-picker>
                    <small class="form-control-feedback" v-if="errors.date_of_issue" v-text="errors.date_of_issue[0]"></small>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="form-group" :class="{'has-danger': errors.date_of_issue}">
                    <!--<label class="control-label">Fecha de emisión</label>-->
                    <label class="control-label">Fecha de vencimiento</label>
                    <el-date-picker v-model="document.date_of_due" type="date" value-format="yyyy-MM-dd" :clearable="false"></el-date-picker>
                    <small class="form-control-feedback" v-if="errors.date_of_due" v-text="errors.date_of_due[0]"></small>
                </div>
            </div>
            <br>
            <div class="col-lg-4">
                <div class="form-group" v-show="document.document_type_id == '03'">
                    <el-checkbox v-model="document.is_receivable" class="font-weight-bold">¿Es venta por cobrar?</el-checkbox>
                </div>
            </div> <br>

            <div class="col-lg-4 py-2 px-0">
                <div class="form-group">
                    <label class="control-label">Vendedor</label>
                    <el-select v-model="document.seller_id"
                               :disabled="typeUser == 'seller'">
                        <el-option v-for="option in sellers"
                                   :key="option.id"
                                   :label="option.name"
                                   :value="option.id"></el-option>
                    </el-select>
                </div>
            </div>

            <div class="col-lg-12" v-show="is_document_type_invoice">
                <table>
                    <thead>
                        <tr width="100%">
                            <th v-if="document.payments.length>0">M. Pago</th>
                            <th v-if="document.payments.length>0">Destino</th>
                            <th v-if="document.payments.length>0">Referencia</th>
                            <th v-if="document.payments.length>0">Monto</th>
                            <th width="15%">
                                <a href="#" @click.prevent="clickAddPayment" class="text-center font-weight-bold text-info">[+ Agregar]</a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(row, index) in document.payments" :key="index">
                            <td>
                                <div class="form-group mb-2 mr-2">
                                    <el-select v-model="row.payment_method_type_id">
                                        <el-option v-for="option in payment_method_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                    </el-select>
                                </div>
                            </td>
                            <td>
                                <div class="form-group mb-2 mr-2">
                                    <el-select v-model="row.payment_destination_id" filterable :disabled="row.payment_destination_disabled">
                                        <el-option v-for="option in payment_destinations" :key="option.id" :value="option.id" :label="option.description"></el-option>
                                    </el-select>
                                </div>
                            </td>
                            <td>
                                <div class="form-group mb-2 mr-2">
                                    <el-input v-model="row.reference"></el-input>
                                </div>
                            </td>
                            <td>
                                <div class="form-group mb-2 mr-2">
                                    <el-input v-model="row.payment"></el-input>
                                </div>
                            </td>
                            <td class="series-table-actions text-center">
                                <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickCancel(index)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                            <br />
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <span slot="footer" class="dialog-footer">
            <template v-if="showClose">
                <el-button @click="clickClose">Cerrar</el-button>
                <el-button class="submit" type="primary" @click="submit" :loading="loading_submit" v-if="generate">Generar</el-button>
            </template>
            <template v-else>
                <el-button class="submit" type="primary" plain @click="submit" :loading="loading_submit" v-if="generate">Generar comprobante</el-button>
                <el-button @click="clickFinalize" v-else>Ir al listado</el-button>
                <el-button type="primary" @click="clickNewOrderNote">Nuevo pedido</el-button>
            </template>
        </span>
    </el-dialog>

    <document-options :showDialog.sync="showDialogDocumentOptions" :recordId="documentNewId" :isContingency="false" :showClose="true" :configuration="configuration"></document-options>

    <sale-note-options :showDialog.sync="showDialogSaleNoteOptions" :recordId="documentNewId" :showClose="true" :configuration="configuration"></sale-note-options>
</div>
</template>

<script>
import DocumentOptions from "@views/documents/partials/options.vue";
import SaleNoteOptions from '@views/sale_notes/partials/option_aqp.vue'
import {exchangeRate, functions} from '@mixins/functions'

export default {
    components: {
        DocumentOptions,
        SaleNoteOptions
    },
    mixins: [functions, exchangeRate],
    props: ["showDialogOptions", "recordId", "showClose", "showGenerate", "type", "id_user2","typeUser", "configuration","itemaqp","mesa_id","mesaIsActivo"],
    watch:{
        cliente_numero(){
            if( (this.tipo_doc == "1" || this.tipo_doc == "0") && this.cliente_numero.length >= 8){
                this.buscar_cliente()
            }else if(this.tipo_doc == "6" && this.cliente_numero.length >= 11){
                this.buscar_cliente()
            }
        }
    },
    computed:{
        isAutoPrint: function () {

            if(this.configuration)
            {
                return this.configuration.auto_print
            }

            return false

        },
    },
    data() {
        return {
            customer_email: "",
            titleDialog: "Finalizando venta...",
            loading: false,
            resource: "order-notes",
            resource_documents: "documents",
            errors: {},
            form: {},
            document: {},
            document_types: [],
            all_document_types: [],
            all_series: [],
            series: [],
            customers: [],
            generate: false,
            loading_submit: false,
            showDialogDocumentOptions: false,
            showDialogSaleNoteOptions: false,
            documentNewId: null,
            is_document_type_invoice: true,
            loading_search: false,
            payment_destinations: [],
            form_cash_document: {},
            payment_method_types: [],
            producto: [],
            sellers: [],
            tipo_doc:'1',
            cliente_numero:null,
            cliente_nombre:'',
            percentage_igv: null
        };
    },

    methods: {
        clickCancel(index) {
            this.document.payments.splice(index, 1);
        },
        clickAddPayment() {
            let payment = (this.document.payments.length == 0) ? this.document.total : 0
            const paymentDestinationId = this.payment_destinations.length > 0 ? this.payment_destinations[0].id : null;

            this.document.payments.push({
                id: null,
                document_id: null,
                date_of_payment: moment().format("YYYY-MM-DD"),
                payment_method_type_id: '01',
                payment_destination_id: paymentDestinationId,
                reference: null,
                payment: payment
            });
        },
        initForm() {
            this.tipo_doc='1',
            this.generate = this.showGenerate ? true : false,
            this.errors = {};
            this.form = {
                id: null,
                external_id: null,
                identifier: null,
                date_of_issue: null,
                order_note: null
            };

            this.form_cash_document = {
                document_id: null,
                sale_note_id: null
            }
            this.cliente_numero=""
            this.cliente_nombre=""

        },
        getCustomer() {
            this.$http
                .get(
                    `/${this.resource_documents}/search/customer/${this.form.order_note.customer_id}`
                )
                .then(response => {
                    this.customers = response.data.customers;
                    this.document.customer_id = this.form.order_note.customer_id;
                    this.changeCustomer();
                });
        },
        changeCustomer() {
            this.validateIdentityDocumentType();
        },
        initDocument() {
            this.document = {
                document_type_id: "03",
                series_id: null,
                establishment_id: null,
                number: "#",
                date_of_issue: moment().format("YYYY-MM-DD"),
                time_of_issue: null,
                customer_id: null,
                currency_type_id: null,
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
                total_igv_free: 0,
                total_igv: 0,
                total_base_isc: 0,
                total_isc: 0,
                total_base_other_taxes: 0,
                total_other_taxes: 0,
                total_taxes: 0,
                total_value: 0,
                total: 0,
                operation_type_id: null,
                date_of_due: moment().format("YYYY-MM-DD"),
                items: [],
                charges: [],
                discounts: [],
                attributes: [],
                guides: [],
                additional_information: null,
                actions: {
                    format_pdf: "a4"
                },
                order_note_id: null,
                is_receivable: false,
                payments: [],
                hotel: {},
                seller_id: 0,
            };
        },
        async changeDateOfIssue() {

            await this.searchExchangeRateByDate(this.document.date_of_issue).then(response => {
                this.document.exchange_rate_sale = response
            });
            //await this.getPercentageIgv2();
            //this.changeCurrencyType();
        },
        async getPercentageIgv2() {

            await this.$http.post(`/store/get_igv`, {
                'establishment_id': this.document.establishment_id,
                'date': this.document.date_of_issue
            })
                .then(response => {
                    this.percentage_igv = response.data;
                });
        },

        resetDocument() {
            this.generate = this.showGenerate ? true : false;
            this.initDocument();
            this.document.document_type_id = this.document_types.length > 0 ? this.document_types[0].id : null;
            this.changeDocumentType();
        },
        validatePaymentDestination() {

            let error_by_item = 0

            this.document.payments.forEach((item) => {
                if (item.payment_destination_id == null) error_by_item++;
            })

            return {
                error_by_item: error_by_item,
            }

        },
        async submit() {
            if (this.document.customer_id==null) {
                return this.$message.error('El Cliente es obligatorio');
            }
            await this.assignDocument();

            let validate_payment_destination = await this.validatePaymentDestination()

            if (validate_payment_destination.error_by_item > 0) {
                return this.$message.error('El destino del pago es obligatorio');
            }

            this.loading_submit = true;
            if (this.document.document_type_id === "80") {
                this.document.prefix = "NV";
                this.resource_documents = "sale-notes";
            } else {
                this.document.prefix = null;
                this.resource_documents = "documents";
            }

            console.log(this.resource_documents)

            this.$http
                .post(`/${this.resource_documents}`, this.document)
                .then(response => {
                    if (response.data.success) {
                        this.documentNewId = response.data.data.id;
                        // console.log(this.document.document_type_id)
                        if (this.document.document_type_id === "80") {//nota de venta
                            this.form_cash_document.sale_note_id = response.data.data.id;
                            this.showDialogSaleNoteOptions = true;
                        } else {
                            this.form_cash_document.document_id = response.data.data.id;
                            this.showDialogDocumentOptions = true;
                        }
                        this.saveCashDocument();

                        //this.autoPrintDocument()

                        if(this.recordId>0 && (this.documentNewId>0)){
                            this.updatePedidoDocument(this.recordId,this.form_cash_document.document_id,this.form_cash_document.sale_note_id)
                            this.$emit('update:recordId',0);
                            this.$emit('update:mesaIsActivo',false);

                            this.initForm()
                            this.initDocument()
                            this.$eventHub.$emit('onLimPiarDatos')
                            this.$eventHub.$emit('handleClickNivel')
                            this.$emit('update:showDialogOptions',false);
                            this.$emit('updateTables'); // update status table
                        }


                        this.loading_submit = false;
                    } else {
                        console.log('ocurrió algun error en else')
                        this.loading_submit = false;
                        //this.clickClose()
                    }
                })
                .catch(error => {
                    // if (error.response.status === 422) {
                    //     this.errors = error.response.data;
                    // } else {
                        //this.$message.error(error.response.data.message);

                    console.log('ocurrió algun error cash')
                    //this.clickClose()
                    // }
                })
                .then(() => {
                    this.loading_submit = false;
                });
        },
        saveCashDocument() {
            this.$http.post(`/cash/cash_document`, this.form_cash_document)
                .then(response => {
                    if (response.data.success) {
                        // console.log(response)
                    } else {
                        this.$message.error(response.data.message);
                    }
                })
                .catch(error => {
                    console.log(error);
                })
        },
        assignDocument() {

            this.document.establishment_id = (this.establishments.length > 0) ? this.establishments[0].id : null;
            this.document.time_of_issue = moment().format("HH:mm:ss");
            this.document.purchase_order = null;
            this.document.operation_type_id = "0101";
            this.document.charges = {};
            this.document.discounts = {};
            this.document.attributes = [];
            this.document.guides = [];
            this.document.additional_information = null;
            this.document.actions = {
                format_pdf: "a4"
            };
            this.document.order_note_id = null;
        },
        async create() {
            this.initForm();
            this.initDocument();
            await this.$http.get(`/documents/tables`)
                .then(response => {
                    this.document_types = response.data.document_types_invoice;
                    this.document_types_guide = response.data.document_types_guide;
                    this.currency_types = response.data.currency_types
                    this.business_turns = response.data.business_turns
                    this.establishments = response.data.establishments
                    this.operation_types = response.data.operation_types
                    this.all_series = response.data.series;
                    this.sellers = response.data.sellers
                    this.discount_types = response.data.discount_types
                    this.charges_types = response.data.charges_types
                    this.payment_method_types = response.data.payment_method_types
                    this.enabled_discount_global = response.data.enabled_discount_global
                    this.company = response.data.company;
                    this.user = response.data.user;
                    this.document_type_03_filter = response.data.document_type_03_filter;
                    this.select_first_document_type_03 = response.data.select_first_document_type_03
                    this.document.establishment_id = (this.establishments.length > 0) ? this.establishments[0].id : null;
                    this.document.document_type_id = (this.document_types.length > 0) ? this.document_types[0].id : null;
                    this.document.operation_type_id = (this.operation_types.length > 0) ? this.operation_types[0].id : null;
                    this.document.seller_id = (this.sellers.length > 0) ? this.id_user2.id : null;
                    this.affectation_igv_types = response.data.affectation_igv_types

                    this.is_client = response.data.is_client;

                    this.payment_destinations = response.data.payment_destinations
                    this.payment_conditions = response.data.payment_conditions;
                    this.document.currency_type_id = "PEN";
                })
            await this.getPercentageIgv2()
            //this.validateIdentityDocumentType()
            // if(this.recordId>0)
            // {
                this.prepararItems()
            // }
            this.clickAddPayment()
            this.changeDateOfIssue()

            this.document.document_type_id = '03';
            this.changeDocumentType()

            //this.startConnectionQzTray()

        },
        prepararItems(){
            this.itemaqp.forEach(element => {
                const percentage_igv = this.percentage_igv;
                const percentage_igv_g = parseFloat(1+ percentage_igv);
                let precio = parseFloat(element.precio);
                let cant = parseFloat(element.cantidad);
                let valorventa = parseFloat(precio/percentage_igv_g);
                let igv = parseFloat(precio-valorventa);
                let total = parseFloat(cant*precio);
                //console.log(element)

                if(element.item.sale_affectation_igv_type_id==20){ // esto agrega producto exonerado
                    this.initProductoExonerado()
                    this.producto.input_unit_price_value=precio;
                    this.producto.quantity=cant;
                    this.producto.item.name = element.item.description;
                    this.producto.item.sale_unit_price = precio;
                    this.producto.item.unit_price=precio;
                    this.producto.total=total;
                    this.producto.total_base_igv=precio*cant;
                    this.producto.total_value=precio*cant;
                    this.producto.unit_price=precio;
                    this.producto.unit_value=precio*cant;
                    this.producto.item_id = element.item.id
                    this.producto.percentage_igv=percentage_igv*100
                }
                else if(element.item.sale_affectation_igv_type_id==10){ // esto es por defecto encomienda grabada
                    this.initProductoGrabado()
                    this.producto.input_unit_price_value=precio;
                    this.producto.quantity=cant;
                    this.producto.item.name = element.item.description;
                    this.producto.item.sale_unit_price =precio;
                    this.producto.total=total;
                    this.producto.total_base_igv=valorventa*cant;
                    this.producto.total_value=valorventa*cant;
                    this.producto.unit_price=precio;
                    this.producto.unit_value=valorventa;
                    this.producto.total_igv= igv*cant;
                    this.producto.total_taxes=igv*cant;
                    this.producto.item_id = element.item.id
                    this.producto.percentage_igv=percentage_igv*100
                }
                let p = JSON.parse(JSON.stringify(this.producto));
                this.document.items.push(p)
                }
            );

            this.onCalculateTotals()
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
        changeDocumentType() {
            // this.filterSeries()
            // this.document.is_receivable = false;
            // this.series = [];
            // if (this.document.document_type_id !== "nv") {
            //     this.filterSeries();
            //     this.is_document_type_invoice = true;
            // } else {
            //     this.is_document_type_invoice = false;
            // }


            this.series = [];
            if (this.document.document_type_id !== "nv") {
                this.filterSeries();
                this.is_document_type_invoice = true;
            } else {
                this.series = _.filter(this.all_series, {
                    document_type_id: "80",
                });
                this.document.series_id =
                    this.series.length > 0 ? this.series[0].id : null;

                this.is_document_type_invoice = false;
            }

        },
        async validateIdentityDocumentType() {
            let identity_document_types = ["0", "1"];
            /*
            0		Doc.trib.no.dom.sin.ruc
            1		DNI
            */
            let customer = _.find(this.customers, {
                id: this.document.customer_id
            });

            if (identity_document_types.includes(customer.identity_document_type_id)) {
                this.document_types = _.filter(this.all_document_types,
                    _.overSome(
                        [
                            // {'id': '01'}, // Factura
                            {
                                'id': '03'
                            }, // Boleta
                            ['id', '80'] // Nota de venta
                        ]
                    )
                );
                // this.document_types = _.filter(this.all_document_types, {id: "03"});
            } else {
                this.document_types = this.all_document_types;
            }

            this.document.document_type_id = this.document_types.length > 0 ? this.document_types[0].id : null;
            await this.changeDocumentType();
        },
        filterSeries() {
            this.document.series_id = null;
            this.series = _.filter(this.all_series, {
                document_type_id: this.document.document_type_id
            });
            this.document.series_id =
                this.series.length > 0 ? this.series[0].id : null;
        },
        clickFinalize() {
            location.href = `/${this.resource}`;
        },
        clickNewOrderNote() {
            this.clickClose();
        },
        clickClose() {
            this.$emit("update:showDialogOptions", false);
            this.initForm();
            this.resetDocument();
        },
        clickToPrint(format) {
            window.open(
                `/${this.resource}/print/${this.form.external_id}/${format}`,
                "_blank"
            );
        },
        clickSendEmail() {
            this.loading = true;
            this.$http
                .post(`/${this.resource}/email`, {
                    customer_email: this.customer_email,
                    id: this.form.id,
                    customer_id: this.form.order_note.customer_id
                })
                .then(response => {
                    if (response.data.success) {
                        this.$message.success("El correo fue enviado satisfactoriamente");
                    } else {
                        this.$message.error("Error al enviar el correo");
                    }
                })
                .catch(error => {
                    this.$message.error("Error al enviar el correo");
                })
                .then(() => {
                    this.loading = false;
                });
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
        async buscar_cliente(){
            let type = this.tipo_doc
            this.loading = true

            let response_local =  await this.$http.get(`/transportes/encomiendas/get-pasajero/${type}/${this.cliente_numero}`)
            if(response_local.data.success){
                this.document.customer_id = response_local.data.data.id
                this.cliente_nombre=response_local.data.data.name
            }else{
                this.cliente_nombre = response_local.data.data.name
            }
            this.loading = false

        },

        updatePedidoDocument(pedido_id,document_id,note_id){
            this.loading = true;
            let data = {
                pedido_id : pedido_id,
                document_id:document_id,
                note_id:note_id,
                mesa_id:this.mesa_id
            }
            this.$http
                .put(`/restaurant/cash/sales/updatePedidoDocument`, data)
                .then((response) => {
                    this.$message.success("Documento actualizado correctamente.");
                    this.$eventHub.$emit('update:reloadData2')
                })
                .finally(() => {
                    this.loading = false;
                    this.errors = {};
                })
                .catch((error) => {
                    this.axiosError(error);
                });
        },
        limitText() {
            if (this.inputValue.length == 8) {
                this.buscar_cliente()
            }
        },
        cambiar_tipo_dosc_cliente(){
            if(this.tipo_doc == "0"){
                this.cliente_numero="99999999"
            }
        },
        startConnectionQzTray(){

            if (!qz.websocket.isActive() && this.isAutoPrint)
            {
                startConnection({host: "192.168.1.200"});
            }

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
    }
};
</script>
