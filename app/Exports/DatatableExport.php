<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use MrCatz\DataTable\MrCatzExport;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DatatableExport implements FromView, ShouldAutoSize, WithStyles
{
    public function __construct(
        private string $title,
        private array $headers,
        private array $rows
    ) {}

    public function view(): View
    {
        return view('exports.datatable-excel', [
            'title' => $this->title,
            'headers' => $this->headers,
            'rows' => $this->rows,
        ]);
    }

    public function styles(Worksheet $sheet): array
    {
        $c = MrCatzExport::colors();
        $lastCol = MrCatzExport::columnLetter(count($this->headers));
        $headerRow = 4;
        $dataStart = 5;
        $lastRow = $headerRow + count($this->rows);

        $sheet->mergeCells("A1:{$lastCol}1");
        $sheet->mergeCells("A2:{$lastCol}2");
        $sheet->mergeCells("A3:{$lastCol}3");
        $sheet->getRowDimension(3)->setRowHeight(8);
        $sheet->getRowDimension($headerRow)->setRowHeight(28);

        $sheet->freezePane("A{$dataStart}");

        for ($r = $dataStart; $r <= $lastRow; $r++) {
            if (($r - $dataStart) % 2 === 1) {
                $sheet->getStyle("A{$r}:{$lastCol}{$r}")->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => $c['stripe']],
                    ],
                ]);
            }
        }

        $sheet->getStyle("A{$dataStart}:A{$lastRow}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => $c['title_text']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],
            2 => [
                'font' => ['size' => 10, 'italic' => true, 'color' => ['rgb' => $c['subtitle_text']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],
            $headerRow => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => $c['header_text']]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $c['header_bg']]],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            "A{$headerRow}:{$lastCol}{$lastRow}" => [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $c['border']]]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            ],
            "A{$headerRow}:{$lastCol}{$headerRow}" => [
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $c['header_border']]]],
            ],
            "A{$lastRow}:{$lastCol}{$lastRow}" => [
                'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => $c['bottom_border']]]],
            ],
        ];
    }
}
