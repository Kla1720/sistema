<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Pedidos pendientes</span> </li>
            </ol>
        </div>
        <div class="card mb-0" v-loading="loading_submit">
            <div class="card-body ">
                <data-table :resource="resource" :typeUser="typeUser">
                    <tr slot="heading">
                        <th>#</th>
                        <th class="text-center">Usuario</th>
                        <th class="text-center">F. Emisión</th>
                        <th class="text-center">Numero</th>
                        <th>Cliente</th>
                        <th class="text-center">Estado</th>
                        <th class="text-center">Enviar</th>
                    <tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td class="text-center">{{ row.user_name }}</td>
                        <td class="text-center">{{ row.date_of_issue }}</td>
                    <td class="text-center">{{ row.identifier }}</td>
                        <td>{{ row.customer_name }}<br/><small v-text="row.customer_number"></small></td>
                        <td class="text-center">
                            {{row.state_description}}
                        </td>

                        <td class="text-center">
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info"
                                    @click.prevent="clickOptionsPdf(row.id)">Ver PDF</button>
                            <button v-if="row.state_type_id != '11' && typeUser == 'admin'"  type="button" class="btn waves-effect waves-light btn-xs btn-info"
                                    @click.prevent="clickOptions(row.id)" >Generar comprobante</button>

                            <a v-if="row.documents.length == 0 && row.state_type_id != '11'" :href="`/order-notes/edit/${row.id}`" type="button" class="btn waves-effect waves-light btn-xs btn-info">Editar</a>
                            <a :href="`/dispatches/create/${row.id}/on`" class="btn waves-effect waves-light btn-xs btn-warning m-1__2">Guía</a>

                        </td>
                    </tr>
                </data-table>
            </div>
            <quotation-options :showDialog.sync="showDialogOptions"
                               :recordId="recordId"
                               :showGenerate="true"
                               :configuration="configuration"
                               :showClose="true"></quotation-options>

            <quotation-options-pdf :showDialog.sync="showDialogOptionsPdf"
                                   :recordId="recordId"
                                   :configuration="configuration"
                                   :showClose="true"></quotation-options-pdf>
        </div>
    </div>
</template>

<script>
import QuotationOptions from './partials/options.vue'
import QuotationOptionsPdf from './partials/options_pdf.vue'
import DataTable from '../../components/DataTable.vue'

export default {
    props:['typeUser', 'isClient','configuration'],
    components: {DataTable,QuotationOptions, QuotationOptionsPdf},
    data() {
        return {
            showDialogVoided: false,
            showImportDialog: false,
            resource: 'order-notes/not-sent',
            recordId: null,
            showDialogOptions: false,
            showDialogOptionsPdf:false,
            showDialogPayments: false,
            loading_submit: false,

        }
    },
    created() {
    },
    methods: {
        clickOptions(recordId = null) {
            this.recordId = recordId
            this.showDialogOptions = true
        },
        clickOptionsPdf(recordId = null) {
            this.recordId = recordId
            this.showDialogOptionsPdf = true
        },
        clickAnulate(id)
        {
            this.voided(`order-notes/voided/${id}`).then(() =>
                this.$eventHub.$emit('reloadData')
            )
        }
    }
}
</script>
