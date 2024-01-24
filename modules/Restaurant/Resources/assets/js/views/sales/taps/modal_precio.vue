<template>
    <el-dialog
        :title="title"
        :visible="visible"
        @close="onClose"
        @open="onCreate"
        width="400px"
    >
        <form autocomplete="off" @submit.prevent="onSubmit">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-7 col-lg-7 col-xl-7 col-sm-7">
                        <div class="form-group">
                            <label for="nombre">Precio</label>
                            <input type="text" id="precio" class="form-control" v-model="item.price" :class="{ 'is-invalid': errors.price }"/>
                            <div v-if="errors.price" class="invalid-feedback">{{ errors.price[0] }}</div>
                        </div>

                        <div class="row text-center">
                            <div class="col-6">
                                <el-button native-type="submit" :disabled="loading" type="primary" class="btn-block" :loading="loading">Guardar</el-button>
                            </div>
                            <div class="col-6">
                                <el-button class="btn-block" @click="onClose">Cancelar</el-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </el-dialog>
</template>

<script>
export default {
    props: {
        visible: {
            type: Boolean,
            required: true,
            default: false,
        },
        item:{
            type: Object,
            required: false,
            default: {},
        }
    },
    data() {
        return {
            title: "Editar Precio",
            errors: {},
            loading: false,
            resource:'restaurant/mesas',
        };
    },
    mounted(){

    },
    methods: {
        onUpdate() {
            let data ={
                id:this.item.id,
                sale_unit_price: this.item.price
            }
            console.log(this.item)
            this.loading = true;
            this.$http
                .post(`/restaurant/taps/item/update-price`, data)
                .then((response) => {
                    this.onClose();
                })
                .finally(() => {
                    this.loading = false;
                    this.errors = {};
                })
                .catch((error) => {
                    this.axiosError(error);
                });
        },
        onSubmit() {
            if (this.item) {
                this.onUpdate();
            }
        },
        onCreate() {
            if (this.item) {
                this.title= "Estas editando el precio de: " + this.item.description
            }
        },
        onClose() {
            this.$emit("update:visible", false);
        }
    },
};
</script>
