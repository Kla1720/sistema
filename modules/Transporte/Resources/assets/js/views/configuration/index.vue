<template>
    <div>
      <div class="page-header pr-0">
        <h2><a href="#"><i class="fas fa-cogs"></i></a></h2>
        <ol class="breadcrumbs">
          <li class="active"><span>Configuración</span></li>
          <li><span class="text-muted">Transporte</span></li>
        </ol>
      </div>
      <template>
        <form autocomplete="off">
          <el-tabs v-model="activeName" type="border-card" class="rounded">
              <el-tab-pane class="mb-3"  name="five">
                  <span slot="label">Afectación IGV</span>
                  <div class="row">
                      <div class="col-md-3">
                          <div class="form-group">
                              <label class="control-label">Boletos/Pasajes ¿IGV Grabado?</label>
                              <el-checkbox v-model="form.pasaje_afecto_igv" :checked="form.pasaje_afecto_igv===true"></el-checkbox>
                          </div>
                      </div>
                      <div class="col-md-3">
                          <div class="form-group">
                              <label class="control-label">Encomiendas ¿IGV Grabado? </label>
                              <el-checkbox v-model="form.encomienda_afecto_igv"  :checked="form.encomienda_afecto_igv===true"></el-checkbox>
                          </div>
                      </div>
                  </div>
                  <div class="row text-center mt-5">
                      <div class="col-3">
                          <el-button
                              @click.prevent="submit"
                              :disabled="loading"
                              type="primary"
                              class="btn-block"
                              :loading="loading"
                          >Guardar</el-button
                          >
                      </div>
                  </div>
              </el-tab-pane>
          </el-tabs>
        </form>
      </template>
    </div>
</template>

<style>
.el-tabs__header,
.el-tabs__nav-wrap {
    border-top-right-radius: 5px;
    border-top-left-radius: 5px ;
}

.el-tag {
  margin-left: 2px;
}
</style>

<script>

export default {
    data() {
      return {
        resource: 'transportes',
        activeName :"five",
        errors: {},
        form: {
            pasaje_afecto_igv: Boolean,
            encomienda_afecto_igv: Boolean
        },
      }
    },
    computed: {

    },
    created() {
        this.getRecords()
    },
    mounted() {

    },
    methods: {
      async getRecords() {
        await this.$http.get(`/${this.resource}/configuration/record`).then(response => {
          if (response.data !== '') {
            this.form = response.data.data;
            console.log(this.form)
          }
        });
      },
      submit() {
        this.$http.post(`/${this.resource}/configuration`, this.form).then(response => {
          let data = response.data;
          if (data.success) {
            this.$message.success(data.message);
          } else {
            this.$message.error(data.message);
          }
          if (data !== undefined && data.configuration !== undefined) {
            this.form = data.configuration
          }
        }).catch(error => {
          if (error.response.status === 422) {
            this.errors = error.response.data.errors;
          } else {
            console.log(error);
          }
        })
      }
    }
}
</script>
