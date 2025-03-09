<?php

namespace App\Imports;

use App\Models\MetricData;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class MetricDataImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        return new MetricData([
            'social_account_id' => $row['social_account_id'],
            'date' => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['date']),
            'followers_count' => $row['followers_count'],
            'engagement_rate' => $row['engagement_rate'],
            'reach' => $row['reach'],
            'impressions' => $row['impressions'],
            'likes' => $row['likes'],
            'comments' => $row['comments'],
            'shares' => $row['shares'],
        ]);
    }

    public function rules(): array
    {
        return [
            'social_account_id' => ['required', 'exists:social_accounts,id'],
            'date' => ['required', 'date'],
            'followers_count' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'engagement_rate' => ['required', 'numeric', 'between:0,100'],
            'reach' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'impressions' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'likes' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'comments' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'shares' => ['required', 'integer', 'min:0', 'max:1000000000'],
            '*.social_account_id' => Rule::unique('metric_data')
                ->where(function ($query) {
                    return $query->whereDate('date', request('date'));
                }),
        ];
    }

    public function customValidationMessages()
    {
        return [
            'social_account_id.exists' => 'ID Akun Sosial tidak valid.',
            'date.required' => 'Tanggal wajib diisi.',
            'date.date' => 'Format tanggal tidak valid.',
            'followers_count.max' => 'Jumlah followers terlalu besar (max: 1 miliar)',
            'engagement_rate.between' => 'Engagement rate harus antara 0 dan 100',
            'reach.max' => 'Jumlah reach terlalu besar (max: 1 miliar)',
            'impressions.max' => 'Jumlah impressions terlalu besar (max: 1 miliar)',
            'likes.max' => 'Jumlah likes terlalu besar (max: 1 miliar)',
            'comments.max' => 'Jumlah comments terlalu besar (max: 1 miliar)',
            'shares.max' => 'Jumlah shares terlalu besar (max: 1 miliar)',
            '*.social_account_id.unique' => 'Data metrik untuk akun dan tanggal ini sudah ada.',
        ];
    }
} 