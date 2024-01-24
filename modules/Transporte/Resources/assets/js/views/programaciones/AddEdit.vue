<template>
    <el-dialog
        :title="title"
        :visible="visible"
        @close="onClose"
        @open="onCreate"
        width="90%"
        :close-on-click-modal="false"
    >
       
        <div class="form-body">

            <el-row>
                <el-steps type="card" :active="active" simple>
                    <el-step title="Programación"></el-step>
                    <el-step title="Rutas"></el-step>
                    <el-step title="Horarios"></el-step>
                    <el-step title="Programaciones"></el-step>
                </el-steps>
            </el-row>
            <el-row>
                <el-col v-if="active == 0" :span="24" :key="1">
                    <el-form ref="formProgramacion" status-icon :model="form" :rules="rulesProgramacion">
                        <el-form-item label="Origen" prop="terminal_origen_id">
                            <el-select placeholder="Seleccionar origen" v-model="form.terminal_origen_id" clearable  >
                                <el-option
                                    v-for="terminal in terminales"
                                    :key="terminal.id"
                                    :value="terminal.id"
                                    :label="terminal.nombre"
                                ></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item v-if="form.terminal_origen_id" label="Destino" prop="terminal_destino_id">
                            <el-select placeholder="Seleccionar destino" v-model="form.terminal_destino_id" clearable >
                                <template v-for="terminal in terminales">
                                    <el-option
                                        v-if="terminal.id != form.terminal_origen_id"
                                        :key="terminal.id"
                                        :value="terminal.id"
                                        :label="terminal.nombre"
                                    ></el-option>
                                </template>
                            </el-select>
                        </el-form-item>
                        <el-form-item label="Vehiculo" prop="vehiculo_id">
                            <el-select placeholder="Seleccionar destino" v-model="form.vehiculo_id" clearable>
                                <template v-for="vehiculo in vehiculos">
                                    <el-option
                                        :key="vehiculo.id"
                                        :value="vehiculo.id"
                                        :label="vehiculo.placa"
                                    ></el-option>
                                </template>
                            </el-select>
                        </el-form-item>

                        <el-form-item label="Hora salida" prop="hora_salida">
                            <el-input type="time" v-model="form.hora_salida" placeholder="Hora Salida" ></el-input>
                        </el-form-item>
                        <div class="row justify-content-center">
                            <label class="row align-items-center mr-1" >Tiempo estimado de viaje: </label>
                            <el-form-item label="Horas" prop="horas">
                                <el-input type="number" min="1" v-model="form.horas" placeholder="Horas" ></el-input>
                            </el-form-item>
                            <el-form-item class="ml-1" label="Minutos" prop="minutos">
                                <el-input type="number" min="0" v-model="form.minutos" placeholder="Minutos" ></el-input>
                            </el-form-item>

                        </div>

                        
                        

                    </el-form>
                </el-col>
                <el-col v-else-if="active == 1" :span="24" :key="1">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="control-label">Seleccionar Ruta</label>
                                <el-select v-model="selectTerminal" filterable remote value-key="id" popper-class="el-select-customers"
                                    placeholder="Buscar origen"
                                    :remote-method="getTerminales"
                                    :loading="loadingTerminales"
                                >
                                    <template v-for="terminal in listTerminales">
                                        <el-option  v-if="terminal.id != form.terminal_origen_id && terminal.id != form.terminal_destino_id" :key="terminal.id" :value="terminal" :label="terminal.nombre">
                                        </el-option>

                                    </template>
                                </el-select>


                            </div>
                        </div>
                        <div class="col-md-6">
                            <el-button :style="{marginTop:'1.95rem'}" @click="addTerminal">Agregar</el-button>
                        </div>
                    </div>
                    
                    <draggable v-model="selectTerminales">
                        <template v-for="(element,index) in selectTerminales" >
                            <div :key="element.id" class="mt-2" v-if="element.id != form.terminal_origen_id && element.id != form.terminal_destino_id">
                                <el-card>
                                    <div class="row justify-content-center">
                                        <div class="col-md-4">
                                            {{element.nombre}}
                                        </div>
                                        <!-- <div class="col-md-4">
                                            <el-form :ref="`element_form`" :model="element" :rules="rulesHorarios" :key="index">
                                                <el-form-item label="" :prop="`hora_salida`" >
                                                    <el-input type="time" v-model="element.hora_salida" ></el-input>
                                                </el-form-item>
                                            </el-form>
                                        </div> -->
                                        <div class="col-md-4">
                                            <el-button type="danger" @click="onDelete(element,index)">Eliminar</el-button>
                                        </div>
                                    </div>
                                </el-card>
                            </div>
                        </template>
                    </draggable>
                    
                </el-col>
                <el-col v-else-if="active == 2" :span="24" :key="2">
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <el-table border stripe :data="rutasHorarios" style="width: 100%" >
                                <el-table-column prop="origen" label="Origen" >
                                    <template v-slot="{ row }"  >
                                        {{  row.origen.nombre }}
                                    </template>
                                </el-table-column>
                                <el-table-column prop="destino" label="Destino" >
                                    <template v-slot="{ row }" >
                                        {{  row.destino.nombre }}
                                    </template>
                                </el-table-column>
                                <!-- <el-table-column prop="hora_salida" label="Hora Salida" /> -->
                                <el-table-column prop="horas" label="Tiempo estimado de viaje (Hr)"  v-slot="{ row, $index }">
                                    <el-form class="d-flex" :ref="`form_${$index}`" :model="row" :rules="rules" :key="$index">
                                        <el-form-item label="" :prop="`horas`" :rules="rules.horas">
                                            <el-input type="number" v-model.number="row.horas" placeholder="horas" ></el-input>
                                        </el-form-item>
                                        <el-form-item class="ml-1" label="" :prop="`minutos`" :rules="rules.minutos">
                                            <el-input type="number" v-model.number="row.minutos" placeholder="Minutos" ></el-input>
                                        </el-form-item>
                                    </el-form>
                                </el-table-column>
                                <!-- <el-table-column prop="hora_salida" ></el-table-column> -->
                            </el-table>
                        </div>
                    
                    </div>
                </el-col>
                <el-col v-else-if="active == 3" :span="24" :key="3">
                    <div class="row mt-2">
                        <div class="col-md-12">
                            <el-table border stripe :data="programacionesFiltradas" style="width: 100%" >
                                <el-table-column prop="origen" label="Origen" >
                                    <template v-slot="{ row }"  >
                                        {{  row.origen.nombre }}
                                    </template>
                                </el-table-column>
                                <el-table-column prop="destino" label="Destino" >
                                    <template v-slot="{ row }" >
                                        {{  row.destino.nombre }}
                                    </template>
                                </el-table-column>
                                <el-table-column prop="hora_salida" label="Hora Salida" >
                                    <template v-slot="{ row }">
                                        <span>{{ row.hora_salida }}</span>
                                    </template>
                                </el-table-column>
                                <el-table-column prop="time_view" label="Tiempo estimado de viaje (Hr)" ></el-table-column>
                            </el-table>
                        </div>
                    
                    </div>
                </el-col>
            </el-row>

            <div class="row text-center mt-3 justify-content-center">
                <div v-if="active > 0" class="col-4">
                    <el-button
                        native-type="submit"
                        :disabled="loading"
                        type="error"
                        class="btn-block"
                        :loading="loading"
                        @click="back"
                    >Atras</el-button
                    >
                </div>
                <div class="col-4">
                    <el-button
                        native-type="submit"
                        :disabled="loading"
                        type="primary"
                        class="btn-block"
                        :loading="loading"
                        @click="next"
                    >
                    
                    {{ active == 3 ? 'Guardar' : 'Siguiente'}}
                    
                    </el-button
                    >
                </div>
                <!-- <div class="col-4">
                    <el-button class="btn-block" @click="onClose">Cancelar</el-button>
                </div> -->
            </div>
        </div>
        
    </el-dialog>
</template>

<script>
import moment from 'moment';
import Draggable from 'vuedraggable';
export default {
    components:{ Draggable },
    props: {
        visible: {
            type: Boolean,
            required: true,
            default: false,
        },
        programacion: {
            type: Object,
            required: false,
            default: {},
        },
        terminales:{
            type:Array,
            default:() => []
        },
        vehiculos:{
            type:Array,
            default:() => []
        },
        userTerminal:{
            type:Object,
            default:{}
        }
    },
    created(){
        this.form.terminal_origen_id = this.userTerminal.terminal_id;
        this.getTerminales()
    },
    data() {
        return {
            form: {
                terminal_origen_id:null,
                terminal_destino_id:null,
                tiempo_estimado:null,
                hora_salida:null,
                vehiculo_id:null
            },
            title: "",
            errors: {},
            loading: false,
            listTerminales:[],
            selectTerminal:null,
            loadingTerminales:false,
            selectTerminales:[],
            onEdit:false,
            programaciones:[],
            rulesProgramacion:{
                terminal_origen_id: [
                    { required: true, message: 'Es obligatorio', trigger: 'change' },
                ],
                terminal_destino_id: [
                    { required: true, message: 'Es obligatorio', trigger: 'change' },
                ],
                vehiculo_id : [
                    { required: true, message: 'Es obligatorio', trigger: 'change' },
                ],
                hora_salida: [
                    { required: true, message: 'Es obligatorio', trigger: 'blur' },
                ],
                horas:[
                    { required: true, message: 'La hora es obligatoria', trigger: 'blur' },
                ],
                minutos:[
                    { required: true, message: 'Los minutos son obligatorios', trigger: 'blur' },
                ],
                // tiempo_estimado: [
                //     { required: true, message: 'Es obligatorio', trigger: 'blur' },
                // ],
            },
            rules:{
                horas:[
                    { required: true, message: 'Ingrese el tiempo estimado de viaje', trigger: 'blur' },
                ],
                minutos:[
                    { required: true, message: 'Ingrese el tiempo estimado de viaje', trigger: 'blur' },
                ],
                
            },
            rulesHorarios:{
                hora_salida:[
                    { required: true, message: 'Ingrese la hora de salida en la terminal', trigger: 'blur' },
                ]
            },
            rutasHorarios:[],
            active: 0
        };
    },
    watch:{

        'form.vehiculo_id'(){
            this.selectTerminales.map( terminal => {
                terminal.vehiculo_id = this.form.vehiculo_id;
                return terminal;
            })

        },
        rutasHorarios:{
            deep: true,
            handler(){

                let fecha = moment().format('YYYY-MM-DD')

                for(let item of this.rutasHorarios){
                    let { origen, destino} = item;
                    
                    let searchOrigen = this.getRutas.find( i => i.id == origen.id );
                    let searchDestino = this.selectTerminales.find( i => i.id == destino.id );

                    let horaSalida = searchOrigen.hora_salida || this.form.hora_salida;

                    const horas = item.horas ? parseInt(item.horas) : 0;
                    const minutos = item.minutos ? parseInt(item.minutos) : 0;

                    const totalMinutos = (horas * 60) + minutos;

                    const date = moment(`${fecha} ${horaSalida}`).add(totalMinutos,'minutes');
                    
                    if(searchDestino) searchDestino.hora_salida = date.format('H:mm');

                }
            }
        }

    },

    computed:{
        programacionesFiltradas(){
            return this.programaciones.filter( item => item.origen.id != this.form.terminal_origen_id || item.destino.id != this.form.terminal_destino_id)
        },
        getRutas(){
            const selectTerminales = [...this.selectTerminales];

            const origen = this.terminales.find( terminal => terminal.id == this.form.terminal_origen_id );
            const destino = this.terminales.find( terminal => terminal.id == this.form.terminal_destino_id );

            if(!origen || !destino) return selectTerminales;


            selectTerminales.unshift(origen);
            selectTerminales.push(destino);

            return selectTerminales;
        },
        destinoHorario(){
            if(!this.programacion) return [];
            if(!this.programacion.destino_horarios) return [];
            return this.programacion.destino_horarios;
        }
    },
    methods: {

        async getTerminales(input = ''){
            try{
                this.loadingTerminales = true;
                const {data} = await this.$http.get(`/transportes/programaciones/get-terminales?search=${input}`);
                this.loadingTerminales = false;
                this.listTerminales = data.data
            }catch(error){
                this.axiosError(error);
            }

        },
        async onUpdate() {
            this.loading = true;
            try{

                let data = {
                     programacion: {
                        ...this.form,
                        tiempo_estimado: ( parseInt(this.form.horas) * 60) +  parseInt(this.form.minutos)
                    },
                    intermedios: this.selectTerminales,
                    programaciones: this.programacionesFiltradas,
                    destinos_horarios: this.rutasHorarios
                };

                const { data:response } =  await this.$http.put(`/transportes/programaciones/${this.programacion.id}/update`,data);
                this.$emit("onUpdateItem", response.data);
                this.$message({
                    type: 'success',
                    message: 'Se editó la programación.'
                });
                this.onClose();
                this.onEdit = false;
            }catch(error){
                this.axiosError(error);
            }finally{
                this.loading = false;
                this.errors = {};

            }
        },
        async validate(){
            const refs = Object.keys(this.$refs);

            return new Promise( async resolve => {
                for(let ref of refs){
                    if(!this.$refs[ref]) continue;
                    const valid = await this.$refs[ref].validate().then( valid => {
                        if(valid) return valid;
                        else return false
                    }).catch( error => false );

                    if(!valid) resolve(false);
                }
                resolve(true);
            })
            

            
        },

        async validateStep2(){

            if(!this.$refs.element_form) return true;

            for(let form of this.$refs.element_form){
                const valid = await form.validate().then( valid => {
                    if(valid) return valid;
                    else return false
                }).catch( error => false );

                if(!valid) return false;
            }
            return true;
        },

        async validateStep3(){
            for(let i = 0; i < this.rutasHorarios.length; i++){
                const valid = await  this.$refs[`form_${i}`].validate().then( valid => {
                    if(valid) return valid;
                    else return false
                }).catch( error => false );

                if(!valid) return false;
            }
            return true;
        },

        back(){

            if(this.active > 0) this.active--;

        },
        async next(){
            

            if(this.active == 0){ // step1
                const valid = await this.$refs.formProgramacion.validate().then( valid => valid).catch( error => false);
                if(!valid) return;
                
                this.active++;
            } else if(this.active == 1){ // step 2

                const valid = await this.validateStep2();
                if(!valid) return;
                
                
                this.generarHorariosProgramaciones();

                this.active++;
            } else if(this.active == 2) { //step 3
                const valid = await this.validateStep3();
                if(!valid) return;

                this.generateProgramaciones();
                this.active++;
            } else if ( this.active == 3){ // step 4
                if(this.onEdit) this.onUpdate();
                else this.onStore();
            }

        },
        async onStore() {

            this.loading = true;
            try{
                let data = {
                    programacion: {
                        ...this.form,
                        tiempo_estimado: ( parseInt(this.form.horas) * 60) +  parseInt(this.form.minutos)
                    },
                    intermedios: this.selectTerminales,
                    programaciones: this.programacionesFiltradas,
                    destinos_horarios: this.rutasHorarios
                }
                const { data:response } = await this.$http.post("/transportes/programaciones/store",data);

                if(!response.success) return this.$message({
                    type:'error',
                    message: response.msg
                });

                this.$emit("onAddItem", response.data);
                this.$message({
                    type: 'success',
                    message: 'Se agregó la programación.'
                });
                this.onClose();
            } catch(error){
                this.axiosError(error);
            }finally{
                this.loading = false;
                this.errors = {};

            }
        },
        onSubmit() {
            if (this.programacion) {
                this.onUpdate();
            } else {
                this.onStore();
            }
        },
        onClose() {
            this.$emit("update:visible", false);
        },
        onCreate() {

            this.selectTerminales = [];
            this.rutasHorarios = [];
            this.active = 0;
            if (this.programacion) {
                this.onEdit = true;
                

                const [horas, minutos] = this.parseTime(this.programacion.tiempo_estimado);

                this.form = {
                    ...this.programacion,
                    horas,
                    minutos
                };
                
                this.selectTerminales = this.mapRutas(this.programacion);
                this.title = "Editar programación";
            } else {
                this.title = "Crear programación";
                this.selectTerminales = [];
                
                this.form = {
                    terminal_origen_id:this.userTerminal.terminal_id,
                    terminal_destino_id:null,
                    hora_salida:null,
                    vehiculo_id:null
                };
            }
        },
        addTerminal(){

            if(!this.selectTerminal) return;

            let find = this.selectTerminales.find( terminal => terminal.terminal_origen_id == this.selectTerminal.id );

            if(find){
                return this.$message({
                    type:'info',
                    message:'La terminal ya se encuentra agregada'
                });
            }


            let ruta = {
                id:this.selectTerminal.id,
                nombre:this.selectTerminal.nombre,
                terminal_origen_id: this.selectTerminal.id,
                hora_salida: null,
                vehiculo_id:this.form.vehiculo_id
            };

            this.selectTerminales.push(ruta);

            

            // this.generateProgramaciones();

            this.selectTerminal = null;
        },

        generateProgramaciones(){
            const selectTerminales = [...this.selectTerminales];

            const origen = this.terminales.find( terminal => terminal.id == this.form.terminal_origen_id );
            const destino = this.terminales.find( terminal => terminal.id == this.form.terminal_destino_id );

            if(!origen || !destino) return;


            selectTerminales.unshift(origen);
            selectTerminales.push(destino);

            this.programaciones = this.programaGeneration(selectTerminales);

        },
        programaGeneration(routes = [], resultRoutes = []){

            if(routes.length == 0) return resultRoutes;

            const terminal = routes.shift();

            for(let item of routes){

                const [ horaView, minutos] = this.calcularTiempoEstimado(terminal, item);

                resultRoutes.push({
                    origen: terminal,
                    destino: item,
                    hora_salida: terminal.hora_salida || this.form.hora_salida,
                    tiempo_estimado: minutos,
                    time_view: horaView
                });
            }

            return this.programaGeneration(routes, resultRoutes);

        },

        convertInTime(time){

            if(time) return 0;

            const [ horas, minutos ] = time.split(':');
            const totalMinutos = horas * 60;

            
            
            return totalMinutos + minutos;
        },

        convertTimeHuman(minutes){

            const hours = parseInt(minutes / 60);
            const moduleMinutes = minutes % 60;

            const finalHours = hours < 10 ? `0${hours}` : hours;
            const finalMinutes = moduleMinutes < 10 ? `0${moduleMinutes}` : moduleMinutes; 
            
            return `${finalHours}:${finalMinutes}`;

        },
        parseTime(minutes){
            const hours = parseInt(minutes / 60);
            const moduleMinutes = minutes % 60;
            return [hours, moduleMinutes];
        },
        calcularTiempoEstimado(origen,destino){

            const terminales = [...this.selectTerminales];

            const terminalOrigen = this.terminales.find( terminal => terminal.id == this.form.terminal_origen_id );
            const terminalDestino = this.terminales.find( terminal => terminal.id == this.form.terminal_destino_id );

            if(!origen || !destino) return;


            terminales.unshift(terminalOrigen);
            terminales.push(terminalDestino);



            let index = terminales.findIndex( item => item.id == origen.id );
            let indexDestino = terminales.findIndex( item => item.id == destino.id);

            const resultTerminales = terminales.slice((index + 1), (indexDestino + 1) );

            let total = 0;
            if(resultTerminales.length <= 0) return 0;

            for( let terminal of resultTerminales){
                let item = this.rutasHorarios.find( item => item.destino.id == terminal.id  )
                if(item) {
                    total += ((item.horas * 60) + item.minutos); 
                }
            }

            
            return  [ this.convertTimeHuman(total), total ];



        },
        generarHorariosProgramaciones(){

            const selectTerminales = [...this.selectTerminales];

            const origen = this.terminales.find( terminal => terminal.id == this.form.terminal_origen_id );
            const destino = this.terminales.find( terminal => terminal.id == this.form.terminal_destino_id );

            if(!origen || !destino) return;


            selectTerminales.unshift(origen);
            selectTerminales.push(destino);

            this.rutasHorarios = this.generarHorarios(selectTerminales);

        },
        generarHorarios(routes = []){
            let term = routes.shift();
            let r = [];

            for(let terminal of routes){
                let existItem = this.destinoHorario.find( ({origen,destino}) => origen.id == term.id && destino.id == terminal.id );

                
                r.push({
                    origen: term,
                    destino: terminal,
                    horas: existItem ? existItem.horas : 0,
                    minutos: existItem ? existItem.minutos : 0
                });
                term = terminal;
            }

            return r;
        },
        mapRutas(programacion){
            if(!programacion.rutas) return [];
            return programacion.rutas.map( terminal => {
                return {
                    id: terminal.id,
                    terminal_origen_id:terminal.id,
                    nombre: terminal.nombre,
                    hora_salida: terminal.pivot.hora_salida,
                }
            });
        },
        async onDelete(terminal,index){

            if(!this.onEdit){

                this.selectTerminales.splice(index,1);

                return this.$message({
                    type: 'success',
                    message: 'Se ha eliminado correctamente'
                });

            }

            this.$confirm(`¿Estás seguro de eliminar la terminal ?`, 'Atención', {
                confirmButtonText: 'Si, continuar',
                cancelButtonText: 'No, cerrar',
                type: 'warning'
            }).then(async() => {

                const { data } = await axios.delete(`/transportes/programaciones/${this.programacion.id}/${terminal.terminal_origen_id}/delete`);

                if(!data.status) return this.$message({
                    type:'error',
                    message: data.msg
                });

                this.$message({
                    type: 'success',
                    message: data.msg
                });
                this.selectTerminales.splice(index,1);

            }).catch((error) => {
                this.axiosError(error);
            });

        },
    },
};
</script>
