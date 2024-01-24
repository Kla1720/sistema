<template>
    <el-dialog :close-on-click-modal="false"
               :close-on-press-escape="false"
               :show-close="false"
               :title="titleDialog"
               :visible="showDialog"
               append-to-body
               width="30%"
               @open="create">
        <!--
        <Keypress
            key-event="keyup"
            @success="checkKey"
        />
        -->
        <Keypress key-event="keyup" :multiple-keys="multiple" @success="checkKeyWithAlt"/>
        <div v-loading="loading">
            <div v-if="form.response_message"
                 class="row mb-4">
                <div class="col-md-12">
                    <el-alert
                        :title="form.response_message"
                        :type="form.response_type"
                        show-icon>
                    </el-alert>
                </div>
            </div>

            <div class="row">

                <div class="col text-center font-weight-bold mt-3">
                    <button class="btn btn-lg btn-info waves-effect waves-light"
                            type="button"
                            @click="clickPrint('a4')">
                        <i class="fa fa-file-alt"></i>
                    </button>
                    <p>Imprimir A4</p>
                </div>

                <div v-if="ShowTicket80"
                    class="col text-center font-weight-bold mt-3">

<!--                    <button v-if="this.configuration.print_silent"-->
<!--                            type="button" class="btn btn-lg btn-info waves-effect waves-light"-->
<!--                            @click="clickPrintSilent('ticket')">-->
<!--                        <i class="fa fa-receipt"></i>-->
<!--                    </button>-->

                    <button class="btn btn-lg btn-info waves-effect waves-light"
                            type="button"
                            @click="clickPrint('ticket')">
                        <i class="fa fa-receipt"></i>
                    </button>
                    <p>Imprimir Ticket 80MM</p>
                </div>

                <div v-if="ShowTicket58"
                     class="col text-center font-weight-bold mt-3">
<!--                    <button v-if="this.configuration.print_silent"-->
<!--                            type="button" class="btn btn-lg btn-info waves-effect waves-light"-->
<!--                            @click="clickPrintSilent('ticket_58')">-->
<!--                        <i class="fa fa-receipt"></i>-->
<!--                    </button>-->
                    <button class="btn btn-lg btn-info waves-effect waves-light"
                            type="button"
                            @click="clickPrint('ticket_58')">
                        <i class="fa fa-receipt"></i>
                    </button>
                    <p>Imprimir Ticket 58MM</p>
                </div>

                <div v-if="ShowTicket50"
                    class="col text-center font-weight-bold mt-3">

<!--                    <button v-if="this.configuration.print_silent" type="button" class="btn btn-lg btn-info waves-effect waves-light" @click="clickPrintSilent('ticket_58')">-->
<!--                        <i class="fa fa-receipt"></i>-->
<!--                    </button>-->

                    <el-popover
                        placement="top-start"
                        :open-delay="1000"
                        width="145"
                        trigger="hover"
                        content="Presiona ALT + P">
                        <el-button slot="reference"
                                   class="btn btn-lg btn-info waves-effect waves-light"
                                   type="button"
                                   @click="clickPrint('ticket_50')">
                            <i class="fa fa-receipt"></i>
                        </el-button>
                    </el-popover>
                    <p>Imprimir Ticket 50MM</p>
                </div>

                <div class="col text-center font-weight-bold mt-3">

                    <button class="btn btn-lg btn-info waves-effect waves-light"
                            type="button"
                            @click="clickPrint('a5')">
                        <i class="fa fa-file-alt"></i>
                    </button>
                    <p>Imprimir A5</p>
                </div>
            </div>
            <div class="row">
                <div v-if="form.image_detraction"
                     class="col-lg-12 col-md-12 col-sm-12 text-center font-weight-bold mt-3">
                    <a :href="`${this.form.image_detraction}`"
                       class="text-center font-weight-bold text-dark"
                       download>Descargar constancia de pago - detracción</a>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <el-input v-model="form.customer_email">
                        <el-button slot="append"
                                   :loading="loading"
                                   icon="el-icon-message"
                                   @click="clickSendEmail">Enviar
                        </el-button>
                    </el-input>
                    <small v-if="errors.customer_email"
                           class="form-control-feedback"
                           v-text="errors.customer_email[0]"></small>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <el-input v-model="form.customer_telephone">
                        <template slot="prepend">+51</template>
                        <el-button slot="append"
                                   @click="clickSendWhatsapp">Enviar
                            <el-tooltip class="item"
                                        content="Se recomienta tener abierta la sesión de Whatsapp web"
                                        effect="dark"
                                        placement="top-start">
                                <i class="fab fa-whatsapp"></i>
                            </el-tooltip>
                        </el-button>
                    </el-input>
                    <small v-if="errors.customer_telephone"
                           class="form-control-feedback"
                           v-text="errors.customer_telephone[0]"></small>
                </div>
            </div>
        </div>
        <span slot="footer"
              class="dialog-footer">
            <template v-if="showClose">
                <el-button @click="clickClose">Cerrar</el-button>
            </template>
            <template v-else>
                <el-button class="list"
                           @click="clickFinalize">Ir al listado</el-button>


                <el-popover
                    :open-delay="1000"
                    placement="top-start"
                    width="145"
                    trigger="hover"
                    content="Presiona ALT + N">
                            <el-button slot="reference"
                                       v-if="!isUpdate"
                                       type="primary"
                                       @click="clickNewDocument"
                            >
                                Nuevo comprobante
                            </el-button>
                </el-popover>
            </template>
        </span>
    </el-dialog>
</template>

<script>
import {mapState, mapActions} from "vuex/dist/vuex.mjs";
import Keypress from "vue-keypress";

export default {
    props: ['showDialog', 'recordId', 'showClose', 'isContingency', 'generatDispatch', 'dispatchId', 'isUpdate', 'configuration', 'table'],
    components: {
        Keypress
    },
    data() {
        return {
            titleDialog: null,
            loading: false,
            resource: 'sale-notes',
            resource_documents: 'documents',
            errors: {},
            form: {},
            multiple: [
                {
                    keyCode: 78, // N
                    modifiers: ['altKey'],
                    preventDefault: true,
                },
                {
                    keyCode: 80, // P
                    modifiers: ['altKey'],
                    preventDefault: true,
                },
            ],
            company: {},
            locked_emission: {},
            // config:{}
        }
    },
    created() {
        this.loadConfiguration(this.$store)
        this.$store.commit('setConfiguration', this.configuration)

    },
    mounted() {
        this.initForm()
    },
    computed: {
        ...mapState([
            'config',
        ]),
        ShowTicket58: function () {
            if (this.config === undefined) return false;
            if (this.config == null) return false;
            if (this.config.show_ticket_58 === undefined) return false;
            if (this.config.show_ticket_58 == null) return false;
            if (
                this.config.show_ticket_58 !== undefined &&
                this.config.show_ticket_58 !== null) {
                return this.config.show_ticket_58;
            }
            return false;
        },
        ShowTicket80: function () {
            if (this.config === undefined) return false;
            if (this.config == null) return false;
            if (this.config.show_ticket_80 === undefined) return false;
            if (this.config.show_ticket_80 == null) return false;
            if (
                this.config.show_ticket_80 !== undefined &&
                this.config.show_ticket_80 !== null) {
                return this.config.show_ticket_80;
            }
            return false;
        },
        ShowTicket50: function () {
            if (this.config === undefined) return false;
            if (this.config == null) return false;
            if (this.config.show_ticket_50 === undefined) return false;
            if (this.config.show_ticket_50 == null) return false;
            if (
                this.config.show_ticket_50 !== undefined &&
                this.config.show_ticket_50 !== null) {
                return this.config.show_ticket_50;
            }
            return false;
        }
    },
    methods: {
        ...mapActions(['loadConfiguration']),
        clickSendWhatsapp() {

            if (!this.form.customer_telephone) {
                return this.$message.error('El número es obligatorio')
            }

            window.open(`https://wa.me/51${this.form.customer_telephone}?text=${this.form.message_text}`, '_blank');

        },
        initForm() {
            this.errors = {};
            this.form = {
                id: null,
                external_id: null,
                identifier: null,
                date_of_issue:null,
                print_ticket: null,
                print_ticket_58: null,
                print_a4: null,
                print_a5: null,
                series:null,
                number:null,
            };

            this.company = {
                soap_type_id: null,
            }
        },
        async create() {

            await this.getCompany()
            await this.getRecord()

            //this.loading = true;
            // await this.$http.get(`/${this.resource}/locked_emission`).then(response => {
            //     this.locked_emission = response.data
            // }).finally(() => this.loading = false);
        },
        async getCompany() {
            this.loading = true;
            await this.$http.get(`/companies/record`)
                .then(response => {
                    if (response.data !== '') {
                        this.company = response.data.data
                    }
                }).finally(() => this.loading = false);
        },
        async getRecord() {
            this.loading = true;

            await this.$http.get(`/${this.resource}/record/${this.recordId}`).then(response => {
                this.form = response.data.data;
                this.titleDialog = `Nota de venta registrada:  ${this.form.serie}-${this.form.number}`

            }).finally(() => {
                this.loading = false
            });

        },
        clickPrint(format) {
            window.open(`/${this.resource}/print/${this.form.external_id}/${format}`, '_blank');
        },
        clickPrintSilent(format){
            const urlPdf = `${window.location.origin}/print/document/${this.form.external_id}/${format}`;
            if(localStorage.printer_silent==""){
                this.$message.error("Configure el nombre de la impresora corréctamente en Configuración, avanzado, Imprimir Ticket en forma silenciosa...");
                return null;
            }
            const nombreImpresora = localStorage.printer_silent;
            const url = `http://localhost:8080/url?urlPdf=${urlPdf}&impresora=${nombreImpresora}`;
            // Elemento DOM, solo es para depurar
            const $estado = "Imprimiendo...";

            fetch(url)
                .then(respuesta => {
                    // Si la respuesta es OK, entonces todo fue bien
                    if (respuesta.status === 200) {
                        this.$message.success("Se imprimió...")
                    } else {
                        // Si no, decodificamos el mensaje para ver el error
                        respuesta.json()
                            .then(mensaje => {
                                this.$message.error("Error imprimiendo: " + mensaje);
                            });
                    }
                });
        },
        clickDownloadImage() {
            window.open(`${this.form.image_detraction}`, '_blank');
        },
        clickDownload(format) {
            window.open(`${this.form.download_pdf}/${format}`, '_blank');
        },
        clickSendEmail() {
            this.loading = true
            this.$http.post(`/${this.resource}/email`, {
                customer_email: this.form.customer_email,
                id: this.form.id
            })
                .then(response => {
                    if (response.data.success) {
                        this.$message.success('El correo fue enviado satisfactoriamente')
                    } else {
                        this.$message.error('Error al enviar el correo')
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors
                    } else {
                        this.$message.error(error.response.data.message)
                    }
                })
                .then(() => {
                    this.loading = false
                })
        },
        clickFinalize() {
            if(this.table) {
                location.href = `/${this.table}`;
            } else {
                location.href = (this.isContingency) ? `/contingencies` : `/${this.resource}`
            }
        },
        clickNewDocument() {
            if(this.table) {
                location.href = `/${this.table}/create`;
            } else {
                this.clickClose();
            }
        },
        clickClose() {
            this.$emit('update:showDialog', false)
            this.initForm()
        },
        checkKey(e) {
            let code = e.event.code;
        },
        checkKeyWithAlt(e) {
            let code = e.event.code;
            if (
                // this.showDialogOptions === true &&
                code === 'KeyN'
            ) {
                this.clickClose()
            }
            if (code === 'KeyP') {
                this.clickPrint('ticket_50'); // Imprime ticket 50 con letra P
            }

        },
    }
}
</script>
