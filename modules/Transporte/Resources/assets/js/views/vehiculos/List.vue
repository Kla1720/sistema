<template>
  <div>
    <div class="page-header pr-0">
      <h2>
        <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
      </h2>
      <ol class="breadcrumbs">
        <li class="active"><span>REGISTRO DE VEHÍCULOS</span></li>
      </ol>
      <div class="right-wrapper pull-right">
        <div class="btn-group flex-wrap">
          <button
            type="button"
            class="btn btn-custom btn-sm mt-2 mr-2"
            @click="onCreateVehiculo"
          >
            <i class="fa fa-plus-circle"></i> Nuevo
          </button>
        </div>
      </div>
    </div>
    <div class="card mb-0" v-if="!vehiculoConfig">
      <div class="card-header bg-info">
        <h3 class="my-0">Listado de vehiculos</h3>
      </div>
      <div class="card-body">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th></th>
                <th>Placa</th>
                <th class="text-center">Nombre</th>
                <th>Asientos</th>
                <th>Pisos</th>
                <th>Nro. Hab. Vehicular</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in items" :key="item.id">
                <td class="text-right">{{ item.id }}</td>
                <td>{{ item.placa }}</td>
                <td class="text-center">{{ item.nombre}}</td>
                <td>{{ item.asientos }}</td>
                <td>{{ item.pisos }}</td>
                <td>{{ item.nro_hab_veh }}</td>
                <td class="text-center">
                  <el-button type="success" @click="onEdit(item)">
                    <i class="fa fa-edit"></i>
                  </el-button>
                  <el-button type="danger" @click="onDelete(item)">
                    <i class="fa fa-trash"></i>
                  </el-button>
                  <el-button type="primary" @click="onEditSeats(item)">
                     <i class="fa fa-cogs"></i>
                  </el-button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <div v-else>
      <div class="card-header bg-info" :style="{justifyContent:'unset'}">
        <el-button type="primary" @click="onBack">
          <i class="fa fa-arrow-left"></i>
        </el-button>
        <h3 class="my-0 m-auto">Configuración de asientos {{ vehiculo.placa }}</h3>
      </div>
      <div class="card-body">

        <config-vehiculo :seats.sync="asientos" :vehiculo.sync="vehiculo" @input-transporte="onUpdateItem" />

      </div>
    </div>
    <ModalAddEdit
      :visible.sync="openModalAddEdit"
      @onAddItem="onAddItem"
      @onUpdateItem="onUpdateItem"
      :vehiculo="vehiculo"
    ></ModalAddEdit>
  </div>
</template>

<script>
import ModalAddEdit from "./AddEdit";
import ConfigVehiculo from './ConfigVehiculo';

export default {
  props: {
    vehiculos: {
      type: Array,
      required: true,
    },
  },
  components: {
    ModalAddEdit,
    ConfigVehiculo
  },
  data() {
    return {
      items: [],
      vehiculo: null,
      openModalAddEdit: false,
      loading: false,
      vehiculoConfig:false,
      asientos:[]
    };
  },
  mounted() {
    this.items = this.vehiculos;
  },
  methods: {
    onDelete(item) {
      this.$confirm(`¿Estás seguro de eliminar el vehiculo ${item.placa}?`, 'Atención', {
          confirmButtonText: 'Si, continuar',
          cancelButtonText: 'No, cerrar',
          type: 'warning'
        }).then(() => {
          this.$http.delete(`/transportes/vehiculos/${item.id}/delete`).then(response => {
            this.$message({
              type: 'success',
              message: response.data.message
            });
            this.items = this.items.filter(i => i.id !== item.id);
          }).catch(error => {
            let response = error.response;
            this.$message({
              type: 'error',
              message: response.data.message
            });
          });
        }).catch();
    },
    onEdit(item) {
      this.vehiculo = { ...item };
      this.openModalAddEdit = true;
    },
    onUpdateItem(data) {
      this.items = this.items.map((i) => {
        if (i.id === data.id) {
          return data;
        }
        return i;
      });
    },
    onAddItem(data) {
      this.items.unshift(data);
    },
    onCreateVehiculo() {
      this.vehiculo = null;
      this.openModalAddEdit = true;
    },
    onEditSeats(vehiculo){
      this.vehiculo = vehiculo;
      this.asientos = this.vehiculo.seats;
      this.vehiculoConfig = true;
    },
    onBack(evt){
      this.vehiculoConfig = false;
      this.vehiculo = null;
      this.asientos = [];

    }
  },
};
</script>
