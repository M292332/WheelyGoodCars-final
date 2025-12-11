<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Tag;
use App\Models\Car;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AdminDashboardController extends Controller
{
    // ADMIN DASHBOARD VIEW
    public function index()
    {
        $this->authorizeAdmin();

        // Tags met count van auto's
        $tags = Tag::withCount([
            'cars as sold_count' => fn($q) => $q->where('status', 'sold'),
            'cars as available_count' => fn($q) => $q->where('status', 'available'),
            'cars'
        ])->get()->sortByDesc('cars_count');

        // Statistieken
        $totalCars = Car::count();
        $soldCars = Car::where('status', 'sold')->count();
        $todayCars = Car::whereDate('created_at', now()->toDateString())->count();
        $totalUsers = User::has('cars')->count();
        $avgCarsPerUser = $totalUsers > 0 ? round($totalCars / $totalUsers, 2) : 0;
        $avgViewsPerCar = $totalCars > 0 ? round(Car::avg('views'), 2) : 0;
        $totalViews = Car::sum('views');

        return view('admin.dashboard', compact(
            'tags',
            'totalCars',
            'soldCars',
            'todayCars',
            'totalUsers',
            'avgCarsPerUser',
            'avgViewsPerCar',
            'totalViews'
        ));
    }

    // ADMIN DASHBOARD STATS JSON
    public function stats(): JsonResponse
    {
        $this->authorizeAdmin();

        $totalCars = Car::count();
        $maxCars = Car::max('id');
        $soldCars = Car::where('status', 'sold')->count();
        $todayCars = Car::whereDate('created_at', now()->toDateString())->count();
        $totalUsers = User::has('cars')->count();
        $maxUsers = User::count();
        $avgCarsPerUser = $totalUsers > 0 ? round($totalCars / $totalUsers, 2) : 0;
        $avgViewsPerCar = $totalCars > 0 ? round(Car::avg('views'), 2) : 0;
        $totalViews = Car::sum('views');

        return response()->json([
            'total_cars' => $totalCars,
            'max_cars' => $maxCars,
            'sold_cars' => $soldCars,
            'today_cars' => $todayCars,
            'total_users' => $totalUsers,
            'max_users' => $maxUsers,
            'avg_cars_per_user' => $avgCarsPerUser,
            'avg_views_per_car' => $avgViewsPerCar,
            'total_views' => $totalViews,
        ]);
    }

    // HULPFUNCTIE: check of gebruiker admin is
    private function authorizeAdmin(): void
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
    }
}
