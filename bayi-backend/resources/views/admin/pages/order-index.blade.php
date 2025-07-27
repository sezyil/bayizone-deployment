@extends('admin.layout.admin')

@section('title', 'Siparişler')

@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">Siparişler</h5>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Sipariş Kodu</th>
                    <th>Müşteri</th>
                    <th>Toplam Tutar</th>
                    <th>Ödeme Tipi</th>
                    <th>EFT Onayı Bekleniyor</th>
                    <th>Sipariş Durumu</th>
                    <th>Sipariş Tarihi</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->order_no }}</td>
                        <td>{{ $order->customer->firm_name }}</td>
                        <td>
                            <span class="badge badge-success">{{ $order->total }}$</span>
                            <span class="badge badge-primary">{{ $order->converted_total }}₺</span>
                        </td>
                        <td>{{ $order->payment_method }}</td>
                        <td>
                            @if ($order->isBankTransfer())
                                @if ($order->waiting_transfer_approve)
                                    <span class="badge badge-warning">Evet</span>
                                @else
                                    <span class="badge badge-success">Onaylandı</span>
                                @endif
                            @else
                                <span class="badge badge-danger">Hayır</span>
                            @endif
                        <td>
                            @if ($order->is_paid)
                                <span class="badge badge-success">Ödendi</span>
                            @else
                                <span class="badge badge-danger">Ödenmedi</span>
                            @endif
                        </td>
                        <td>{{ $order->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.order.show', $order->id) }}"
                                class="btn btn-primary btn-sm">Görüntüle</a>
                            {{-- <a href="{{ route('admin.order.edit', $order->id) }}" class="btn btn-primary btn-sm">Düzenle</a>
                            <a href="{{ route('admin.order.delete', $order->id) }}" class="btn btn-danger btn-sm">Sil</a>  --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{-- pagination --}}
        <div class="card-footer">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
