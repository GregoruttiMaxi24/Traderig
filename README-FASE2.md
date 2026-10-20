# TRADERIG - Fase 2: layout y base de datos

## Cómo aplicarla (una sola persona del equipo, en una rama nueva)

```bash
git checkout -b feature/layout-y-base-de-datos
```

1. Copiá el contenido de esta carpeta **encima** de la raíz del proyecto (respetando las carpetas).
   Estos archivos reemplazan a los que trae Laravel:
   - `app/Models/User.php`
   - `database/seeders/DatabaseSeeder.php`
   - `routes/web.php`
2. Borrá `resources/views/welcome.blade.php` (ya no se usa).
3. En `.env` poné:
   ```
   APP_NAME=TRADERIG
   APP_LOCALE=es
   ```
4. Reconstruí la base con datos de prueba:
   ```bash
   php artisan migrate:fresh --seed
   php artisan serve
   ```
5. Subí el trabajo:
   ```bash
   git add .
   git commit -m "Fase 2: layout Bootstrap, migraciones, modelos y seeders"
   git push -u origin feature/layout-y-base-de-datos
   ```
   Abrí el Pull Request para que otra persona lo revise antes de unirlo a `main`.

## Usuarios de prueba (solo desarrollo)

| Rol | Email | Contraseña |
|---|---|---|
| admin | admin@traderig.test | password |
| user | usuario@traderig.test | password |

## Modelo de datos

| Tabla | Qué guarda | Relaciones |
|---|---|---|
| users | cuentas (+ role, phone, city, is_blocked) | tiene muchas listings, bids, trade_offers |
| categories | categorías de componentes | tiene muchas listings |
| listings | publicaciones (type: sale/auction, accepts_trade) | de un user, de una category |
| listing_photos | fotos de cada publicación | de una listing |
| auctions | subasta de una publicación (1 a 1) | de una listing; winner = user |
| bids | pujas | de una auction y de un user |
| trade_offers | ofertas de trueque con diferencia de dinero opcional | a una listing, de un user, con offered_listing opcional |

Notas de diseño:
- `listings.price` es el precio de venta, o el precio base si la publicación es una subasta.
- Si una publicación tiene `accepts_trade = true`, puede recibir ofertas de intercambio.
- `role` e `is_blocked` no son asignables en masa, para que nadie se haga admin desde un formulario.
- Las fotos se guardan en disco (Fase 4, con `php artisan storage:link`); la base solo guarda la ruta.

## Logo e identidad

El navbar usa automáticamente `public/img/logo.svg` (o `.png` / `.webp`) si existe. Mientras no
haya logo muestra el nombre en texto. Los colores y tipografías están como variables al inicio de
`public/css/traderig.css`: con la identidad final se cambian solo ahí.

Bootstrap 5.3 se carga por CDN, así que no hace falta `npm install` para esta fase.
