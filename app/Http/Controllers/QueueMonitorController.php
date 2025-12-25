<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Contracts\MetricsRepository;

class QueueMonitorController extends Controller
{
    public function index()
    {
        // Simplified version - Redis extension not installed
        // For full functionality, install php-redis extension
        
        $recentJobs = collect([]);
        $failedJobs = collect([]);
        $pendingJobs = collect([]);
        $jobsPerMinute = 0;
        $queueSizes = ['default' => 0];

        return view('queue-monitor.index', compact(
            'recentJobs',
            'failedJobs',
            'pendingJobs',
            'jobsPerMinute',
            'queueSizes'
        ));
    }
}
