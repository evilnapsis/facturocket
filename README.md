# Facturocket 🚀

**Facturocket** es un sistema web de facturación electrónica orientado a la legislación fiscal de México (CFDI 4.0). Está desarrollado sobre la arquitectura MVC utilizando el micro-framework **LegBox** (PHP) y base de datos MySQL/MariaDB.

---

## 🛠️ Características Principales

- **Gestión de Clientes (Receptores):** Registro de clientes incluyendo RFC, Régimen Fiscal, Uso de CFDI, Dirección y Código Postal.
- **Gestión de Productos/Servicios:** Administración de catálogo con códigos SAT, claves de unidad de medida, tasas de impuestos y precios.
- **Facturación Electrónica (CFDI 4.0):**
  - Soporte para emisión de comprobantes de **Ingreso (Facturas)**, **Egreso (Notas de Crédito)** y **Pago (Complementos de Pago)**.
  - Relación de CFDIs previos (Sustitución, Nota de crédito, etc.).
  - Configuración y asignación automática de series y folios consecutivos.
- **Configuración del Emisor:**
  - Carga y resguardo del Certificado de Sello Digital (**CSD**: Archivo `.cer`, `.key` y contraseña).
  - Configuración del PAC / Proveedor Autorizado de Certificación (Serial / API Key).
- **Catálogos del SAT:** Incorporación de catálogos oficiales autogestionables:
  - Regímenes Fiscales
  - Usos de CFDI
  - Formas y Métodos de Pago
  - Claves de Unidades de Medida
  - Claves de Producto o Servicio
  - Objetos de Impuesto (IVA, ISR, IEPS)

---

## 📂 Estructura del Proyecto

El proyecto está organizado bajo el patrón Modelo-Vista-Controlador (MVC) con la estructura LegBox:

```text
facturocket/
│
├── core/
│   ├── app/
│   │   ├── action/      # Controladores que procesan formularios/acciones POST y llamadas AJAX
│   │   ├── model/       # Clases de acceso y mapeo de datos (ORM básico)
│   │   └── view/        # Vistas de la aplicación (páginas PHP/HTML)
│   ├── autoload.php     # Autocarga de clases internas
│   └── controller/      # Controladores base y clases del framework (Database, View, etc.)
│
├── assets/              # Archivos multimedia, fuentes y complementos estáticos
├── css/                 # Hojas de estilo personalizadas
├── js/                  # Scripts de Javascript
├── plugins/             # Librerías y extensiones de terceros
├── vendor/              # Dependencias instaladas vía Composer (ej. FPDF u otras API de timbrado)
├── index.php            # Punto de entrada de la aplicación
├── schema.sql           # Estructura e inserciones base de la base de datos
└── README.md            # Documentación del proyecto
```

---

## 📋 Requisitos del Sistema

- **Servidor Web:** Apache (XAMPP es recomendado)
- **PHP:** Versión 7.4 o superior (Compatible con PHP 8.x)
- **Base de Datos:** MySQL / MariaDB
- **Gestor de Dependencias:** Composer (opcional para dependencias de terceros)

---

## ⚙️ Instalación y Configuración

1. **Clonar o descargar el proyecto** dentro del directorio público de tu servidor web (por ejemplo, `C:\xampp\htdocs\facturocket`).
2. **Crear e importar la Base de Datos:**
   - Accede a tu gestor de base de datos (como phpMyAdmin) y crea una base de datos llamada `facturocket`.
   - Importa el archivo [schema.sql](file:///c:/xampp/htdocs/facturocket/schema.sql) para estructurar las tablas e insertar los catálogos base del SAT.
3. **Configurar la conexión a la Base de Datos:**
   - La configuración por defecto se encuentra en [core/controller/Database.php](file:///c:/xampp/htdocs/facturocket/core/controller/Database.php):
     ```php
     $this->user = "root";
     $this->pass = "";
     $this->host = "localhost";
     $this->ddbb = "facturocket";
     ```
   - Si tus credenciales de MySQL son diferentes, modifícalas en este archivo.
4. **Instalar dependencias de Composer:**
   - Abre una terminal en la raíz del proyecto y ejecuta:
     ```bash
     composer install
     ```

---

## 🔑 Credenciales de Acceso por Defecto

Una vez que el servidor esté activo, puedes ingresar al sistema con las siguientes credenciales de administrador base:

- **Usuario:** `admin`
- **Contraseña:** `admin`
