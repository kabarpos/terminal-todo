<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view-analytics');
    }

    public function index()
    {
        // Contoh data metrics
        $metrics = [
            [
                'name' => 'Total Posts',
                'value' => '1,234',
                'trend' => 12.5
            ],
            [
                'name' => 'Total Views',
                'value' => '2.1M',
                'trend' => 8.2
            ],
            [
                'name' => 'Engagement Rate',
                'value' => '4.8%',
                'trend' => -2.1
            ],
            [
                'name' => 'Avg. Response Time',
                'value' => '2.3h',
                'trend' => 15.4
            ]
        ];

        // Contoh data top content
        $topContent = [
            [
                'id' => 1,
                'title' => 'How to Improve Your Social Media Strategy',
                'thumbnail' => '/images/content-1.jpg',
                'date' => '2024-03-15',
                'platform' => 'Instagram',
                'views' => '125K',
                'engagementRate' => 5.2
            ],
            [
                'id' => 2,
                'title' => '10 Tips for Better Content Creation',
                'thumbnail' => '/images/content-2.jpg',
                'date' => '2024-03-14',
                'platform' => 'Facebook',
                'views' => '98K',
                'engagementRate' => 4.8
            ],
            [
                'id' => 3,
                'title' => 'Understanding Your Audience',
                'thumbnail' => '/images/content-3.jpg',
                'date' => '2024-03-13',
                'platform' => 'Twitter',
                'views' => '76K',
                'engagementRate' => 3.9
            ]
        ];

        return Inertia::render('Analytics/Index', [
            'metrics' => $metrics,
            'topContent' => $topContent
        ]);
    }
} 