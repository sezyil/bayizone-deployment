@extends('admin.layout.admin')

@section('title', 'Kupon Güncelle')

@section('content')
    <div class="card">
        <div class="card-header header-elements-inline">
            <h5 class="card-title">Kupon Güncelle</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <form action="{{ route('admin.coupon.update', ['id' => $coupon->id]) }}" method="POST" autocomplete="off">
                        @csrf
                        <div class="form-group">
                            <label for="">Kupon Kodu</label>
                            <input type="text" name="code" class="form-control" readonly value="{{ $coupon->code }}">
                            <x-input-error for="code" />
                        </div>

                        <div class="form-group">
                            <label for="">Geçerlilik Tarihi</label>
                            <input type="date" name="expires_at" class="form-control"
                                value="{{ old('expires_at', $coupon->expires_at->format('Y-m-d')) }}"
                                min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                            <x-input-error for="expires_at" />
                        </div>

                        <div class="form-group">
                            <label for="percentage">İndirim Oranı (%)</label>
                            <input type="text" name="percentage" id="percentage" class="form-control" max="100"
                                value="{{ old('percentage', $coupon->percentage) }}">
                            <x-input-error for="percentage" />
                        </div>

                        <div class="form-group">
                            <label for="">Ürün Grupları</label>
                            <select name="product_group" id="" class="form-control"
                                value="{{ old('product_group', $coupon->product_group) }}">
                                <option value="">Seçiniz</option>
                                @foreach ($product_group as $k => $v)
                                    <option value="{{ $k }}" @if ($coupon->product_group == $k) selected @endif>
                                        {{ $v }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error for="product_group" />
                        </div>

                        {{-- only_cash --}}
                        <div class="form-group">
                            <label for="">Sadece Nakit Ödemelerde Geçerli Olsun Mu?</label>
                            <select name="only_cash" id="" class="form-control"
                                value="{{ old('only_cash', $coupon->only_cash) }}">
                                @if ($coupon->only_cash)
                                    <option value="1" selected>Evet</option>
                                    <option value="0">Hayır</option>
                                @else
                                    <option value="0" selected>Hayır</option>
                                    <option value="1">Evet</option>
                                @endif
                            </select>
                            <x-input-error for="only_cash" />
                        </div>

                        <div class="form-group">
                            <label for="">Müşteri Bazlı Mı?</label>
                            <select name="customer_based" id="" class="form-control"
                                value="{{ old('customer_based', $coupon->customer_based) }}">
                                @if ($coupon->customer_based)
                                    <option value="1" selected>Evet</option>
                                    <option value="0">Hayır</option>
                                @else
                                    <option value="0" selected>Hayır</option>
                                    <option value="1">Evet</option>
                                @endif
                            </select>
                            <x-input-error for="customer_based" />
                        </div>


                        <div class="form-group" id="customer_based_input">
                            {{-- müşteriler multiselect --}}
                            <label for="">Müşteriler</label>
                            <select name="customers[]" id="customers" class="form-control" multiple>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}"
                                        {{ in_array($customer->id, $coupon->customers->pluck('id')->toArray()) ? 'selected' : '' }}>
                                        {{ $customer->firm_name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error for="customers" />
                        </div>


                        <div class="form-group">
                            <label for="">Kullanım Limiti (Boş bırakırsanız sınırsız olur)</label>
                            <input type="number" name="limit" class="form-control"
                                value="{{ old('limit', $coupon->limit) }}">
                            <x-input-error for="limit" />
                        </div>

                        <div class="form-group">
                            {{-- is_active --}}
                            <label for="">Aktif Mi?</label>
                            <select name="is_active" id="" class="form-control"
                                value="{{ old('is_active', $coupon->is_active) }}">
                                <option value="1">Evet</option>
                                <option value="0">Hayır</option>
                            </select>
                            <x-input-error for="is_active" />
                        </div>

                        <button type="submit" class="btn btn-primary">Kupon Güncelle</button>
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
            $('select[name="customer_based"]').change(function() {
                if ($(this).val() == 1) {
                    $('#customer_based_input').show();
                } else {
                    $('#customer_based_input').hide();
                }
            }).trigger('change');
        });
    </script>
@endpush
