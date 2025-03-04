<?php

namespace App\Exports;

use App\Models\SocialMediaReport;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SocialMediaReportsExport implements FromQuery, WithHeadings, WithMapping, WithStyles
{
    use Exportable;

    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = SocialMediaReport::query()
            ->with(['category', 'user'])
            ->orderBy('posting_date', 'desc');

        // Filter berdasarkan bulan
        if (!empty($this->filters['month'])) {
            $query->whereRaw('DATE_FORMAT(posting_date, "%Y-%m") = ?', [$this->filters['month']]);
        }

        // Filter berdasarkan kategori
        if (!empty($this->filters['category_id'])) {
            $query->where('category_id', $this->filters['category_id']);
        }

        // Filter berdasarkan user
        if (!empty($this->filters['user_id'])) {
            $query->where('user_id', $this->filters['user_id']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Tanggal Posting',
            'Jenis Konten',
            'Link URL',
            'Tim',
            'Tanggal Dibuat',
            'Terakhir Diupdate'
        ];
    }

    public function map($report): array
    {
        return [
            $report->posting_date->format('d/m/Y'),
            $report->category->name,
            $report->url,
            $report->user->name,
            $report->created_at->format('d/m/Y H:i'),
            $report->updated_at->format('d/m/Y H:i')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ],
        ];
    }
} 