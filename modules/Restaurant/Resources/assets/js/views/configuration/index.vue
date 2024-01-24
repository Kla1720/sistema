<template>
    <div>
      <div class="page-header pr-0">
        <h2><a href="#"><i class="fas fa-cogs"></i></a></h2>
        <ol class="breadcrumbs">
          <li class="active"><span>Configuración</span></li>
          <li><span class="text-muted">Restaurante</span></li>
        </ol>
      </div>
      <template>
        <form autocomplete="off">
          <el-tabs v-model="activeName" type="border-card" class="rounded">
<!--            <el-tab-pane class="mb-3"  name="first">-->
<!--              <span slot="label">Ambientes</span>-->
<!--              <div class="row">-->
<!--                <div class="col-md-12">-->
<!--                  <div class="row mt-4">-->
<!--                    <div class="col-md-4">-->
<!--                    <label class="control-label">-->
<!--                      Habilitar Ambiente 1-->
<!--                    </label>-->
<!--                    <div :class="{'has-danger': errors.enabled_environment_1}"-->
<!--                          class="form-group">-->
<!--                      <el-switch v-model="form.enabled_environment_1"-->
<!--                                  active-text="Si"-->
<!--                                  inactive-text="No"-->
<!--                                  @change="submit"></el-switch>-->
<!--                      <small v-if="errors.enabled_environment_1"-->
<!--                              class="form-control-feedback"-->
<!--                              v-text="errors.enabled_environment_1[0]"></small>-->
<!--                      <br>-->
<!--                      <label class="control-label">Cantidad de mesas</label>-->
<!--                      <el-slider-->
<!--                        :disabled="!form.enabled_environment_1"-->
<!--                        v-model="form.tables_quantity"-->
<!--                        :step="1"-->
<!--                        :min="5"-->
<!--                        :max="50"-->
<!--                        show-stops-->
<!--                        @change="submit">-->
<!--                      </el-slider>-->
<!--                    </div>-->
<!--                  </div>-->
<!--                  <div class="col-md-4">-->
<!--                    <label class="control-label">-->
<!--                      Habilitar Ambiente 2-->
<!--                    </label>-->
<!--                    <div :class="{'has-danger': errors.enabled_environment_2}"-->
<!--                          class="form-group">-->
<!--                      <el-switch v-model="form.enabled_environment_2"-->
<!--                                  active-text="Si"-->
<!--                                  inactive-text="No"-->
<!--                                  @change="submit"></el-switch>-->
<!--                      <small v-if="errors.enabled_environment_2"-->
<!--                              class="form-control-feedback"-->
<!--                              v-text="errors.enabled_environment_2[0]"></small>-->
<!--                      <br>-->
<!--                      <label class="control-label">Cantidad de mesas</label>-->
<!--                      <el-slider-->
<!--                        :disabled="!form.enabled_environment_2"-->
<!--                        v-model="form.tables_quantity_environment_2"-->
<!--                        :step="1"-->
<!--                        :min="5"-->
<!--                        :max="50"-->
<!--                        show-stops-->
<!--                        @change="submit">-->
<!--                      </el-slider>-->
<!--                    </div>-->
<!--                  </div>-->
<!--                    <div class="col-md-4">-->
<!--                      <label class="control-label">-->
<!--                        Habilitar Ambiente 3-->
<!--                      </label>-->
<!--                      <div :class="{'has-danger': errors.enabled_environment_3}"-->
<!--                            class="form-group">-->
<!--                        <el-switch v-model="form.enabled_environment_3"-->
<!--                                    active-text="Si"-->
<!--                                    inactive-text="No"-->
<!--                                    @change="submit"></el-switch>-->
<!--                        <small v-if="errors.enabled_environment_3"-->
<!--                                class="form-control-feedback"-->
<!--                                v-text="errors.enabled_environment_3[0]"></small>-->
<!--                        <br>-->
<!--                        <label class="control-label">Cantidad de mesas</label>-->
<!--                        <el-slider-->
<!--                          :disabled="!form.enabled_environment_3"-->
<!--                          v-model="form.tables_quantity_environment_3"-->
<!--                          :step="1"-->
<!--                          :min="5"-->
<!--                          :max="50"-->
<!--                          show-stops-->
<!--                          @change="submit">-->
<!--                        </el-slider>-->
<!--                      </div>-->
<!--                    </div>-->
<!--                    <div class="col-md-4">-->
<!--                      <label class="control-label">-->
<!--                        Habilitar Ambiente 4-->
<!--                      </label>-->
<!--                      <div :class="{'has-danger': errors.enabled_environment_4}"-->
<!--                            class="form-group">-->
<!--                        <el-switch v-model="form.enabled_environment_4"-->
<!--                                    active-text="Si"-->
<!--                                    inactive-text="No"-->
<!--                                    @change="submit"></el-switch>-->
<!--                        <small v-if="errors.enabled_environment_4"-->
<!--                                class="form-control-feedback"-->
<!--                                v-text="errors.enabled_environment_4[0]"></small>-->
<!--                        <br>-->
<!--                        <label class="control-label">Cantidad de mesas</label>-->
<!--                        <el-slider-->
<!--                          :disabled="!form.enabled_environment_4"-->
<!--                          v-model="form.tables_quantity_environment_4"-->
<!--                          :step="1"-->
<!--                          :min="5"-->
<!--                          :max="50"-->
<!--                          show-stops-->
<!--                          @change="submit">-->
<!--                        </el-slider>-->
<!--                      </div>-->
<!--                    </div>-->
<!--                  </div>-->
<!--                </div>-->
<!--              </div>-->
<!--            </el-tab-pane>-->
            <el-tab-pane class="mb-3"  name="second">
              <span slot="label">Usuarios</span>
              <div class="row d-flex align-items-end">
                <div class="col-sm-4 col-md-4 mt-4">
                  <div :class="{'has-danger': errors.user_id}"
                        class="form-group">
                    <label class="control-label">Usuario
                    </label>
                    <el-select v-model="form_role.user_id"
                                filterable>
                      <el-option v-for="option in users"
                                  :key="option.id"
                                  :label="option.name"
                                  :value="option.id"></el-option>
                    </el-select>
                    <small v-if="errors.user_id"
                            class="form-control-feedback"
                            v-text="errors.user_id[0]"></small>
                  </div>
                </div>
                <div class="col-sm-4 col-md-4 mt-4">
                  <div :class="{'has-danger': errors.role_id}"
                        class="form-group">
                    <label class="control-label">Rol
                    </label>
                    <el-select v-model="form_role.role_id"
                                filterable>
                      <el-option v-for="option in roles"
                                  :key="option.id"
                                  :label="option.name"
                                  :value="option.id"></el-option>
                    </el-select>
                    <small v-if="errors.role_id"
                            class="form-control-feedback"
                            v-text="errors.role_id[0]"></small>
                  </div>
                </div>
                <div class="col-sm-4 col-md-4 mt-4">
                  <el-button class="submit" type="primary" @click="sendFormRole" :disabled="isFormRole">Guardar
                  </el-button>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>Usuario</th>
                          <th>Correo</th>
                          <th>Rol</th>
                          <th>Accesos</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="user in users" :key="user.id">
                          <td>{{user.name}}</td>
                          <td>{{user.email}}</td>
                          <td>{{user.restaurant_role_name }}</td>
                          <td>
                            <template v-if="user.restaurant_role_code == 'ADM'">
                              <el-tag type="warning" v-for="(item, index) in permission_adm" :key="index + 'ADM'">{{ item }}</el-tag>
                            </template>
                            <template v-else-if="user.restaurant_role_code == 'CAJA'">
                              <el-tag type="warning" v-for="(item, index) in permission_caja" :key="index + 'CAJA'">{{ item }}</el-tag>
                            </template>
                            <template v-else-if="user.restaurant_role_code == 'KITBAR'">
                               <el-tag type="warning" v-for="(item, index) in permission_kitbar" :key="index + 'KITBAR'">{{ item }}</el-tag>
                            </template>
                            <template v-else-if="user.restaurant_role_code == 'MOZO'">
                              <el-tag type="warning" v-for="(item, index) in permission_mozo" :key="index + 'MOZO'">{{ item }}</el-tag>
                            </template>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </el-tab-pane>
            <!--<el-tab-pane class="mb-3"  name="third">
              <span slot="label">Mozos</span>
              <div class="row d-flex align-items-end">
                <div class="col-sm-4 col-md-4 mt-4">
                  <div :class="{'has-danger': errors.name}"
                        class="form-group">
                    <label class="control-label">Nombre
                    </label>
                    <el-input v-model="form_waiter.name" dusk="name"></el-input>
                    <small v-if="errors.name"
                            class="form-control-feedback"
                            v-text="errors.name[0]"></small>
                  </div>
                </div>
                <div class="col-sm-4 col-md-4 mt-4">
                  <div :class="{'has-danger': errors.last_name}"
                        class="form-group">
                    <label class="control-label">Apellido
                    </label>
                    <el-input v-model="form_waiter.last_name" dusk="last_name"></el-input>
                    <small v-if="errors.last_name"
                            class="form-control-feedback"
                            v-text="errors.last_name[0]"></small>
                  </div>
                </div>
                <div class="col-sm-4 col-md-4 mt-4">
                  <el-button class="submit" type="primary" @click="sendFormWaiter">Agregar
                  </el-button>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-12">
                  <div class="table-responsive">
                    <table class="table">
                      <thead>
                        <tr>
                          <th>Nombre</th>
                          <th>Apellidos</th>
                          <th></th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="waiter in waiters" :key="waiter.id + 'W'">
                          <td>{{waiter.name}}</td>
                          <td>{{waiter.last_name}}</td>
                          <td>
                            <button type="button" class="btn waves-effect waves-light btn-xs btn-danger" @click.prevent="clickDeleteWaiter(waiter.id)">
                              <i class="fa fa-trash"></i>
                            </button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </el-tab-pane>-->
             <el-tab-pane class="mb-3"  name="four">
              <span slot="label">Notas</span>
                <Notas/>
            </el-tab-pane>

              <el-tab-pane class="mb-3"  name="five">
                  <span slot="label">Impresoras</span>
                  <div class="row">
                      <div class="col-md-3">
                          <div class="form-group">
                              <label class="control-label">Impresora Cocina</label>
                              <el-input v-model="ip_impresora_cocina"></el-input>

                              <label class="control-label">Nombre Impresora</label>
                              <el-input v-model="nombre_impresora_cocina"></el-input>
                          </div>
                      </div>

                      <div class="col-md-3">
                          <div class="form-group">
                              <label class="control-label">Impresora Barra</label>
                              <el-input v-model="ip_impresora_barra"></el-input>

                              <label class="control-label">Nombre Impresora</label>
                              <el-input v-model="nombre_impresora_barra"></el-input>
                          </div>
                      </div>
                      <div class="col-md-3">
                          <div class="form-group">
                              <label class="control-label">Impresora Precuenta</label>
                              <el-input v-model="ip_impresora_precuenta"></el-input>

                              <label class="control-label">Nombre Impresora</label>
                              <el-input v-model="nombre_impresora_precuenta"></el-input>

                          </div>

                              <label class="control-label">VER PDF?</label>
                              <el-checkbox v-model="impresora_precuenta_is_pdf" :checked="(impresora_precuenta_is_pdf)=='true'"></el-checkbox>

                      </div>
                      <div class="col-md-12">
                          <p class="text-center">
                              <button class="btn waves-effect waves-light btn-xs btn-danger" type="button" @click.prevent="clickSavePrinters">
                              <i class="fa fa-save"></i>
                              </button>
                          </p>
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
import { io } from 'socket.io-client'
import {deletable} from '@mixins/deletable'
import Notas from '../notes/index.vue'
const url = 'https://milanmario.com'
const SOCKET = io(url, {
  reconnectionDelayMax: 100,
  transports: ['polling'],
  autoConnect: false,
})

//connect()  no nos sirve, porque queremos control total del servidor de socket, utilizar modulo restaurante, donde si se implementa socket en el propio servidor, pedir manual

//  function connect(username = 'usuario') {
//     SOCKET.auth = { username }
//     SOCKET.connect()
//  }

export default {
    mixins: [deletable],
    components: {Notas},
    data() {
      return {
        resource: 'restaurant',
        errors: {},
        form: {
          menu_pos: true,
          menu_order: true,
          menu_tables: true,
          menu_bar: true,
          menu_kitchen: true,
          first_menu: 'POS',
          tables_quantity: 15
        },
        activeName: 'second',
        menu_list: [
          {id: 1, description: 'POS', name: 'POS'},
          {id: 2, description: 'Mesas', name: 'TABLES'},
          {id: 3, description: 'Pedidos', name: 'ORDER'},
        ],
        users: {},
        roles: {},
        form_role: {
          user_id: '',
          role_id: ''
        },
        form_waiter: {
          name: null,
          last_name: null,
          id: null
        },
        waiters: [],
        info : {
          ruc:null,
          userEmail: null,
          socketServer: null
        },
        socket: null,
        permission_adm: ['Todos los menús'],
        permission_caja: ['POS', 'Mesas', 'Pedidos'],
        permission_mozo: ['POS', 'Mesas', 'Comandas'],
        permission_kitbar: ['Comandas'],

        ip_impresora_cocina:null,
        ip_impresora_barra:null,
        ip_impresora_precuenta: null,
        nombre_impresora_cocina: null,
        nombre_impresora_barra: null,
        nombre_impresora_precuenta: null,
        impresora_precuenta_is_pdf: null,
      }
    },
    computed: {
      isFormRole: function () {
        return (this.form_role.user_id != '' && this.form_role.role_id != '') ? false : true
      }
    },
    created() {
      this.getRecords();
      this.getUsers();
      this.getWaiters();

      this.ip_impresora_cocina          = localStorage.ip_impresora_cocina
      this.ip_impresora_barra           = localStorage.ip_impresora_barra
      this.ip_impresora_precuenta       = localStorage.ip_impresora_precuenta
      this.nombre_impresora_cocina      = localStorage.nombre_impresora_cocina
      this.nombre_impresora_barra       = localStorage.nombre_impresora_barra
      this.nombre_impresora_precuenta   = localStorage.nombre_impresora_precuenta

      this.impresora_precuenta_is_pdf   = localStorage.impresora_precuenta_is_pdf

        console.log(this.impresora_precuenta_is_pdf)

    },
    mounted() {

    },
    methods: {
      async getRecords() {
        await this.$http.get(`/${this.resource}/configuration/record`).then(response => {
          if (response.data !== '') {
            this.form = response.data.data;
            const infoData = response.data.info
            this.info.ruc = infoData.ruc
            this.info.userEmail = infoData.userEmail
            this.info.socketServer = infoData.socketServer
          }
        });
        this.$http.get(`/${this.resource}/get-roles`).then(response => {
          if (response.data !== '') {
            this.roles = response.data.data;
          }
        });

        this.sendCompany()
      },
      getUsers() {
        this.$http.get(`/${this.resource}/get-users`).then(response => {
          if (response.data !== '') {
            this.users = response.data.data;
          }
        });
      },
      getWaiters() {
        this.$http.get(`/${this.resource}/waiter`).then(response => {
          this.waiters = response.data.data
        });
      },
      submit() {
        this.$http.post(`/${this.resource}/configuration`, this.form).then(response => {
          let data = response.data;
          if (data.success) {
            this.$message.success(data.message);
            this.resetTablensAndEnvClients()
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
      },
      sendFormRole() {
        this.$http.post(`/${this.resource}/user/set-role`, this.form_role).then(response => {
          let data = response.data;
          if (data.success) {
            this.$message.success(data.message);
          } else {
            this.$message.error(data.message);
          }
        }).catch(error => {
          if (error.response.status === 422) {
            this.errors = error.response.data.errors;
          } else {
            console.log(error);
          }
        }).then(() => {
          this.getUsers();
          const role = this.roles.find(x => x.id == this.form_role.role_id)
          const user = this.users.find(x => x.id == this.form_role.user_id)
          this.sendUserUpdate(user.email, role.code )
          this.form_role.user_id = '';
          this.form_role.role_id = '';
          // this.loading_submit = false;
        });
      },
      initFormWaiter() {
        this.form_waiter = {
          name: null,
          last_name: null,
          id: null
        }
      },
      sendFormWaiter() {
        this.$http.post(`/${this.resource}/waiter`, this.form_waiter).then(response => {
          let data = response.data;
          if (data.success) {
            this.$message.success(data.message);
          } else {
            this.$message.error(data.message);
          }
          this.getWaiters()
          this.initFormWaiter()

        }).catch(error => {
          if (error.response.status === 422) {
            this.errors = error.response.data.errors;
          } else {
            console.log(error);
          }
        }).then(() => {

        });
      },
      clickDeleteWaiter(id) {
        this.destroy(`/${this.resource}/waiter/${id}`).then(() =>
          this.getWaiters()
        )
      },
      resetTablensAndEnvClients() {
        SOCKET.emit('reset-table-envs')
      },
      sendUserUpdate(userEmail, roleCode) {
        const data = {
          user_email: userEmail,
          role_code: roleCode,
        }
        SOCKET.emit('data-user-profile', data)
      },
      sendCompany() {
        const data = {
          ruc: this.info.ruc,
          user: this.info.userEmail
        }
        SOCKET.emit('data-company', data)
      },
      clickSavePrinters(){
          localStorage.ip_impresora_cocina            = this.ip_impresora_cocina
          localStorage.ip_impresora_barra             = this.ip_impresora_barra
          localStorage.ip_impresora_precuenta         = this.ip_impresora_precuenta
          localStorage.nombre_impresora_cocina            = this.nombre_impresora_cocina
          localStorage.nombre_impresora_barra             = this.nombre_impresora_barra
          localStorage.nombre_impresora_precuenta         = this.nombre_impresora_precuenta

          localStorage.impresora_precuenta_is_pdf  = this.impresora_precuenta_is_pdf

          this.$notify({
              title: '',
              message: 'Se guardó la configuración de las impresoras; recuerde que ésta configuración es válida solo para este dispositivo',
              type: 'success'
          })
      }
    }
}
</script>
