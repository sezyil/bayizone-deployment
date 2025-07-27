@extends('admin.layout.admin')

@section('title', 'Kuponlar')

@section('header-buttons')
    <a href="{{ route('admin.coupon.create') }}" class="btn btn-primary btn-sm">Kupon Oluştur</a>
@endsection

@section('content')
    <x-success-message />
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">Kuponlar</h5>
        </div>



        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Kupon Kodu</th>
                    <th>İndirim Oranı</th>
                    <th>Müşteri Bazlı Mı?</th>
                    <th>Geçerlilik Tarihi</th>
                    <th>Limit</th>
                    <th>Oluşturulma Tarihi</th>
                    <th>Oluşturan</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($coupons as $coupon)
                    <tr>
                        <td>{{ $coupon->code }}</td>
                        <td>{{ $coupon->percentage }}%</td>
                        <td>
                            @if ($coupon->customer_based)
                                <span class="badge badge-success">Evet</span>
                            @else
                                <span class="badge badge-danger">Hayır</span>
                            @endif
                        </td>
                        <td>{{ $coupon->expires_at->format('d.m.Y H:i') }}</td>
                        <td>{{ $coupon->limit }}</td>
                        <td>{{ $coupon->created_at->format('d.m.Y H:i') }}</td>
                        <td>{{ $coupon->createdBy?->fullname }}</td>
                        <td>
                            <a href="{{ route('admin.coupon.edit', ['id' => $coupon->id]) }}"
                                class="btn btn-primary btn-sm">Düzenle</a>
                            {{-- <a href="{{ route('admin.coupon.edit', $coupon->id) }}" class="btn btn-primary btn-sm">Düzenle</a> --}}
                            <a href="{{ route('admin.coupon.delete', $coupon->id) }}" class="btn btn-danger btn-sm">Sil</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{-- pagination --}}
        <div class="card-footer">
            {{ $coupons->links() }}
        </div>
    </div>
@endsection
