# TRADERIG

Plataforma para comprar, vender, intercambiar y subastar componentes de PC usados
como, por ejemplo, placas de video, procesadores, memorias RAM, placas madre y almacenamiento.
Permite intercambiar con una diferencia de dinero y publicar subastas.

Tesis de la carrera de Diseño y Desarrollo Web.

## Tecnologías

- Laravel (PHP 8.3 o superior) con vistas Blade
- MariaDB
- Bootstrap 5 (por CDN) y CSS propio en `public/css/traderig.css`
- JavaScript propio en `public/js/traderig.js`

No hace falta Node.js ni compilar nada: Bootstrap se carga por CDN.

## Requisitos

- PHP 8.3 o superior
- Composer
- MariaDB (por ejemplo desde Laragon) y un cliente como HeidiSQL

## Instalación

1. Clonar el repositorio dentro de la carpeta de proyectos (por ejemplo `D:\laragon\www`):

   ```bash
   git clone <url-del-repo>
   cd traderig
   ```

2. Crear en MariaDB una base de datos vacía llamada `traderig`.

3. Instalar las dependencias:

   ```bash
   composer install
   ```

4. Copiar `.env.example` a `.env`:

   ```bash
   copy .env.example .env
   ```

   Abrir `.env` y completar `DB_USERNAME` y `DB_PASSWORD` con los datos de la MariaDB local.
   `APP_NAME`, `APP_LOCALE` y `DB_DATABASE` ya vienen configurados.

5. Generar la clave de la aplicación, crear las tablas y cargar los datos de prueba:

   ```bash
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   php artisan serve
   ```

6. Abrir http://127.0.0.1:8000

## Usuarios de prueba

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | admin@traderig.test | password |
| Usuario | usuario@traderig.test | password |

Los datos de ejemplo (publicaciones y subastas) se cargan solo con `APP_ENV=local`.
Para volver a empezar con la base limpia: `php artisan migrate:fresh --seed`.

## Tests

```bash
php artisan test
```

## Cómo trabajamos

- Nadie trabaja directo en `main`.
- Antes de empezar: `git checkout main` y `git pull`.
- Cada tarea en su propia rama: `git checkout -b feature/nombre-de-la-tarea`.
- Commits chicos y con mensaje claro.
- Al terminar: `git push` y abrir un Pull Request. Otra persona lo revisa antes de unirlo a `main`.
- Nunca se modifica una migración ya subida: se crea una nueva.
- El archivo `.env` no se sube nunca (cada uno tiene el suyo).

## Estructura básica

- `app/Http/Controllers`: controladores
- `app/Models`: modelos Eloquent
- `database/migrations` y `database/seeders`: esquema y datos de prueba
- `resources/views`: vistas Blade
- `routes/web.php`: rutas