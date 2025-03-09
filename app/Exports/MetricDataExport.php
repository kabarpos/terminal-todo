<?php

namespace App\Exports;

use App\Models\MetricData;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MetricDataExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $accountId;
    protected $startDate;
    protected $endDate;

    public function __construct($accountId = null, $startDate = null, $endDate = null)
    {
        $this->accountId = $accountId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query()
    {
        return MetricData::query()
            ->with(['account.platform'])
            ->when($this->accountId, function ($query) {
                return $query->where('social_account_id', $this->accountId);
            })
            ->when($this->startDate && $this->endDate, function ($query) {
                return $query->whereBetween('date', [$this->startDate, $this->endDate]);
            })
            ->orderBy('date', 'desc');
    }

    public function headings(): array
    {
        return [
            'Platform',
            'Akun',
            'Tanggal',
            'Followers',
            'Engagement Rate (%)',
            'Reach',
            'Impressions',
            'Likes',
            'Comments',
            'Shares',
            'Total Interaksi'
        ];
    }

    public function map($row): array
    {
        return [
            $row->account->platform->name ?? '-',
            $row->account->name ?? '-',
            $row->date->format('d/m/Y'),
            $row->followers_count,
            $row->engagement_rate,
            $row->reach,
            $row->impressions,
            $row->likes,
            $row->comments,
            $row->shares,
            $row->likes + $row->comments + $row->shares
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
} 