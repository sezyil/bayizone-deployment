@extends('admin.layout.admin')

@section('title', 'Müşteriler')

@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">Müşteriler</h5>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Firma Adı</th>
                    <th>Yetkili</th>
                    <th>Email</th>
                    <th>Telefon</th>
                    <th>Adres</th>
                    <th>Kayıt Tarihi</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $customer)
                    <tr>
                        <td>{{ $customer->firm_name }}</td>
                        <td>{{ $customer->authorized_person }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->address }}
                            <br />
                            {{ $customer->city?->name ?? '--' }}/{{ $customer->state?->name ?? '--' }}/{{ $customer->country?->translateName(true) ?? '---' }}
                        </td>
                        <td>{{ $customer->created_at->format('d.m.Y H:i') }}</td>
                        <td>
                            {{-- <a href="{{ route('admin.customer.edit', $customer->id) }}"
                            class="btn btn-primary btn-sm">Düzenle</a>
                        <a href="{{ route('admin.customer.delete', $customer->id) }}"
                            class="btn btn-danger btn-sm">Sil</a> --}}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{-- pagination --}}
        <div class="card-footer">
            {{ $customers->links() }}
        </div>
    </div>
@endsection
