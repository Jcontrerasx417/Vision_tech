<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'VisionTech' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --dc-black: #0d0d0d;
            --dc-ink: #171717;
            --dc-yellow: #f8c900;
            --dc-yellow-dark: #d9ac00;
            --dc-yellow-soft: #fff3b0;
            --dc-line: #e8e2c8;
        }

        body {
            background: #f7f4e8;
            color: var(--dc-ink);
        }

        .navbar-drone {
            background: var(--dc-black);
            border-bottom: 3px solid var(--dc-yellow);
        }

        .navbar-drone .navbar-brand,
        .navbar-drone .nav-link,
        .navbar-drone .navbar-text {
            color: #fff;
        }

        .navbar-drone .nav-link:hover,
        .navbar-drone .navbar-brand:hover {
            color: var(--dc-yellow);
        }

        .btn-primary {
            --bs-btn-bg: var(--dc-yellow);
            --bs-btn-border-color: var(--dc-yellow);
            --bs-btn-color: #111;
            --bs-btn-hover-bg: var(--dc-yellow-dark);
            --bs-btn-hover-border-color: var(--dc-yellow-dark);
            --bs-btn-hover-color: #111;
            font-weight: 700;
        }

        .btn-outline-primary {
            --bs-btn-color: #111;
            --bs-btn-border-color: #111;
            --bs-btn-hover-bg: #111;
            --bs-btn-hover-border-color: #111;
            --bs-btn-hover-color: #fff;
        }

        .surface {
            background: #fff;
            border: 1px solid var(--dc-line);
            border-radius: 8px;
            box-shadow: 0 14px 30px rgba(17, 17, 17, .06);
        }

        .badge-status {
            background: #111;
            color: var(--dc-yellow);
            border-radius: 999px;
            padding: .35rem .65rem;
            font-size: .78rem;
            font-weight: 700;
        }

        .text-yellow {
            color: var(--dc-yellow);
        }

        .admin-shell {
            display: grid;
            grid-template-columns: 230px minmax(0, 1fr);
            gap: 1.25rem;
            align-items: start;
        }

        .admin-sidebar {
            position: sticky;
            top: 1rem;
            background: #111;
            border: 1px solid #282828;
            border-radius: 8px;
            padding: .75rem;
        }

        .admin-sidebar a {
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-decoration: none;
            border-radius: 6px;
            padding: .72rem .85rem;
            margin-bottom: .25rem;
            font-weight: 700;
        }

        .admin-sidebar a:hover,
        .admin-sidebar a.active {
            background: var(--dc-yellow);
            color: #111;
        }

        .admin-section-title {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: end;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .admin-table {
            margin: 0;
            vertical-align: middle;
        }

        .admin-table thead th {
            background: #111;
            color: var(--dc-yellow);
            border-color: #111;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .admin-table td {
            border-color: #f0ead4;
        }

        .admin-metric {
            border-left: 5px solid var(--dc-yellow);
        }

        .cart-link {
            position: relative;
            width: 42px;
            height: 38px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid rgba(255,255,255,.45);
            border-radius: 8px;
            color: #fff;
            text-decoration: none;
            font-size: 1.15rem;
        }

        .cart-link:hover {
            color: var(--dc-yellow);
            border-color: var(--dc-yellow);
        }

        .cart-count {
            position: absolute;
            top: -8px;
            right: -8px;
            min-width: 22px;
            height: 22px;
            padding: 0 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: var(--dc-yellow);
            color: #111;
            font-size: .75rem;
            font-weight: 900;
        }

        @media (max-width: 992px) {
            .admin-shell {
                grid-template-columns: 1fr;
            }

            .admin-sidebar {
                position: static;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-drone">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="{{ route('home') }}">VisionTech</a>
        <div class="navbar-nav me-auto">
            <a class="nav-link" href="{{ route('catalogo.index') }}">Catalogo</a>
            @auth
                @if(auth()->user()->hasRole('cliente'))
                    <a class="nav-link" href="{{ route('cliente.pedidos') }}">Mis pedidos</a>
                    <a class="nav-link" href="{{ route('cliente.perfil') }}">Mi cuenta</a>
                    <a class="nav-link" href="{{ route('cliente.direcciones.index') }}">Direcciones</a>
                @endif
                @if(auth()->user()->hasRole('administrador', 'personal_logistico'))
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a>
                @endif
                @if(auth()->user()->hasRole('proveedor'))
                    <a class="nav-link" href="{{ route('proveedor.dashboard') }}">Proveedor</a>
                @endif
            @endauth
        </div>
        <div class="d-flex align-items-center gap-2">
            @auth
                @if(auth()->user()->hasRole('cliente'))
                    @php
                        $cartCount = auth()->user()->carrito?->items()->sum('cantidad') ?? 0;
                    @endphp
                    <a class="cart-link" href="{{ route('carrito.index') }}" title="Carrito">
                        <span aria-hidden="true">🛒</span>
                        <span class="cart-count">{{ $cartCount }}</span>
                    </a>
                @endif
                <span class="navbar-text small">{{ auth()->user()->name }} / {{ auth()->user()->role }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf <button class="btn btn-outline-light btn-sm">Salir</button></form>
            @else
                <a class="btn btn-outline-light btn-sm" href="{{ route('login') }}">Ingresar</a>
                <a class="btn btn-primary btn-sm" href="{{ route('register') }}">Registro</a>
            @endauth
        </div>
    </div>
</nav>
<main class="container py-4">
    @if(session('status')) <div class="alert alert-success">{{ session('status') }}</div> @endif
    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach
        </div>
    @endif
    @yield('content')
</main>
@stack('scripts')
</body>
</html>
