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
                            <span style="line-height: 19.6px;">@lang('Hello!') {{ Str::ucfirst($user->fullname) }},</span>
                        </p>
                        <p style="line-height: 140%;"><span style="line-height: 19.6px;">
                                @lang('email/company-customer/created-new-company-customer.body', ['company' => $company_name])
                            </span></p>
                        <p style="line-height: 140%;"> </p>
                        <p style="line-height: 140%;">
                            <span style="line-height: 19.6px;">
                                <strong>@lang('email/company-customer/created-new-company-customer.company_code') </strong>{{ $code }}<br>
                                <strong>@lang('email/company-customer/created-new-company-customer.email_address') </strong> {{ $user->email }}<br>
                                <strong>@lang('email/company-customer/created-new-company-customer.password') </strong> {{ $password }}
                            </span>
                        </p>
                        <p style="line-height: 140%;"> </p>
                    </div>

                </td>
            </tr>
        </tbody>
    </table>
@endsection
@section('button')
    <x-mail.button :uri="$uri" :text="__('email/company-customer/created-new-company-customer.login')" />
@endsection
