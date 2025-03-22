<?php

namespace App\Exports;

use App\Models\MetricData;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class MetricDataExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $accountId;
    protected $startDate;
    protected $endDate;
    protected $dateRange;

    public function __construct($accountId = null, $startDate = null, $endDate = null, $dateRange = null)
    {
        $this->accountId = $accountId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->dateRange = $dateRange;
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        \Log::info('MetricDataExport: collection being built', [
            'accountId' => $this->accountId,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'dateRange' => $this->dateRange
        ]);
        
        try {
            $query = MetricData::with(['account.platform']);
                
            // Filter berdasarkan akun
            if (!empty($this->accountId)) {
                $query->where('social_account_id', $this->accountId);
            }
                
            // Apply date filter
            if (!empty($this->startDate) && !empty($this->endDate)) {
                \Log::info('MetricDataExport: filtering by date range', [
                    'startDate' => $this->startDate,
                    'endDate' => $this->endDate
                ]);
                $query->whereBetween('date', [$this->startDate, $this->endDate]);
            } else if (!empty($this->dateRange) && $this->dateRange != 'custom') {
                $endDate = now();
                $startDate = $endDate->copy()->subDays((int)$this->dateRange);
                \Log::info('MetricDataExport: filtering by days', [
                    'days' => $this->dateRange,
                    'startDate' => $startDate->format('Y-m-d'),
                    'endDate' => $endDate->format('Y-m-d')
                ]);
                $query->whereBetween('date', [$startDate, $endDate]);
            }
            
            $query->orderBy('date', 'desc');
            
            // Log jumlah data yang akan diexport
            $count = $query->count();
            \Log::info('MetricDataExport: total rows to export', ['count' => $count]);
            
            if ($count === 0) {
                \Log::warning('MetricDataExport: No data to export with these filters!');
                // Kembalikan collection kosong
                return collect([]);
            }
            
            // Limit jumlah data jika terlalu banyak untuk mencegah memory issues
            $limit = 5000; // Batasi maksimal 5000 baris
            if ($count > $limit) {
                \Log::warning("MetricDataExport: Data too large, limiting to {$limit} rows");
                $query->limit($limit);
            }
            
            $data = $query->get();
            \Log::info('MetricDataExport: Data successfully fetched', ['count' => $data->count()]);
            return $data;

        } catch (\Exception $e) {
            \Log::error('Error in MetricDataExport collection(): ' . $e->getMessage(), [
                'exception' => $e
            ]);
            
            // Kembalikan collection kosong jika terjadi error
            return collect([]);
        }
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
        // Jika row null atau tidak valid, kembalikan baris kosong
        if (!$row || !isset($row->id)) {
            return [
                'N/A', 'N/A', 'N/A', 0, 0, 0, 0, 0, 0, 0, 0
            ];
        }
        
        try {
            // Pastikan relasi account dan platform ada
            $platform = isset($row->account) && isset($row->account->platform) 
                ? $row->account->platform->name 
                : 'Tidak Diketahui';
                
            $account = isset($row->account) ? $row->account->name : 'Tidak Diketahui';
            
            // Format tanggal dengan aman
            $date = $row->date ? $row->date->format('d/m/Y') : 'n/a';
            
            // Kembalikan data dengan nilai default jika null
            return [
                $platform,
                $account,
                $date,
                $row->followers_count ?? 0,
                $row->engagement_rate ?? 0,
                $row->reach ?? 0,
                $row->impressions ?? 0,
                $row->likes ?? 0,
                $row->comments ?? 0,
                $row->shares ?? 0,
                ($row->likes ?? 0) + ($row->comments ?? 0) + ($row->shares ?? 0)
            ];
        } catch (\Exception $e) {
            \Log::error('Error mapping row in export: ' . $e->getMessage(), [
                'row_id' => $row->id ?? 'unknown',
                'exception' => $e
            ]);
            
            // Kembalikan data kosong jika terjadi error
            return [
                'ERROR',
                'Error mapping data',
                date('d/m/Y'),
                0, 0, 0, 0, 0, 0, 0, 0
            ];
        }
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
} 