{{-- Start: General Info --}}
@include('export.excel.shipment.export-header', ['data' => $data, 'logo' => $logo])
{{-- End: General Info --}}

{{-- Start: Offer Items --}}
@include('export.excel.shipment.export-items', ['data' => $data])
{{-- End: Offer Items --}}

{{-- Start: Offer Footer --}}
@include('export.excel.shipment.export-footer', ['data' => $data])
{{-- End: Offer Footer --}}
