<aside class="admin-sidebar">
    <a href="{{ route('proveedor.dashboard') }}" class="{{ request()->routeIs('proveedor.dashboard') ? 'active' : '' }}">
        Resumen <span>01</span>
    </a>
    <a href="{{ route('proveedor.perfil.edit') }}" class="{{ request()->routeIs('proveedor.perfil.*') ? 'active' : '' }}">
        Mi perfil <span>02</span>
    </a>
    <a href="{{ route('proveedor.productos.index') }}" class="{{ request()->routeIs('proveedor.productos.*') ? 'active' : '' }}">
        Mis productos <span>03</span>
    </a>
    <a href="{{ route('proveedor.pedidos.index') }}" class="{{ request()->routeIs('proveedor.pedidos.*') ? 'active' : '' }}">
        Pedidos <span>04</span>
    </a>
    <a href="{{ route('catalogo.index') }}">
        Ver catalogo <span>05</span>
    </a>
</aside>
