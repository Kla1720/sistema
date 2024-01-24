<template>
    <div style="width: 100%">
        <div class="card mb-0 page-header pr-0">
        </div>
            <div class="card-body">
                <div class="row">
                    <!-- piso -->
                    <div class="col-md-12 col-sm-12 pb-2 text-center">
                        <el-button-group>
                            <a href="#" class="btn btn-success px-4 py-2 mr-1 mb-2" size="medium" :class="AqpTap.active==1 ? 'btn-warning': '' " @click="cargarTap(1)">POS</a>

                            <a href="#" class="btn btn-success px-4 py-2 mb-2" size="medium" :class="AqpTap.active==2 ? 'btn-warning': '' " @click="cargarTap(2)">MESAS</a>

                            <a href="#" class="btn btn-success px-4 py-2 ml-1 mb-2" size="medium" :class="AqpTap.active==3 ? 'btn-warning': '' " @click="cargarTap(3)">PEDIDOS</a>

                            <a href="#" class="btn btn-success px-4 py-2 ml-1 mb-2" :class="AqpTap.active==4 ? 'btn-warning': '' " @click="cargarTap(4)">PRECIOS</a>

                        </el-button-group>
                    </div>
                </div>
                <div class="row">
                    <div v-if="AqpTap.active==1" class="col-md-12 m-auto">
                        <div class="row">
                            <div class="col-md-8 text-center">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h3 class="font-weight-bold">Seleccione sus productos</h3>
                                    </div>

                                </div>
<!--                                <div class="row ">-->
<!--                                    <div class="col-md-12" style="height: 700px; overflow-y: scroll">-->
<!--                                        <el-table-->
<!--                                            :height="650"-->
<!--                                            :data="items.filter(data => !search || data.description.toLowerCase().includes(search.toLowerCase()))"-->
<!--                                            style="width: 100%">-->
<!--                                            <el-table-column-->
<!--                                            style="display: block;float: left">-->
<!--                                                <template slot-scope="scope">-->
<!--                                                    <div @click="agregarItem(scope.row)" style="background: rgb(233 233 233);padding: 10px;border-radius: 8px;">-->
<!--                                                        <img :src="'/storage/uploads/items/'+scope.row.image_small" class="image" width="150" height="150" style="max-width: 100%">-->
<!--                                                        <p class="text-center pb-0 mb-0 mt-2" style="font-size: 2em">  S/ {{ scope.row.sale_unit_price }}</p>-->
<!--                                                        <p class="text-center pb-0 mb-0" style="font-size: 1.2em"><b>{{scope.row.description}}</b></p>-->
<!--                                                    </div>-->
<!--                                                </template>-->
<!--                                            </el-table-column>-->
<!--                                            <el-table-column-->
<!--                                                align="right">-->
<!--                                                <template slot="header" slot-scope="scope">-->
<!--                                                    <el-input-->
<!--                                                        v-model="search"-->
<!--                                                        size="mini"-->
<!--                                                        placeholder="Escriba el nombre"/>-->
<!--                                                </template>-->
<!--                                            </el-table-column>-->
<!--                                        </el-table>-->

<!--                                        <el-divider></el-divider>-->

<!--                                        <div style="text-align: center">-->
<!--                                            <el-pagination-->
<!--                                                background-->
<!--                                                layout="prev, pager, next"-->
<!--                                                @current-change="handleCurrentChange"-->
<!--                                                :page-size="pageSize"-->
<!--                                                :total="total_page">-->
<!--                                            </el-pagination>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->



                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <div class="row no-gutters">
                                            <template v-for="(item, index) in categorias">
                                                <div class="col" :key="index">
                                                    <div @click="filterResults(item.id)" class="card p-0 m-0 mb-1 mr-1 text-center">
                                                        <div
                                                            :style="{ backgroundColor: item.color }"
                                                            class="card-body pointer rounded-0"
                                                            style="font-weight: bold;color: white;font-size: 18px;"
                                                        >
                                                            {{ item.name }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                            <div v-for="(item , index ) in filtrarCategorias" :key="item.id" class="col-lg-2 col-md-3  col-sm-4 col-xs-6 mb-2">
                                                <div :class="{active: activeList[index]}" class="t1 el-card box-card is-always-shadow float-left" @click="agregarItem(item,index)">
                                                    <img :src="'/storage/uploads/items/'+item.image_small" class="image" width="150" height="150" style="max-width: 100%;">
                                                    <div class="mt-2 p-md-2 p-sm-0">
                                                        <span class="font-large font-18 font-weight-bold">  S/ {{ item.sale_unit_price }}</span>
                                                        <div class="bottom clearfix">
                                                            <span style="width: 150px;display:block" class="font-medium font-weight-bold"> {{ item.description }}</span>
                                                            <!--<el-button type="text" class="button"><h5></h5>{{item.description}}</el-button>-->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                            </div>
                            <div class="col-md-4 border-left">
                                <div class="row text-center">
                                    <div class="col-md-12 table-responsive px-0">
                                        <h3><b style="color: #0A7CB5"> RESUMEN DEL PEDIDO</b></h3>
                                        <h5>Clientes Varios</h5>
                                        <div style="width: 100%" v-loading="loading"></div>
                                        <table class="table">
                                            <tbody>
                                            <tr v-for="(item, index) in pedidos_detalles" :key="index">
                                                <td class="py-2">
                                                    <table class="table m-0 m-0" style="border:0px">
                                                        <tr>
                                                            <td style="border: 0px;margin: 0; padding: 0" class="text-left">
                                                                <el-tooltip class="item" effect="dark" content="Disminuir" placement="top-start">
                                                                    <el-button type="warning" class="ml-0 mb-2" icon="el-icon-remove-outline" @click="disminuirCantidad(item)"> </el-button>
                                                                </el-tooltip>
                                                                <el-tooltip class="item" effect="dark" content="Incrementar" placement="top-start">
                                                                    <el-button type="success" class="ml-0 mb-2" icon="el-icon-plus" @click="incrementarCantidad(item)"> </el-button>
                                                                </el-tooltip>
                                                            </td>

                                                            <td style="border: 0px;margin: 0; padding: 0" class="text-left">
                                                                <p style="font-size: 1.5em">
                                                                    <span class="text-blue">S/ {{ item.precio }}</span> x <span style="color:darkgreen">{{ item.cantidad }}</span>
                                                                    {{ item.descripcion }}
                                                                </p>
                                                            </td>

                                                            <td style="border: 0px;margin: 0; padding: 0" class="text-right">
                                                                <el-tooltip class="item" effect="dark" content="Añadir Nota" placement="top-start">
                                                                    <el-button type="primary" icon="el-icon-document"> </el-button>
                                                                </el-tooltip>
                                                                <el-tooltip class="item" effect="dark" content="Borrar item" placement="top-start">
                                                                    <el-button type="danger" icon="el-icon-delete" @click="borrarItem(index,item)"> </el-button>
                                                                </el-tooltip>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <p style="font-size: 2em"><b style="color:blue">TOTAL:</b> <b style="color:darkgreen">{{total}}</b></p>
                                        <el-button type="primary" icon="el-icon-save" @click="finalizarVenta"> Finalizar Venta</el-button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div> <!-- FIN SECCION POS LIBRE -->
                    <div v-else-if="AqpTap.active==2" class="col-md-12"> <!-- INICIO SECCION MESAS -->
                        <el-tabs v-model="activeName" @tab-click="handleClickNivel">

                            <el-tab-pane  v-for="nivel in niveles" :key="nivel.id" :label="nivel.nombre" :name="nivel.nombre">

                                <div class="row">
                                    <div v-if="vistaMesas" class="col-md-8 text-center px-md-5 px-sm-0">
                                        <div :style="{background:controlarEstadosMesas(mesa)}"  v-for="mesa in nivel.mesas" :key="mesa.id" class="el-card box-card is-always-shadow m-1" style="width: 80px; float: left" v-on:click="seleccionaMesa(mesa)">
                                            <div  class="text item">
                                                <h3 class="text-white"><b>{{ mesa.numero }}</b></h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div v-else class="col-md-8 text-center">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <h3 class="font-weight-bold">Estás haciendo pedido para la mesa: <b style="color: #0A7CB5">{{ mesaActivo.numero}}</b></h3>
                                            </div>
                                            <div class="col-md-12 text-center">
                                                <div class="row no-gutters">
                                                    <template v-for="(item, index) in categorias">
                                                        <div class="col" :key="index">
                                                            <div @click="filterResults(item.id)" class="card p-0 m-0 mb-1 mr-1 text-center">
                                                                <div
                                                                    :style="{ backgroundColor: item.color }"
                                                                    class="card-body pointer rounded-0"
                                                                    style="font-weight: bold;color: white;font-size: 18px;"
                                                                >
                                                                    {{ item.name }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div :class="{active: activeList[index]}" v-for="(item, index) in filtrarCategorias" :key="item.id" class="t1 el-card box-card is-always-shadow m-4 float-left" @click="agregarItem(item, index)">
                                                <img :src="'/storage/uploads/items/'+item.image_small" class="image" width="100%" height="150" style="max-width: 100%">
                                                <div>
                                                    <span class="mt-2 font-large font-18 font-weight-bold">  S/ {{ item.sale_unit_price }}</span>
                                                    <div class="bottom clearfix">
                                                        <span class="font-medium font-weight-bold" style="width: 150px; display:block"> {{ item.description }}</span>
<!--                                                        <el-button type="text" class="button"><h5></h5>{{item.description}}</el-button>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-4 border-left">
                                        <div class="row text-center">
                                            <div v-if="mesaIsActivo" class="col-md-12 table-responsive px-0">
                                                <h3>Pedidos de la mesa: <b style="color: #0A7CB5">{{ mesaActivo.numero}}</b></h3>
                                                <h5>Clientes Varios</h5>
                                                <div style="width: 100%" v-loading="loading"></div>
                                                <table class="table">
                                                    <tbody>
                                                        <tr v-for="(item, index) in pedidos_detalles" :key="index">
                                                            <td class="py-2">
                                                                <table class="table m-0 m-0" style="border:0px">
                                                                    <tr>
                                                                        <td style="border: 0px;margin: 0; padding: 0" class="text-left">
                                                                            <el-tooltip class="item" effect="dark" content="Disminuir" placement="top-start">
                                                                                <el-button class="ml-0 mb-2" type="warning" icon="el-icon-remove-outline" @click="disminuirCantidad(item)"> </el-button>
                                                                            </el-tooltip>
                                                                            <el-tooltip class="item" effect="dark" content="Incrementar" placement="top-start">
                                                                                <el-button class="ml-0 mb-2" type="success" icon="el-icon-plus" @click="incrementarCantidad(item)"> </el-button>
                                                                            </el-tooltip>
                                                                        </td>

                                                                        <td style="border: 0px;margin: 0; padding: 0" class="text-left">
                                                                            <p style="font-size: 1.5em">
                                                                                <span class="text-blue">S/ {{ item.precio }}</span> x <span style="color:darkgreen">{{ item.cantidad }}</span>
                                                                                {{ item.descripcion }}
                                                                            </p>
                                                                        </td>

                                                                        <td style="border: 0px;margin: 0; padding: 0" class="text-right">
                                                                            <el-tooltip class="item" effect="dark" content="Añadir Nota" placement="top-start">
                                                                                <el-button type="primary" icon="el-icon-document"> </el-button>
                                                                            </el-tooltip>
                                                                            <el-tooltip class="item" effect="dark" content="Borrar item" placement="top-start">
                                                                                <el-button type="danger" icon="el-icon-delete" @click="borrarItem(index,item)"> </el-button>
                                                                            </el-tooltip>
                                                                        </td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                    <p style="font-size: 2em"><b style="color:blue">TOTAL:</b> <b style="color:darkgreen">{{total}}</b></p>
                                                <div v-if="pedidos_detalles.length>0" class="text-center">
                                                    <table class="table">
                                                        <tr>
                                                            <td style="width: 50%"><el-button size="small" icon="el-icon-s-order" class="btn-block" @click="precuenta">Precuenta</el-button></td>
                                                            <td style="width: 50%"><el-button size="small" icon="el-icon-dish" class="btn-block" @click="enviar_comanda(pedidoId)">Enviar Comanda</el-button></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <el-button v-if="pedidoId>0" type="primary" icon="el-icon-save" @click="finalizarVenta"> Finalizar Venta</el-button>
                                                    <el-button v-else type="danger" icon="el-icon-plus" @click="abrirMesa"> Abrir Mesa</el-button>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </el-tab-pane>


                        </el-tabs>
                    </div><!-- SECCION MESAS -->
                    <div v-else-if="AqpTap.active==3" class="col-md-12">
                        <tenant-restaurant-pedidos></tenant-restaurant-pedidos>
                    </div> <!-- SECCION PEDIDOS -->
                    <div v-else-if="AqpTap.active==4" class="col-md-12">
                        <tenant-restaurant-precios></tenant-restaurant-precios>
                    </div> <!-- SECCION EDICION PRECIOS-->

                </div>
            </div>

            <tenant-restaurant-pedidos-options :showDialogOptions.sync="showDialogOptions"
                               :recordId.sync="pedidoId"
                               :showGenerate="true"
                               :showClose="true"
                               :itemaqp="itemaqp"
                               :id_user2="id_user2"
                               :type-user="type_user"
                               :mesa_id="mesaActivo.id"
                               :mesaIsActivo.sync="mesaIsActivo"
                                @onLimPiarDatos="onLimPiarDatos"
                                @handleClickNivel = "handleClickNivel"
                                @updateTables="liberarMesa"
                               :configuration="configuration"></tenant-restaurant-pedidos-options>

    </div>
</template>
<script>
import tenantRestaurantPedidosOptions from './partials/options.vue'
import tenantRestaurantPrecios from './taps/precios.vue'
import tenantRestaurantPedidos from './taps/pedidos.vue'
import SocketClient from '@mixins/socket.js'

export default {
    mixins:[SocketClient],
    components: {
        tenantRestaurantPedidosOptions,
        tenantRestaurantPrecios,
        tenantRestaurantPedidos,
        // tenantRestaurantPos
    },
    props: {
        configuracionSocket:{
            type: Object,
            default: () => ({})
        },
        configuration: {
            type: Object,
            required: true,
            default: () => ({})
        },
        items: {
            type: Array,
            required: true,
        },
        id_user2:{
            type: Object,
            required: true,
        },
        type_user:{
            type: String,
            required: true,
        },
        categorias: {
            type: Array,
            required: true,
        },

    }, //'typeUser'
    computed:{
        isAutoPrint: function () {

            if(this.configuration)
            {
                return this.configuration.auto_print
            }

            return false

        },
        displayData() {
            if(this.search == null) return this.pedidos_detalles;

            this.filtered = this.pedidos_detalles.filter(data => !this.search || data.name.toLowerCase().includes(this.search.toLowerCase()));

            this.total_page = this.filtered.length;

            return this.filtered.slice(this.pageSize * this.page - this.pageSize, this.pageSize * this.page);
        },

        // filtrarCategorias() {
        //     console.log(this.categoria_id)
        //
        //     let tempRecipes = this.items_filtrado
        //
        //
        //     tempRecipes = tempRecipes.filter((item) => {
        //
        //         return (item.category_id === this.categoria_id)
        //     })
        //
        //     console.log(tempRecipes)
        //
        //     return new Array(tempRecipes)
        // },
    },
    mounted() {
        this.activeList = new Array(this.items.length).fill(false)
    },
    data() {
        return {
            showDialogOptions:false,
            loading : false,
            resource: "restaurant",
            recordId: null,
            AqpTap:{
                //active:1 //pos
                active:2 //mesas
                //active:4 //precios
            },
            activeName: '',
            mesas:[],
            mesaActivo: {},
            mesaIsActivo:false,
            niveles:[],
            vistaMesas:null,
            pedidos_detalles:[
                // {
                //     id:null,
                //     cant:2,
                //     price:34.66,
                //     description:'papas fritas'
                // }
            ],
            total:0,
            pedidoId:0,
            filtered: [],
            search: '',
            page: 1,
            pageSize: 4,
            total_page: 5,
            socketClient:null,
            activeList: [],
            categoria_id: null,
            filtrarCategorias: [],

            ip_impresora_cocina:null,
            ip_impresora_barra:null,
            ip_impresora_precuenta:null,
            nombre_impresora_cocina:null,
            nombre_impresora_barra:null,
            nombre_impresora_precuenta:null,
            impresora_precuenta_is_pdf:null,
            itemaqp:{},
            colors: ["#1cb973", "#bf7ae6", "#fc6304", "#9b4db4", "#77c1f3"],
        };
    },
    created() {
        this.handleClickNivel()
        this.vistaMesas = true
        this.initSocket();

        this.filtrarCategorias = this.items

        this.ip_impresora_cocina          = localStorage.ip_impresora_cocina
        this.ip_impresora_barra           = localStorage.ip_impresora_barra
        this.ip_impresora_precuenta       = localStorage.ip_impresora_precuenta
        this.nombre_impresora_cocina      = localStorage.nombre_impresora_cocina
        this.nombre_impresora_barra       = localStorage.nombre_impresora_barra
        this.nombre_impresora_precuenta   = localStorage.nombre_impresora_precuenta
        this.impresora_precuenta_is_pdf   = localStorage.impresora_precuenta_is_pdf

        this.startConnectionQzTray()
    },
    methods: {
        getColor(i) {
            return this.colors[i % this.colors.length];
        },
        filterResults ( id) {
            this.categoria_id= id
            if(this.categoria_id){
                this.filtrarCategorias = this.items.filter(item => item.category_id == this.categoria_id);
                //console.log(this.filtrarCategorias);
            }

        },
        initSocket(){

            if(!this.configuracionSocket) return

            if(!this.configuracionSocket.active) return;

            try{

                const protocol = window.location.protocol;

                const { domain, port } = this.configuracionSocket;

                const { Manager } = this.io;

                const url = `${protocol}//${domain}:${port}`;

                const manager = new Manager(`${url}`);

                this.socketClient = manager.socket("/");

                this.socketClient.on('mesa-ocupada', (params) => {
                    this.onUpdateItem();
                });

                this.socketClient.on("liberar-mesa", (payload)=>{
                    this.updateTable(payload);
                    this.onLimPiarDatos();
                    this.vistaMesas=true;
                    this.mesaIsActivo=false;
                });

            }catch(error){
                this.socketClient = null;
            }

        },
        startConnectionQzTray(){

            if (!qz.websocket.isActive() && this.isAutoPrint && this.ip_impresora_cocina !='undefined')
            {
                startConnection({host: this.ip_impresora_cocina, usingSecure: false},this.nombre_impresora_cocina);
            }
            if (!qz.websocket.isActive() && this.isAutoPrint && this.ip_impresora_precuenta!='undefined')
            {
                startConnection({host: this.ip_impresora_precuenta, usingSecure: false},this.nombre_impresora_precuenta);
            }
            if (!qz.websocket.isActive() && this.isAutoPrint && this.ip_impresora_barra!='undefined')
            {
                startConnection({host: this.ip_impresora_barra, usingSecure: false},this.nombre_impresora_barra);
            }

        },
        notificationAll(){
            if(this.socketClient) this.socketClient.emit('mesa-ocupada',true);
        },
        async onUpdateItem(){

            this.handleClickNivel()
            //console.log("socket jugando")

        },
        cargarTap(id){
            this.AqpTap.active=id
            this.vistaMesas=true
            this.onLimPiarDatos()
            if(id==2){ // si es vista mesas
                this.handleClickNivel()
            }
        },
        handleClickNivel(tab, event) { // se activa al dar click en el tap de mesas, en sus niveles
            //console.log(tab, event);

            console.log("handleclick activado")
            this.onLimPiarDatos()
            this.cargarNivelesMesas(tab)

        },
        async cargarNivelesMesas(tab){ //esto carga mesas y sus estados

            console.log("ESTADO DE MESAS ACTUALIZADO");
            try{
                this.loading = true;
                const { data } = await this.$http.get(`/restaurant/niveles/records`);
                this.loading = false;
                this.niveles = data.data;
                this.activeName= (this.niveles.length>0 && tab ==null) ? this.niveles[0].nombre : tab.name  //para activar primer TAB del nivel
                this.vistaMesas=true


            }catch(error){
                this.loading = false;
                if(error.response) this.axiosError(error);

            }
        },
        seleccionaMesa(mesa){
            this.vistaMesas=false
            this.mesaActivo=mesa
            this.mesaIsActivo=true

            this.verificarEstadoMesa(mesa)
        },
        agregarItem(producto,index){
            this.activeList = this.activeList.map(() => false)
            this.activeList[index] = true

            let exist = this.checkIfExists(producto.id)

            if(this.pedidoId>0){
                if(exist){
                    let cantidad = this.pedidos_detalles.find(item2 => item2.producto_id === producto.id).cantidad+=1
                    let producto_detalle_id = this.pedidos_detalles.find(item2 => item2.producto_id === producto.id).id
                    this.actualizarCantidad(producto_detalle_id,cantidad)
                }else{
                    let data ={
                        pedido_id:this.pedidoId,
                        producto_id:producto.id,
                        descripcion:producto.description,
                        cantidad:1,
                        precio:producto.sale_unit_price,
                        item:producto
                    }
                    this.insertarItem(this.pedidoId, data)
                }
            }
            else{
                if(exist){
                    this.pedidos_detalles.find(item2 => item2.producto_id === producto.id).cantidad +=1

                    this.$notify({
                        title: '',
                        message: producto.description + ' se incrementó en 1',
                        type: 'success'
                    })

                }else{
                    this.pedidos_detalles.push({producto_id: producto.id, cantidad: 1, precio: producto.sale_unit_price , descripcion: producto.description,item:producto});

                    this.$notify({
                        title: '',
                        message: producto.description + ' agregado',
                        type: 'success'
                    })
                }
            }

            this.calculateTotal()
        },
        disminuirCantidad(item){
            if(item.cantidad==1){
                return
            }else if(this.pedidoId>0){
                let cantidad = this.pedidos_detalles.find(item2 => item2.producto_id === item.producto_id).cantidad -=1
                this.actualizarCantidad(item.id,cantidad)
            } else {
                this.pedidos_detalles.find(item2 => item2.producto_id === item.producto_id).cantidad -=1
            }

            this.calculateTotal()
        },
        incrementarCantidad(item){
            let cantidad =  this.pedidos_detalles.find(item2 => item2.producto_id === item.producto_id).cantidad +=1
            if(this.pedidoId>0){
                this.actualizarCantidad(item.id,cantidad)
            }else{
                this.pedidos_detalles.find(item2 => item2.producto_id === item.producto_id).cantidad +=1
            }
            this.calculateTotal()
        },
        async insertarItem(pedido_id, item){
            this.loading = true;
            let data = {
                pedido_id : pedido_id,
                item: item
            }
            await this.$http
                .post(`/restaurant/cash/sales/item/insert-item`, data)
                .then((response) => {

                    this.cargarPedidosDetalle(this.pedidoId)

                    this.$notify({
                        title: '',
                        message: 'Producto agregado...',
                        type: 'success'
                    })

                })
                .finally(() => {
                    this.loading = false;
                    this.errors = {};
                })
                .catch((error) => {
                    this.axiosError(error);
                });
        },
        async actualizarCantidad(pedido_detalle_id, cantidad){
            this.loading = true;
            let data = {
                pedido_detalle_id : pedido_detalle_id,
                cantidad: cantidad
            }
            await this.$http
                .put(`/restaurant/taps/pedidos-detalles/update-quantity`, data)
                .then((response) => {

                    this.cargarPedidosDetalle(this.pedidoId)

                    this.$notify({
                        title: '',
                        message: 'Cantidad editada...',
                        type: 'success'
                    })

                })
                .finally(() => {
                    this.loading = false;
                    this.errors = {};
                })
                .catch((error) => {
                    this.axiosError(error);
                });
        },
        async borrarItem(index,item){
            if(this.pedidoId>0){
                let data = {
                    pedido_id : this.pedidoId,
                    item_id: item.id
                }
                this.loading = true;
                await this.$http
                    .put(`/restaurant/cash/sales/item/delete_item`, data)
                    .then((response) => {
                        this.cargarPedidosDetalle(this.pedidoId)

                        this.$notify({
                            title: '',
                            message: 'Producto eliminado',
                            type: 'success'
                        })
                    })
                    .finally(() => {
                        this.loading = false;
                        this.errors = {};
                    })
                    .catch((error) => {
                        this.axiosError(error);
                    });
            }else{
                this.pedidos_detalles.splice(index, 1);
            }
            this.calculateTotal()
        },
        checkIfExists(itemId) {
            return this.pedidos_detalles.find(item2 => item2.producto_id === itemId)
        },
        calculateTotal() {
            let total = 0;

            this.pedidos_detalles.forEach(row => {

                // isc
                total += parseFloat(row.cantidad*row.precio)

                this.total=total

            });
        },
        abrirMesa(){

            let data = {
                pedido: {
                    mesa_id:this.mesaActivo.id,
                    estado_mesa:'1',
                    tipo:'mesa',
                    pedidos_detalle:this.pedidos_detalles
                },
            }

            this.loading = true;
            this.$http
                .post("/restaurant/cash/sales/store", data)
                .then((response) => {

                    this.$notify({
                        title: '',
                        message: 'Pedido guardado...',
                        type: 'success'
                    })

                    this.onLimPiarDatos()

                    this.pedidoId = response.data.data.id
                    this.cargarPedidosDetalle(this.pedidoId)

                })
                .finally(() => {
                    this.loading = false;
                    this.errors = {};
                })
                .catch((error) => {
                    this.axiosError(error);
                });

            // if(this.socketClient) this.socketClient.emit('mesa-ocupada',true);//recarga estado mesas
            if(this.socketClient) this.socketClient.emit("liberar-mesa", {nivel_id: this.mesaActivo.nivel_id , mesa_id: this.mesaActivo.id, estado:1});

        },
        onLimPiarDatos(){
            //this.pedidoId=null
            this.pedidos_detalles=[]
            this.total=0
        },
       async cargarPedidosDetalle(pedido_id){
            try{
                this.loading = true;
                const { data } = await this.$http.get(`/restaurant/cash/sales/get-pedidos-detalles/${pedido_id}`);
                this.loading = false;
                this.pedidos_detalles = data.data;
                this.itemaqp = data.data
                this.calculateTotal()

            }catch(error){
                this.loading = false;
                if(error.response) this.axiosError(error);
            }
        },
       async verificarEstadoMesa(mesa){
            try{

                this.loading = true;

                await this.$http.post(`/restaurant/cash/sales/estado-mesa`, {
                    'mesa_id': mesa.id,
                    'nivel_id': mesa.nivel_id
                })
                .then(data => {
                    this.loading = false;
                    this.pedidoId = data.data.pedidoId;
                    if(this.pedidoId>0){
                        this.cargarPedidosDetalle(this.pedidoId)
                    }else{
                        this.onLimPiarDatos()
                    }
                });

            }catch(error){
                this.loading = false;
                if(error.response) this.axiosError(error);
                console.log(error)
            }
       },
       controlarEstadosMesas(mesa){
            let bg_estado = "#00C040"

           if(mesa.estado=="0"){
               bg_estado="#00C040"
           }else if (mesa.estado=="1"){
               bg_estado="#ff0000"
           }
            return bg_estado
       },

       finalizarVenta(pedidoId = null){

            if(this.pedidos_detalles.length<=0){
                this.$notify({
                    title: '',
                    message: 'Agregue productos...',
                    type: 'error'
                })
            }else{
                console.log(this.pedidos_detalles)
                this.showDialogOptions = true
            }

           //this.onLimPiarDatos()
           //this.handleClickNivel()
       },
        generateHtml() {
            let productsHtml = "";
            let data=this.pedidos_detalles
            for (let i = 0; i < data.length; i++) {
                productsHtml += `<tr>
                        <td>${data[i].cantidad}</td>
                        <td>${data[i].descripcion}</td>
                        <td style="text-align:right;">${data[i].precio}</td>
                        <td style="text-align:right;">${data[i].precio * data[i].cantidad}</td>
                    </tr>`;
            }

            return `
            <table border="0" width="99%" style="font-size:15px; font-family: Sans-serif, Arial;width: 250px;margin-bottom: 20px;">
              <tr><th colspan="4" style="text-align:center;"> <b><span style="font-size:25px">PRECUENTA</span></b> </th></tr>
              <tr><th colspan="4" style="text-align:center;"> <b>MESA: ${this.mesaActivo.numero}</b> </th></tr>
              <tr>
                <th><b>CANT.</b></th>
                <th><b>DESCRIPCI\xD3N</b></th>
                <th><b>P.UNIT</b></th>
                <th style="text-align:right;"><b>TOTAL</b></th>
              </tr>
              ${productsHtml}
              <tr>
              <td colspan="3" style="text-align:right;"><b>TOTAL</b></td>
              <td style="text-align:right;"> <b> ${this.total}</b></td>
              </tr>
            </table>
            <br/>`;
        },

        printTicket(html_pdf){

            if (html_pdf.length > 0)
            {
                const config = getUpdatedConfig()
                const opts = getUpdatedConfig()

                const printData = [
                    {
                        type: 'html',
                        format: 'plain',
                        data: html_pdf,
                        options: opts
                    }
                ]

                qz.print(config, printData)
                    .then(()=>{

                        this.$notify({
                            title: '',
                            message: 'Impresión en proceso...',
                            type: 'success'
                        })

                    })
                    .catch(displayError)
            }

        },
        printPdf(){

        },

        precuenta(){
            this.printTicket(this.generateHtml())
        },
       async enviar_comanda(pedido_id){
            try{
                this.loading = true;
                window.open(`/sales/${pedido_id}/comanda_pdf_print`);
                this.loading = false;

            }catch(error){
                this.loading = false;
                if(error.response) this.axiosError(error);

                this.$notify({
                    title: 'error',
                    message: 'Error al imprimir...',
                    type: 'error'
                })
                this.loading = false;
            }
        },
        handleCurrentChange(val) {
            this.page = val;
        },

                // busca la mesa y actualiza su estado, ademas de que si otro usuario, esta en la misma mesa, lo bloquea
        liberarMesa(){
            let payload = {nivel_id: this.mesaActivo.nivel_id , mesa_id: this.mesaActivo.id, estado: 0};

            this.updateTable(payload)
            if(this.socketClient) this.socketClient.emit("liberar-mesa", payload);
            this.onLimPiarDatos();
            this.vistaMesas=true;
            this.mesaIsActivo=false;
        },
        updateTable({nivel_id, mesa_id, estado}){
            this.niveles.forEach(nivel => {
                if(nivel.id == nivel_id){
                    nivel.mesas.forEach(mesa => {
                        if(mesa.id == mesa_id){
                            mesa.estado = estado;
                        }
                    })
                }
            });
        },

    }
}
</script>
<style>
.t1{
    border: 2px solid #e7e5e5;
}
.t1.active{
    border: 2px solid #47a447
}

</style>
