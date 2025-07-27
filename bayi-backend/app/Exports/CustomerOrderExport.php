<?php

namespace App\Exports;

use App\Models\Customer\CustomerOffer;
use App\Models\Customer\CustomerOrder;
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


class CustomerOrderExport implements FromView, ShouldAutoSize, WithDrawings, WithDefaultStyles, WithStyles
{
    protected CustomerOrder $order;
    protected string $lang;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct(CustomerOrder $order, $lang)
    {
        $this->order = $order;
        $this->lang = $lang;
    }

    public function collection()
    {
        return $this->order;
    }

    public function view(): View
    {
        app()->setLocale($this->lang);
        $data = $this->order;
        return view('export.excel.customerorder.export-main', [
            'logo' => $data->customer->image,
            'data' => $data,
        ]);
    }

    public function drawings()
    {
        $data= $this->order;
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('This is my logo');
        $drawing->setPath(public_path($data->customer->image));
        $drawing->setHeight(90);
        $drawing->setCoordinates('F1');
        $drawing->setCoordinates2('G7');
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
        return [
            /* H is total price column text red  */
            'H' => [
                'font' => [
                    'color' => ['argb' => Color::COLOR_RED],
                ],
            ],
            // J is total volume column text blue
            'J' => [
                'font' => [
                    'color' => ['argb' => Color::COLOR_BLUE],
                ],
            ],
            // L is total Package column text magenta
            'L' => [
                'font' => [
                    'color' => ['argb' => Color::COLOR_MAGENTA],
                ],
            ],
            /* 8 is header text-center */
            8 => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }
}
