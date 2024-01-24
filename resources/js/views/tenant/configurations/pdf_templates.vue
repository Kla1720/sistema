<template>
    <div>
        <div class="page-header pr-0">
            <h2><a href="#"><i class="fas fa-cogs"></i></a></h2>
            <ol class="breadcrumbs">
                <li class="active"><span>Configuración</span></li>
                <li><span class="text-muted">Plantilla PDF</span></li>
            </ol>
            <div class="right-wrapper pull-right">
                <button class="btn btn-custom btn-sm  mt-2 mr-2"
                        type="button"
                        @click="addSeeder"><i class="el-icon-refresh"></i>
                    Actualizar listado
                </button>
            </div>
        </div>
        <div class="card">
            <div class="card-body pb-5">
                <div class="row">
                    <div class="col-3">
                        <label class="control-label">Establecimiento</label>
                        <el-select v-model="form.establishment_id"
                                   @change="changeEstablishment()">
                            <el-option v-for="option in establishments"
                                       :key="option.id"
                                       :label="option.description"
                                       :value="option.id"></el-option>
                        </el-select>
                    </div>
                    <div class="col-3">
                        <label class="control-label">Plantilla actual</label>
                        <el-input v-model="form.current_format"
                                  readonly></el-input>
                        <small v-if="form.current_format"
                               style="cursor:pointer">
                            <a @click="viewModalImage(form.current_format)">
                                Ver plantilla
                            </a>
                        </small>
                    </div>
                </div>
                <div class="row">
                    <div v-for="template in formatos" class="my-2 col-sm-12 col-md-6 col-lg-4 col-xl-3">
                        <el-card :id="template.id"
                                 :body-style="{ padding: '0px' }">
                            <a @click="viewImage(template)" v-if="template.urls.invoice">
                                <img :src="path.origin+'/'+template.urls.invoice"
                                     class="image"
                                     style="width: 100%"></a>
                            <div style="padding: 14px;">
                                <span class="text-center">{{ template.name }}</span>
                                <div v-if="form.establishment_id"
                                     class="bottom clearfix text-right">
                                    <el-radio v-model="form.current_format"
                                              :label="template.name"
                                              @change="changeFormat(template.name)">
                                        <span v-if="form.current_format == template.name">Activo</span>
                                        <span v-else>Activar</span>
                                    </el-radio>
                                </div>
                                <div v-if="form.current_format == template.name && form.current_format == 'aqpfact_01'" class="row">
                                    <div class="col-md-3">
                                        <label class="control-label float-left">
                                            <el-tooltip class="item" effect="dark" content="Color primario de la plantilla." placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                            <input type="color" id="primary_color" class="field-radio" v-bind:value="form.color1"  @change="changeColor1(form.current_format,$event)">
                                        </label>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="control-label float-left">
                                            <el-tooltip class="item" effect="dark" content="Color secundario de la plantilla." placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>
                                            <input type="color" id="secondary_color" class="field-radio"  v-bind:value="form.color2" @change="changeColor2(form.current_format,$event)">
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="control-label float-left">
                                            <el-tooltip class="item" effect="dark" content="Cambiar fondo para cotización. Se recomienda diseñar según el formato por
                                        defecto, o editar en Photoshop o Corel y sinó descargar de www.aqpfact.pe/recursos" placement="top-start">
                                                <i class="fa fa-info-circle"></i>
                                            </el-tooltip>

                                            <el-upload slot="append"
                                                       :headers="headers"
                                                       :data="{'type': 'fondo'}"
                                                       action="/configurations/uploads"
                                                       :show-file-list="false"
                                                       :on-success="successUpload">
                                                <el-button type="primary" icon="el-icon-upload"></el-button>
                                            </el-upload>

                                        </label>
                                    </div>

                                </div>
                            </div>
                        </el-card>
                    </div>
                </div>
            </div>
        </div>
        <el-dialog
            :visible.sync="modalImage"
            width="100">
            <span>
                <div class="block" v-if="template.urls.invoice">
                    <el-carousel arrow="always" :interval="10000" height="550px">
                        <el-carousel-item>
                            <img  :src="path.origin+'/'+template.urls.invoice"
                                class="image"
                                style="width: 100%; height:100%;">
                        </el-carousel-item>
                        <el-carousel-item v-if="template.urls.guide != ''">
                            <img  :src="path.origin+'/'+template.urls.guide"
                                class="image"
                                style="width: 100%; height:100%;">
                        </el-carousel-item>
                    </el-carousel>
                </div>
            </span>
            <span slot="footer"
                  class="dialog-footer">
                <el-button @click="modalImage = false">Cerrar</el-button>
                <el-button v-if="form.establishment_id"
                           type="primary"
                           @click="changeFormat(template.name)">Activar</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<style scoped>
.el-carousel__item:nth-child(2n) {
background-color: #99a9bf;
}

.el-carousel__item:nth-child(2n+1) {
background-color: #d3dce6;
}
</style>

<script>

export default {
    props: [
        'path_image',
        'typeUser',
        'establishments'
    ],

    data() {
        return {
            loading_submit: false,
            headers: headers_token,
            resource: 'configurations',
            errors: {},
            form: {},
            formatos: [],
            path: location,
            modalImage: false,
            template: {
                name: '',
                urls: {}
            },
            color1:{
                dato:''
            },
            color2:{
                dato:''
            }
        }
    },
    async created() {
        await this.$http.get(`/${this.resource}/record`) .then(response => {
            if (response.data !== ''){
                // console.log(response.data);
                this.form = response.data.data;
            }
            // console.log(this.placeholder)
        });

        await this.$http.get(`/${this.resource}/getFormats`).then(response => {
            if (response.data !== '') this.formatos = response.data.formats
            // if (response.data !== '') this.formatos = response.data.filter(r => this.image(r.formats))
        });
    },
    methods: {
        changeFormat(value) {
            this.modalImage = false
            this.form = {
                formats: value,
                establishment: this.form.establishment_id,
            }

            this.$http.post(`/${this.resource}/changeFormat`, this.form).then(response => {
                this.$message.success(response.data.message);
                location.reload()
            })

        },
        changeEstablishment() {
            var establishment = this.form.establishment_id;
            var selected = _.filter(this.establishments, {'id': establishment})[0];
            // console.log(selected.template_pdf);
            this.form.current_format = selected.template_pdf;
        },
        addSeeder() {
            this.$http.all([
                this.$http.get(`/${this.resource}/templates/ticket/refresh`),
                this.$http.get(`/${this.resource}/addSeeder`)
            ]).then(
                this.$http.spread((...responses) => {
                    this.$message.success(responses[0].data.message);
                    this.$message.success(responses[1].data.message);
                    location.reload()
                })
            )

        },
        viewImage(template) {
            this.template = template
            this.modalImage = true
        },
        viewModalImage(name) {
            this.template = this.formatos.filter(template => template.name == name)[0]
            this.modalImage = true
        },

        changeColor1(value,e){
            this.modalImage = false
            this.color1.dato=e.target.value
            this.$http.post(`/${this.resource}/changeColor1`, this.color1).then(response =>{
                this.$message.success(response.data.message);
                //alert('El color primario fué cambiado corréctamente, el sistema necesita recargarse.');
                //location.reload()
                //alert('El color primario se cambió correctamente: ' +e.target.value);
            })
            //alert('cambiando color: '+ ' - otro: '+value+ ' color: ' +e.target.value);
        },
        changeColor2(value,e){
            this.modalImage = false
            this.color2.dato=e.target.value
            this.$http.post(`/${this.resource}/changeColor2`, this.color2).then(response =>{
                this.$message.success(response.data.message);
                //alert('El color secundario fué cambiado corréctamente, el sistema necesita recargarse.');
                //location.reload()
                //alert('El color primario se cambió correctamente: ' +e.target.value);
            })
            //alert('cambiando color: '+ ' - otro: '+value+ ' color: ' +e.target.value);
        },
        successUpload(response, file, fileList) {
            if (response.success) {
                this.$message.success(response.message)
                this.form[response.type] = response.name
            } else {
                this.$message({message:'Error al subir el archivo', type: 'error'})
            }
        }
    }
}
</script>
