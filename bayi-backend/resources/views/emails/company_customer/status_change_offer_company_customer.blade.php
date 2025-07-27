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
                            <span style="line-height: 19.6px;">@lang('Hello!')</span>
                        </p>
                        <p style="line-height: 140%;"><span style="line-height: 19.6px;">
                                @if ($is_new)
                                    @lang('email/company-customer/offer-status-changed.body_new', ['number' => $offer->offer_no])
                                @else
                                    @lang('email/company-customer/offer-status-changed.body_updated', ['number' => $offer->offer_no])
                                @endif
                                <br />
                                @if ($is_new)
                                    @lang('email/company-customer/offer-status-changed.create_date') {{ \Carbon\Carbon::parse($offer->created_at)->format('d.m.Y') }}
                                @else
                                    @lang('email/company-customer/offer-status-changed.update_date') {{ \Carbon\Carbon::parse($offer->updated_at)->format('d.m.Y') }}
                                @endif
                                <br />
                                @lang('email/company-customer/offer-status-changed.status') {{ $status }}
                            </span></p>
                        <p style="line-height: 140%;"> </p>
                    </div>

                </td>
            </tr>
        </tbody>
    </table>
@endsection

@if ($is_new)
    @section('button')
        <x-mail.button :uri="$redirect_url" :text="__('email/company-customer/offer-status-changed.go_to_offer')" />
    @endsection
@endif
