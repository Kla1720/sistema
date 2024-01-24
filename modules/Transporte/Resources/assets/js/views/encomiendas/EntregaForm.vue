<template>
    <el-dialog :title="titleDialog" width="40%"  :visible="showDialogEntrega"  @open="create"  :close-on-click-modal="false" :close-on-press-escape="false" append-to-body :show-close="false">

        <div class="form-body">
            <div class="row" >
                <div class="col-lg-12 col-md-12 table-responsive">
                    <div class="form-group">
                        <label for="">Ingrese la contraseña <span class="text-danger">*</span></label>
                        <el-input type="password" v-model="password" placeholder="****" maxlength="4"></el-input>
                    </div>
                </div>

            </div>
        </div>

        <div class="form-actions text-right pt-2">
            <el-button @click.prevent="close()">Cerrar</el-button>
             <el-button type="primary" @click="submit" >Guardar</el-button>
        </div>
    </el-dialog>
</template>

<script>
export default {
    props: ['showDialogEntrega','encomienda','recordId'],
    data() {
        return {
            titleDialog: 'Series',
            resource_documents:'documents',
            loading: false,
            errors: {},
            form: {},
            password:""
        }
    },
    async created() {
    },
    methods: {

        initForm(){
            this.form = {
                id: this.recordId
            }
        },
        create(){
            this.initForm()
        },
        async submit(){
            if(this.password==this.encomienda.clave){
                this.$http
                    .put(`/transportes/encomiendas/entregar`, this.form)
                    .then(async (response) => {
                        if (response.data.success) {
                            this.$message.success(response.data.message);
                            this.$emit('onUpdateItem',response.data.encomienda);
                            this.$emit("update:showDialogEntrega", false);
                        } else {
                            this.$message.error(response.data.message);
                        }
                    })
                    .catch((error) => {
                        if (error.response) {
                            this.$message.error(error.response.data.message);
                        }
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            }
            else {
                this.$message.error("Ingrese la contraseña correcta");
            }

        },
        close() {
            this.password="";
            this.$emit('update:showDialogEntrega', false)

        },
        async clickCancelSubmit() {

        },
    }
}
</script>
