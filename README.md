# TRADERIG

Plataforma web para **comprar, vender, intercambiar y subastar componentes de PC usados**: placas de video, procesadores, memorias RAM, motherboards, almacenamiento, fuentes, gabinetes, monitores y periféricos.

## De qué se trata

Hoy, quien quiere vender un componente usado lo publica en sitios generales donde es difícil describirlo técnicamente, y quien busca uno no puede comparar ni intercambiar. TRADERIG reúne en un solo lugar tres formas de operar:

- **Venta directa:** el vendedor publica un componente con precio fijo.
- **Subasta:** el vendedor define un precio inicial, un aumento mínimo y una fecha de cierre; los compradores pujan y gana la oferta más alta cuando vence el plazo.
- **Intercambio:** el comprador propone cambiar uno de sus componentes (con una diferencia de dinero opcional, que puede pagar cualquiera de las partes).

Cada publicación incluye fotos, descripción, estado de conservación y especificaciones técnicas en pares de nombre y valor, que sirven para cualquier categoría (por ejemplo, socket y núcleos en un procesador, o memoria e interfaz en una placa de video).

El chat privado entre dos usuarios **no está disponible libremente**: se abre cuando una de las partes se compromete (por ejemplo, al enviar una propuesta de intercambio). Así se evita que las operaciones se cierren fuera de la plataforma.

Es un trabajo grupal, desarrollado en Laravel.

## Funcionalidades implementadas

**Cuentas y perfiles**

- Registro, inicio y cierre de sesión.
- Perfil editable (datos personales y cambio de contraseña).
- Página pública de cada vendedor, con sus publicaciones activas.
- Roles de usuario y de administrador. Las cuentas inactivas no pueden operar.

**Catálogo**

- Portada con categorías y publicaciones.
- Detalle de la publicación con galería de fotos y miniaturas, descripción, observaciones del vendedor, especificaciones técnicas y datos del vendedor.
- Precios en pesos enteros, sin centavos.

**Publicar un componente (ABM completo)**

- Alta con 1 a 5 fotos, especificaciones dinámicas, tipo (venta o subasta) y opción de aceptar intercambios.
- Mis publicaciones: tabla con todo lo que publicó el usuario.
- Edición de datos y de fotos (agregar, quitar y elegir la principal).
- Baja con pantalla de confirmación que avisa qué se pierde (fotos, pujas, propuestas y chats).

**Subastas**

- Pujas con validación del aumento mínimo y bloqueo de la fila para evitar pujas simultáneas.
- Historial con las últimas 5 pujas, cuenta regresiva y panel que se actualiza solo cada 5 segundos.
- Cierre automático al vencer el plazo: se determina el ganador (la puja más alta) y se muestra en la publicación.

**Intercambios y chat**

- Proponer un intercambio: se ofrece una publicación propia, una diferencia de dinero (indicando quién la paga), un mensaje, o una combinación. Una sola propuesta pendiente por persona y publicación.
- Al enviar la propuesta se abre el chat privado entre las dos partes.
- Chat con actualización automática cada 5 segundos y acceso solo para los participantes.
- Enlace "Mis chats" en el menú del usuario.

**Administración**

- Panel con totales y últimos usuarios registrados.

**En desarrollo (próximas etapas)**

- Aceptar o rechazar propuestas de intercambio desde la plataforma.
- Botón "Comprar" con estado reservado y chat con el vendedor.
- Chat entre el ganador de una subasta y el vendedor.
- Listados de Subastas y de Productos con filtros.
- Moderación completa desde el panel de administración.

## Tecnologías

| Área | Tecnología |
|---|---|
| Lenguaje y framework | PHP 8.3 o superior y Laravel 13 |
| Base de datos | MariaDB (migraciones, seeders y Eloquent) |
| Vistas | Blade y Bootstrap 5.3 (cargado desde CDN) |
| Estilos | CSS propio en `public/css/traderig.css` |
| JavaScript | JavaScript puro (galería de fotos y editor de especificaciones) y Vue 3 con Composition API (panel de subastas y chat) |
| Compilación del frontend | Vite 8 con `@vitejs/plugin-vue` y `laravel-vite-plugin` |
| Tipografías | Roboto, Roboto Mono y Open Sans (Google Fonts) |
| Pruebas | PHPUnit 12 |
| Control de versiones | Git y GitHub (ramas por tarea y Pull Requests) |

Bootstrap y las tipografías se cargan desde internet, por lo que hace falta conexión para ver el sitio con su estilo completo.

## Requisitos

Instalar antes de empezar:

- PHP 8.3 o superior y [Composer](https://getcomposer.org/)
- [Node.js](https://nodejs.org/) LTS (20.19 o superior), que incluye npm
- MariaDB
- Git

Para comprobar que están instalados:

```bash
php -v
composer -V
node -v
npm -v
mariadb --version
git --version
```

## Cómo instalar y correr el proyecto

1. Clonar el repositorio y entrar a la carpeta:

   ```bash
   git clone https://github.com/GregoruttiMaxi24/Traderig.git
   cd Traderig
   ```

   Si recibiste el proyecto en un .zip, descomprimilo, entrá a la carpeta y empezá por el paso 2.

2. Instalar las dependencias de PHP:

   ```bash
   composer install
   ```

3. Crear el archivo de entorno y generar la clave de la aplicación:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   En Windows PowerShell, el primer comando puede reemplazarse por:

   ```powershell
   Copy-Item .env.example .env
   ```

4. Crear una base de datos vacía llamada `traderig` (desde HeidiSQL, phpMyAdmin o la consola de MariaDB):

   ```sql
   CREATE DATABASE traderig CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

5. Abrir `.env` y completar los datos de la conexión: `DB_USERNAME`, `DB_PASSWORD` y, si tu MariaDB no usa el puerto 3306, `DB_PORT`.

6. Crear las tablas y cargar los datos de ejemplo (categorías, usuarios de prueba y, con `APP_ENV=local`, publicaciones y subastas):

   ```bash
   php artisan migrate --seed
   ```

7. Instalar las dependencias de JavaScript y compilar los recursos:

   ```bash
   npm install
   npm run build
   ```

8. Habilitar el acceso público a las imágenes:

   ```bash
   php artisan storage:link
   ```

9. Levantar el servidor:

   ```bash
   php artisan serve
   ```

   Si vas a modificar JavaScript o Vue, abrí una segunda terminal y dejá corriendo también:

   ```bash
   npm run dev
   ```

10. Abrir http://127.0.0.1:8000

## Usuarios de prueba

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | admin@traderig.test | password |
| Usuario | usuario@traderig.test | password |

Los datos de ejemplo se cargan solo con `APP_ENV=local`. Para volver a empezar con la base limpia (**borra todas las tablas**):

```bash
php artisan migrate:fresh --seed
```

## Pruebas

```bash
php artisan test
```

Cubren la publicación de componentes (acceso, validación y almacenamiento de fotos, especificaciones, subastas), el estado de las subastas y la portada.

## Estructura del proyecto

| Carpeta | Contenido |
|---|---|
| `routes/web.php` | Rutas del sitio |
| `app/Http/Controllers` | Controladores (publicaciones, subastas, pujas, intercambios, chat, perfil, administración) |
| `app/Models` | Modelos Eloquent (usuarios, publicaciones, fotos, subastas, pujas, intercambios, conversaciones y mensajes) |
| `resources/views` | Vistas Blade |
| `resources/js` | Componentes Vue (subasta y chat), servicios y `app.js` |
| `database/migrations` y `database/seeders` | Esquema y datos de ejemplo |
| `public/css/traderig.css` | Estilos propios |
| `docs/` | Documentación técnica |

La explicación detallada de cómo se implementó la publicación con fotos está en [`docs/publicacion-con-fotos.md`](docs/publicacion-con-fotos.md).

## Cómo trabajamos

- Nadie trabaja directo en `main`.
- Antes de empezar: `git checkout main` y `git pull`.
- Cada tarea en su propia rama: `git checkout -b feature/nombre-de-la-tarea`.
- Commits chicos y con mensaje claro.
- Al terminar: `git push` y abrir un Pull Request. Otra persona lo revisa antes de unirlo a `main` (con "Create a merge commit").
- Nunca se modifica una migración ya subida: se crea una nueva.
- El archivo `.env` no se sube nunca (cada uno tiene el suyo).