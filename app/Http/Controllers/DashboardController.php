<?php

namespace App\Http\Controllers;

use App\Models\CampaignEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $company = $user->companies()->first();

        // If user has no company, redirect to create one
        if (!$company) {
            return redirect()->route('companies.create')
                ->with('info', 'Please create a company to get started.');
        }

        // KPI Cards
        $totalContacts = $company->contacts()->count();
        $subscribedContacts = $company->contacts()->where('status', 'subscribed')->count();
        $totalCampaigns = $company->campaigns()->count();
        $activeCampaigns = $company->campaigns()->whereIn('status', ['sending', 'queued'])->count();

        // Email Events Stats (last 30 days)
        $startDate = now()->subDays(30);
        
        $eventStats = CampaignEvent::whereHas('contact', function($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->where('created_at', '>=', $startDate)
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        $totalSent = $eventStats['sent'] ?? 0;
        $totalDelivered = $eventStats['delivered'] ?? 0;
        $totalBounced = $eventStats['bounced'] ?? 0;
        $totalComplaints = $eventStats['complained'] ?? 0;

        $deliveryRate = $totalSent > 0 ? round(($totalDelivered / $totalSent) * 100, 2) : 0;
        $bounceRate = $totalSent > 0 ? round(($totalBounced / $totalSent) * 100, 2) : 0;

        // Delivery Trends (last 7 days)
        $trendData = CampaignEvent::whereHas('contact', function($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->where('created_at', '>=', now()->subDays(7))
            ->where('type', 'delivered')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Recent Global Activity (latest 10 events)
        $recentEvents = CampaignEvent::whereHas('contact', function($q) use ($company) {
                $q->where('company_id', $company->id);
            })
            ->with(['contact', 'campaign'])
            ->latest()
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'totalContacts',
            'subscribedContacts',
            'totalCampaigns',
            'activeCampaigns',
            'totalSent',
            'totalDelivered',
            'deliveryRate',
            'bounceRate',
            'totalBounced',
            'totalComplaints',
            'trendData',
            'recentEvents'
        ));
    }
}
