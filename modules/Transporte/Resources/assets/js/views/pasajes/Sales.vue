<template>
    <div>
        <div v-loading="load">
        <div class="row pt-3">
            <div class="col-md-12 text-center card-header">
                <span class="mr-2"><a class="btn btn-primary btn-sm" href="/transportes/pasajes"> <i class="fa fa-arrow-left"></i>  </a></span>
                <div class="clock-border">
                    <span class="claro">Hora del servidor:</span>
                    <div class="clock-inner">
                        <div class="hour">{{fecha}}</div>
                        <div class="dots"> &nbsp  &nbsp</div>
                        <div class="secs">{{timer}}</div>
                        <div class="mode"></div>
                    </div>
                </div>
                <span class="claro">Venta de boletos terminal  <el-tag >{{ terminal.nombre }}</el-tag></span>
            </div>

            <div class="col-xl-3 col-md-3 col-12 card card-transparent card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="" class="control-label">Tipo de venta</label>
                            <el-select v-model="tipoVenta"
                                       id="tipo-venta"
                                       popper-class="el-select-customers"
                                       placeholder="Tipo de venta"
                                       :disabled="pasajero ? true : false"
                            >
                                <el-option :value="1" label="Venta Libre">
                                </el-option>
                                <el-option  :value="2" label="Venta Normal">
                                </el-option>
                            </el-select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="control-label">Origen</label>
                            <el-select v-model="terminalId" filterable remote  popper-class="el-select-customers"
                                       placeholder="Buscar origen"
                                       :remote-method="searchTerminales"
                                       :loading="loadingTerminales"
                                       @change="searchDestinos"
                            >
                                <el-option v-for="terminal in terminales" :key="terminal.id" :value="terminal.id" :label="terminal.nombre">
                                </el-option>
                            </el-select>

                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="control-label">Destino</label>
                            <el-select v-model="destino"
                                       value-key="id"
                                       :loading="loadingDestinos"
                                       popper-class="el-select-customers"
                                       placeholder="Destino"
                                       @change="getProgramaciones"
                            >
                                <template v-for="destino in terminales">
                                    <!--<el-option  :key="destino.terminal_destino_id" :value="destino.destino.id" :label="`${destino.destino.nombre}`">-->
                                    <el-option v-if="destino.id != terminalId"  :key="destino.id" :value="destino" :label="`${destino.nombre}`">
                                    </el-option>
                                </template>

                            </el-select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div v-if="destino" class="col-md-12">
                        <div  class="form-group">
                            <label for="" class="control-label">Fecha salida</label>
                            <el-date-picker
                                v-model="fecha_salida"
                                type="date"
                                value-format="yyyy-MM-dd"
                                placeholder="Fecha salida"
                                @change="getProgramaciones">
                            </el-date-picker>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div v-loading="loadingProgramaciones" v-if="destino && tipoVenta == 2" class="col-md-12">
                        <div v-if="programaciones.length > 0" class="row mt-2">
                            <div class="col-md-12 px-0">
                                <table class="table table-striped table-border responsive">
                                    <thead>
                                    <tr>
                                        <th>Terminal</th>
                                        <th>Vehiculo</th>
                                        <th>Hora salida</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="programacion in programaciones" :key="programacion.id">
                                        <td>{{ programacion.destino.nombre }}</td>
                                        <td>{{ programacion.vehiculo.placa }}</td>
                                        <td>{{ programacion.hora_salida.substring(0,5) }}</td>
                                        <td>
                                            <template v-if="!selectProgramacion">
                                                <el-button type="success" size="mini" @click="seleccionar(programacion)">
                                                    <i class="fa fa-check"></i>
                                                </el-button>
                                            </template>
                                            <template v-else>
                                                <el-button v-if="selectProgramacion.id == programacion.id" type="danger" size="mini" @click="quitar">
                                                    <i class="fa fa-minus-circle"></i>
                                                </el-button>
                                                <el-button v-else type="success" size="mini" @click="seleccionar(programacion)">
                                                    <i class="fa fa-check"></i>
                                                </el-button>
                                            </template>

                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <div v-else class="row mt-2">
                            <div class="col-12">
                                <el-alert
                                    title="No hay programaciones"
                                    center
                                    type="info"
                                    :closable="false">
                                </el-alert>
                            </div>
                        </div>
                    </div>
                    <div v-if="destino && tipoVenta == 1" class="col-md-6">
                        <div class="from-group">
                            <label for="" class="control-label">Hora salida</label>
                            <el-input type="time" v-model="horaSalida" id="hora-salida"></el-input>
                        </div>
                    </div>

                    <div v-if="destino && tipoVenta == 1" class="col-md-6">
                        <div class="from-group" :style="{marginTop:'1.85rem'}">
                            <el-button type="primary" @click="openModal = true" >Realizar venta</el-button>
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-xl-9 col-md-9 col-12 pl-1 pr-0">
                <div class="row card-body no-gutters align-items-start px-0">
                    <template v-if="asientos.length > 0 && tipoVenta == 2" >
                        <div class="row justify-content-center" style="width: 100%">
                            <div class="col-md-2">
                                <div v-if="vehiculo" class="row justify-content-center">
                                    <div class="form-group">
                                        <label for="" class="pl-3 text-right pr-2 control-label" style="width:40%;float: left">Piso</label>
                                        <div style="width: 60%;float: left">
                                            <el-select v-model="piso" placeholder="Piso" :disabled="vehiculo.pisos == 1">
                                                <el-option v-for="floor in vehiculo.pisos" :label="floor" :key="floor" :value="floor" >
                                                </el-option>
                                            </el-select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="" class="control-label">Disponible</label>
                                    <svg id="60611b2ba670a" gc-seat-static="0" gc-seat-element-id="2" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="51px" height="38px" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd;" viewBox="0 0 51 38" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 9.554 13.384 58.672 38.443">
                                <g id="Capa_x0020_1">
                                    <metadata id="CorelCorpID_0Corel-Layer"></metadata>
                                    <path class="fil0 str0" :style="stateAsiento(1)"  d="M12 34c1,0 1,0 2,0l-2 0z"></path>
                                    <path class="fil1 str0" :style="stateAsiento(1)" d="M12 4c-2,0 -3,0 -3,0 -5,2 -6,7 -6,15 0,8 2,14 7,15 0,0 1,0 2,0 1,-1 1,-4 0,-6 2,-9 2,-9 0,-18 1,-1 1,-4 0,-6z"></path>
                                    <path class="fil0 str0" :style="stateAsiento(1)" d="M14 4c-1,0 -2,0 -2,0l2 0z"></path>
                                    <path class="fil0 str0" :style="stateAsiento(1)" d="M49 19c0,-8 -1,-14 -3,-15 -3,-1 -10,0 -19,0 -4,0 -9,0 -13,0 1,1 1,4 0,6 2,9 2,10 0,18 1,2 1,5 0,6 4,0 8,0 13,0 8,0 17,1 19,0 2,-1 3,-7 3,-15z"></path>

                                    <path class="fil2 str1" :style="stateAsiento(1,{isSeat:true})" d="M48 29c-3,0 -6,0 -10,0 -1,0 -2,0 -3,0 -12,0 -22,-1 -24,-1 -3,-1 -1,-4 -1,-9 0,-4 -2,-8 1,-9 4,-1 19,-1 24,-1 1,0 2,0 3,0 4,0 7,0 10,0 2,2 2,18 0,20z"></path>
                                    <path class="fil3 str2" :style="stateAsiento(1,{isBelt:true})" d="M18 4c1,2 1,4 0,6 2,10 2,9 0,18 1,3 1,3 0,6l4 0c1,-1 1,-4 0,-5 2,-9 2,-11 0,-19 1,-2 1,-5 0,-6l-4 0z"></path>
                                    <path class="fil4 str1" :style="stateAsiento(1)" d="M42 34l-18 0c0,0 0,0 0,0l0 3c0,0 0,0 0,0l18 0c0,0 0,0 0,0l0 -3c0,0 0,0 0,0z"></path>
                                    <path class="fil4 str1" :style="stateAsiento(1)" d="M42 1l-18 0c0,0 0,0 0,0l0 3c0,0 0,0 0,0l18 0c0,0 0,0 0,0l0 -3c0,0 0,0 0,0z"></path>
                                </g>
                            </svg>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="" class="control-label">Ocupado</label>
                                    <svg id="60611b2ba670a" gc-seat-static="0" gc-seat-element-id="2" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="51px" height="38px" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd;" viewBox="0 0 51 38" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 9.554 13.384 58.672 38.443">
                                <g id="Capa_x0020_1">
                                    <metadata id="CorelCorpID_0Corel-Layer"></metadata>
                                    <path class="fil0 str0" :style="stateAsiento(2)"  d="M12 34c1,0 1,0 2,0l-2 0z"></path>
                                    <path class="fil1 str0" :style="stateAsiento(2)" d="M12 4c-2,0 -3,0 -3,0 -5,2 -6,7 -6,15 0,8 2,14 7,15 0,0 1,0 2,0 1,-1 1,-4 0,-6 2,-9 2,-9 0,-18 1,-1 1,-4 0,-6z"></path>
                                    <path class="fil0 str0" :style="stateAsiento(2)" d="M14 4c-1,0 -2,0 -2,0l2 0z"></path>
                                    <path class="fil0 str0" :style="stateAsiento(2)" d="M49 19c0,-8 -1,-14 -3,-15 -3,-1 -10,0 -19,0 -4,0 -9,0 -13,0 1,1 1,4 0,6 2,9 2,10 0,18 1,2 1,5 0,6 4,0 8,0 13,0 8,0 17,1 19,0 2,-1 3,-7 3,-15z"></path>

                                    <path class="fil2 str1" :style="stateAsiento(2,{isSeat:true})" d="M48 29c-3,0 -6,0 -10,0 -1,0 -2,0 -3,0 -12,0 -22,-1 -24,-1 -3,-1 -1,-4 -1,-9 0,-4 -2,-8 1,-9 4,-1 19,-1 24,-1 1,0 2,0 3,0 4,0 7,0 10,0 2,2 2,18 0,20z"></path>
                                    <path class="fil3 str2" :style="stateAsiento(2,{isBelt:true})" d="M18 4c1,2 1,4 0,6 2,10 2,9 0,18 1,3 1,3 0,6l4 0c1,-1 1,-4 0,-5 2,-9 2,-11 0,-19 1,-2 1,-5 0,-6l-4 0z"></path>
                                    <path class="fil4 str1" :style="stateAsiento(2)" d="M42 34l-18 0c0,0 0,0 0,0l0 3c0,0 0,0 0,0l18 0c0,0 0,0 0,0l0 -3c0,0 0,0 0,0z"></path>
                                    <path class="fil4 str1" :style="stateAsiento(2)" d="M42 1l-18 0c0,0 0,0 0,0l0 3c0,0 0,0 0,0l18 0c0,0 0,0 0,0l0 -3c0,0 0,0 0,0z"></path>
                                </g>
                            </svg>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="" class="control-label">Reservado</label>
                                    <svg id="60611b2ba670a" gc-seat-static="0" gc-seat-element-id="2" xmlns="http://www.w3.org/2000/svg" xml:space="preserve" width="51px" height="38px" version="1.1" style="shape-rendering:geometricPrecision; text-rendering:geometricPrecision; image-rendering:optimizeQuality; fill-rule:evenodd; clip-rule:evenodd;" viewBox="0 0 51 38" xmlns:xlink="http://www.w3.org/1999/xlink" enable-background="new 9.554 13.384 58.672 38.443">
                                <g id="Capa_x0020_1">
                                    <metadata id="CorelCorpID_0Corel-Layer"></metadata>
                                    <path class="fil0 str0" :style="stateAsiento(3)"  d="M12 34c1,0 1,0 2,0l-2 0z"></path>
                                    <path class="fil1 str0" :style="stateAsiento(3)" d="M12 4c-2,0 -3,0 -3,0 -5,2 -6,7 -6,15 0,8 2,14 7,15 0,0 1,0 2,0 1,-1 1,-4 0,-6 2,-9 2,-9 0,-18 1,-1 1,-4 0,-6z"></path>
                                    <path class="fil0 str0" :style="stateAsiento(3)" d="M14 4c-1,0 -2,0 -2,0l2 0z"></path>
                                    <path class="fil0 str0" :style="stateAsiento(3)" d="M49 19c0,-8 -1,-14 -3,-15 -3,-1 -10,0 -19,0 -4,0 -9,0 -13,0 1,1 1,4 0,6 2,9 2,10 0,18 1,2 1,5 0,6 4,0 8,0 13,0 8,0 17,1 19,0 2,-1 3,-7 3,-15z"></path>

                                    <path class="fil2 str1" :style="stateAsiento(3,{isSeat:true})" d="M48 29c-3,0 -6,0 -10,0 -1,0 -2,0 -3,0 -12,0 -22,-1 -24,-1 -3,-1 -1,-4 -1,-9 0,-4 -2,-8 1,-9 4,-1 19,-1 24,-1 1,0 2,0 3,0 4,0 7,0 10,0 2,2 2,18 0,20z"></path>
                                    <path class="fil3 str2" :style="stateAsiento(3,{isBelt:true})" d="M18 4c1,2 1,4 0,6 2,10 2,9 0,18 1,3 1,3 0,6l4 0c1,-1 1,-4 0,-5 2,-9 2,-11 0,-19 1,-2 1,-5 0,-6l-4 0z"></path>
                                    <path class="fil4 str1" :style="stateAsiento(3)" d="M42 34l-18 0c0,0 0,0 0,0l0 3c0,0 0,0 0,0l18 0c0,0 0,0 0,0l0 -3c0,0 0,0 0,0z"></path>
                                    <path class="fil4 str1" :style="stateAsiento(3)" d="M42 1l-18 0c0,0 0,0 0,0l0 3c0,0 0,0 0,0l18 0c0,0 0,0 0,0l0 -3c0,0 0,0 0,0z"></path>
                                </g>
                            </svg>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div style="width: 100%" v-loading="loadAsientosOcupados"></div>
                    <div v-if="asientos.length > 0 && tipoVenta == 2 && vehiculo" style="width: 100%">
                        <bus v-if="piso == 1" :change-color="changeColor" :image-front="vehiculo.img_front" :image-back="vehiculo.img_back" :seats.sync="asientosPisoUno" :ancho-vehiculo="vehiculo.ancho_vehiculo" @dbclick="dbClick"  />
                        <bus v-if="piso == 2" :change-color="changeColor" :image-front="vehiculo.img_front" :image-back="vehiculo.img_back"  :seats.sync="asientosPisoDos" :ancho-vehiculo="vehiculo.ancho_vehiculo" @dbclick="dbClick"  />
                    </div>
                </div>
                <div class="row card-body no-gutters">
                    <div class="col-md-8 text-left">
                        <p><b>Leyenda( Se vendió en el terminal de )</b></p>

                        <ul class="list-group flex-md-row">
                            <li class="list-group-item" v-for="terminal in terminales" :key="terminal.id" :value="terminal.id" :style="{'background':terminal.color,'padding': '3px 5px','margin': '0 5px'}">
                             <b class="text-white">{{terminal.nombre}}</b>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-2 text-left">
                        <p><b>Manifiesto</b></p>
                        <el-button v-if="existe_manifiesto"  type="primary" @click="imprimirManifiesto(id_manifiesto)">Imprimir</el-button>
                        <el-button v-else type="primary" @click="generarManifiesto">Generar</el-button>
                    </div>
                    <div class="col-md-2 text-left">
                        <p><b>Traspaso</b></p>
                        <el-button  type="primary" @click="traspasarPasajeros">Traspasar</el-button>
                    </div>

                    <div class="col-md-12" v-if="selectProgramacion.id>0">
                        <h3>Listado de pasajeros vendidos</h3>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                <tr>
                                    <th>ASIENTO</th>
                                    <th>RUTA</th>
                                    <th>DNI</th>
                                    <th>NOMBRES</th>
                                    <th>TIPO</th>
                                    <th>CELULAR</th>
                                    <th>PRECIO</th>
                                    <th>VENDEDOR</th>
                                </tr>
                                </thead>
                                <tbody>
                                <template v-if="asientosOcupados.length > 0">
                                    <tr v-for="pasaje in asientosOcupados" :key="pasaje.id" :class="{'border-left border-info': (pasaje.nombre_pasajero),'text-success border-left border-success': (pasaje.document)}">
                                        <td><b>{{ pasaje.numero_asiento }}</b></td>
                                        <td>{{ pasaje.origen.nombre + ' - ' + pasaje.destino.nombre }}</td>
                                        <td>{{ (pasaje.pasajero)? pasaje.pasajero.number : '' }}</td>
                                        <td>{{ (pasaje.pasajero)? pasaje.pasajero.name : pasaje.nombre_pasajero }}</td>
                                        <td>{{ (pasaje.nombre_pasajero) ? "RESERVA" : "VENTA" }}</td>
                                        <td>{{ (pasaje.pasajero)? pasaje.pasajero.telephone : pasaje.telefono }}</td>
                                        <td>{{ pasaje.precio }}</td>
                                        <td>{{ pasaje.user_name }}</td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr>
                                        <td colspan="9">
                                            <el-alert type="info" title="No hay pasajes"  center/>
                                        </td>
                                    </tr>
                                </template>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <venta-asiento-libre
            @onCancel=" onCancel  "
            :visible.sync="openModal"
            :asiento="asiento"
            :tipo-venta="tipoVenta"
            :transportePasaje="pasajero"
            :estadosAsientos="estadoAsientos"
            :programacion="selectProgramacion"
            :fechaSalida="fecha_salida"
            @onUpdateItem="onUpdateItem"
            :establishment="establishment"
            :allSeries="series"
            :document-types-invoice="documentTypesInvoice"
            :payment-method-types="paymentMethodTypes"
            :payment-destinations="paymentDestinations"
            :configuration="configuration"
            @onSuccessVenta="onSuccessVenta"
            @anularBoleto="anularBoleto"
            @notificationAll="notificationAll"
            :document_type_03_filter="document_type_03_filter"
            :is-cash-open="isCashOpen"
            :destino="destino"
            :origen="origen"
            :horaSalida="horaSalida"
            :reloj="reloj"
            :fecha="fecha"
            :hora="hora"
        />

        <document-options
        :showDialog.sync="showDialogDocumentOptions"
        :recordId="documentId"
        :isContingency="false"
        :showClose="true"
        :configuration="configuration"
        ></document-options>

        <documents-voided
        :showDialog.sync="showDialogVoided"
        :recordId="documentId"></documents-voided>

        <detalle-boleto
        :tipo-venta.sync="tipoVenta"
        :document-types-invoice="documentTypesInvoice"
        :visible.sync="visible"
        :programacion="selectProgramacion"
        :estados-asientos="estadoAsientos"
        :fecha-salida="fecha_salida"
        :establishment="establishment"
        :series="series"
        :payment-destinations="paymentDestinations"
        :payment-method-types="paymentMethodTypes"
        :configuration="configuration"
        :asiento="asiento"
        :pasaje="pasajero"
        :user="user"
        @anularBoleto="anularBoleto"
        @onSuccessVenta="onSuccessVenta"
        @onUpdateItem="onUpdateItem"
        @notificationAll="notificationAll"
        />


        <documents-voided
        :showDialog.sync="showDialogVoided"
        :recordId="documentId"></documents-voided>

        <generar-manifiesto
            :visible.sync="modalManifiestoVisible"
            :series="series"
            :fecha="fecha_salida"
            :programacion="selectProgramacion"
            :choferes="choferes"
            :origen="origen"
            :destino="destino"
            :existe_manifiesto.sync="existe_manifiesto"
        />
        <generar-traspaso
            :visible.sync="modalTraspasoVisible"
            :series="series"
            :fecha="fecha_salida"
            :programacion="selectProgramacion"
            :choferes="choferes"
            :origen="origen"
            :destino="destino"
            :asientos_ocupados="asientosOcupados"
        />

    </div>
    </div>
</template>
<style>
.clock-border {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 380px;
    padding: 3px 10px;
    height: auto;
    background: black;
    color: #fff;
}
.clock-inner {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 230px;
    height: auto;
    color: white;
    font-size: 15pt
}
.dots {
    font-size: 15px;
}
.red {
    color: red;
}
.yellow {
    color: yellow;
}
.green {
    color: green;
}
</style>
<script>
import Bus from '../bus/Bus';
import VentaAsientoLibre from './VentaAsientoLibre.vue';
import DocumentOptions from "@views/documents/partials/options.vue";
import DocumentsVoided from '@views/documents/partials/voided.vue';
import DetalleBoleto from './DetalleBoleto.vue';
import GenerarManifiesto from './../manifiestos/GenerarManifiestoSimple.vue';
import GenerarTraspaso from './../manifiestos/GenerarTraspasoSimple.vue';
import SocketClient from '@mixins/socket.js'

import moment from 'moment';

export default {
    mixins:[SocketClient],
    props:{
        configuracionSocket:{
            type: Object,
            default: () => ({})
        },
        isCashOpen:{
            type:Boolean,
            required:true,
            default:false,
        },
        document_type_03_filter:{
            type:Boolean,
            required:true,
        },
        terminal:{
            type:Object,
            required:true,
            default:{},
        },
        estadoAsientos:{
            type:Array,
            required:true,
            default:() => []
        },
        establishment:{
            type:Object,
            required:true
        },
        series:{
            type:Array,
            default:() => []
        },
        documentTypesInvoice:{
            type:Array,
            default:() => []
        },
        paymentMethodTypes: {
            type: Array,
            required: true,
        },
        paymentDestinations: {
            type: Array,
            required: true,
        },
        configuration:{
            type: Object,
            required: true,
        },
        itemPasajero:{
            type:Object|null,
            default:() => null
        },
        user:{
            type:Object,
            required:true,
        },
        choferes:{
            type:Array,
            default:() => []
        },

    },
    components:{
        Bus,
        VentaAsientoLibre,
        DocumentOptions,
        DocumentsVoided,
        DetalleBoleto,
        GenerarManifiesto,
        GenerarTraspaso
    },
    watch:{
        terminalId(newVal){
            this.origen = this.terminales.find( ter => ter.id == this.terminalId );
        },
        // tipoVenta(){
        //     this.asiento = null;
        // }
        // destinoId(newVal){
        //     let exist = this.destinos.find( destino => destino.destino.id == newVal );
        //     if(exist) this.destino = exist.destino;
        // }
    },
    mounted() {
        this.setTime();
    },
    async created(){
        this.load = true;
        // this.searchCiudad();
        this.$eventHub.$on('reloadData',async() => {
            this.cancelarBoleto();
        });

        await this.searchTerminales();
        this.terminalId = this.terminal.id;
        // await this.searchDestinos();
        await this.onCreate();
        this.load = false;

        this.initSocket();

    },
    data(){
        return ({
            openModal:false,
            load:false,
            tipoVenta:2,
            visibleAsientoLibre:false,
            pasajero:this.itemPasajero,
            pasajeroId:null,
            showDialogVoided:false,
            vehiculo:null,
            guardarSeats:false,
            asientos:[],
            loadingCiudades:false,
            ciudades:[],
            ciudad:null,


            terminales:[],
            ruta:null,


            fecha_salida: moment().format('YYYY-MM-DD'),
            destinos:[],

            loadingProgramaciones:false,
            programaciones:[],
            selectProgramacion:{},
            piso:1,

            visible:false,
            asiento:null,

            //document
            showDialogDocumentOptions:false,
            documentId:null,

            loadingDestinos:false,
            terminalId:null,

            loadingTerminales:false,
            horaSalida:null,
            destino:null,
            origen:null,

            reloj:null,
            fecha:null,
            hora:null,
            socketClient:null,


            timeHora: null,
            timeMinuto: null,
            timeSegundo: null,
            asientosOcupados: [],
            loadAsientosOcupados:false,
            modalManifiestoVisible:false,
            existe_manifiesto:false,
            id_manifiesto:null,
            modalTraspasoVisible:false
        });
    },
    computed:{

        asientosPisoUno:function(){
            return this.asientos.filter(  asiento => asiento.piso == 1);
        },
        asientosPisoDos:function(){
            return this.asientos.filter(  asiento => asiento.piso == 2);
        },

        timer(){

            if(this.timeHora == null && this.timeMinuto == null && this.timeSegundo == null) return '';

            const formatHora = this.timeHora < 10 ? `0${this.timeHora}` : this.timeHora;
            const formatMinuto = this.timeMinuto < 10 ? `0${this.timeMinuto}` : this.timeMinuto;
            const formatSegundo = this.timeSegundo < 10 ? `0${this.timeSegundo}` : this.timeSegundo;
            return `${formatHora}:${formatMinuto}:${formatSegundo}`
        }

    },
    methods:{

        changeColor(asiento,config){

            const existePasaje = this.asientosOcupados.find(  item => item.asiento_id == asiento.id );

            if(existePasaje) {
                if(existePasaje.estado_asiento_id == 2){
                    if(config.isBelt){ //Hay un path que se pinta de azul
                        return{
                            fill:'#fff'
                        };
                    }

                    return {
                        fill: existePasaje.origen.color || '#ff0000', // '#ff0000'
                        animation:'none',
                        color: '#fff'
                    }
                }

                if(existePasaje.estado_asiento_id == 3){
                    if(config.isSeat){
                        return {
                            fill:'#fff',
                            animation:'reservado 1s infinite'
                        }
                    }

                    return {
                        fill:'#fff',
                        animation:'none'
                    }
                }
            }

            if(this.asiento) {
                if(asiento.id == this.asiento.id){
                    if(config.isSeat){
                        return {
                            fill:'#ff6600',
                            animation:'reservado 1s infinite'
                        }
                    }
                    return {
                        fill:'#ff6600',
                        animation:'none'
                    }

                }
            }

            return {
                fill:'#fff',
                animation:'none'
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

                this.socketClient.on('venta-completada', (params) => {
                    if( this.selectProgramacion.id ) this.onUpdateItem();
                });


            }catch(error){
               this.socketClient = null;
            }

        },
        nuevaVenta(){
            this.pasajero = {};
            this.selectProgramacion = {};
            this.programaciones = [];
            this.destino = null;
            this.fecha_salida = null;
            //this.horaSalida = null;
            this.origen = null;
        },
        stateAsiento(estado,config={}){

            /** Manejo de los estados del asiento */
            if(estado == 1){//Disponible
                return {
                    fill:'#fff',
                    animation:'none'
                }
            }else if(estado == 2 ){ //Ocupado

                if(config.isBelt){ //Hay un path que se pinta de azul
                    return{
                        fill:'#fff'
                    };
                }
                return {
                    fill: this.terminal.color || '#ff0000',
                    animation:'none'
                }

            }else if(estado == 3){ //Reservado

                if(config.isSeat){
                    return {
                        fill:'#fff',
                        animation:'reservado 1s infinite'
                    }
                }

                return {
                    fill:'#fff',
                    animation:'none'
                }



            }
        },

        async onCreate(){
            if(this.itemPasajero){
                this.tipoVenta = this.itemPasajero.tipo_venta;
                this.terminalId = this.itemPasajero.origen_id;
                this.fecha_salida = this.itemPasajero.fecha_salida;
                this.destino =  this.destinos.find(dest => dest.id ==  this.itemPasajero.destino_id);
                this.asiento = this.itemPasajero.asiento;
                this.horaSalida = this.itemPasajero.hora_salida;
                if(this.tipoVenta == 2){
                    await this.getProgramaciones();
                    let programacion = this.programaciones.find( p  => p.id === this.itemPasajero.programacion.id)
                    this.seleccionar(programacion);
                }

            }

        },
        changeTipoVenta(){

            if(this.tipoVenta == 2){
                let programacion = this.programaciones.find( p  => p.id === this.itemPasajero.programacion.id)
                this.seleccionar(programacion);
            }

        },

        notificationAll(){
            if(this.socketClient) this.socketClient.emit('venta-completada',true);
        },
        async onSuccessVenta(documentId){
            this.documentId = documentId;
            this.showDialogDocumentOptions = true;
        },

        quitar(){
            this.selectProgramacion = {};
            this.asientos = [];
            this.existe_manifiesto = false
        },

        async getProgramaciones(){
            if(this.fecha_salida){
                this.loadingProgramaciones = true;
                this.programaciones = [];
                this.selectProgramacion = {};
                this.asientos = [];
                let data = {
                    origen_id:this.terminalId,
                    destino_id:this.destino.id,
                    fecha_salida:this.fecha_salida
                }
                const { data:programaciones } = await this.$http.post(`/transportes/sales/programaciones-disponibles`,data);
                this.loadingProgramaciones = false;
                this.programaciones = programaciones.programaciones;
            }

        },
        async searchTerminales(input = ''){
            this.loadingTerminales = true;
            const { data } = await this.$http.get(`/transportes/encomiendas/get-terminales?search=${input}`);
            this.loadingTerminales = false;
            this.terminales = data.terminales;
        },

        dbClick(asiento){
            this.pasajero = null;

            if(this.terminalId !== this.terminal.id){
                alert('Para evitar confusiones, solo se puede visualizar los estados, mas nó vender. Cambie a su terminal asignado por el administrador.')
                return
            }

            if(asiento.type != 'ss') return;

            const existePasaje = this.asientosOcupados.find(  item => item.asiento_id == asiento.id );

            if(existePasaje){
                this.pasajero = existePasaje;
                this.asiento = asiento;
                this.asiento.estado_asiento_id = existePasaje.estado_asiento_id;
                this.visible = true;
                return;
            }

            this.asientos = this.asientos.map( seat => {
                if(seat.estado_asiento_id == 4){
                    seat.estado_asiento_id = 1;
                }
                return seat;
            } );
            asiento.estado_asiento_id = 4;
            this.openModal = true;
            this.asiento = asiento;
        },

        searchCiudad(value= ''){
            this.loadingCiudades = true;
            this.$http.get(`/transportes/sales/get-ciudades?search=${value}`)
            .then( response => {
                const { data } = response;
                this.ciudades = data.data;
                this.loadingCiudades = false;
            }).catch(error => {
                this.axiosError(error);
            })
        },
        async seleccionar(programacion){
            this.visibleAsientoLibre = false;
            this.selectProgramacion = programacion;

            if(this.tipoVenta == 2){
                this.loadAsientosOcupados = true;
                this.asientosOcupados = await this.getAsientosOcupados(programacion, this.fecha_salida)

                this.vehiculo = programacion.vehiculo;

                this.asientos = programacion.vehiculo.seats;

                this.loadAsientosOcupados = false;

                this.verificarManifiesto(this.selectProgramacion);

                this.$nextTick(() => this.$forceUpdate());
            }
        },

        async verificarManifiesto(programacion){

            let form ={
                programacion: programacion,
                fecha_salida: this.fecha_salida
            }
            try{
                const { data } = await this.$http.post('/transportes/manifiestos/verificar-manifiesto',form);

                if(data.success){
                    this.existe_manifiesto = true
                    this.id_manifiesto=data.manifiesto
                }

            }catch(error){
                return null;
            }
        },
        async getAsientosOcupados(programacion, fechaSalida){

            const {data} = await this.$http.post('/transportes/sales/obtener-asientos',{
                programacion_id:programacion.id,
                fecha_salida: fechaSalida
            });

            return data;


        },

        async onUpdateItem(){
            this.pasajero = {};
            if(this.tipoVenta == 2){
                this.visibleAsientoLibre = true;
                // let program = this.selectProgramacion;
                // await this.getProgramaciones();
                // this.selectProgramacion = this.programaciones
                // .find(  programacion => programacion.id == program.id );
                this.asientos = this.selectProgramacion.vehiculo.seats;
                this.vehiculo = this.selectProgramacion.vehiculo;
                this.asientosOcupados = await this.getAsientosOcupados(this.selectProgramacion, this.fecha_salida);
                this.visibleAsientoLibre = false;
            }else if(this.tipoVenta == 1){
                this.destino = null;
                this.horaSalida = null;
            }

        },

        imprimirManifiesto(id){
            window.open(`/transportes/manifiestos/${id}/imprimir-manifiesto`);
        },
        generarManifiesto(){
            if(this.selectProgramacion.id){
                this.tipo = 2;
                this.modalManifiestoVisible = true;
            }else {
                this.$message({
                    type: 'warning',
                    message: 'Seleccione una programación.'
                });
            }

        },

        traspasarPasajeros(){
            if(this.selectProgramacion.id){
                this.tipo = 2;
                this.modalTraspasoVisible = true;
            }else {
                this.$message({
                    type: 'warning',
                    message: 'Seleccione una programación.'
                });
            }
        },

        anularBoleto(pasaje){
            this.documentId = pasaje.document_id;
            this.pasajeroId = pasaje.id;
            this.showDialogVoided = true;

        },
        async cancelarBoleto(){
            try{
                const { data } = await axios.delete(`/transportes/pasajes/${this.pasajeroId}/delete`);

                if(!data.success){
                    this.$message({
                        type: 'error',
                        message: data.message
                    });
                }

                this.$message({
                    type: 'success',
                    message: data.message
                });

                this.onUpdateItem();

            }catch(error){

                this.$message({
                    type: 'error',
                    message: 'Lo sentimos ha ocurrido un error'
                });

            }
        },
        async searchDestinos(){
            this.loadingDestinos = true;
            const { data } = await this.$http.get(`/transportes/pasajes/${this.terminalId}/get-destinos`);
            this.loadingDestinos = false;
            this.destinos = data.destinos;

            this.getProgramaciones()
        },
        onCancel(){
            this.pasajero = {};
            if(this.asiento){
                this.asiento.estado_asiento_id = 1;
                this.asiento = null;
            }
        },
        async setTime(){
           const {data} = await this.$http.get(`/documents/fecha-actual`);

            this.fecha  = data.fecha
            this.hora  = data.hora

            const time = moment(`${this.fecha} ${this.hora}`);

            this.timeHora = time.hours();
            this.timeMinuto = time.minutes();
            this.timeSegundo = time.seconds();

            this.startTimer();
        },
        startTimer(){
            setInterval(() => {

                this.timeSegundo++;

                if(this.timeSegundo >= 60){
                    this.timeMinuto++;
                    this.timeSegundo = 0;
                }

                if(this.timeMinuto >= 60){
                    this.timeHora++;
                    this.timeMinuto = 0;
                }

                if(this.timeHora > 23){
                    this.timeHora = 0;
                    this.timeMinuto = 0;
                }

            }, 1000);
        }
    }
}
</script>
