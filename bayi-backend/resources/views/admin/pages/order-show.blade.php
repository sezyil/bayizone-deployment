@extends('admin.layout.admin')

@section('title', 'Sipariş Detayı')

@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">Sipariş Detayı</h5>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Sipariş Kodu</label>
                        <input type="text" class="form-control" value="{{ $order->order_no }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="">Müşteri</label>
                        <input type="text" class="form-control" value="{{ $order->customer->firm_name }}" readonly>
                    </div>
                    {{-- tax --}}
                    <div class="form-group">
                        <label for="">Vergi</label>
                        <input type="text" class="form-control" value="{{ $order->tax_amount }}$" readonly>
                    </div>
                    {{-- indirim --}}
                    <div class="form-group">
                        <label for="">İndirim</label>
                        <input type="text" class="form-control" value="{{ $order->discount_amount }}$" readonly>
                    </div>
                    <div class="form-group">
                        <label for="">Toplam Tutar</label>
                        <input type="text" class="form-control" value="{{ $order->total }}$" readonly>
                    </div>
                    {{-- currency_Rate --}}
                    <div class="form-group">
                        <label for="">Döviz Kuru</label>
                        <input type="text" class="form-control" value="{{ $order->currency_rate }}" readonly>
                    </div>
                    <div class="form-group">
                        <label for="">Toplam Tutar (TL)</label>
                        <input type="text" class="form-control" value="{{ $order->converted_total }}₺" readonly>
                    </div>
                    <div class="form-group">
                        <label for="">Ödeme Tipi</label>
                        <input type="text" class="form-control" value="{{ $order->payment_method }}" readonly>
                    </div>

                </div>
                <div class="col-md-6">
                    {{-- ödeme tarihi --}}
                    <div class="form-group">
                        <label for="">Ödeme Tarihi</label>
                        <input type="text" class="form-control"
                            value="{{ $order->payment_date ? \Carbon\Carbon::parse($order->payment_date)->format('d.m.Y H:i') : 'Ödenmedi' }}"
                            readonly>
                    </div>
                    <div class="form-group">
                        <label for="">Sipariş Durumu</label>
                        <input type="text" class="form-control" value="{{ $order->is_paid ? 'Ödendi' : 'Ödenmedi' }}"
                            readonly>
                    </div>
                    <div class="form-group">
                        <label for="">Sipariş Tarihi</label>
                        <input type="text" class="form-control" value="{{ $order->created_at->format('d.m.Y H:i') }}"
                            readonly>
                    </div>
                    {{-- kupon --}}
                    @if ($order->coupon)
                        <div class="form-group">
                            <label for="">Kupon Kodu</label>
                            <input type="text" class="form-control" value="{{ $order->coupon->code }}" readonly>
                        </div>
                    @else
                        <div class="form-group">
                            <label for="">Kupon</label>
                            <input type="text" class="form-control" value="Yok" readonly>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="">Ip Adresi</label>
                        <input type="text" class="form-control" value="{{ $order->ip_address }}" readonly>
                    </div>
                </div>
            </div>

            @if ($order->coupon)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Kupon Detayları</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Kupon Kodu</th>
                                        <th>İndirim Oranı</th>
                                        <th>İndirim Tutarı</th>
                                        <th>İndirim Tutarı (TL)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $order->coupon->code }}</td>
                                        <td>{{ $order->discount_percentage }}%</td>
                                        <td>{{ $order->discount_amount }}$</td>
                                        <td>{{ $order->converted_discount_amount }}₺</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            @endif


            @if ($order->payment_method == \App\Enums\OrderPaymentMethods::BANK_TRANSFER->value)
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Havale / EFT Bilgileri</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">

                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Transfer Tarihi</th>
                                        <th>Transfer Yapan</th>
                                        <th>Banka Adı</th>
                                        <th>Referans No</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($order->transfer_date)->format('d.m.Y') }}</td>
                                        <td>{{ $order->transfer_account_name }}</td>
                                        <td>{{ $order->transfer_bank_name }}</td>
                                        <td>{{ $order->transfer_reference_no }}</td>
                                        <td>
                                            @if (!$order->is_paid)
                                                <button type="button" class="btn btn-success" role="pay-order"
                                                    data-url="{{ route('admin.order.approve', $order->id) }}"
                                                    data-order-id="{{ $order->id }}">Ödendi Olarak İşaretle</button>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            @endif

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Sipariş Detayları</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Ürün</th>
                                    <th>Fiyat</th>
                                    <th>Miktar</th>
                                    <th>Toplam</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->lines as $line)
                                    <tr>
                                        <td>{{ $line->name }}</td>
                                        <td>
                                            <span class="badge badge-success">{{ $line->price }}$</span>
                                        </td>
                                        <td>{{ $line->quantity }}</td>
                                        <td>{{ $line->quantity * $line->price }}$</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Ödeme Geçmişi</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Tarih</th>
                                    <th>Ödeme ID</th>
                                    <th>Ödeme Durumu</th>
                                    <th>Ödeme Hata Kodu</th>
                                    <th>Ödeme Hata Mesajı</th>
                                    <th>Ödeme Token</th>

                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($order->paymentLogs->sortByDesc('created_at') as $log)
                                    <tr>
                                        <td>{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                                        <td>{{ $log->payment_id }}</td>
                                        <td>{{ $log->payment_status }}</td>
                                        <td>{{ $log->payment_error_code }}</td>
                                        <td>{{ $log->payment_error_message }}</td>
                                        <td>{{ $log->payment_token }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6">Ödeme geçmişi bulunamadı.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '[role="pay-order"]', function() {
            const url = $(this).data('url');
            /* confirm box */
            const approved = confirm("Siparişi ödendi olarak işaretlemek istediğinize emin misiniz?");
            if (!approved) {
                return;
            }
            window.location = url;
        });
    </script>
@endpush
