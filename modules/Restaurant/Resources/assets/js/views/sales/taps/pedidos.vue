<template>
    <div class="row mt-5 d-flex justify-content-center">
        <div class="col-xl-3 col-md-3 col-sm-12" v-loading="loading_recibido">
            <div class="kanban-aqp recibido-aqp p-2">
                <div class="px-2 py-2 text-center"><i class="el-icon-timer px-2 icon"></i>  <span class="title">PREPARACIÓN</span></div>
                    <el-card v-for="item in preparaciones" :key="item.id" class="box-card col-md-12 mb-2">
                        <div slot="header" class="clearfix">
                            <span>Pedido # {{ item.order_id }}</span>
                            <el-button style="float: right; padding: 3px 0" type="text" @click="pasar_a_enviado(item)">Pasar a Enviado <i class="el-icon-right"></i></el-button>
                        </div>
                        <div class="text item">
                            <b>TOTAL: S/ {{item.total}}</b>
                        </div>
                    </el-card>
            </div>
        </div>

        <div class="col-xl-3 col-md-3 col-sm-12 mx-2" v-loading="loading_enviado">
            <div class="kanban-aqp enviado-aqp p-2">
                <div class="px-2 py-2 text-center"><i class="el-icon-position px-2 icon"></i><span class="title">ENVIADOS</span></div>
                    <el-card v-for="item in enviados" :key="item.id" class="box-card col-md-12 mb-2">
                        <div slot="header" class="clearfix">
                            <span>Pedido # {{ item.order_id }}</span>
                            <el-button style="float: right; padding: 3px 0" type="text" @click="pasar_a_entregado(item)">Pasar a Entregado <i class="el-icon-right"></i></el-button>
                        </div>
                        <div class="text item">
                            <b>TOTAL: S/ {{item.total}}</b>
                        </div>
                    </el-card>
            </div>
        </div>

        <div class="col-xl-3 col-md-3 col-sm-12" v-loading="loading_entregado">
            <div class="kanban-aqp entregado-aqp p-2">
                <div class="px-2 py-2 text-center"><i class="el-icon-finished px-2 icon"></i><span class="title">ENTREGADOS</span></div>
                    <el-card v-for="item in entregados" :key="item.id" class="box-card col-md-12 mb-2">
                        <div slot="header" class="clearfix">
                            <span>Pedido # {{ item.order_id }}</span>
<!--                            <el-button style="float: right; padding: 3px 0" type="text">Pasar Completado <i class="el-icon-right"></i></el-button>-->
                        </div>
                        <div class="text item">
                            <b>TOTAL: S/ {{item.total}}</b>
                        </div>
                    </el-card>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            loading:false,
            loading_recibido :false,
            loading_enviado :false,
            loading_entregado :false,
            tableData: [],
            preparaciones:[],
            enviados:[],
            entregados:[],
            search: '',
            visible:false,
            item: {}
        }
    },
    created() {
        this.get_recibidos()
        this.get_enviados()
        this.get_entregados()
    },
    methods: {

        async get_recibidos(){
            try{
                this.loading_recibido = true;
                const { data } = await this.$http.get(`/restaurant/taps/pedidos`);
                this.preparaciones = data.data;
                this.loading_recibido = false;

            }catch(error){
                this.loading_recibido = false;
                if(error.response) this.axiosError(error);

            }
        },
        async get_enviados(){
            try{
                this.loading_enviado = true;
                const { data } = await this.$http.get(`/restaurant/taps/enviados`);
                this.enviados = data.data;
                this.loading_enviado = false;

            }catch(error){
                this.loading_enviado = false;
                if(error.response) this.axiosError(error);

            }
        },

        async get_entregados(){
            try{
                this.loading_entregado = true;
                const { data } = await this.$http.get(`/restaurant/taps/entregados`);
                this.entregados = data.data;
                this.loading_entregado = false;

            }catch(error){
                this.loading_entregado = false;
                if(error.response) this.axiosError(error);

            }
        },

       async pasar_a_enviado(pedido){
            try{

                this.loading = true;

                await this.$http.post(`/restaurant/taps/pedidos/update-state`, {
                    'id': pedido.id,
                    'status_order_id': 3 //despachado
                })
                    .then(data => {
                        this.loading = false;
                        this.get_recibidos()
                        this.get_enviados()
                        this.get_entregados()
                    });

            }catch(error){
                this.loading = false;
                if(error.response) this.axiosError(error);
                console.log(error)
            }
        },

        async pasar_a_entregado(pedido){
            try{

                this.loading = true;

                await this.$http.post(`/restaurant/taps/pedidos/update-state`, {
                    'id': pedido.id,
                    'status_order_id': 4 //entregado
                })
                    .then(data => {
                        this.loading = false;
                        this.get_recibidos()
                        this.get_enviados()
                        this.get_entregados()
                    });

            }catch(error){
                this.loading = false;
                if(error.response) this.axiosError(error);
                console.log(error)
            }
        }

    },
}
</script>
