<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use League\Csv\Reader;
use League\Csv\Writer;
use SplTempFileObject;

class UserImportExportService
{
    protected array $exportableFields = [
        'id',
        'name',
        'email',
        'phone',
        'status',
        'created_at',
        'email_verified_at'
    ];

    protected array $importableFields = [
        'name',
        'email',
        'phone',
        'password',
        'status'
    ];

    /**
     * Export users to CSV
     */
    public function exportToCsv(Collection $users = null): Writer
    {
        $users = $users ?? User::all();
        
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        
        // Add headers
        $csv->insertOne($this->exportableFields);
        
        // Add data
        $users->each(function ($user) use ($csv) {
            $row = [];
            foreach ($this->exportableFields as $field) {
                $row[] = $user->$field;
            }
            $csv->insertOne($row);
        });
        
        return $csv;
    }

    /**
     * Import users from CSV
     */
    public function importFromCsv($file): array
    {
        $csv = Reader::createFromPath($file->getPathname());
        $csv->setHeaderOffset(0);
        
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => []
        ];
        
        foreach ($csv as $offset => $record) {
            try {
                // Validate record
                $validator = Validator::make($record, [
                    'name' => ['required', 'string', 'max:255'],
                    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                    'phone' => ['nullable', 'string', 'max:20'],
                    'password' => ['nullable', 'string', 'min:8'],
                    'status' => ['nullable', 'string', 'in:active,pending,inactive']
                ]);

                if ($validator->fails()) {
                    throw new \Exception($validator->errors()->first());
                }

                // Create user
                $user = User::create([
                    'name' => $record['name'],
                    'email' => $record['email'],
                    'phone' => $record['phone'] ?? null,
                    'password' => Hash::make($record['password'] ?? Str::random(12)),
                    'status' => $record['status'] ?? 'pending'
                ]);

                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Row {$offset}: {$e->getMessage()}";
            }
        }
        
        return $results;
    }

    /**
     * Get import template
     */
    public function getImportTemplate(): Writer
    {
        $csv = Writer::createFromFileObject(new SplTempFileObject());
        
        // Add headers
        $csv->insertOne($this->importableFields);
        
        // Add sample data
        $csv->insertOne([
            'John Doe',
            'john@example.com',
            '+1234567890',
            'password123',
            'active'
        ]);
        
        return $csv;
    }

    /**
     * Validate import file
     */
    public function validateImportFile($file): bool
    {
        if (!$file->isValid()) {
            throw new \Exception('Invalid file');
        }

        if ($file->getClientOriginalExtension() !== 'csv') {
            throw new \Exception('File must be a CSV');
        }

        $csv = Reader::createFromPath($file->getPathname());
        $csv->setHeaderOffset(0);
        
        $headers = $csv->getHeader();
        $requiredFields = ['name', 'email'];
        
        foreach ($requiredFields as $field) {
            if (!in_array($field, $headers)) {
                throw new \Exception("Missing required field: {$field}");
            }
        }
        
        return true;
    }
} 