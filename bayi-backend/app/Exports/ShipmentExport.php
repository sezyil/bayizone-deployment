<?php

namespace App\Exports;

use App\Models\Customer\CustomerOffer;
use App\Models\Customer\CustomerOrder;
use App\Models\CustomerShipment;
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


class ShipmentExport implements FromView, ShouldAutoSize, WithDrawings, WithDefaultStyles, WithStyles
{
    protected CustomerShipment $shipment;
    protected string $lang;
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct(CustomerShipment $shipment, $lang)
    {
        $this->shipment = $shipment;
        $this->lang = $lang;
    }

    public function collection()
    {
        return $this->shipment;
    }

    public function view(): View
    {
        app()->setLocale($this->lang);
        $data = $this->shipment;
        return view('export.excel.shipment.export-main', [
            'logo' => $data->customer->image,
            'data' => $data,
        ]);
    }

    public function drawings()
    {
        $data = $this->shipment;
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
            /* g is total price column text red  */
            'H' => [
                'font' => [
                    'color' => ['argb' => Color::COLOR_RED],
                ],
            ],
            // I is total volume column text blue
            'J' => [
                'font' => [
                    'color' => ['argb' => Color::COLOR_BLUE],
                ],
            ],
            // K is total Package column text magenta
            'L' => [
                'font' => [
                    'color' => ['argb' => Color::COLOR_MAGENTA],
                ],
            ],
            'N' => [
                'font' => [
                    'color' => ['argb' => Color::COLOR_GREEN],
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
