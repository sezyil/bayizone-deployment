<table>
    <thead>
        <tr>
            <th colspan="9" style="text-align: center;" bgcolor="#f0f0f0">
                Fişler
            </th>
        </tr>
        <tr>
            <th>
                Fiş No
            </th>
            <th>
                Müşteri
            </th>
            <th>
                Hareket Tipi
            </th>
            <th>
                Ödeme Durumu
            </th>
            <th>
                Tutar
            </th>
            <th>
                Hareket Tipi
            </th>
            <th>
                İşlem Tarihi
            </th>
            <th>
                Vade Tarihi
            </th>
            {{-- Açıklama --}}
            <th>
                Açıklama
            </th>


        </tr>
    </thead>
    <tbody>
        @foreach ($data as $line)
            <tr>
                <td>

                    {{ $line->fiche_no }}
                </td>
                <td style="text-align: center;">
                    {{ $line->companyCustomer->company_name }}
                </td>
                <td>
                    {{ $line->getFicheTypeDescription() }}
                </td>
                <td bgcolor="{{ $line->is_paid ? 'green' : 'red' }}">
                    {{ $line->is_paid ? 'Ödendi' : 'Ödenmedi' }}
                </td>
                <td>
                    @if ($line->io_type == 0)
                        -
                    @endif
                    {{ $line->getCurrency?->format($line->amount, true) }}
                </td>
                <td bgcolor="{{ $line->io_type == 1 ? 'green' : 'red' }}">
                    {{ $line->getIoTypeDescription() }}
                </td>
                <td>
                    {{ \Carbon\Carbon::parse($line->date)->isoFormat('LL') }}
                </td>
                @if (!$line->due_date)
                    <td>
                        ----
                    </td>
                @else
                    @if ($line->isOverdue())
                        <td bgcolor="red">
                            {{ \Carbon\Carbon::parse($line->due_date)->isoFormat('LL') }}
                        </td>
                    @else
                        <td>
                            {{ \Carbon\Carbon::parse($line->due_date)->isoFormat('LL') }}
                        </td>
                    @endif
                @endif
                {{-- Açıklama --}}
                <td>
                    {{ $line->description ?? '---' }}
                </td>



            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" style="text-align: right;">
                Toplam
            </td>
            <td>
                {{ $summary['formatted_balance'] }}
            </td>
        </tr>
    </tfoot>
</table>
