<template>
    <el-dialog :title="title" :visible="visible" @close="onClose" @open="onCreate">
        <div class="row mt-2">
            <div class="col-md-12">
                <table class="table table-bordered table-stripped responsive">
                    <thead>
                    <th>Dni</th>
                    <th>Nombre apellidos</th>
                    <th>Edad</th>
                    <th># Asiento</th>
                    <th>Asistió?</th>
                    </thead>
                    <tbody>
                    <tr v-for="pasaje in this.pasajes">
                        <td>
                            <div class="form-group mb-2 mr-2">
                                <p :class="{ 'text-success': pasaje.asistencia }" v-model="pasaje.pasajero.number">{{pasaje.pasajero.number}}</p>
                            </div>
                        </td>
                        <td>
                            <div class="form-group mb-2 mr-2">
                                <p :class="{ 'text-success': pasaje.asistencia }" v-model="pasaje.pasajero.name">{{pasaje.pasajero.name}}</p>
                            </div>
                        </td>
                        <td>
                            <div class="form-group mb-2 mr-2 text-center">
                                <p :class="{ 'text-success': pasaje.asistencia }" v-model="pasaje.pasajero.edad">{{pasaje.pasajero.edad}}</p>
                            </div>
                        </td>
                        <td>
                            <div class="form-group mb-2 mr-2 text-center">
                                <p :class="{ 'text-success': pasaje.asistencia }" v-model="pasaje.numero_asiento"><b>{{pasaje.numero_asiento}}</b></p>
                            </div>
                        </td>

                        <td>
                            <div class="form-group mb-2 mr-2">
<!--                                <input type="checkbox" id="jack" value="Jack" v-model="pasaje.asistencia" :checked="(pasaje.asistencia)">-->
                                <el-checkbox v-model="pasaje.asistencia"  v-on:change="confirmar(pasaje.id,pasaje.asistencia)"></el-checkbox>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </el-dialog>
</template>
<script>
import moment from 'moment'
export default {
    props:{
        visible: {
            type: Boolean,
            required: true,
            default: false,
        },
        programacion:{
            type: Object|null,
            required: true,
            default: () => ({}),
        },
        manifiesto:{
            type: Object|null,
            required: true,
            default: () => ({}),
        }
    },
    data(){
        return ({
            title:'Registrar asistencia de pasajeros',
            loading:false,
            errors:{},
            pasajes:[]
        });
    },
    methods:{
        onClose(){
            this.$emit('update:visible',false);
        },
        async onCreate(){
            this.listaPasajeros(this.programacion.manifiesto)
        },
        onStore(){
            // this.loading = true;
            // this.$http.post('/transportes/manifiestos/guardar-manifiesto',this.manifiesto)
            // .then( async response => {
            //     this.loading = false;
            //
            //     await this.$message({
            //         type: 'success',
            //         message: response.data.message
            //     });
            //     this.onClose();
            // }).catch(error => {
            //     this.loading = false;
            //     this.axiosError(error);
            // });
        },
        initForm(){

        },
        async onUpdate(){

            // try{
            //
            //     this.loading = true;
            //     const { data } = await this.$http.put(`/transportes/manifiestos/${this.manifiesto.id}/actualizar-manifiesto`,this.manifiesto);
            //     this.loading = false;
            //
            //     await this.$message({
            //         type: 'success',
            //         message: data.message
            //     });
            //     this.$emit('update:visible',false);
            //
            // }catch(error){
            //     this.loading = false;
            //     if(error.response) this.axiosError(error);
            //
            // }

        },
        async listaPasajeros(){
            this.loading = true;
            this.$http.get(`/transportes/manifiestos/${this.manifiesto.id}/lista-pasajeros`)
            .then( async response => {
                this.pasajes=response.data.pasajes

                console.log(this.pasajes)
                this.loading = false;
            }).catch(error => {
                this.loading = false;
                this.axiosError(error);
            });
        },
        confirmar(pasaje_id, estado){

            let data={
                id:pasaje_id,
                estado:estado
            }
            this.loading = true;
            this.$http.post('/transportes/manifiestos/confirmar-asistencia',data)
            .then( async response => {

                this.loading = false;
                await this.$message({
                    type: 'success',
                    message: response.data.message
                });

            }).catch(error => {
                this.loading = false;
                this.axiosError(error);
            });
        }
    }

}
</script>
