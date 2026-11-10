# Traderig

Traderig es un marketplace para comprar, vender, intercambiar y subastar componentes de PC. El proyecto está desarrollado con Laravel y conserva las tecnologías y convenciones que ya utiliza la aplicación.

## Funcionalidades principales

- Inicio con categorías y publicaciones destacadas.
- Publicaciones de componentes para venta o subasta.
- Intercambios y perfiles de vendedores.
- Carga de fotos para acompañar una publicación.
- Galería de fotos en el detalle del componente.

## Publicar un componente con fotos

La creación de publicaciones está disponible para usuarios autenticados con una cuenta activa. El formulario solicita los datos del componente y permite adjuntar entre 1 y 5 imágenes en formato JPG, PNG o WebP, con un máximo de 5 MB por imagen.

La primera imagen seleccionada se utiliza como foto principal. En la página de detalle se muestra la galería y, cuando hay más de una foto, se puede elegir cuál ver usando las miniaturas. Si una publicación no tiene imágenes, se muestra un estado alternativo.

Las imágenes se guardan en el disco público de Laravel, dentro de `storage/app/public/listings`. Para que puedan mostrarse desde el navegador, hay que crear el enlace público:

```bash
php artisan storage:link
```

Las publicaciones también admiten observaciones opcionales del vendedor y entre 1 y 10 especificaciones técnicas en pares de nombre y valor. Ese formato flexible sirve para distintas categorías: por ejemplo, memoria e interfaz en una tarjeta gráfica, o socket y núcleos en un procesador. Las especificaciones se guardan como JSON en `listings.specifications`; las observaciones se guardan en `listings.seller_notes`. La migración correspondiente se aplica con `php artisan migrate`.

Al guardar una publicación, el servidor valida los datos y las imágenes. La creación del componente, sus fotos y, si corresponde, los datos iniciales de la subasta se realizan juntos. Si ocurre un error durante el proceso, se revierten los cambios y se intenta limpiar las imágenes que ya se habían guardado.

## Cómo está implementada la publicación con fotos

El recorrido está separado entre rutas, controlador, vista y JavaScript. Cada parte cumple una función distinta:

- **Rutas (`routes/web.php`)**: `listings.create` abre el formulario y `listings.store` recibe sus datos. Ambas están dentro del middleware `auth` y `EnsureUserIsActive`, por lo que un visitante o una cuenta inactiva no puede publicar.
- **`ListingController::create()`**: consulta las categorías ordenadas por nombre y envía a la vista las opciones de categoría, estado y tipo. Así, el formulario usa los valores disponibles en los modelos en lugar de duplicar esas listas en la plantilla.
- **`ListingController::store()`**: valida el envío, crea la publicación, guarda sus fotos y crea la subasta cuando corresponde. Si todo termina bien, redirige al detalle.
- **`ListingController::show()`**: comprueba si el visitante puede ver la publicación, prepara sus relaciones y entrega a la plantilla las fotos ordenadas para la galería.
- **Especificaciones**: el formulario permite agregar y quitar filas de nombre/valor en el navegador; el controlador valida y limpia esas filas antes de persistirlas. La ficha muestra las especificaciones solo cuando existen y separa las observaciones del vendedor de la descripción principal.
- **Estado de subasta (`AuctionController::state()`)**: responde con los datos actuales de la subasta y el historial de pujas, incluida la cantidad de ofertas. La interfaz Vue usa esa respuesta para mantener actualizados los importes y el estado del panel.
- **Vista `listings/create.blade.php`**: muestra los campos y el selector de archivos. `enctype="multipart/form-data"` es necesario para que el navegador envíe archivos junto con los campos normales.
- **Vista `listings/show.blade.php`**: genera la imagen principal, las miniaturas, las observaciones y la tabla de especificaciones. La condición `hidden` deja visible solo la foto seleccionada, y los atributos `aria-label` y `aria-pressed` comunican el estado de los botones a tecnologías de asistencia.
- **`resources/js/app.js`**: crea vistas previas en el formulario, permite cambiar la foto visible y abrirla ampliada, y administra las filas dinámicas de especificaciones. La lógica solo se conecta cuando encuentra los elementos correspondientes, así que no interfiere con otras pantallas.

### Decisiones menos obvias del controlador

**Validación de fotos y campos de subasta**

- `photos` valida que se haya enviado una lista de entre 1 y 5 archivos. `photos.*` aplica las reglas a cada archivo: que sea una imagen, que su contenido y extensión correspondan a JPG, PNG o WebP, y que no supere 5120 KB (5 MB).
- `exclude_unless:type,auction` hace que la fecha de cierre y el incremento mínimo solo sean obligatorios cuando el tipo es una subasta. Para una venta directa, esos campos se excluyen de la validación.
- `Rule::in(...)` restringe los valores aceptados a las opciones definidas por el modelo; evita guardar valores arbitrarios enviados desde el navegador.

**Elegir qué datos se guardan**

`Arr::only($data, [...])` crea una lista explícita de los campos que pertenecen a la publicación. Esto evita guardar por accidente otros valores validados que tienen otro destino, como las fotos o el incremento de la subasta. El usuario se obtiene de la sesión con `$request->user()`; no se confía en un `user_id` enviado por el formulario. `boolean('accepts_trade')` normaliza el valor del checkbox, que puede no aparecer en el envío cuando está desmarcado.

**Transacción y archivos**

`DB::transaction(...)` agrupa las escrituras de base de datos. Si falla la creación de la publicación, de una foto o de la subasta, Laravel revierte esos cambios para no dejar registros incompletos.

Los archivos del disco no forman parte de una transacción de base de datos. Por eso, cada ruta guardada se agrega a `$storedPhotoPaths`. Si ocurre un error después de guardar alguna imagen, el `catch` intenta borrarlas y luego vuelve a lanzar el error original. De esa manera el fallo no se oculta y tampoco quedan archivos huérfanos si la limpieza funciona.

Dentro de la transacción, `$request->user()->listings()->create(...)` crea la publicación a través de la relación del usuario. Si el tipo es subasta, también crea el registro relacionado con el precio inicial, el precio actual, el incremento y el cierre. `putFile('listings', $photo)` guarda cada archivo en el disco público con un nombre generado por Laravel; la base de datos conserva la ruta, no el contenido del archivo. La primera foto recibe `is_main = true` y cada una recibe un `sort_order`.

**Preparar el detalle**

En `show()`, `abort_unless($canSee, 404)` impide mostrar publicaciones inactivas a personas que no sean su dueño o un administrador. La respuesta 404 también evita revelar que una publicación privada existe.

`load([...])` carga de antemano las relaciones que necesita la vista (categoría, vendedor, fotos y subasta). Esto evita consultas repetidas al renderizar los datos relacionados. Las pujas se consultan solo si existe subasta; si no, se usa una colección vacía para que la vista reciba un tipo de dato consistente. Finalmente, `sortByDesc('is_main')` pone la foto principal primero y `values()` reordena los índices de la colección para que coincidan con los índices de las miniaturas.

### Vista previa y selección de fotos en JavaScript

`URL.createObjectURL(file)` crea una URL temporal local para mostrar una foto antes de subirla; no la envía ni la guarda en el servidor. Cuando el usuario cambia su selección, `URL.revokeObjectURL(url)` libera las URLs anteriores y `replaceChildren()` limpia las vistas previas viejas. Esto evita acumular recursos temporales mientras se cambia la selección.

En la galería, un único manejador de clics por contenedor detecta qué miniatura se presionó. El índice de `data-photo-index` determina qué imagen mostrar; luego se actualizan `hidden`, la clase visual activa y `aria-pressed`. Así, la imagen visible, el estilo de la miniatura y el estado accesible se mantienen sincronizados.

## Tecnologías

- PHP y Laravel.
- Blade y Bootstrap para las vistas.
- Vue para el panel interactivo de subastas.
- JavaScript y CSS existentes en el proyecto.
- Vite para compilar los recursos del frontend.

No se agregó una dependencia de frontend para implementar la carga de fotos o la galería.

## Preparar el proyecto localmente

Requisitos: PHP compatible con la versión indicada en `composer.json`, Composer, Node.js con npm y una base de datos configurada.

1. Instalar las dependencias de PHP:

   ```bash
   composer install
   ```

2. Crear el archivo de entorno y generar la clave de la aplicación:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   En Windows PowerShell, el primer comando puede reemplazarse por:

   ```powershell
   Copy-Item .env.example .env
   ```

3. Configurar la conexión a la base de datos en `.env` y ejecutar las migraciones:

   ```bash
   php artisan migrate
   ```

4. Instalar las dependencias de JavaScript y compilar los recursos:

   ```bash
   npm install
   npm run build
   ```

5. Habilitar el acceso público a las imágenes:

   ```bash
   php artisan storage:link
   ```

6. Iniciar el servidor local:

   ```bash
   php artisan serve
   ```

## Pruebas

Para ejecutar las pruebas de publicación y de la página de inicio:

```bash
php artisan test --compact tests/Feature/ListingControllerTest.php tests/Feature/AuctionControllerTest.php tests/Feature/SiteTest.php
```

Para ejecutar toda la suite:

```bash
php artisan test
```

Las pruebas de publicaciones cubren el acceso autenticado, la validación y almacenamiento de fotos, el límite de cantidad y tamaño, la validación y persistencia de especificaciones y observaciones, la creación de subastas y la galería del detalle. `AuctionControllerTest` verifica que el estado de la subasta incluya la cantidad de pujas y que no se publique si el componente está inactivo.
