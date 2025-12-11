@extends('layouts.app')

@section('content')
<div class="container-fluid p-4" style="min-height:100vh; background: #f8f9fa;">
    <h2 class="mb-4">Beheerder Dashboard</h2>

    <button class="btn btn-outline-primary mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#statsOverviewCollapse" aria-expanded="true" aria-controls="statsOverviewCollapse">
        Statistieken tonen/verbergen
    </button>
    <div class="collapse show" id="statsOverviewCollapse">
        <div class="row g-4 mb-4">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h5 class="card-title">Aantal auto's aangeboden</h5>
                        <div class="display-4 fw-bold" id="total-cars">{{ $total_cars ?? '...' }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h5 class="card-title">Aantal verkocht</h5>
                        <div class="display-4 fw-bold" id="sold-cars">{{ $sold_cars ?? '...' }}</div>
                        <div class="progress mt-2" style="height: 18px;">
                            <div id="progress-sold-cars" class="progress-bar bg-success" style="width: 0%;">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h5 class="card-title">Aantal vandaag aangeboden</h5>
                        <div class="display-4 fw-bold" id="today-cars">{{ $today_cars ?? '...' }}</div>
                        <div class="progress mt-2" style="height: 18px;">
                            <div id="progress-today-cars" class="progress-bar bg-warning" style="width: 0%;">0</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h5 class="card-title">Aantal aanbieders</h5>
                        <div class="display-4 fw-bold" id="total-users">{{ $total_users ?? '...' }}</div>
                        <canvas id="usersPie" height="325"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h5 class="card-title">Gemiddeld aantal auto's per aanbieder</h5>
                        <div class="display-4 fw-bold" id="avg-cars-per-user">{{ $avg_cars_per_user ?? '...' }}</div>
                        <canvas id="avgBar" height="325"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card shadow h-100">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h5 class="card-title">Views statistieken</h5>
                        <div>
                            <div class="fw-bold">Totaal aantal views:</div>
                            <div class="display-6 fw-bold" id="total-views">{{ $total_views ?? '...' }}</div>
                        </div>
                        <div class="mt-3">
                            <div class="fw-bold">Gemiddeld aantal views per auto:</div>
                            <div class="display-6 fw-bold" id="avg-views-per-car">{{ $avg_views_per_car ?? '...' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button class="btn btn-outline-secondary mb-3" type="button" data-bs-toggle="collapse" data-bs-target="#tagsOverviewCollapse" aria-expanded="false" aria-controls="tagsOverviewCollapse">
        Tags Overzicht tonen/verbergen
    </button>
    <div class="collapse" id="tagsOverviewCollapse">
        <table class="table">
            <thead>
                <tr>
                    <th>Tag</th>
                    <th>Kleur</th>
                    <th>Totaal gebruikt</th>
                    <th>Bij beschikbare auto's</th>
                    <th>Bij verkochte auto's</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tags as $tag)
                    <tr>
                        <td>{{ $tag->name }}</td>
                        <td><span class="badge" style="background: {{ $tag->color }}">{{ $tag->color }}</span></td>
                        <td>{{ $tag->cars_count }}</td>
                        <td>{{ $tag->available_count }}</td>
                        <td>{{ $tag->sold_count }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
function fetchDashboardStats() {
    fetch("{{ route('admin.dashboard.stats') }}")
        .then(res => res.json())
        .then(data => {
            document.getElementById('total-cars').textContent = data.total_cars;

            document.getElementById('sold-cars').textContent = data.sold_cars;
            let soldCarsPercent = Math.min(100, Math.round((data.sold_cars / (data.total_cars || 1)) * 100));
            document.getElementById('progress-sold-cars').style.width = soldCarsPercent + '%';
            document.getElementById('progress-sold-cars').textContent = data.sold_cars;

            document.getElementById('today-cars').textContent = data.today_cars;
            let todayCarsPercent = Math.min(100, Math.round((data.today_cars / (data.total_cars || 1)) * 100));
            document.getElementById('progress-today-cars').style.width = todayCarsPercent + '%';
            document.getElementById('progress-today-cars').textContent = data.today_cars;

            document.getElementById('total-users').textContent = data.total_users;

            if(window.usersPieChart) window.usersPieChart.destroy();
            window.usersPieChart = new Chart(document.getElementById('usersPie'), {
                type: 'doughnut',
                data: {
                    labels: ['Aanbieders', 'Overig'],
                    datasets: [{
                        data: [data.total_users, Math.max(1, data.max_users - data.total_users)],
                        backgroundColor: ['#0d6efd', '#dee2e6'],
                    }]
                },
                options: {responsive: true, plugins: {legend: {display: false}}}
            });

            document.getElementById('avg-cars-per-user').textContent = data.avg_cars_per_user;

            if(window.avgBarChart) window.avgBarChart.destroy();
            window.avgBarChart = new Chart(document.getElementById('avgBar'), {
                type: 'bar',
                data: {
                    labels: ['Gemiddeld'],
                    datasets: [{
                        label: 'Auto\'s per aanbieder',
                        data: [data.avg_cars_per_user],
                        backgroundColor: '#ffc107'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {legend: {display: false}},
                    scales: {y: {beginAtZero: true, max: Math.max(5, Math.ceil(data.avg_cars_per_user + 1))}}
                }
            });

            document.getElementById('total-views').textContent = data.total_views;
            document.getElementById('avg-views-per-car').textContent = data.avg_views_per_car;

            let avgViewsPercent = 0;
            if (data.total_views > 0 && data.avg_views_per_car > 0) {
                avgViewsPercent = Math.round((data.avg_views_per_car / data.total_views) * 100);
            }
            document.getElementById('progress-avg-views-per-car').style.width = avgViewsPercent + '%';
            document.getElementById('progress-avg-views-per-car').textContent = avgViewsPercent + '%';
        });
}

fetchDashboardStats();
setInterval(fetchDashboardStats, 10000);
</script>
@endsection