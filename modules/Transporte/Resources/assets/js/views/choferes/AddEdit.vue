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
              <label for="dni">Dni</label>
              <input type="text" id="dni" class="form-control" v-model="form.dni" :class="{ 'is-invalid': errors.dni }"/>
              <div v-if="errors.dni" class="invalid-feedback">{{ errors.dni[0] }}</div>
          </div>
          <div class="form-group">
              <label for="nombre">Nombre</label>
              <input type="text" id="nombre" class="form-control" v-model="form.nombre" :class="{ 'is-invalid': errors.nombre }"/>
              <div v-if="errors.nombre" class="invalid-feedback">{{ errors.nombre[0] }}</div>
          </div>
          <div class="form-group">
              <label for="licencia">Licencia</label>
              <input type="text" id="licencia" class="form-control" v-model="form.licencia" :class="{ 'is-invalid': errors.licencia }"/>
              <div v-if="errors.licencia" class="invalid-feedback">{{ errors.licencia[0] }}</div>
          </div>
          <div class="form-group">
              <label for="categoria">Categoria</label>
              <input type="text" id="categoria" class="form-control" v-model="form.categoria" :class="{ 'is-invalid': errors.categoria }"/>
              <div v-if="errors.categoria" class="invalid-feedback">{{ errors.categoria[0] }}</div>
          </div>

        <div class="row text-center">
          <div class="col-6">
            <el-button
              native-type="submit"
              :disabled="loading"
              type="primary"
              class="btn-block"
              :loading="loading"
              >Guardar</el-button
            >
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
    chofer: {
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
        .put(`/transportes/choferes/${this.chofer.id}/update`, this.form)
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
        .post("/transportes/choferes/store", this.form)
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
      if (this.chofer) {
        this.onUpdate();
      } else {
        this.onStore();
      }
    },
    onClose() {
      this.$emit("update:visible", false);
    },
    onCreate() {
      if (this.chofer) {
        this.form = this.chofer;
        this.title = "Editar conductor";
      } else {
        this.title = "Crear conductor";
        this.form = {
          active: true,
        };
      }
    },
  },
};
</script>
