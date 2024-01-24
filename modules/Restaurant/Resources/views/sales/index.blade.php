@extends('tenant.layouts.app')

@section('content')

    <tenant-restaurant-sales-index :configuracion-socket='@json($configuracion_socket)'  :id_user2='@json($id_user2)' :type_user='@json($type_user)' :configuration='@json($configuration)' :items='@json($items)' :categorias='@json($categorias)'></tenant-restaurant-sales-index>

@endsection
@push('scripts')
    <script type="text/javascript">
        var count = 0;
        $(document).on("click", "#card-click", function(event){
            count = count + 1;
            if (count == 1) {
                $("#card-section").removeClass("card-collapsed");
            }
        });
    </script>

    <!-- QZ -->
    <script src="{{ asset('js/sha-256.min.js') }}"></script>
    <script src="{{ asset('js/qz-tray.js') }}"></script>
    <script src="{{ asset('js/rsvp-3.1.0.min.js') }}"></script>
    <script src="{{ asset('js/jsrsasign-all-min.js') }}"></script>
    <script src="{{ asset('js/sign-message.js') }}"></script>
    <script src="{{ asset('js/function-qztray.js') }}"></script>
    <!-- END QZ -->

@endpush
