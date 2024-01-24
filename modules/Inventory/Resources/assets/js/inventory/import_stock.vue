<template>
    <el-dialog :title="titleDialog" :visible="showDialog" @close="close" @open="create" class="dialog-import">
        <form autocomplete="off" @submit.prevent="submit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12">
                        <a href="/formats/inventories.xlsx" target="_new">Descargar formato</a>
                        <p class="text-center"><b>Lo que cucederá despúes.</b></p>
                        <p>
                            - El stock que tiene actualmente, se cambiará por la información que ingrese en este archivo.<br>
                            - Sino se encuentra el código del producto, no se hará nada.<br>
                        </p>
                    </div>
                    <div class="col-md-12 mt-4">
                        <div class="form-group text-center" :class="{'has-danger': errors.file}">
                            <el-upload
                                ref="upload"
                                :headers="headers"
                                action="/inventory/import_stock"
                                :show-file-list="true"
                                :auto-upload="false"
                                :multiple="false"
                                :on-error="errorUpload"
                                :limit="1"
                                :on-success="successUpload">
                                <el-button slot="trigger" type="primary">Seleccione un archivo (xlsx)</el-button>
                            </el-upload>
                            <small class="form-control-feedback" v-if="errors.file" v-text="errors.file[0]"></small>
                        </div>
                    </div>

                </div>
            </div>
            <div class="form-actions text-right mt-4">
                <el-button @click.prevent="close()">Cancelar</el-button>
                <el-button type="primary" native-type="submit" :loading="loading_submit">Procesar</el-button>
            </div>
        </form>
    </el-dialog>
</template>

<script>

export default {
    props: ['showDialog'],
    data() {
        return {
            loading_submit: false,
            headers: headers_token,
            titleDialog: null,
            resource: 'items',
            errors: {},
            form: {},
        }
    },
    created() {
        this.initForm()
    },
    methods: {
        initForm() {
            this.errors = {}
            this.form = {
                file: null,
            }
        },
        create() {
            this.titleDialog = 'Importar Stock'
        },
        async submit() {
            this.loading_submit = true
            await this.$refs.upload.submit()
            this.loading_submit = false
        },
        close() {
            this.$emit('update:showDialog', false)
            this.initForm()
        },
        successUpload(response, file, fileList) {
            if (response.success) {
                this.$message.success(response.message)
                this.$eventHub.$emit('reloadData')
                this.$eventHub.$emit('reloadTables')
                this.$refs.upload.clearFiles()

                let message = `<strong>Los siguientes códigos no fueron encontrados: <br></strong>${response.data.noexiste }`
                this.$notify({
                    title: "",
                    dangerouslyUseHTMLString: true,
                    message: message,
                    type: "warning",
                    duration: 6000
                })

                this.close()
            } else {
                this.$message({message:response.message, type: 'error'})
            }
        },
        errorUpload(response) {
            console.log(response)
        }
    }
}
</script>
