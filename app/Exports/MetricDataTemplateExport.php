<?php

namespace App\Exports;

use App\Models\SocialAccount;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MetricDataTemplateExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return SocialAccount::with('platform')->get();
    }

    public function headings(): array
    {
        return [
            'social_account_id',
            'Platform',
            'Akun',
            'date',
            'followers_count',
            'engagement_rate',
            'reach',
            'impressions',
            'likes',
            'comments',
            'shares'
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->platform->name ?? '-',
            $row->name,
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Style untuk header
        $sheet->getStyle('1')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0']
            ]
        ]);

        // Tambahkan validasi untuk kolom
        $lastRow = $sheet->getHighestRow();
        
        // Validasi untuk date (kolom D)
        $sheet->getStyle('D2:D'.$lastRow)->getNumberFormat()
            ->setFormatCode('dd/mm/yyyy');

        // Validasi untuk angka (kolom E-K)
        $sheet->getStyle('E2:K'.$lastRow)->getNumberFormat()
            ->setFormatCode('#,##0');

        // Kunci kolom ID dan informasi akun
        $sheet->getStyle('A1:C'.$lastRow)->applyFromArray([
            'protection' => [
                'locked' => true,
                'hidden' => true
            ]
        ]);

        $sheet->getProtection()->setSheet(true);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
} 