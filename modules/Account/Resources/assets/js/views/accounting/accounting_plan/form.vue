<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group" :class="{'has-danger': errors.cuenta}">
                            <label class="control-label">Cuenta</label>
                            <el-input v-model="form.cuenta" :readonly="recordId!=null" :maxlength="2"></el-input>
                            <small class="form-control-feedback" v-if="errors.cuenta" v-text="errors.cuenta[0]"></small>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group" :class="{'has-danger': errors.nombre}">
                            <label class="control-label">Nombre</label>
                            <el-input v-model="form.nombre"></el-input>
                            <small class="form-control-feedback" v-if="errors.nombre" v-text="errors.nombre[0]"></small>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="form-group" :class="{'has-danger': errors.currency_type_id}">
                            <label class="control-label">Moneda</label>
                            <el-select v-model="form.currency_type_id" @change="changeCurrencyType">
                                <el-option v-for="option in currency_types" :key="option.id" :value="option.id" :label="option.description"></el-option>
                            </el-select>
                            <small class="form-control-feedback" v-if="errors.currency_type_id" v-text="errors.currency_type_id[0]"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Guardar</el-button>
            </div>
        </form>
    </el-dialog>

</template>

<script>

import {calculateRowItem} from "../../../../../../../../resources/js/helpers/functions";

export default {
    props: ['showDialog', 'recordId','external'],
    data() {
        return {
            loading_submit: false,
            titleDialog: null,
            resource: 'accounting',
            errors: {},
            form: {},
            currency_types: [],
        }
    },
    async created() {
        this.initForm()
        await this.$http.get(`/${this.resource}/tables`)
            .then(response => {
                this.document_types = response.data.document_types_invoice;
                this.document_types_guide = response.data.document_types_guide;
                this.currency_types = response.data.currency_types;
                this.form.currency_type_id = (this.currency_types.length > 0)?this.currency_types[0].id:null;
                this.changeCurrencyType()
            })
    },
    methods: {
        initForm() {
            this.errors = {}
            this.form = {
                id: null,
                cuenta: null,
                nombre: null,
                nivel:null,
                tipo:null,
                currency_type_id: null,
                is_update:false
            }
        },
        changeCurrencyType() {
            this.currency_type = _.find(this.currency_types, {'id': this.form.currency_type_id})
        },
        create() {
            this.titleDialog = (this.recordId)? 'Editar Cuenta':'Nueva Cuenta'
            if (this.recordId) {
                this.$http.get(`/${this.resource}/plan/record/${this.recordId}`)
                    .then(response => {
                        this.form = response.data
                        console.log(this.form);
                        this.form.is_update = true
                    })
            }
        },
        submit() {
            this.loading_submit = true
            this.$http.post(`/${this.resource}`, this.form)
                .then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message)
                        if (this.external) {
                            this.$eventHub.$emit('reloadDataCardBrands', response.data.id)
                        } else {
                            this.$eventHub.$emit('reloadData')
                        }
                        this.close()
                    } else {
                        this.$message.error(response.data.message)
                    }
                })
                .catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data
                    } else {
                        console.log(error)
                    }
                })
                .then(() => {
                    this.loading_submit = false
                })
        },
        close() {
            this.$emit('update:showDialog', false)
            this.initForm()
        },
    }
}
</script>
