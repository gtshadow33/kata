# KATA

KATA es una pequeña herramienta CLI para crear y gestionar proyectos PHP.

Su objetivo es ofrecer una estructura básica de proyecto con:

* Sistema de rutas.
* Controladores.
* Comandos desde la terminal.
* Autoload PSR-4.
* Estructura organizada de archivos.

## Requisitos

* PHP 8.2 o superior.
* Composer.

## Instalación

Añade KATA a tu proyecto PHP:

```bash
composer require gts/kata
```

Después ejecuta el instalador:

```bash
./vendor/bin/kata install
```

El instalador creará una estructura similar a esta:

```text
proyecto/
├── kata
├── public/
│   ├── index.php
│   ├── .htaccess
│   └── assets/
│       ├── css/
│       ├── js/
│       └── images/
├── web/
│   └── router.php
└── vendor/
```

## Comandos

### Crear un controlador

```bash
php kata make:controller UserController
```

Este comando crea un controlador dentro de:

```text
src/Controllers/UserController.php
```

### Crear una ruta GET

```bash
php kata route get /hola UserController@index
```

Esto registra una ruta en:

```text
web/router.php
```

La ruta ejecutará el método `index` del controlador `UserController`.

## Ejemplo de ruta

El archivo `web/router.php` puede contener:

```php
<?php

use ProyectoPrueba\Controllers\UserController;

$router->get('/hola', [UserController::class, 'index']);
```

## Ejecutar el proyecto

Puedes iniciar el servidor PHP integrado con:

```bash
php -S localhost:8000 -t public public/index.php
```

Después visita:

```text
http://localhost:8000/hola
```

## Estado del proyecto

KATA se encuentra en desarrollo.

Actualmente incluye:

* Instalador de estructura.
* CLI básica.
* Sistema de rutas GET y POST.
* Creación de controladores.
* Registro de rutas desde la terminal.
* Detección automática del namespace PSR-4.

## Licencia

Este proyecto está distribuido bajo la licencia MIT.
