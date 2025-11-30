<div align="center">
  <img src="https://laravel.com/img/logomark.min.svg" alt="Laravel" width="100">
  
  # 🍔 TasteTracker Backend API
  
  **Sistema de gestión de pedidos para restaurantes**
  
  [![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
  [![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
  [![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
  [![Sanctum](https://img.shields.io/badge/Sanctum-Auth-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/docs/sanctum)
</div>

---

## 📖 Descripción

Esta API RESTful maneja la lógica de negocio completa de un sistema de restaurante, desde la gestión del menú hasta el procesamiento transaccional de pedidos y reportes administrativos.

## 🚀 Características Principales

* **Autenticación Robusta:** Sistema de Login/Registro seguro usando **Laravel Sanctum** (Tokens Bearer).
* **Roles de Usuario:** Soporte para Administradores, Meseros, Cocina y Clientes.
* **Gestión de Menú:** CRUD completo para Categorías y Productos con subida de imágenes.
* **Pedidos Transaccionales:** Creación de pedidos con integridad de datos (Atomic Transactions) y snapshot de precios históricos.
* **Flujo de Estados:** Control del ciclo de vida del pedido (`pending` → `preparing` → `ready` → `served` → `paid`).
* **Dashboard Administrativo:** Reportes de ventas del día, productos más vendidos y rendimiento de meseros.
* **Soft Deletes:** Papelera de reciclaje para productos eliminados.
* **API Resources:** Transformación optimizada de respuestas JSON.

## 🛠️ Tecnologías

* **Framework:** Laravel 11 (PHP ^8.2)
* **Base de Datos:** MySQL 8
* **Seguridad:** Laravel Sanctum
* **Optimización:** Eager Loading, API Resources, Database Transactions

---

## ⚙️ Instalación y Configuración Local

Sigue estos pasos para clonar y ejecutar el proyecto en tu máquina local:

### 1. Prerrequisitos
* PHP >= 8.2
* Composer
* MySQL

### 2. Clonar el repositorio
```bash
git clone https://github.com/NormanSMA/tastetracker-backend.git
cd tastetracker-backend
```

### 3. Instalar dependencias
```bash
composer install
```

### 4. Configurar entorno
Duplica el archivo de ejemplo y genera la clave de aplicación:
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Base de Datos
Crea una base de datos vacía en MySQL llamada `bd_tastetracker` (o el nombre que prefieras).

Edita el archivo `.env` con tus credenciales:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bd_tastetracker
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Migraciones y Seeders (Datos de prueba)
Ejecuta este comando para crear las tablas y poblar la base de datos con usuarios y menú de prueba:
```bash
php artisan migrate:fresh --seed
php artisan storage:link
```

### 7. Ejecutar Servidor
```bash
php artisan serve
```
La API estará disponible en: **http://127.0.0.1:8000**

---

## 📚 Documentación de la API

### Credenciales de Prueba (Seeders)
* **Admin:** `nsma@tastetracker.com` / `password`
* **Mesero:** `anton@tastetracker.com` / `password`

### Endpoints Principales

| Método | Endpoint | Descripción | Auth |
|--------|----------|-------------|------|
| **Auth** |
| POST | `/api/login` | Iniciar sesión y obtener Token | ❌ |
| GET | `/api/user-profile` | Obtener datos del usuario actual | ✅ |
| POST | `/api/logout` | Cerrar sesión | ✅ |
| **Categorías** |
| GET | `/api/categories` | Listar categorías del menú | ❌ |
| POST | `/api/categories` | Crear nueva categoría | ✅ |
| PUT | `/api/categories/{id}` | Actualizar categoría | ✅ |
| DELETE | `/api/categories/{id}` | Eliminar categoría | ✅ |
| **Productos** |
| GET | `/api/products` | Listar productos activos | ❌ |
| GET | `/api/products/{id}` | Ver detalle de producto | ❌ |
| POST | `/api/products` | Crear nuevo producto | ✅ |
| PUT | `/api/products/{id}` | Actualizar producto | ✅ |
| DELETE | `/api/products/{id}` | Eliminar producto (Soft Delete) | ✅ |
| **Pedidos** |
| GET | `/api/orders` | Listar todos los pedidos | ✅ |
| POST | `/api/orders` | Crear un nuevo pedido (Transacción) | ✅ |
| GET | `/api/orders/{id}` | Ver detalle de pedido | ✅ |
| PATCH | `/api/orders/{id}/status` | Cambiar estado (cocina/mesero) | ✅ |
| **Dashboard** |
| GET | `/api/dashboard` | Reportes y estadísticas | ✅ |

**Nota:** Para probar los endpoints protegidos (✅) en Postman, debes enviar el header:
```
Authorization: Bearer {TU_TOKEN_AQUI}
```

---

## 📂 Estructura del Proyecto

```
tastetracker-backend/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Lógica de negocio
│   │   ├── Requests/        # Validaciones de entrada
│   │   └── Resources/       # Transformación JSON
│   └── Models/              # Modelos Eloquent
├── database/
│   ├── migrations/          # Esquema de DB
│   └── seeders/             # Datos de prueba
├── routes/
│   └── api.php              # Definición de rutas
└── config/
    └── cors.php             # Configuración CORS
```

---

## 🔐 Seguridad

* **Sanctum Tokens:** Autenticación basada en tokens Bearer
* **Validaciones:** Form Requests personalizados
* **Transacciones:** Uso de `DB::transaction` para integridad de datos
* **CORS:** Configurado para desarrollo local
* **Precios del servidor:** Cálculos desde la DB, no desde el frontend

---

## 🎯 Flujo de Estados de Pedidos

```
pending → preparing → ready → served → paid
           ↓
        cancelled
```

---

## 🚀 Deploy en Producción

Para deploy, asegúrate de:

1. Configurar `CORS` para tu dominio específico
2. Cambiar `APP_ENV=production` en `.env`
3. Ejecutar `php artisan config:cache`
4. Configurar servidor web (Nginx/Apache)
5. Usar HTTPS para seguridad

---

## 📝 Ejemplo de Request: Crear Pedido

```json
POST /api/orders
Authorization: Bearer {token}

{
  "area_id": 1,
  "table_number": "Mesa 5",
  "order_type": "dine_in",
  "customer_id": null,
  "notes": "Cliente pidió rapidez",
  "items": [
    {
      "product_id": 1,
      "quantity": 2,
      "notes": "Sin cebolla"
    },
    {
      "product_id": 3,
      "quantity": 1
    }
  ]
}
```

**Respuesta:**
```json
{
  "id": 15,
  "table_number": "Mesa 5",
  "status": "pending",
  "order_type": "dine_in",
  "total": 37.50,
  "customer": "Cliente General",
  "waiter": "Antonio Morales",
  "area": "Salón Principal",
  "items": [
    {
      "id": 25,
      "product_name": "Hamburguesa Clásica",
      "quantity": 2,
      "unit_price": 12.50,
      "subtotal": 25.00,
      "notes": "Sin cebolla"
    }
  ],
  "created_at": "2025-11-29 22:00"
}
```

---

##  Autores

Este proyecto fue desarrollado por:

- **Hoowerts Gross**
- **Antony Maltez**
- **Jorge Rodriguez**
- **Norman Acevedo**
