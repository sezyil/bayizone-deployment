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
                            <span style="line-height: 19.6px;">Merhaba {{ $order->customer->firm_name }},</span>
                        </p>
                        <p style="line-height: 140%;"> </p>
                        <p style="line-height: 140%;"><span style="line-height: 19.6px;">
                                {{ $order->order_no }} no lu siparişiniz oluşturuldu. Sipariş detayları aşağıdaki
                                gibidir.
                                <p style="line-height: 140%;"> </p>
                                Tarih: {{ $order->created_at->format('d.m.Y') }}
                            </span></p>
                        <p style="line-height: 140%;"> </p>
                        <table border="1" style="border-collapse: collapse; width: 100%;">
                            <thead>
                                <tr>
                                    <th style="text-align: center;vertical-align: middle">Ürün</th>
                                    <th style="text-align: center;vertical-align: middle">Miktar</th>
                                    <th style="text-align: center;vertical-align: middle">Fiyat</th>
                                    <th style="text-align: center;vertical-align: middle">Toplam(KDV Dahil)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->lines as $line)
                                    <tr>
                                        <td style="padding:5px;">{{ $line->name }}</td>
                                        <td style="padding:5px;">
                                            @if ($line->type == 'subscription')
                                                {{ $line->item_data['duration'] }} (Ay)
                                            @elseif ($line->item_data['is_boolean'])
                                                {{ (int) $line->quantity }} (Ay)
                                            @else
                                                {{ (int) $line->quantity }} adet
                                            @endif
                                        </td>
                                        <td style="padding:5px;">@currencyFormat($line->price)</td>
                                        <td style="padding:5px;">@currencyFormat($line->total)</td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <td colspan="3" style="text-align: right;padding:5px;">Toplam</td>
                                    <td style="padding:5px;">@currencyFormat($order->total)</td>
                                </tr>
                            </tfoot>
                        </table>
                        <p style="line-height: 140%;"> </p>
                        <p style="line-height: 140%;"><span style="line-height: 19.6px;">
                                Ödeme işlemleri ve sipariş durumu hakkında bilgi almak için panelinizi ziyaret
                                edebilirsiniz.
                            </span></p>

                    </div>

                </td>
            </tr>
        </tbody>
    </table>
@endsection
