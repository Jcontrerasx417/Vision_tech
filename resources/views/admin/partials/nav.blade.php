<aside class="admin-sidebar">
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        Dashboard <span>01</span>
    </a>
    <a href="{{ route('admin.pedidos.index') }}" class="{{ request()->routeIs('admin.pedidos.*') ? 'active' : '' }}">
        Pedidos <span>02</span>
    </a>
    @if(auth()->user()->hasRole('administrador'))
        <a href="{{ route('admin.productos.index') }}" class="{{ request()->routeIs('admin.productos.*') ? 'active' : '' }}">
            Productos <span>03</span>
        </a>
        <a href="{{ route('admin.proveedores.index') }}" class="{{ request()->routeIs('admin.proveedores.*') ? 'active' : '' }}">
            Proveedores <span>04</span>
        </a>
        <a href="{{ route('admin.solicitudes.index') }}" class="{{ request()->routeIs('admin.solicitudes.*') ? 'active' : '' }}">
            Solicitudes <span>05</span>
        </a>
    @endif
    <a href="{{ route('admin.drones.index') }}" class="{{ request()->routeIs('admin.drones.*') ? 'active' : '' }}">
        Drones <span>{{ auth()->user()->hasRole('administrador') ? '06' : '03' }}</span>
    </a>
    <a href="{{ route('facturas.index') }}" class="{{ request()->routeIs('facturas.*') ? 'active' : '' }}">
        Facturas <span>FX</span>
    </a>
    <a href="{{ route('admin.estaciones.index') }}" class="{{ request()->routeIs('admin.estaciones.*') ? 'active' : '' }}">
        Estaciones <span>{{ auth()->user()->hasRole('administrador') ? '07' : '04' }}</span>
    </a>
    <a href="{{ route('admin.mantenimientos.index') }}" class="{{ request()->routeIs('admin.mantenimientos.*') ? 'active' : '' }}">
        Mantenimiento <span>{{ auth()->user()->hasRole('administrador') ? '08' : '05' }}</span>
    </a>
</aside>
