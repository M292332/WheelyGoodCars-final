<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>WheelyGoodCars</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body>
    <nav class="navbar navbar-expand-md navbar-dark d-print-none bg-black">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('home') }}"><strong class="text-primary">Wheely</strong> good cars<strong class="text-primary">!</strong></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link text-light" href="{{ route('cars.index') }}">Alle auto's</a></li>
                    @auth
                        <li class="nav-item"><a class="nav-link text-light" href="{{ route('cars.mine') }}">Mijn aanbod</a></li>
                        <li class="nav-item"><a class="nav-link text-light" href="{{ route('cars.create') }}">Aanbod plaatsen</a></li>
                    @endauth
                </ul>
                <ul class="navbar-nav">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link text-light" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                            </li>
                        @endif
                    @endauth
                    @guest
                        <li class="nav-item"><a class="nav-link text-secondary" href="{{ route('register') }}">Registreren</a></li>
                        <li class="nav-item"><a class="nav-link text-secondary" href="{{ route('login') }}">Inloggen</a></li>
                    @endguest
                    @auth
                        <li class="nav-item"><a class="nav-link text-secondary" href="{{ route('logout') }}">Uitloggen</a></li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

    @livewireScripts
    @yield('scripts')
</body>
</html>
