{{-- Start: General Info --}}
@include('export.excel.transaction.export-header', ['data' => $data, 'logo' => $logo])
{{-- End: General Info --}}

{{-- Start: Offer Items --}}
@include('export.excel.transaction.export-items', ['data' => $data])
{{-- End: Offer Items --}}

{{-- Start: Offer Footer --}}
@include('export.excel.transaction.export-footer', ['data' => $data])
{{-- End: Offer Footer --}}
