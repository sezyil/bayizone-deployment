<?php

namespace App\Exports;

use App\Libraries\Client\SanctumHelper;
use App\Models\Customer\CustomerOffer;
use App\Models\Customer\CustomerOrder;
use App\Models\CustomerShipment;
use App\Models\CustomerTransactions\CustomerTransaction;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;


class TransactionExport implements FromView, ShouldAutoSize, WithDrawings, WithDefaultStyles, WithStyles
{
    /**
     * @var $transactions
     */
    protected  $transactions;
    protected $logo;
    protected $summaryData;
    protected $startDate;
    protected $endDate;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct($transactions, $summaryData, $startDate = null, $endDate = null)
    {
        $this->transactions = $transactions;
        $this->logo = SanctumHelper::getCustomer()->image;
        $this->summaryData = $summaryData;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->transactions;
    }

    public function view(): View
    {
        $data = $this->transactions;
        $dateFormat = fn ($date) => Carbon::parse($date)->isoFormat('LL');
        $startDate = $this->startDate ? $dateFormat($this->startDate) : $dateFormat($data->last()->date);
        $endDate = $this->endDate ? $dateFormat($this->endDate) : $dateFormat($data->first()->date);


        return view('export.excel.transaction.export-main', [
            'logo' => $this->logo,
            'data' => $data,
            'summary' => $this->summaryData,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path($this->logo));
        $drawing->setHeight(90);
        $drawing->setCoordinates('C1');
        $drawing->setCoordinates2('D4');
        $drawing->setOffsetX(10);
        $drawing->setOffsetY(10);

        return $drawing;
    }

    public function defaultStyles(Style $defaultStyle)
    {
        /* // Configure the default styles
        return $defaultStyle->getFont()->setSize(16); */

        // Or return the styles array
        return [
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
            /* vertical */
            'alignment' => [
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }
}
