<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>{{ title }}</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <button type="button" class="btn btn-custom btn-sm  mt-2 mr-2" @click.prevent="clickCreate()"><i class="fa fa-plus-circle"></i> Nuevo</button>

            </div>
        </div>
        <div class="card mb-0">
            <div class="card-header bg-info">
                <h3 class="my-0">Listado de {{ title }}</h3>
            </div>
            <div class="card-body">
                <data-table :resource="resource">
                    <tr slot="heading">
                        <th>#</th>
                        <th>Cuenta</th>
                        <th>Nombre</th>
                        <th>Nivel</th>
                        <th>Tipo</th>
                        <th>Analisis</th>
                        <th>Moneda</th>
                        <th>Balance comprobacion</th>
                        <th>Cuenta Equivalente</th>
                        <th>Amarre Debe</th>
                        <th>Amarre haber</th>
                        <th class="text-right">Acciones</th>
                    <tr>
                    <tr slot-scope="{ index, row }">
                        <td>{{ index }}</td>
                        <td>{{ row.cuenta }}</td>
                        <td>{{ row.nombre }}</td>
                        <td>{{ row.nivel }}</td>
                        <td>{{ row.tipo }}</td>
                        <td>{{ row.analisis }}</td>
                        <td>{{ row.moneda }}</td>
                        <td>{{ row.balanca_comprobacion }}</td>
                        <td>{{ row.cuenta_equivalente }}</td>
                        <td>{{ row.amarre_debe }}</td>
                        <td>{{ row.amarre_haber }}</td>
                        <td>{{ row.created_at }}</td>
                        <td class="text-right">
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-info" @click.prevent="clickCreate(row.id)">Editar</button>
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickDelete(row.id)">Eliminar</button>
                        </td>
                    </tr>
                </data-table>
            </div>

            <accounting-plan-form
                :showDialog.sync="showDialog"
                :recordId="recordId"
            ></accounting-plan-form>
        </div>
    </div>
</template>

<script>

import AccountingPlanForm from './form.vue'
import DataTable from '../../../components/DatatableAccountingPlan.vue'
import {deletable} from '../../../../../../../../resources/js/mixins/deletable'

export default {
    mixins: [deletable],
    props: ['typeUser'],
    components: {AccountingPlanForm,DataTable},
    data() {
        return {
            title: null,
            showDialog: false,
            resource: 'accounting',
            recordId: null,
            records: [],
        }
    },
    created() {
        this.title = 'Plan de cuentas'
        this.$eventHub.$on('reloadData', () => {
            this.getData()
        })
        this.getData()
    },
    methods: {
        getData() {
            this.$http.get(`/${this.resource}/plan/records`)
                .then(response => {
                    this.records = response.data
                })
        },
        clickCreate(recordId = null) {
            this.recordId = recordId
            this.showDialog = true
        },
        clickDelete(id) {
            this.destroy(`/${this.resource}/plan/${id}`).then(() =>
                this.$eventHub.$emit('reloadData')
            )
        }
    }
}
</script>
