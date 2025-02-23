<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SettingsSchemaService
{
    protected array $registeredGroups = [];
    protected array $fieldTypes = [
        'text', 'textarea', 'number', 'email', 
        'file', 'image', 'boolean', 'select', 
        'multiselect', 'color', 'date', 'datetime'
    ];

    /**
     * Register a new settings group
     */
    public function registerGroup(string $key, string $label, array $fields): void
    {
        $this->validateFields($fields);
        
        $this->registeredGroups[$key] = [
            'key' => $key,
            'label' => $label,
            'fields' => $this->processFields($fields)
        ];

        $this->clearSchemaCache();
    }

    /**
     * Get all registered groups
     */
    public function getGroups(): array
    {
        return Cache::remember('settings.schema', 3600, function () {
            return $this->registeredGroups;
        });
    }

    /**
     * Get specific group schema
     */
    public function getGroup(string $key): ?array
    {
        return $this->registeredGroups[$key] ?? null;
    }

    /**
     * Validate field configuration
     */
    protected function validateFields(array $fields): void
    {
        foreach ($fields as $key => $field) {
            if (!isset($field['type']) || !in_array($field['type'], $this->fieldTypes)) {
                throw new \InvalidArgumentException("Invalid field type for {$key}");
            }

            if (!isset($field['label'])) {
                throw new \InvalidArgumentException("Label is required for {$key}");
            }

            // Validate type-specific requirements
            match($field['type']) {
                'select', 'multiselect' => $this->validateOptionsField($field, $key),
                'file', 'image' => $this->validateFileField($field, $key),
                default => null
            };
        }
    }

    /**
     * Process and normalize fields
     */
    protected function processFields(array $fields): array
    {
        return collect($fields)->map(function ($field, $key) {
            return array_merge([
                'key' => $key,
                'required' => false,
                'help' => null,
                'default' => null,
                'validation' => null,
            ], $field);
        })->toArray();
    }

    /**
     * Validate fields with options
     */
    protected function validateOptionsField(array $field, string $key): void
    {
        if (!isset($field['options']) || !is_array($field['options'])) {
            throw new \InvalidArgumentException("Options are required for select/multiselect field {$key}");
        }
    }

    /**
     * Validate file fields
     */
    protected function validateFileField(array $field, string $key): void
    {
        $field['allowed_types'] = $field['allowed_types'] ?? [];
        $field['max_size'] = $field['max_size'] ?? 2048; // 2MB default
    }

    /**
     * Clear schema cache
     */
    protected function clearSchemaCache(): void
    {
        Cache::forget('settings.schema');
    }

    /**
     * Get validation rules for a group
     */
    public function getValidationRules(string $groupKey): array
    {
        $group = $this->getGroup($groupKey);
        if (!$group) return [];

        $rules = [];
        foreach ($group['fields'] as $field) {
            if ($field['validation']) {
                $rules[$field['key']] = $field['validation'];
            } else {
                // Default validation based on type
                $rules[$field['key']] = $this->getDefaultValidation($field);
            }
        }

        return $rules;
    }

    /**
     * Get default validation rules based on field type
     */
    protected function getDefaultValidation(array $field): array
    {
        $rules = ['nullable'];

        switch ($field['type']) {
            case 'email':
                $rules[] = 'email';
                break;
            case 'number':
                $rules[] = 'numeric';
                break;
            case 'file':
                $rules[] = 'file';
                if (!empty($field['allowed_types'])) {
                    $rules[] = 'mimes:' . implode(',', $field['allowed_types']);
                }
                if (!empty($field['max_size'])) {
                    $rules[] = 'max:' . $field['max_size'];
                }
                break;
            case 'image':
                $rules[] = 'image';
                if (!empty($field['max_size'])) {
                    $rules[] = 'max:' . $field['max_size'];
                }
                break;
            case 'boolean':
                $rules[] = 'boolean';
                break;
            case 'select':
                if (!empty($field['options'])) {
                    $rules[] = 'in:' . implode(',', array_keys($field['options']));
                }
                break;
            case 'multiselect':
                $rules[] = 'array';
                if (!empty($field['options'])) {
                    $rules[] = 'in:' . implode(',', array_keys($field['options']));
                }
                break;
        }

        if (!empty($field['required'])) {
            $rules[0] = 'required';
        }

        return $rules;
    }
} 