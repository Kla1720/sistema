<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>{{ title }}</span></li>
            </ol>
            <div class="right-wrapper pull-right">
            </div>
        </div>
        <div class="card mb-0">
            <div class="card-header bg-info">
                <h3 class="my-0"> {{ title }}</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">

                       <template  v-for="(option,ind) in records">
<!--                            <template v-if="option.id === 3">-->
<!--                                <el-checkbox class="plan_documents d-block" :disabled="true"  v-model="option.active"  :label="option.id"  :key="ind"  @change="submit(option.id)">{{option.name+' (Pronto)'}}</el-checkbox>-->
<!--                            </template>-->
                            <template>
                                <el-checkbox class="plan_documents d-block"  v-model="option.active"  :label="option.id"  :key="ind"  @change="submit(option.id)">{{option.name}}</el-checkbox>
                            </template>
                       </template>
                    </div>
                </div>
            </div>

        </div>
        <div v-if="isActiveBussinessTurn('restaurant')" class="card">
            <div class="card-header bg-info">
                <h3 class="my-0">Impresoras</h3>
            </div>
            <div class="card-body">
                <form autocomplete="off" @submit.prevent="submit">
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-12">
                                <b>Impresora para la cocina</b>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">Nombre</label>
                                <el-input v-model="form.PrinterNombre1" ></el-input>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">Ruta</label>
                                <el-input v-model="form.PrinterRuta1" ></el-input>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">Tipo Conexión</label>
                                <el-select v-model="form.PrinterTipoConexion1" @change="changeTipoConexion1">
                                    <el-option
                                            v-for="item in conexiones"
                                            :key="item.id"
                                            :label="item.name"
                                            :value="item.name">
                                    </el-option>
                                </el-select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <b>Impresora para la barra</b>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">Nombre</label>
                                <el-input v-model="form.PrinterNombre2" ></el-input>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">Ruta</label>
                                <el-input v-model="form.PrinterRuta2" ></el-input>
                            </div>
                            <div class="col-md-4">
                                <label class="control-label">Tipo Conexión</label>
                                <el-select v-model="form.PrinterTipoConexion2" @change="changeTipoConexion2">
                                    <el-option
                                            v-for="item in conexiones"
                                            :key="item.id"
                                            :label="item.name"
                                            :value="item.name">
                                    </el-option>
                                </el-select>
                            </div>
                        </div>
                        <div class="form-actions text-right pt-2">
                            <el-button type="primary" native-type="submit" :loading="loading_submit" @click="submit2">Guardar</el-button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>

    export default {
        props:['path_image'],
        data() {
            return {
                loading_submit: false,
                title: null,
                business_turns:[],
                resource: 'bussiness_turns',
                records: [],
                form: {},
                conexiones: [{_id: '1', name: 'RED'}, {_id: '2', name: 'USB'}],
            }
        },
        async created() {

            this.title = 'Giros de negocio';
            await this.getRecords();
            await this.$http.get(`/configurations/record`) .then(response => {
                if (response.data !== ''){
                    this.form = response.data.data;
                }
            });
            await this.$http.get(`/documents/tables`)
                .then(response => {
                    this.business_turns = response.data.business_turns
                })
        },
        methods: {

            submit(id) {
                this.loading_submit = true;

                this.$http.post(`/${this.resource}`,{id}).then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                        this.getRecords()
                    }
                    else {
                        this.$message.error(response.data.message);
                    }
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data.errors;
                    }
                    else {
                        console.log(error);
                    }
                }).then(() => {
                    this.loading_submit = false;
                    if(id===3){
                        location.reload();
                    }
                });
            },
            submit2() {
                this.loading_submit = true;

                this.$http.post(`/configurations`, this.form).then(response => {
                    if (response.data.success) {
                        this.$message.success(response.data.message);
                    }
                    else {
                        this.$message.error(response.data.message);
                    }
                }).catch(error => {
                    if (error.response.status === 422) {
                        this.errors = error.response.data;
                    }
                    else {
                        console.log(error);
                    }
                }).then(() => {
                    this.loading_submit = false;
                });
            },
            getRecords(){
                this.$http.get(`/${this.resource}/records`)
                    .then(response => {
                        this.records = response.data
                    })
            },
            changeTipoConexion1() {
                // this.form.PrinterTipoConexion1 = _.find(this.conexiones, {'name': this.conexiones.item.name})
            },
            changeTipoConexion2() {
                // alert(this.form.PrinterTipoConexion2);
                // this.form.PrinterTipoConexion2 = _.find(this.conexiones, {'name': this.form.PrinterTipoConexion1})
            },
            isActiveBussinessTurn(value){
                return (_.find(this.business_turns,{'value':value})) ? true:false
            }
        }
    }
</script>
