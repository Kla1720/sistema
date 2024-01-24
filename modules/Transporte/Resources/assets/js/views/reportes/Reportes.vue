<template>
    <div>
        <div class="page-header pr-0">
            <h2>
                <a href="/dashboard"><i class="fas fa-tachometer-alt"></i></a>
            </h2>
            <ol class="breadcrumbs">
                <li class="active"><span>REPORTES</span></li>
            </ol>
        </div>
        <div class="card mb-0 mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <el-tabs v-model="tab" >
                            <el-tab-pane label="Reporte ventas por oficina" name="reporte1">
                                <reporte-ventas-por-dia :oficinas="oficinas" />
                            </el-tab-pane>
                            <el-tab-pane label="Reporte Avance de venta por buses" name="reporte2">
                                <reporte-porcentaje-programaciones />
                            </el-tab-pane>
                            <el-tab-pane label="Reporte venta por buses" name="reporte3">
                                <reporte-ventas-buses />
                            </el-tab-pane>
                        </el-tabs>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>

import ReporteVentasPorDia from './tabs/ReporteVentasPorDia';
import ReportePorcentajeProgramaciones from './tabs/ReportePorcentajeProgramaciones';
import ReporteVentasBuses from './tabs/ReporteVentasBuses';
export default {
    components:{
        ReporteVentasPorDia,
        ReportePorcentajeProgramaciones,
        ReporteVentasBuses

    },
    props:{
        oficinas:{
            type:Array,
            default: () => ([])
        }
    },
    data(){
        return {
            tab: "reporte1",
        }
    }
    
}
</script>