<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Client;
use App\Models\Deal;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $dateRange = $request->get('range', '30');
        $startDate = now()->subDays($dateRange);

        $stats = [
            'total_properties' => Property::count(),
            'active_properties' => Property::where('status', 'active')->count(),
            'total_clients' => Client::count(),
            'active_clients' => Client::where('status', 'active')->count(),
            'total_deals' => Deal::count(),
            'completed_deals' => Deal::where('status', 'completed')->count(),
            'total_value' => Deal::where('status', 'completed')->sum('deal_value'),
            'total_commission' => Deal::where('status', 'completed')->sum('commission_amount'),
        ];

        $periodStats = [
            'new_properties' => Property::where('created_at', '>=', $startDate)->count(),
            'new_clients' => Client::where('created_at', '>=', $startDate)->count(),
            'new_deals' => Deal::where('created_at', '>=', $startDate)->count(),
        ];

        $propertiesByStatus = Property::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $propertiesByCity = Property::select('city', DB::raw('count(*) as count'))
            ->groupBy('city')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        $agentPerformance = User::where('role', 'agent')
            ->withCount(['properties', 'deals'])
            ->orderByDesc('properties_count')
            ->limit(10)
            ->get();

        $recentActivities = \Spatie\Activitylog\Models\Activity::with('causer')
            ->latest()
            ->limit(20)
            ->get();

        return Inertia::render('Dashboard/Index', [
            'stats' => $stats,
            'periodStats' => $periodStats,
            'propertiesByStatus' => $propertiesByStatus,
            'propertiesByCity' => $propertiesByCity,
            'agentPerformance' => $agentPerformance,
            'recentActivities' => $recentActivities,
            'dateRange' => $dateRange
        ]);
    }
}