{{-- Start: General Info --}}
@include('export.excel.customeroffer.export-header', ['data' => $data, 'logo' => $logo])
{{-- End: General Info --}}

{{-- Start: Offer Items --}}
@include('export.excel.customeroffer.export-items', ['data' => $data])
{{-- End: Offer Items --}}

{{-- Start: Offer Footer --}}
@include('export.excel.customeroffer.export-footer', ['data' => $data])
{{-- End: Offer Footer --}}
