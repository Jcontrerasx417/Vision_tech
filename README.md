# VisionTech 🚁

## Plataforma Ecommerce con Simulación Inteligente de Entregas por Drones

VisionTech es una plataforma web desarrollada con Laravel orientada al comercio electrónico y la logística inteligente mediante drones. El sistema permite a los usuarios realizar compras en línea, gestionar pedidos, realizar pagos, visualizar tracking GPS y simular entregas automatizadas utilizando drones y estaciones de entrega.

El proyecto fue desarrollado con fines académicos, integrando conceptos de ecommerce, geolocalización, simulación logística, roles multiusuario y pasarelas de pago.

---

# 📌 Vista General

VisionTech busca simular un ecosistema moderno de entregas automatizadas donde:

- Los clientes realizan pedidos desde una tienda online.
- El sistema valida el peso y disponibilidad logística.
- El pago debe aprobarse antes de asignar un dron.
- Un dron simulado realiza el envío mostrando tracking GPS en tiempo real.
- El usuario puede recibir el pedido en estación o domicilio.
- Los administradores controlan drones, estaciones, mantenimientos y logística.

---

# ✨ Funcionalidades Principales

## Ecommerce
- Registro e inicio de sesión.
- Gestión de roles y permisos.
- Catálogo de productos.
- Carrito de compras.
- Gestión de stock.
- Gestión de proveedores.
- Gestión de pedidos.
- Facturación automática.
- Historial de compras.

## Logística Inteligente
- Simulación de drones.
- Tracking GPS en tiempo real.
- Visualización de rutas en mapa.
- Control de batería y mantenimiento.
- Asignación automática de drones.
- Simulación de retorno a base.
- Estaciones de entrega.

## Pagos
- Integración preparada con Mercado Pago Checkout Pro.
- Pago simulado para entorno demo/académico.
- Validación de estados de pago.
- Restricción de envío hasta aprobación del pago.

## Administración
- Panel administrativo completo.
- Gestión de productos y categorías.
- Gestión de proveedores.
- Gestión de drones y estaciones.
- Gestión de mantenimientos.
- Gestión de solicitudes y logística.

---

# 👥 Roles del Sistema

## Cliente
- Comprar productos.
- Guardar direcciones.
- Crear pedidos.
- Consultar facturas.
- Visualizar tracking GPS.
- Elegir método de entrega.

## Administrador
- Gestionar productos.
- Gestionar pedidos.
- Gestionar drones y estaciones.
- Supervisar logística.
- Gestionar mantenimientos.
- Validar operaciones del sistema.

## Personal Logístico
- Supervisar entregas.
- Monitorear drones.
- Gestionar incidencias.
- Coordinar entregas domiciliarias.

## Proveedor
- Gestionar productos propios.
- Consultar pedidos relacionados.
- Actualizar stock.

---

# 🛒 Flujo Principal del Pedido

```text
Cliente selecciona productos
        ↓
Agrega productos al carrito
        ↓
Selecciona dirección o estación
        ↓
Confirma pedido
        ↓
Realiza el pago
        ↓
Pago aprobado
        ↓
Sistema genera factura
        ↓
Se asigna dron disponible
        ↓
Inicia simulación de vuelo
        ↓
Cliente visualiza tracking GPS
        ↓
Entrega completada
        ↓
Dron retorna a base
```

---

# 💳 Flujo de Pago y Regla del Dron

## Regla de negocio principal

> Un dron NO puede ser asignado ni iniciar una entrega hasta que el pago del pedido haya sido aprobado.

### Flujo

```text
Pedido creado
        ↓
Pago pendiente
        ↓
Esperar confirmación
        ↓
Pago aprobado
        ↓
Generar factura
        ↓
Asignar dron
        ↓
Iniciar envío
```

### Estados posibles del pago
- pending
- approved
- rejected
- cancelled

### Estados posibles del pedido
- en preparación
- pendiente de pago
- pagado
- enviado
- entregado
- cancelado

---

# 🛰️ Simulación de Drones y Tracking

VisionTech incorpora un sistema de simulación logística basado en drones.

## Características de simulación
- Generación dinámica de coordenadas GPS.
- Movimiento en tiempo real sobre mapa.
- Simulación de altitud.
- Consumo de batería.
- Estado operativo del dron.
- Retorno automático a base.
- Envío a mantenimiento por batería baja.

## Tecnologías utilizadas
- Leaflet.js
- OpenStreetMap
- APIs de geolocalización
- Simulación backend con Laravel

## Tracking GPS
El usuario puede:
- Ver la ubicación del pedido.
- Consultar el estado del dron.
- Observar el recorrido en mapa.
- Recibir notificaciones del estado del envío.

---

# 🛠️ Stack Tecnológico

| Tecnología | Uso |
|---|---|
| Laravel | Framework principal |
| PHP | Backend |
| Blade | Vistas |
| Bootstrap | Interfaz |
| SQLite | Base de datos |
| Leaflet | Mapas |
| OpenStreetMap | Geolocalización |
| Mercado Pago | Pasarela de pago |
| PHPUnit | Testing |

---

# ⚙️ Instalación del Proyecto

## 1. Clonar el repositorio

```bash
git clone https://github.com/usuario/visiontech.git
cd visiontech
```

---

## 2. Instalar dependencias

```bash
composer install
```

---

## 3. Copiar archivo de entorno

```bash
cp .env.example .env
```

---

## 4. Generar clave de aplicación

```bash
php artisan key:generate
```

---

## 5. Configurar base de datos SQLite

Crear archivo:

```bash
database/database.sqlite
```

Configurar `.env`

```env
DB_CONNECTION=sqlite
```

---

## 6. Ejecutar migraciones y seeders

```bash
php artisan migrate --seed
```

---

## 7. Levantar servidor

```bash
php artisan serve
```

Servidor:

```text
http://127.0.0.1:8000
```

---

# 🔐 Variables de Entorno Importantes

## Mercado Pago

```env
MERCADO_PAGO_ACCESS_TOKEN=
MERCADO_PAGO_PUBLIC_KEY=
MERCADO_PAGO_CURRENCY=COP
MERCADO_PAGO_VERIFY_SSL=false
```

## Aplicación

```env
APP_NAME=VisionTech
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000
```

---

# 🧪 Cómo Probar Pagos

## Opción 1 — Pago Simulado 

El sistema incluye un flujo de pago simulado para:
- pruebas académicas
- demostraciones
- desarrollo local

Permite:
- aprobar pagos manualmente
- generar facturas
- activar flujo del dron

---

## Opción 2 — Mercado Pago TEST

Utilizar credenciales TEST oficiales de Mercado Pago:

```env
MERCADO_PAGO_ACCESS_TOKEN=TEST-XXXX
MERCADO_PAGO_PUBLIC_KEY=TEST-XXXX
```

Luego:
- crear usuarios de prueba
- usar tarjetas TEST
- validar estados del checkout

Documentación oficial:
https://www.mercadopago.com.co/developers/es

---

# 🧰 Comandos Útiles

## Ejecutar servidor

```bash
php artisan serve
```

## Limpiar caché

```bash
php artisan optimize:clear
```

## Ejecutar migraciones

```bash
php artisan migrate
```

## Ejecutar seeders

```bash
php artisan db:seed
```

## Ejecutar pruebas

```bash
php artisan test
```

## Ver rutas

```bash
php artisan route:list
```

---

# 📂 Estructura Básica del Proyecto

```text
visiontech/
│
├── app/
│   ├── Models/
│   ├── Http/
│   ├── Services/
│   └── Console/
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
│
├── public/
│
├── resources/
│   ├── views/
│   ├── css/
│   └── js/
│
├── routes/
│   ├── web.php
│   ├── api.php
│   └── console.php
│
├── storage/
├── tests/
└── .env
```

---

# 📅 Plan de Desarrollo (Scrum)

| Sprint | Estado | Objetivo |
|---|---|---|
| Sprint 1 | ✅ | Registro de usuarios y login |
| Sprint 2 | ✅ | Catálogo de productos y carrito |
| Sprint 3 | ✅ | Sistema de pagos |
| Sprint 4 | ✅ | Tracking GPS y simulación de drones |

---

# 📚 Arquitectura General

## Módulos principales

- Autenticación y roles
- Ecommerce
- Pagos
- Facturación
- Tracking GPS
- Simulación de drones
- Gestión logística
- Panel administrativo
- Gestión de proveedores

---

# ⚠️ Notas Académicas

## Importante

VisionTech es un proyecto académico y de simulación.

El sistema:
- NO controla drones reales.
- NO realiza vuelos reales.
- NO opera logística aérea real.
- NO reemplaza sistemas certificados de aeronáutica.

Toda la funcionalidad de drones corresponde a:
- simulaciones visuales
- tracking conceptual
- lógica académica de negocio

---

# 🚧 Estado del Proyecto

## Estado actual
- ✅ MVP funcional
- ✅ Sistema de autenticación
- ✅ Catálogo y carrito
- ✅ Integración de pagos
- ✅ Simulación GPS
- ✅ Tracking en mapa
- ✅ Panel administrativo
- ✅ Gestión de proveedores

---

# 👨‍💻 Integrantes / Autores

## Equipo VisionTech

- Juan David Contreras
- Leonardo Waked
- David Chia


---

