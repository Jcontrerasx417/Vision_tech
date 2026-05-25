@extends('layouts.app')

@section('content')
<style>
    .landing-hero {
        margin-left: calc(50% - 50vw);
        width: 100vw;
        min-height: 76vh;
        display: flex;
        align-items: center;
        color: #fff;
        background-image:
            linear-gradient(90deg, rgba(0, 0, 0, .94) 0%, rgba(0, 0, 0, .72) 42%, rgba(0, 0, 0, .20) 100%),
            url('https://images.unsplash.com/photo-1508614589041-895b88991e3e?auto=format&fit=crop&w=1900&q=88');
        background-size: cover;
        background-position: center;
        border-bottom: 6px solid var(--dc-yellow);
    }

    .landing-wrap {
        width: min(1140px, calc(100% - 2rem));
        margin: 0 auto;
        padding: 5rem 0;
    }

    .landing-pill {
        display: inline-flex;
        padding: .5rem .85rem;
        background: rgba(248, 201, 0, .16);
        border: 1px solid rgba(248, 201, 0, .55);
        color: var(--dc-yellow);
        border-radius: 999px;
        font-weight: 800;
    }

    .promo-card {
        min-height: 280px;
        overflow: hidden;
        position: relative;
        color: #fff;
        border-radius: 8px;
        background-size: cover;
        background-position: center;
        box-shadow: 0 18px 36px rgba(0,0,0,.12);
    }

    .promo-card::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(0,0,0,.15), rgba(0,0,0,.78));
    }

    .promo-card > div {
        position: absolute;
        left: 1.25rem;
        right: 1.25rem;
        bottom: 1.25rem;
    }

    .brand-strip {
        margin-top: -2.4rem;
        position: relative;
        z-index: 2;
        background: #111;
        color: #fff;
        border: 1px solid #292929;
        border-radius: 8px;
        padding: 1rem;
    }

    .brand-strip strong {
        color: var(--dc-yellow);
    }
</style>

<section class="landing-hero">
    <div class="landing-wrap">
        <div class="col-lg-7">
            <div class="landing-pill mb-3">VisionTech Market</div>
            <h1 class="display-2 fw-bold mb-3">Compra tecnologia y productos livianos sin esperar de mas.</h1>
            <p class="lead mb-4">Catalogo visual, entregas rapidas en Bucaramanga y seguimiento GPS del pedido en tiempo real.</p>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('catalogo.index') }}" class="btn btn-primary btn-lg">Explorar catalogo</a>
                @guest
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">Crear cuenta</a>
                @endguest
                @auth
                    @if(auth()->user()->hasRole('cliente'))
                        <a href="{{ route('cliente.pedidos') }}" class="btn btn-outline-light btn-lg">Mis pedidos</a>
                    @elseif(auth()->user()->hasRole('proveedor'))
                        <a href="{{ route('proveedor.dashboard') }}" class="btn btn-outline-light btn-lg">Panel proveedor</a>
                    @elseif(auth()->user()->hasRole('administrador', 'personal_logistico'))
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-light btn-lg">Panel admin</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</section>

<section class="brand-strip">
    <div class="row g-3 text-center">
        <div class="col-md-3"><strong>Marketplace</strong><div class="small text-white-50">productos de proveedores</div></div>
        <div class="col-md-3"><strong>Bucaramanga</strong><div class="small text-white-50">entrega local</div></div>
        <div class="col-md-3"><strong>GPS</strong><div class="small text-white-50">seguimiento visual</div></div>
        <div class="col-md-3"><strong>Drones</strong><div class="small text-white-50">logistica inteligente</div></div>
    </div>
</section>

<section class="py-5">
    <div class="admin-section-title">
        <div>
            <span class="badge-status">Destacados</span>
            <h2 class="h1 fw-bold mt-2 mb-1">Una vitrina hecha para mirar y comprar</h2>
            <p class="text-muted mb-0">Productos con imagen, detalle, proveedor y disponibilidad.</p>
        </div>
        <a href="{{ route('catalogo.index') }}" class="btn btn-outline-primary">Ver productos</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="promo-card" style="background-image:url('https://images.unsplash.com/photo-1518770660439-4636190af475?auto=format&fit=crop&w=1100&q=85')">
                <div>
                    <span class="badge text-bg-warning text-dark mb-2">Tecnologia</span>
                    <h3 class="h2 fw-bold">Componentes, sensores y accesorios</h3>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="promo-card" style="background-image:url('https://images.unsplash.com/photo-1609091839311-d5365f9ff1c5?auto=format&fit=crop&w=900&q=85')">
                        <div><h3 class="h4 fw-bold">Energia portatil</h3><p class="mb-0">Productos livianos y listos para enviar.</p></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="promo-card" style="background-image:url('https://images.unsplash.com/photo-1511920170033-f8396924c348?auto=format&fit=crop&w=900&q=85')">
                        <div><h3 class="h4 fw-bold">Productos locales</h3><p class="mb-0">Proveedores que publican directamente.</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
