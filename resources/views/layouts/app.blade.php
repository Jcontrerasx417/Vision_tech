<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'VisionTech' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --dc-black: #1a1c1d;
            --dc-ink: #1a1c1d;
            --dc-yellow: #ffd700;
            --dc-yellow-dark: #e9c400;
            --dc-yellow-soft: #fff4b8;
            --dc-line: #d0c6ab;
            --dc-muted: #5f5e5e;
            --dc-bg: #f9f9fa;
            --dc-surface-low: #f3f3f4;
            --dc-surface-high: #e8e8e9;
            --dc-tertiary: #00696f;
            --dc-success: #22c55e;
            --dc-danger: #ba1a1a;
        }

        body {
            background: var(--dc-bg);
            color: var(--dc-ink);
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background-image: radial-gradient(rgba(208, 198, 171, .55) .5px, transparent .5px);
            background-size: 24px 24px;
        }

        .navbar-drone {
            background: rgba(249, 249, 250, .96);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--dc-line);
        }

        .navbar-drone .navbar-brand,
        .navbar-drone .nav-link,
        .navbar-drone .navbar-text {
            color: var(--dc-ink);
        }

        .navbar-drone .nav-link:hover,
        .navbar-drone .navbar-brand:hover {
            color: #705d00;
        }

        .navbar-brand {
            letter-spacing: -.02em;
        }

        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 500, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
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
            box-shadow: 0 10px 24px rgba(17, 17, 17, .045);
        }

        .badge-status {
            background: var(--dc-black);
            color: var(--dc-yellow);
            border-radius: 999px;
            padding: .35rem .65rem;
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .badge-paid {
            background: var(--dc-success);
            color: #fff;
        }

        .badge-pending {
            background: var(--dc-yellow);
            color: #705d00;
        }

        .badge-muted-soft {
            background: var(--dc-surface-high);
            color: var(--dc-muted);
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
            background: #fff;
            border: 1px solid var(--dc-line);
            border-radius: 8px;
            padding: .75rem;
            box-shadow: 0 10px 24px rgba(17, 17, 17, .045);
        }

        .admin-sidebar a {
            color: var(--dc-ink);
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
            background: var(--dc-surface-high);
            color: #4d4732;
            border-color: var(--dc-line);
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .admin-table td {
            border-color: #f0ead4;
        }

        .admin-table tbody tr {
            transition: box-shadow .15s ease, border-color .15s ease, background .15s ease;
        }

        .admin-table tbody tr:hover {
            box-shadow: inset 4px 0 0 var(--dc-yellow);
            background: #fffdf0;
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
            border: 1px solid var(--dc-line);
            border-radius: 8px;
            color: var(--dc-ink);
            text-decoration: none;
            font-size: 1.15rem;
        }

        .cart-link:hover {
            color: #705d00;
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

        .vt-page-heading {
            display: flex;
            flex-wrap: wrap;
            align-items: end;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .vt-kicker {
            color: var(--dc-muted);
            font-size: .75rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .vt-card {
            background: #fff;
            border: 1px solid var(--dc-line);
            border-radius: 8px;
            box-shadow: 0 10px 24px rgba(17, 17, 17, .045);
        }

        .vt-icon-box {
            width: 48px;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            border: 1px solid var(--dc-line);
            background: var(--dc-surface-low);
            color: #705d00;
            flex: 0 0 auto;
        }

        .vt-map-preview {
            position: relative;
            min-height: 260px;
            overflow: hidden;
            border-radius: 8px;
            border: 1px solid var(--dc-line);
            background:
                linear-gradient(135deg, rgba(26, 28, 29, .08), rgba(26, 28, 29, .01)),
                repeating-linear-gradient(0deg, transparent 0 34px, rgba(126, 119, 95, .14) 35px),
                repeating-linear-gradient(90deg, transparent 0 34px, rgba(126, 119, 95, .14) 35px),
                var(--dc-surface-high);
        }

        .vt-map-preview::before {
            content: "";
            position: absolute;
            inset: 20% -10% auto 12%;
            height: 4px;
            background: var(--dc-yellow);
            transform: rotate(-12deg);
            box-shadow: 70px 34px 0 var(--dc-yellow), 150px 2px 0 var(--dc-yellow);
        }

        .vt-map-preview.is-locked {
            filter: grayscale(.95);
        }

        .vt-map-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: end;
            padding: 1rem;
            background: linear-gradient(180deg, transparent, rgba(26, 28, 29, .72));
            color: #fff;
        }

        .vt-map-lock {
            position: absolute;
            inset: 0;
            display: grid;
            place-items: center;
            padding: 1.5rem;
            text-align: center;
            background: rgba(26, 28, 29, .52);
            color: #fff;
            backdrop-filter: blur(2px);
        }

        .vt-timeline {
            display: grid;
            grid-template-columns: 18px 1fr;
            gap: .85rem;
        }

        .vt-timeline-rail {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: .35rem;
        }

        .vt-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: var(--dc-yellow);
        }

        .vt-dot.active {
            width: 14px;
            height: 14px;
            border: 3px solid var(--dc-yellow);
            background: #fff;
            box-shadow: 0 0 0 6px rgba(255, 215, 0, .18);
        }

        .vt-line {
            width: 1px;
            flex: 1;
            min-height: 42px;
            background: var(--dc-line);
        }

        .vt-metric {
            background: #fff;
            border: 1px solid var(--dc-line);
            border-radius: 8px;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
        }

        .vt-action-lock {
            opacity: .42;
            pointer-events: none;
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
                    <a class="nav-link" href="{{ route('facturas.index') }}">Facturas</a>
                    <a class="nav-link" href="{{ route('cliente.perfil') }}">Mi cuenta</a>
                    <a class="nav-link" href="{{ route('cliente.direcciones.index') }}">Direcciones</a>
                @endif
                @if(auth()->user()->hasRole('administrador', 'personal_logistico'))
                    <a class="nav-link" href="{{ route('admin.dashboard') }}">Admin</a>
                    <a class="nav-link" href="{{ route('facturas.index') }}">Facturas</a>
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
                        <span class="material-symbols-outlined" aria-hidden="true">shopping_cart</span>
                        <span class="cart-count">{{ $cartCount }}</span>
                    </a>
                @endif
                <span class="navbar-text small">{{ auth()->user()->name }} / {{ auth()->user()->role }}</span>
                <form method="POST" action="{{ route('logout') }}">@csrf <button class="btn btn-outline-primary btn-sm">Salir</button></form>
            @else
                <a class="btn btn-outline-primary btn-sm" href="{{ route('login') }}">Ingresar</a>
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
