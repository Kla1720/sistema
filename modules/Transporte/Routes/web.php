<?php


$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);



if ($hostname) {
	Route::domain($hostname->fqdn)->group(function () {
		// Route::post('transportes/sales/programaciones-disponibles', 'TransporteSalesController@getProgramacionesDisponibles');
		Route::middleware(['web', 'auth', 'redirect.module', 'locked.tenant'])
			->prefix('transportes')
			->group(function () {
			    //index
                Route::get('/', 'TransporteSalesController@index') ->name('tenant.transportes.index');
			    //Bus
                Route::get('sales/{pasaje?}', 'TransporteSalesController@index');
				Route::get('sales/get-ciudades', 'TransporteSalesController@getCiudades');
				Route::post('sales/programaciones-disponibles', 'TransporteSalesController@getProgramacionesDisponibles');
				Route::post('sales/obtener-asientos', 'TransporteSalesController@getAsientosOcupados');
				Route::post('sales/realizar-venta-boleto', 'TransporteSalesController@realizarVenta');
				Route::put('sales/{pasaje}/actualizar-boleto', 'TransporteSalesController@updateVenta');
				Route::put('sales/{pasaje}/venta-boleto-reservado', 'TransporteSalesController@ventaReservado');
                Route::get('sales/borrar-reserva/{id}','TransporteSalesController@eliminarReserva');
                Route::post('sales/listado_pasajeros', 'TransporteSalesController@listadoPasajeros');

				// Vehiculos
				Route::get('vehiculos', 'TransporteVehiculoController@index');
				Route::post('vehiculos/store', 'TransporteVehiculoController@store');
				Route::put('vehiculos/{id}/update', 'TransporteVehiculoController@update');
				Route::delete('vehiculos/{id}/delete', 'TransporteVehiculoController@destroy');
				Route::post('vehiculos/{vehiculo}/guardar-asientos','TransporteVehiculoController@guardarAsientos');
				Route::delete('vehiculos/{asiento}/eliminar','TransporteVehiculoController@eliminarAsiento');
                Route::post('vehiculos/get-all', 'TransporteVehiculoController@getAllVehiculos');
                Route::get('vehiculos/{id}/get-asientos', 'TransporteVehiculoController@getAsientos');
				// Choferes
				Route::get('choferes', 'TransporteChoferController@index');
				Route::post('choferes/store', 'TransporteChoferController@store');
				Route::put('choferes/{id}/update', 'TransporteChoferController@update');
				Route::delete('choferes/{id}/delete', 'TransporteChoferController@destroy');
				// Destinos
				Route::get('destinos', 'TransporteDestinoController@index');
				Route::post('destinos/store', 'TransporteDestinoController@store');
				Route::put('destinos/{id}/update', 'TransporteDestinoController@update');
				Route::delete('destinos/{id}/delete', 'TransporteDestinoController@destroy');
				Route::post('destinos/{id}/change-status', 'TransporteDestinoController@changeRoomStatus');
                Route::get('destinos/search-districts', 'TransporteDestinoController@searchDistricts');
                Route::get('destinos/get-districts', 'TransporteDestinoController@getDistritos');
                // Encomiendas
                Route::get('encomiendas', 'TransporteEncomiendaController@index');
				Route::get('encomiendas/get-encomiendas', 'TransporteEncomiendaController@getEncomiendas');
				Route::get('encomiendas/get-encomiendas-notes', 'TransporteEncomiendaController@getEncomiendasNotes');
				Route::post('encomiendas/store', 'TransporteEncomiendaController@store');
				Route::get('encomiendas/get-clientes','TransporteEncomiendaController@getClientes');
				Route::get('encomiendas/get-terminales','TransporteEncomiendaController@getTerminales');
				Route::get('encomiendas/{terminal}/get-destinos','TransporteEncomiendaController@getDestinos');
				Route::post('encomiendas/programaciones-disponibles','TransporteEncomiendaController@getProgramacionesDisponibles');
				Route::put('encomiendas/{encomienda}/update','TransporteEncomiendaController@update');
                Route::put('encomiendas/entregar','TransporteEncomiendaController@entregar');
				Route::delete('encomiendas/{encomienda}/delete','TransporteEncomiendaController@destroy');
				Route::get('encomiendas/get-productos','TransporteEncomiendaController@getProductos');
                Route::get('encomiendas/get-pasajero/{type?}/{number?}','TransporteEncomiendaController@getPasajero');
                Route::get('entregar-encomiendas','TransporteEncomiendaController@entregarEncomiendasIndex');
                Route::get('encomiendas/buscar/{numero_dni}', 'TransporteEncomiendaController@buscarEncomienda');
                Route::get('encomiendas/buscar-nota/{numero_dni}', 'TransporteEncomiendaController@buscarEncomiendaNota');

				//terminales
				Route::get('terminales','TransporteTerminalesController@index');
				Route::post('terminales/store','TransporteTerminalesController@store');
				Route::put('terminales/{terminal}/update','TransporteTerminalesController@update');
				Route::delete('terminales/{terminal}/delete','TransporteTerminalesController@destroy');
                // Pasajes
                Route::get('pasajes', 'TransportePasajeController@index');
				Route::delete('pasajes/{pasaje}/delete', 'TransportePasajeController@destroy');
				Route::get('pasajes/{terminal}/get-destinos','TransportePasajeController@getDestinos');
                Route::get('pasajes/get-pasajes-notes', 'TransportePasajeController@getPasajesNotes');
				Route::get('pasajes/get-pasajes','TransportePasajeController@getPasajes');
				//programaciones
				Route::get('programaciones','TransporteProgramacionesController@index');
				Route::get('programaciones/programaciones','TransporteProgramacionesController@getProgramaciones');
                Route::get('programaciones/programaciones/list','TransporteProgramacionesController@getProgramacionesList');
				Route::post('programaciones/store','TransporteProgramacionesController@store');
				Route::put('programaciones/{programacion}/update','TransporteProgramacionesController@update');
				Route::put('programaciones/{programacion}/configuracion-rutas','TransporteProgramacionesController@configuracionRutas');
				Route::delete('programaciones/{programacion}/delete','TransporteProgramacionesController@destroy');
				Route::get('programaciones/get-terminales','TransporteProgramacionesController@getTerminales');
				Route::delete('programaciones/{programacion}/{terminal}/delete','TransporteProgramacionesController@deleteRuta');
                Route::put('programacion/{id}/activar','TransporteProgramacionesController@activar');
                Route::put('programacion/{id}/desactivar','TransporteProgramacionesController@desactivar');

				//Manifiestos
				Route::get('manifiestos/','TransporteManifiestosController@index');
				Route::post('manifiestos/get-programaciones','TransporteManifiestosController@getProgramaciones');
				Route::post('manifiestos/guardar-manifiesto','TransporteManifiestosController@store');
				Route::put('manifiestos/{manifiesto}/actualizar-manifiesto','TransporteManifiestosController@update');
				Route::get('manifiestos/{manifiesto}/imprimir-manifiesto','TransporteManifiestosController@imprimirManifiesto');
				Route::get('manifiestos/{manifiesto}/asignar-encomiendas','TransporteManifiestosController@indexManifiestoEncomiendas');
				Route::post('manifiestos/get-encomiendas','TransporteManifiestosController@getEncomiendas');
				Route::post('manifiestos/get-encomiendas-sin-asignar','TransporteManifiestosController@getEncomiendasSinAsignar');
				Route::post('manifiestos/asignacion-encomienda','TransporteManifiestosController@asignarEncomienda');
				Route::post('manifiestos/desasignar-encomienda','TransporteManifiestosController@desasignarEncomienda');
				Route::get('manifiestos/get-manifiestos','TransporteManifiestosController@getManifiestos');
                Route::post('manifiestos/verificar-manifiesto','TransporteManifiestosController@verificarManifiestos');
                Route::get('manifiestos/{manifiesto}/lista-pasajeros','TransporteManifiestosController@listaPasajeros');
                Route::post('manifiestos/confirmar-asistencia','TransporteManifiestosController@confirmarAsistencia');

				//usuarios terminales
				Route::get('usuarios-terminales','TransporteUsersTerminalController@index');
				Route::get('usuarios-terminales/get-tables','TransporteUsersTerminalController@getTables');
				Route::post('usuarios-terminales/store','TransporteUsersTerminalController@store');
				Route::put('usuarios-terminales/{usuario_terminal}/update','TransporteUsersTerminalController@update');
				Route::delete('usuarios-terminales/{id}/delete','TransporteUsersTerminalController@destroy');


				//Reportes
				Route::get('reportes','TransporteReportes@index');
				Route::post('reportes/reporte-venta-por-dia/preview','TransporteReportes@getPreviewReporteVentarPorDia');
				Route::post('reportes/reporte-venta-por-dia','TransporteReportes@reporteDiarioPorVentas');

				// REPORTE DE AVANCE DE BUSES POR PORCENTAJE
				Route::post('reportes/reporte-porcentaje-programaciones/preview','TransporteReportes@getPreviewReportePorcentajeProgramaciones');
				Route::post('reportes/reporte-porcentaje-programaciones','TransporteReportes@getReportePorcentajeProgramaciones');


				// REPORTE DE VENTA POR BUSES
				Route::post('reportes/reporte-ventas-buses/preview','TransporteReportes@getPreviewReporteVentaBuses');
				Route::post('reportes/reporte-ventas-buses','TransporteReportes@getReporteVentaBuses');

                // CONFIGURACION
                Route::post('configuration','TransporteConfigurationController@setConfiguration');
                Route::get('configuration','TransporteConfigurationController@configuration');
                Route::get('configuration/record','TransporteConfigurationController@record');
			});
	});
}
