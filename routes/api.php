<?php

Route::get('generate_token', 'Tenant\Api\MobileController@getSeries');

$hostname = app(Hyn\Tenancy\Contracts\CurrentHostname::class);
if ($hostname) {
    Route::domain($hostname->fqdn)->group(function () {

        Route::post('login', 'Tenant\Api\MobileController@login');
        Route::post('login-guia-facil', 'Tenant\Api\MobileGuiaFacilController@login');

        Route::middleware(['auth:api', 'locked.tenant'])->group(function () {
            //MOBILE
            Route::get('document/series', 'Tenant\Api\MobileController@getSeries');
            Route::get('document/paymentmethod', 'Tenant\Api\MobileController@getPaymentmethod');
            Route::get('document/tables', 'Tenant\Api\MobileController@tables');
            Route::get('document/customers', 'Tenant\Api\MobileController@customers');
            Route::post('document/email', 'Tenant\Api\MobileController@document_email');
            Route::post('sale-note', 'Tenant\Api\SaleNoteController@store');
            Route::get('sale-note/series', 'Tenant\Api\SaleNoteController@series');
            Route::get('sale-note/lists', 'Tenant\Api\SaleNoteController@lists');
            Route::post('item', 'Tenant\Api\MobileController@item');
            Route::post('items/{id}/update', 'Tenant\Api\MobileController@updateItem');
            Route::post('item/upload', 'Tenant\Api\MobileController@upload');
            Route::post('person', 'Tenant\Api\MobileController@person');
            Route::get('document/search-items', 'Tenant\Api\MobileController@searchItems');
            Route::get('document/search-customers', 'Tenant\Api\MobileController@searchCustomers');
            Route::post('sale-note/email', 'Tenant\Api\SaleNoteController@email');
            Route::post('sale-note/{id}/generate-cpe', 'Tenant\Api\SaleNoteController@generateCPE');
            Route::get('sale-note/record/{id}', 'Tenant\Api\SaleNoteController@record');

            Route::get('report', 'Tenant\Api\MobileController@report');

            Route::post('documents', 'Tenant\Api\DocumentController@store');
            Route::get('documents/lists', 'Tenant\Api\DocumentController@lists');
            Route::get('documents/lists/{startDate}/{endDate}', 'Tenant\Api\DocumentController@lists');
            Route::post('documents/updatedocumentstatus', 'Tenant\Api\DocumentController@updatestatus');
            Route::post('summaries', 'Tenant\Api\SummaryController@store');
            Route::post('voided', 'Tenant\Api\VoidedController@store');
            Route::post('retentions', 'Tenant\Api\RetentionController@store');
            Route::post('dispatches', 'Tenant\Api\DispatchController@store');
            Route::post('documents/send', 'Tenant\Api\DocumentController@send');
            Route::post('summaries/status', 'Tenant\Api\SummaryController@status');
            Route::post('voided/status', 'Tenant\Api\VoidedController@status');
            Route::get('services/ruc/{number}', 'Tenant\Api\ServiceController@ruc');
            Route::get('services/dni/{number}', 'Tenant\Api\ServiceController@dni');
            Route::post('services/consult_cdr_status', 'Tenant\Api\ServiceController@consultCdrStatus');
            Route::post('services/validate_cpe', 'Tenant\Api\ServiceController@validateCpe');
            Route::post('perceptions', 'Tenant\Api\PerceptionController@store');

            Route::post('dispatches/send', 'Tenant\Api\DispatchController@send');
            Route::post('dispatches/status_ticket', 'Tenant\Api\DispatchController@statusTicket');

            Route::post('documents_server', 'Tenant\Api\DocumentController@storeServer');
            Route::get('document_check_server/{external_id}', 'Tenant\Api\DocumentController@documentCheckServer');

            //liquidacion de compra
            Route::post('purchase-settlements', 'Tenant\Api\PurchaseSettlementController@store');
            Route::get('persons/suppliers', 'Tenant\Api\MobileController@suppliers');

            //Pedidos
            Route::get('orders', 'Tenant\Api\OrderController@records');
            Route::post('orders', 'Tenant\Api\OrderController@store');

            //Company
            Route::get('company', 'Tenant\Api\CompanyController@record');

            // Cotizaciones
            Route::get('quotations/list', 'Tenant\Api\QuotationController@list');
            Route::post('quotations', 'Tenant\Api\QuotationController@store');
            Route::post('quotations/email', 'Tenant\Api\QuotationController@email');

            //Caja
            Route::post('cash/restaurant', 'Tenant\Api\CashController@storeRestaurant');

            Route::get('document/record/{id}', 'Tenant\Api\DocumentController@record');

            Route::get('cash/records', 'Tenant\Api\CashController@recordsMovil');
            Route::get('cash/record', 'Tenant\Api\CashController@recordMovil');
            Route::post('cash', 'Tenant\Api\CashController@store');
            Route::get('cash/close/{cash}', 'Tenant\Api\CashController@close');

            Route::get('expenses/tables', 'Tenant\Api\ExpenseController@tables');
            Route::post('expenses', 'Tenant\Api\ExpenseController@store');

            Route::get('incomes/tables', 'Tenant\Api\IncomeController@tables');
            Route::get('cash/movimientos/{id}', 'Tenant\Api\CashController@movimientos');
            Route::post('incomes', 'Tenant\Api\IncomeController@store');


            //////guias
//            Route::prefix('dispatches')->group(function () {
//                Route::get('', 'Tenant\DispatchController@index')->name('tenant.dispatches.index');
//                Route::get('/columns', 'Tenant\DispatchController@columns');
//                Route::get('/records', 'Tenant\DispatchController@records');
//                Route::get('/create/{document?}/{type?}/{dispatch?}', 'Tenant\DispatchController@create');
//                Route::post('/tables', 'Tenant\DispatchController@tables');
//                //Route::post('', 'Tenant\DispatchController@store');
//                Route::get('/record/{id}', 'Tenant\DispatchController@record');
//                Route::post('/sendSunat/{document}', 'Tenant\DispatchController@sendDispatchToSunat');
//                Route::post('/email', 'Tenant\DispatchController@email');
//                Route::get('/generate/{sale_note}', 'Tenant\DispatchController@generate');
//                Route::get('/record/{id}/tables', 'Tenant\DispatchController@generateDocumentTables');
//                Route::post('/record/{id}/set-document-id', 'Tenant\DispatchController@setDocumentId');
//                Route::get('/client/{id}', 'Tenant\DispatchController@dispatchesByClient');
//                Route::post('/items', 'Tenant\DispatchController@getItemsFromDispatches');
//                Route::post('/getDocumentType', 'Tenant\DispatchController@getDocumentTypeToDispatches');
//                Route::get('/data_table', 'Tenant\DispatchController@data_table');
//                Route::get('/search/customers', 'Tenant\DispatchController@searchCustomers');
//                Route::get('/search/customer/{id}', 'Tenant\DispatchController@searchClientById');
//                //Route::post('/status_ticket', 'Tenant\Api\DispatchController@statusTicket');
//                Route::get('create_new/{table}/{id}', 'Tenant\DispatchController@createNew');
//                Route::get('/get_origin_addresses/{establishment_id}', 'Tenant\DispatchController@getOriginAddresses');
//                Route::get('/get_delivery_addresses/{person_id}', 'Tenant\DispatchController@getDeliveryAddresses');
//
//
//            });

            Route::prefix('conductores')->group(function () {
                Route::post('/listar', 'Tenant\Api\MobileGuiaFacilController@conductores');
                Route::post('/guardar', 'Tenant\Api\MobileGuiaFacilController@guardarConductor');
                Route::post('/actualizar', 'Tenant\Api\MobileGuiaFacilController@guardarConductor');
                Route::delete('/eliminar/{id}', 'Tenant\Api\MobileGuiaFacilController@eliminarConductor');
            });

            Route::prefix('vehiculos')->group(function () {
                Route::post('/listar', 'Tenant\Api\MobileGuiaFacilController@vehiculos');
                Route::post('/guardar', 'Tenant\Api\MobileGuiaFacilController@guardarVehiculo');
                Route::post('/actualizar', 'Tenant\Api\MobileGuiaFacilController@guardarVehiculo');
                Route::delete('/eliminar/{id}', 'Tenant\Api\MobileGuiaFacilController@eliminarVehiculo');
            });

            Route::prefix('transportistas')->group(function () {
                Route::post('/listar', 'Tenant\Api\MobileGuiaFacilController@transportistas');
                Route::post('/guardar', 'Tenant\Api\MobileGuiaFacilController@guardarTransportista');
                Route::post('/actualizar', 'Tenant\Api\MobileGuiaFacilController@guardarTransportista');
                Route::delete('/eliminar/{id}', 'Tenant\Api\MobileGuiaFacilController@eliminarTransportista');
            });

//            Route::prefix('dispatch_carrier')->group(function () {
//                Route::get('', 'Tenant\DispatchCarrierController@index')->name('tenant.dispatch_carrier.index');
//                Route::get('/columns', 'Tenant\DispatchCarrierController@columns');
//                Route::get('/records', 'Tenant\DispatchCarrierController@records');
//                Route::get('/create/{document?}/{type?}/{dispatch?}', 'Tenant\DispatchCarrierController@create');
//                Route::post('/tables', 'Tenant\DispatchCarrierController@tables');
//                Route::post('', 'Tenant\DispatchCarrierController@store');
//                Route::get('/record/{id}', 'Tenant\DispatchCarrierController@record');
//                Route::post('/sendSunat/{document}', 'Tenant\DispatchCarrierController@sendDispatchToSunat');
//                Route::post('/email', 'Tenant\DispatchCarrierController@email');
//                Route::get('/generate/{sale_note}', 'Tenant\DispatchCarrierController@generate');
//                Route::get('/record/{id}/tables', 'Tenant\DispatchCarrierController@generateDocumentTables');
//                Route::post('/record/{id}/set-document-id', 'Tenant\DispatchCarrierController@setDocumentId');
//                Route::get('/client/{id}', 'Tenant\DispatchCarrierController@dispatchesByClient');
//                Route::post('/items', 'Tenant\DispatchCarrierController@getItemsFromDispatches');
//                Route::post('/getDocumentType', 'Tenant\DispatchCarrierController@getDocumentTypeToDispatches');
//                Route::get('/data_table', 'Tenant\DispatchCarrierController@data_table');
//                Route::get('/search/customers', 'Tenant\DispatchCarrierController@searchCustomers');
//                Route::get('/search/customer/{id}', 'Tenant\DispatchCarrierController@searchClientById');
//                Route::post('/status_ticket', 'Tenant\Api\DispatchCarrierController@statusTicket');
//                Route::get('create_new/{table}/{id}', 'Tenant\DispatchCarrierController@createNew');
//                Route::get('/get_origin_addresses/{establishment_id}', 'Tenant\DispatchCarrierController@getOriginAddresses');
//                Route::get('/get_delivery_addresses/{person_id}', 'Tenant\DispatchCarrierController@getDeliveryAddresses');
//            });

            Route::prefix('guia-remitente')->group(function () {
                Route::post('/guardar', 'Tenant\Api\DispatchController@store');
                Route::get('/all-view-dispatch-data', 'Tenant\Api\MobileGuiaFacilController@getAllViewDispatchdata');
                Route::get('/get-cliente-by-number/{num_doc}', 'Tenant\Api\MobileGuiaFacilController@getClienteByNumber');

            });

            Route::prefix('guia-transportista')->group(function () {
                Route::post('/guardar', 'Tenant\Api\MobileGuiaFacilController@store');
            });




        });
        Route::get('documents/search/customers', 'Tenant\DocumentController@searchCustomers');

        // Route::post('services/consult_status', 'Tenant\Api\ServiceController@consultStatus');
        Route::post('services/validate_cpe', 'Tenant\Api\ServiceController@validateCpe');
        Route::get('services/validate_cpe/{company_number}/{document_type_code}/{series}/{number}/{date_of_issue}/{total}', 'Tenant\Api\ServiceController@validateCpe2');
        Route::get('services/validate_cpe_sunat/{company_number}/{document_type_code}/{series}/{number}/{date_of_issue}/{total}', 'Tenant\Api\ServiceController@validateCpeSunat');
        Route::post('services/validate_cpe_sunat', 'Tenant\Api\ServiceController@validateCpeSunat');
        Route::get('services/nombre/{nombre}', 'Tenant\Api\ServiceController@nombre');
        Route::post('services/consult_status', 'Tenant\Api\ServiceController@consultStatus');
        Route::post('documents/status', 'Tenant\Api\ServiceController@documentStatus');

        Route::get('sendserver/{document_id}/{query?}', 'Tenant\DocumentController@sendServer');
        Route::post('configurations/generateDispatch', 'Tenant\ConfigurationController@generateDispatch');
    });
} else {
    Route::domain(env('APP_URL_BASE'))->group(function () {

        //Clients
        Route::post('clients', 'System\Api\ClientController@store');
        Route::post('clients/update', 'System\Api\ClientController@update');
        Route::get('clients/tables', 'System\Api\ClientController@tables');

        Route::middleware(['auth:system_api'])->group(function () {

            //reseller
            Route::post('reseller/detail', 'System\Api\ResellerController@resellerDetail');
            // Route::post('reseller/lockedAdmin', 'System\Api\ResellerController@lockedAdmin');
            // Route::post('reseller/lockedTenant', 'System\Api\ResellerController@lockedTenant');

            Route::get('restaurant/partner/list', 'System\Api\RestaurantPartnerController@list');
            Route::post('restaurant/partner/store', 'System\Api\RestaurantPartnerController@store');
            Route::post('restaurant/partner/search', 'System\Api\RestaurantPartnerController@search');

        });

    });

}
