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
        <div class="form-group">
          <label for="placa">Placa</label>
          <input type="text" id="placa" class="form-control" v-model="form.placa" :class="{ 'is-invalid': errors.placa }"/>
          <div v-if="errors.placa" class="invalid-feedback">{{ errors.placa[0] }}</div>
        </div>

        <div class="form-group">
            <label for="nombre">Nombre</label>
            <input type="text" id="nombre" class="form-control" v-model="form.nombre" :class="{ 'is-invalid': errors.nombre }"/>
            <div v-if="errors.nombre" class="invalid-feedback">{{ errors.nombre[0] }}</div>
        </div>

<!--        <div class="form-group">-->
<!--            <label for="asientos">Asientos</label>-->
<!--            <input type="text" id="asientos" class="form-control" v-model="form.asientos" :class="{ 'is-invalid': errors.asientos }"/>-->
<!--            <div v-if="errors.asientos" class="invalid-feedback">{{ errors.asientos[0] }}</div>-->
<!--        </div>-->

        <div class="form-group">
            <label for="pisos">Pisos</label>
            <input type="text" id="pisos" class="form-control" v-model="form.pisos" :class="{ 'is-invalid': errors.pisos }"/>
            <div v-if="errors.pisos" class="invalid-feedback">{{ errors.pisos[0] }}</div>
        </div>

          <div class="form-group">
              <label for="nro_hab_veh">Nro. Habilitación Vehicular</label>
              <input type="text" id="nro_hab_veh" class="form-control" v-model="form.nro_hab_veh" :class="{ 'is-invalid': errors.nro_hab_veh }"/>
              <div v-if="errors.pisos" class="invalid-feedback">{{ errors.nro_hab_veh[0] }}</div>
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
    vehiculo: {
      type: Object,
      required: false,
      default: {},
    },
  },
  data() {
    return {
      form: {},
      title: "",
      errors: {},
      loading: false,
    };
  },
  methods: {
    onUpdate() {
      this.loading = true;
      this.$http
        .put(`/transportes/vehiculos/${this.vehiculo.id}/update`, this.form)
        .then((response) => {
          this.$emit("onUpdateItem", response.data.data);
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
    onStore() {
      this.loading = true;
      this.$http
        .post("/transportes/vehiculos/store", this.form)
        .then((response) => {
          this.$emit("onAddItem", response.data.data);
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
      if (this.vehiculo) {
        this.onUpdate();
      } else {
        this.onStore();
      }
    },
    onClose() {
      this.$emit("update:visible", false);
    },
    onCreate() {
      if (this.vehiculo) {
        this.form = this.vehiculo;
        this.title = "Editar vehículo";
      } else {
        this.title = "Crear vehículo";
        this.form = {
          active: true,
        };
      }
    },
  },
};
</script>
