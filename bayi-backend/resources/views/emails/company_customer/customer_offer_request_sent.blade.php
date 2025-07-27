@extends('emails.system.layout.main')
@section('title', $title)
@section('content')
    <table id="u_content_text_1" style="font-family:arial,helvetica,sans-serif;" role="presentation" cellpadding="0"
        cellspacing="0" width="100%" border="0">
        <tbody>
            <tr>
                <td class="v-container-padding-padding"
                    style="overflow-wrap:break-word;word-break:break-word;padding:20px 90px 30px 60px;font-family:arial,helvetica,sans-serif;"
                    align="left">

                    <div class="v-text-align v-font-size"
                        style="font-size: 14px; line-height: 140%; text-align: left; word-wrap: break-word;">
                        <p style="line-height: 140%;">
                            <span
                                style="line-height: 19.6px;">{{ __('email/company-customer/offer-request-sent.greeting', ['companyName' => $data->companyCustomer->company_name]) }}<br />
                        </p>
                        <br />
                        <p style="line-height: 140%;">
                            <span style="line-height: 19.6px;">
                                {!! __('email/company-customer/offer-request-sent.quote_request_received', ['firmName' => $data->customer->firm_name]) !!}
                            </span>
                        </p>
                        <br />
                        <p style="line-height: 140%;">
                            <span style="line-height: 19.6px;">
                                {!! __('email/company-customer/offer-request-sent.quote_request_number', ['requestNo' => $data->request_no]) !!}
                            </span>
                        </p>
                        <br />
                        <p style="line-height: 140%;">
                            <span style="line-height: 19.6px;">
                                @lang('email/company-customer/offer-request-sent.creation_date', ['createdAt' => \Carbon\Carbon::parse($data->created_at)->format('d.m.Y')])<br />
                            </span>
                        </p>
                        <br />
                        <p style="line-height: 140%;">
                            <span style="line-height: 19.6px;">
                                @lang('email/company-customer/offer-request-sent.quote_request_details')
                            </span>
                        </p>

                        <table border="1" style="border-collapse: collapse; width: 100%;">
                            <colgroup>
                                <col style="width: 33.3333%">
                                <col style="width: 20%">
                                <col style="width: 46.6667%">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th style="text-align: center;vertical-align: middle">@lang('email/company-customer/offer-request-sent.table_product')</th>
                                    <th style="text-align: center;vertical-align: middle">@lang('email/company-customer/offer-request-sent.table_quantity')</th>
                                    <th style="text-align: center;vertical-align: middle">@lang('email/company-customer/offer-request-sent.table_variant')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->lines as $line)
                                    <tr>
                                        <td style="padding:5px;vertical-align:middle;">
                                            {{ $line->product->description->name }}</td>
                                        <td style="padding:5px;text-align:center;vertical-align:middle;">
                                            {{ $line->quantity }}</td>
                                        <td style="padding:5px;vertical-align:middle;">
                                            @forelse ($line->variants as $variant)
                                                <p>{{ \App\Enums\VariantTypesEnum::getDescription($variant->type, $lang) }}
                                                    :
                                                    {{ $variant->value[$lang] ?? $variant->value['tr'] }}</p>
                                                @if (!$loop->last)
                                                    <hr>
                                                @endif
                                            @empty
                                                ---
                                            @endforelse
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </td>
            </tr>
        </tbody>
    </table>
@endsection
