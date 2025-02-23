<?php

if (!function_exists('format_page_title')) {
    function format_page_title($title)
    {
        $site_title = Cache::get('website_settings')['site_title'] ?? 'Website';
        return "{$title} - {$site_title}";
    }
} 