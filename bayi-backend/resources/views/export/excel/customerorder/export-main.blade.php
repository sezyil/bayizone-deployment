{{-- Start: General Info --}}
@include('export.excel.customerorder.export-header', ['data' => $data, 'logo' => $logo])
{{-- End: General Info --}}

{{-- Start: Offer Items --}}
@include('export.excel.customerorder.export-items', ['data' => $data])
{{-- End: Offer Items --}}

{{-- Start: Offer Footer --}}
@include('export.excel.customerorder.export-footer', ['data' => $data])
{{-- End: Offer Footer --}}
