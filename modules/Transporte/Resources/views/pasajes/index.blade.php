@extends('tenant.layouts.app')

@section('content')
    <tenant-transporte-pasajes
        {{-- :list-programaciones='@json($programaciones)' --}}
        {{-- :pasajes='@json($pasajes)' --}}
        :establishment='@json($establishment)'
        :series='@json($series)'
        :document-types-invoice='@json($document_types_invoice)'
        :payment-method-types='@json($payment_method_types)'
        :payment-destinations='@json($payment_destinations)'
        :user-terminal='@json($terminal)'
        :configuration='@json($configuration)'
        :estado-asientos='@json($estadosAsientos)'
        :user='@json($user)'
    />

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
