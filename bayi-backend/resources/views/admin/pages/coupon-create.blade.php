@extends('admin.layout.admin')

@section('title', 'Kupon Oluştur')

@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">Kupon Oluştur</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form action="{{ route('admin.coupon.store') }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="form-group">
                            <label for="">Kupon Kodu</label>
                            <input type="text" name="code" class="form-control" readonly value="{{ $coupon_code }}">
                            <x-input-error for="code" />
                        </div>

                        <div class="form-group">
                            <label for="">Geçerlilik Tarihi</label>
                            <input type="date" name="expires_at" class="form-control" value="{{ old('expires_at') }}"
                                min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            <x-input-error for="expires_at" />
                        </div>

                        <div class="form-group">
                            <label for="percentage">İndirim Oranı (%)</label>
                            <input type="text" name="percentage" id="percentage" class="form-control" max="100"
                                value="{{ old('percentage') }}">
                            <x-input-error for="percentage" />
                        </div>

                        <div class="form-group">
                            <label for="">Ürün Grupları</label>
                            <select name="product_group" id="" class="form-control"
                                value="{{ old('product_group') }}">
                                <option value="">Seçiniz</option>
                                @foreach ($product_group as $k => $v)
                                    <option value="{{ $k }}">{{ $v }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="product_group" />
                        </div>

                        {{-- only_cash --}}
                        <div class="form-group">
                            <label for="">Sadece Nakit Ödemelerde Geçerli Olsun Mu?</label>
                            <select name="only_cash" id="" class="form-control" value="{{ old('only_cash', 0) }}">
                                <option value="0">Hayır</option>
                                <option value="1">Evet</option>
                            </select>
                            <x-input-error for="only_cash" />
                        </div>

                        <div class="form-group">
                            <label for="">Müşteri Bazlı Mı?</label>
                            <select name="customer_based" id="" class="form-control"
                                value="{{ old('customer_based', 0) }}">
                                <option value="0">Hayır</option>
                                <option value="1">Evet</option>
                            </select>
                            <x-input-error for="customer_based" />
                        </div>

                        <div class="form-group" id="customer_based_input">
                            {{-- müşteriler multiselect --}}
                            <label for="">Müşteriler</label>
                            <select name="customers[]" id="customers" class="form-control" multiple>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}">{{ $customer->firm_name }}</option>
                                @endforeach
                            </select>
                            <x-input-error for="customers" />
                        </div>


                        <div class="form-group">
                            <label for="">Kullanım Limiti (Boş bırakırsanız sınırsız olur)</label>
                            <input type="number" name="limit" class="form-control" value="{{ old('limit') }}">
                            <x-input-error for="limit" />
                        </div>

                        <div class="form-group">
                            {{-- is_active --}}
                            <label for="">Aktif Mi?</label>
                            <select name="is_active" id="" class="form-control" value="{{ old('is_active', 1) }}">
                                <option value="1">Evet</option>
                                <option value="0">Hayır</option>
                            </select>
                            <x-input-error for="is_active" />
                        </div>

                        <button type="submit" class="btn btn-primary">Kupon Oluştur</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#customer_based_input').hide();
            $('select[name="customer_based"]').on('change', function() {
                if ($(this).val() == 1) {
                    $('#customer_based_input').show();
                } else {
                    $('#customer_based_input').hide();
                }
            });
        });
    </script>
@endpush
