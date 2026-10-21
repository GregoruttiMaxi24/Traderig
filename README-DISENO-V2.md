# TRADERIG - Diseño v2 (estilo de los mockups, con la marca del grupo)

## Cómo aplicarlo (rama nueva)
```bash
git checkout -b feature/diseno-v2
```
1. Copiá el contenido de esta carpeta sobre la raíz del proyecto, reemplazando archivos
   (cambian `routes/web.php`, `DatabaseSeeder.php`, navbar, footer, home, CSS y JS).
2. No hay migraciones. Cargá los datos de ejemplo:
   ```bash
   composer dump-autoload
   php artisan migrate:fresh --seed
   php artisan serve
   ```
   (`DemoListingSeeder` crea 10 publicaciones, 3 subastas con pujas y 2 usuarios más; solo corre con `APP_ENV=local`.)
3. Pruebas: `php artisan test`.

## Qué se tomó de los mockups y cómo se adaptó
| Mockup | En TRADERIG |
|---|---|
| Home: destacados, colecciones, subastas en vivo, tendencias, pie | Publicaciones destacadas, categorías en ranking, subastas en vivo con cuenta regresiva, vendedores destacados, pie |
| Producto: foto grande + panel con precio, botones, detalles y artista | `/publicaciones/{id}`: foto, precio o puja actual, "Pujar" / "Proponer intercambio", pestañas Detalles y Actividad (pujas), vendedor |
| Perfil: portada, avatar, estadísticas, filtros y grilla | `/vendedores/{id}`: portada con circuitos, avatar con inicial, estadísticas, filtros (todas, venta, subastas, intercambio) y grilla paginada |

Se conservó la identidad de marca: paleta oficial, Roboto / Open Sans / Roboto Mono e isologotipo vertical.
Se dejó afuera lo propio de NFT (billetera, ETH, token, contrato, blockchain).

## Piezas reutilizables para las próximas fases
- `<x-listing-card :listing="$listing" />`: tarjeta de venta o subasta (con cuenta regresiva).
- `<x-avatar :user="$user" size="md" />`: avatar con inicial (sm, md, lg, xl).
- `<x-field ... />`: campo de formulario (Fase 3).
- Clases: `.tr-card`, `.tr-section__title`, `.tr-pill`, `.tr-panel`, `.tr-specs`, `.tr-badge`, `.btn-outline-tr`.
- Botones "Pujar" y "Proponer intercambio": ya están en el detalle, desactivados hasta que existan las rutas
  `auctions.bids.store` (Fase 6) y `trade-offers.create` (Fase 5); se activan solos.
- El enlace "Catálogo" del menú aparece solo cuando exista la ruta `listings.index` (Fase 4).

## Aviso para Fase 4
`ListingController` hoy solo tiene `show`. Al agregar el CRUD, definan `/publicaciones/crear` antes de
`/publicaciones/{listing}` (o dejen `whereNumber`, que ya evita el choque).
