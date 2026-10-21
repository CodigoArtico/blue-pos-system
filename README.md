<p align="center">
  <img src="public/images/favicon/favicon2.png" width="110" alt="Blue POS System Logo">
</p>

<h1 align="center">Blue POS System — Point of Sale & Management Platform</h1>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 11">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/MySQL-8.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL 8.x">
  <img src="https://img.shields.io/badge/Bootstrap-5.x-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap 5">
  <img src="https://img.shields.io/badge/License-MIT-green?style=for-the-badge" alt="License MIT">
</p>

<p align="center">
  <b>Sistema integral y modular de Punto de Venta (POS), Facturación Electrónica, Inventario y Control de Ventas desarrollado en Laravel.</b>
</p>

---

> [!NOTE]
> ### 👋 Un saludo del autor
> *¡Hola a todos! Este proyecto lo desarrollé en el **2024** para el ecosistema **Código Ártico**. Decidí liberarlo de manera abierta y gratuita para que la comunidad de desarrolladores, estudiantes o emprendedores puedan utilizarlo, revisarlo o tomarlo como base para algún proyecto pequeño. ¡Espero que les sea de utilidad y les sirva de apoyo en sus ideas!*

---

## 🚀 Características Principales

- 🛒 **Punto de Venta (POS) Rápido e Intuitivo:**
  - Registro de ventas en segundos, selección ágil de clientes, métodos de pago múltiples (efectivo, transferencias, billeteras digitales) y generación instantánea de tickets de venta.
- 📑 **Facturación Electrónica Integrada:**
  - Emisión de Boletas, Facturas, Notas de Crédito y Notas de Débito con integración para certificados digitales (SUNAT).
  - Consulta automática de **DNI (RENIEC)** y **RUC (SUNAT)** conectada vía API REST.
- 📦 **Control de Inventario y Productos:**
  - Catálogo completo con códigos de barras, categorías, marcas, presentaciones, precios al por mayor y menor.
  - Alertas visuales y sonoras de **Stock Crítico** para evitar quiebres de inventario.
  - Módulo de compras y registro de proveedores.
- 🍽️ **Gestión de Mesas y Comandas:**
  - Módulo visual para restaurantes y cafeterías con apertura de mesas, comandas y cierre de cuentas.
- 💰 **Caja y Control Financiero:**
  - Apertura y cierre de caja con arqueo, reporte diario de ingresos vs. egresos y balance acumulado.
- 📊 **Informes y Estadísticas en Vivo:**
  - Dashboard interactivo con gráficos de rendimiento: ventas por comprobante, métodos de pago más usados, productos estrella y top de vendedores.
  - Generación de reportes detallados en formato **PDF**.
- 🔐 **Seguridad y Control de Roles:**
  - Autenticación robusta y control de permisos granulares gestionados con **Spatie Laravel Permission**.
- 🌐 **Sincronización con Tienda Web:**
  - Comparte catálogo de productos y base de datos en tiempo real con [Blue Ecommerce Store](https://github.com/CodigoArtico/blue-ecommerce-store).

---

## 📋 Requisitos Previos

Asegúrate de contar con las siguientes herramientas en tu entorno local o servidor:

- **PHP** >= 8.2 (con extensiones: `pdo_mysql`, `openssl`, `mbstring`, `curl`, `fileinfo`, `gd`)
- **Composer** (v2.x)
- **Node.js** >= 18.x y **NPM**
- **MySQL** >= 8.0 (o MariaDB >= 10.4)

---

## 🛠️ Instalación y Puesta en Marcha

Sigue este paso a paso para clonar y ejecutar el sistema en tu entorno local:

### 1. Clonar el Repositorio
```bash
git clone https://github.com/CodigoArtico/blue-pos-system.git
cd blue-pos-system
```

### 2. Instalar Dependencias
```bash
# Dependencias de PHP
composer install

# Dependencias de Frontend
npm install
```

### 3. Configurar Variables de Entorno (`.env`)
Copia la plantilla de entorno:

```bash
# En Windows (PowerShell):
copy .env.example .env

# En Linux / macOS:
cp .env.example .env
```

Genera la clave de seguridad de la aplicación:
```bash
php artisan key:generate
```

Abre tu archivo `.env` y configura la conexión a tu base de datos:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=blue_store_db
DB_USERNAME=root
DB_PASSWORD=
```

> [!TIP]
> Si planeas utilizar la tienda web [Blue Ecommerce Store](https://github.com/CodigoArtico/blue-ecommerce-store), asegúrate de que ambos proyectos apunten al **mismo nombre de base de datos** (`blue_store_db`).

### 4. Ejecutar Migraciones y Seeders (Datos de Demostración)
Este comando creará todo el esquema relacional de tablas (roles, permisos, categorías, marcas, empresa de muestra y usuario admin):

```bash
php artisan migrate --seed
```

### 5. Crear el Enlace Simbólico de Almacenamiento
Para la correcta visualización de imágenes de productos y comprobantes:
```bash
php artisan storage:link
```

### 6. Compilar Assets y Levantar el Servidor
En una terminal compila los assets del frontend:
```bash
npm run dev
# o para producción: npm run build
```

En otra terminal inicia el servidor local de Laravel:
```bash
php artisan serve --port=8000
```

Ingresa desde tu navegador a: **`http://localhost:8000`**

---

## 👤 Credenciales de Acceso por Defecto

Una vez ejecutado el seeder, puedes iniciar sesión inmediatamente con el usuario administrador:

| Campo | Valor |
|---|---|
| **URL de Acceso** | `http://localhost:8000/login` |
| **Correo Electrónico** | `Administrador@gmail.com` |
| **Contraseña** | `12345678` |
| **Rol Asignado** | `administrador` (Acceso completo) |

---

## 🔗 Ecosistema Conectado

Este repositorio forma parte de la suite **Blue**:
* 🛒 **Panel Administrativo & POS:** [blue-pos-system](https://github.com/CodigoArtico/blue-pos-system) *(Este repositorio)*
* 🛍️ **Tienda Online Ecommerce:** [blue-ecommerce-store](https://github.com/CodigoArtico/blue-ecommerce-store)

---

## 💬 Contacto & Soporte

Si tienes dudas, consultas o deseas compartir mejoras para el proyecto:
- **Telegram Oficial:** [@codigoartico](https://t.me/codigoartico)
- **TikTok:** [@codigoartico](https://www.tiktok.com/@codigoartico)
- **GitHub:** [CodigoArtico](https://github.com/CodigoArtico)

---

## 📜 Licencia

Distribuido bajo la Licencia **MIT**. Consulta el archivo `LICENSE` para más detalles.  
Desarrollado con dedicación por **Código Ártico** (2024).