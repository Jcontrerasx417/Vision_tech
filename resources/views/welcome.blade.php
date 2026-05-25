@extends('layouts.app')

@section('content')
<style>
    .vt-landing-hero {
        margin-left: calc(50% - 50vw);
        width: 100vw;
        min-height: 76vh;
        display: flex;
        align-items: center;
        color: #fff;
        background-image:
            linear-gradient(90deg, rgba(26, 28, 29, .94) 0%, rgba(26, 28, 29, .76) 44%, rgba(26, 28, 29, .18) 100%),
            url('https://images.unsplash.com/photo-1508614589041-895b88991e3e?auto=format&fit=crop&w=1900&q=88');
        background-size: cover;
        background-position: center;
        border-bottom: 6px solid var(--dc-yellow);
    }

    .vt-landing-wrap {
        width: min(1140px, calc(100% - 2rem));
        margin: 0 auto;
        padding: 5rem 0;
    }

    .vt-hero-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .45rem .75rem;
        background: var(--dc-yellow);
        color: #221b00;
        border-radius: 4px;
        font-size: .75rem;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
    }

    .vt-bento {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 1.25rem;
    }

    .vt-bento-large {
        grid-column: span 8;
    }

    .vt-bento-small {
        grid-column: span 4;
    }

    .vt-feature {
        min-height: 260px;
        padding: 2rem;
        overflow: hidden;
        position: relative;
    }

    .vt-feature-dark {
        background: var(--dc-black);
        color: #fff;
    }

    .vt-feature-yellow {
        background: var(--dc-yellow);
        color: #221b00;
    }

    .vt-feature-img {
        height: 170px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--dc-line);
    }

    .vt-feature-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: grayscale(.7);
        transition: filter .2s ease, transform .2s ease;
    }

    .vt-feature:hover .vt-feature-img img {
        filter: grayscale(0);
        transform: scale(1.02);
    }

    .vt-process-number {
        position: absolute;
        top: -2rem;
        left: 0;
        color: rgba(126, 119, 95, .2);
        font-size: 7rem;
        font-weight: 800;
        line-height: 1;
    }

    @media (max-width: 768px) {
        .vt-bento-large,
        .vt-bento-small {
            grid-column: span 12;
        }
    }
</style>

<section class="vt-landing-hero">
    <div class="vt-landing-wrap">
        <div class="col-lg-7">
            <span class="vt-hero-pill mb-4">
                <span class="material-symbols-outlined">drone</span>
                Logistica autonoma 2.0
            </span>
            <h1 class="display-2 fw-bold mb-3">VisionTech lleva tus pedidos por dron, solo despues del pago.</h1>
            <p class="lead mb-4 text-white-50">Compra productos livianos, paga de forma segura y rastrea la entrega cuando la operacion este realmente activa.</p>
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

<section class="vt-card p-3 p-md-4" style="margin-top: -2rem; position: relative; z-index: 2;">
    <div class="row g-3 text-center">
        <div class="col-md-3"><div class="vt-kicker">Marketplace</div><strong>Productos de proveedores</strong></div>
        <div class="col-md-3"><div class="vt-kicker">Pago seguro</div><strong>Dron bloqueado hasta pagar</strong></div>
        <div class="col-md-3"><div class="vt-kicker">Tracking</div><strong>Mapa solo con entrega activa</strong></div>
        <div class="col-md-3"><div class="vt-kicker">Operacion</div><strong>Admin logistico en tiempo real</strong></div>
    </div>
</section>

<section class="py-5">
    <div class="vt-page-heading">
        <div>
            <div class="vt-kicker">Plataforma</div>
            <h2 class="h1 fw-bold mt-2 mb-1">Eficiencia industrial en una experiencia simple</h2>
            <p class="text-muted mb-0">El flujo completo queda claro: pide, paga y rastrea cuando el dron este asignado.</p>
        </div>
        <a href="{{ route('catalogo.index') }}" class="btn btn-outline-primary">Ver productos</a>
    </div>

    <div class="vt-bento">
        <div class="vt-card vt-feature vt-bento-large">
            <span class="material-symbols-outlined text-yellow fs-1 mb-3">analytics</span>
            <h3 class="h4 fw-bold">Tracking en vivo</h3>
            <p class="text-muted col-lg-8">Seguimiento visual, estado de pedido y puntos GPS cuando el pago ya fue aprobado y existe una entrega asignada.</p>
            <div class="row g-3 mt-3">
                <div class="col-md-7">
                    <div class="vt-feature-img">
                        <img src="https://images.unsplash.com/photo-1524661135-423995f22d0b?auto=format&fit=crop&w=900&q=85" alt="Mapa de entrega">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="vt-map-preview h-100"></div>
                </div>
            </div>
        </div>

        <div class="vt-card vt-feature vt-feature-dark vt-bento-small">
            <span class="material-symbols-outlined text-yellow fs-1 mb-3">payments</span>
            <h3 class="h4 fw-bold">Pagos primero</h3>
            <p class="text-white-50">La regla esta blindada: sin pago aprobado no hay dron, envio ni tracking.</p>
            <div class="border rounded p-3 mt-4">
                <div class="small text-white-50">TRANSACCION</div>
                <div class="font-monospace">**** **** **** 8829</div>
            </div>
        </div>

        <div class="vt-card vt-feature vt-feature-yellow vt-bento-small">
            <span class="material-symbols-outlined fs-1 mb-3">package</span>
            <h3 class="h4 fw-bold">Carga optimizada</h3>
            <p class="mb-0">El peso del pedido define si un dron disponible puede tomar la entrega.</p>
        </div>

        <div class="vt-card vt-feature vt-bento-large d-md-flex align-items-center gap-4">
            <div class="flex-grow-1">
                <span class="material-symbols-outlined text-yellow fs-1 mb-3">precision_manufacturing</span>
                <h3 class="h4 fw-bold">Flota autonoma</h3>
                <p class="text-muted mb-md-0">Panel logistico para supervisar pedidos, pagos, drones disponibles y entregas activas.</p>
            </div>
            <div class="vt-feature-img flex-shrink-0" style="width: min(100%, 360px);">
                <img src="https://images.unsplash.com/photo-1473968512647-3e447244af8f?auto=format&fit=crop&w=900&q=85" alt="Dron industrial">
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="vt-card p-4 p-md-5">
        <div class="text-center mb-5">
            <div class="vt-kicker">Proceso</div>
            <h2 class="h1 fw-bold mt-2">Pedido simple, operacion controlada</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4 position-relative">
                <div class="vt-process-number">01</div>
                <div class="position-relative pt-5">
                    <h3 class="h5 fw-bold">Pide</h3>
                    <p class="text-muted">Selecciona productos, destino y tipo de entrega.</p>
                </div>
            </div>
            <div class="col-md-4 position-relative">
                <div class="vt-process-number">02</div>
                <div class="position-relative pt-5">
                    <h3 class="h5 fw-bold">Paga</h3>
                    <p class="text-muted">La confirmacion del pago activa la asignacion del dron.</p>
                </div>
            </div>
            <div class="col-md-4 position-relative">
                <div class="vt-process-number">03</div>
                <div class="position-relative pt-5">
                    <h3 class="h5 fw-bold">Rastrea</h3>
                    <p class="text-muted">El mapa aparece solo cuando la entrega esta lista para operar.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
