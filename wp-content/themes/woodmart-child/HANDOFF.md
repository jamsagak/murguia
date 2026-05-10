# Joyería Murguía — Handoff para nuevo colaborador

> Pasa este archivo completo a tu Claude antes de empezar.
> Después de leerlo, Claude tendrá todo el contexto del proyecto y podrá
> conectarse al servidor, ver el estado real y arrancar a trabajar.

---

## 1. Qué es este proyecto

Rediseño web completo para **Joyería Murguía** (joyería de lujo en Perú).
Stack: WordPress 6.x + WooCommerce + tema padre **WoodMart** + **WoodMart Child Theme** custom
donde vive TODO el código propio. Sin page builders — PHP puro + SCF (Secure Custom Fields).

- Sitio en producción: **https://murguia.jamweb.space**
- Repo git: **https://github.com/jamsagak/murguia.git**

---

## 2. Acceso al servidor

| Campo     | Valor                   |
|-----------|-------------------------|
| Host      | `207.244.240.169`       |
| Usuario   | `murguiajamweb`         |
| Puerto    | `22`                    |
| Auth      | Llave pública (sin password) |
| Panel     | cPanel                  |

```bash
# Conectar
ssh murguiajamweb@207.244.240.169

# Alias recomendado en ~/.ssh/config
Host murguia
    HostName 207.244.240.169
    User murguiajamweb
    Port 22
    IdentityFile ~/.ssh/id_rsa
# → después solo: ssh murguia
```

El WordPress vive en `/home/murguiajamweb/public_html/`.
El child theme está en `.../wp-content/themes/woodmart-child/`.

> **NUNCA editar archivos directamente en el servidor.**
> Flujo siempre: local → git commit → scp al servidor.

---

## 3. Repo git

```bash
git clone https://github.com/jamsagak/murguia.git
cd murguia

# Antes de trabajar
git pull origin main

# Commits recientes (estado actual)
# a6f5336  CSS: forzar renderizado de SVG inline en botones del theme
# 672d67c  Single product: guía de tallas editable por pieza + fixes de galería
# 6971106  Assets: extender dequeue con 5 handles extra de WoodMart/Elementor
# 86cef48  Single product: rediseño completo con galería vertical, specs, trust bar, tabs y slider
# b272729  Shop: paginación con elipsis y cleanup agresivo de assets WoodMart
# 0a298cd  Baseline inicial: child theme woodmart-child custom en producción
```

---

## 4. Estado actual del proyecto

### Páginas completadas ✓
| Template              | URL                                  | Estado |
|-----------------------|--------------------------------------|--------|
| `front-page.php`      | murguia.jamweb.space/                | ✓      |
| `archive-product.php` | murguia.jamweb.space/tienda/         | ✓      |
| `single-product.php`  | murguia.jamweb.space/producto/...    | ✓      |
| `page-contact.php`    | murguia.jamweb.space/contacto/       | ✓      |

### Página pendiente ✗
| Template          | Slug     | Prefijo ACF |
|-------------------|----------|-------------|
| `page-about.php`  | nosotros | `ab_`       |

### Qué tiene cada página terminada

**Home (`front-page.php`)**
- Hero pantalla completa con nav transparente que se vuelve sólido al hacer scroll
- Sección de colecciones
- Sección de bestsellers con slider horizontal (flechas + contador)
- Sección de contacto/cita con formulario

**Shop (`archive-product.php`)**
- Layout boxed (max-width 1440px), sidebar izquierdo con filtros
- Filtros: Categoría > Precio (slider siempre visible) > Piedra > Metal > Ordenar
- Sidebar toggle en mobile, overlay oscuro
- Paginación con elipsis: `‹ 1 … 4 5 6 … 55 ›`
- Filtros se preservan en URLs de paginación

**Single Product (`single-product.php`)**
- Galería vertical con thumbs a la izquierda (horizontal en mobile ≤768px)
- Imagen principal 1:1 con `object-fit: contain` (no recorta joyas)
- Lightbox full-screen: flechas, teclado (ESC/←/→), caption 1/5 2/5...
- Tabla de specs dinámica: atributos WC visibles + SKU + disponibilidad
- Trust bar: 4 ítems con SVG inline, textos editables desde ACF
- Tabs dinámicos: Descripción / Detalles técnicos / Cuidado de la pieza
  (solo aparecen si tienen contenido)
- Slider de productos relacionados (hasta 6, responsive 3/2/1)
- **Guía de tallas**: botón + modal, imagen por producto desde ACF
  (el botón no aparece si el campo está vacío)

**Contacto (`page-contact.php`)**
- Formulario de solicitud de cita con handler PHP + redirect con `?cita=ok/error`
- Mapa / dirección

---

## 5. Arquitectura técnica

### Plugin de campos: SCF (Secure Custom Fields)
Fork gratuito de ACF 6.x — API 100% compatible con ACF.
Diferencias importantes:
- `relationship` **no existe** → usar `post_object` con `multiple: 1`
- Options Pages (ACF Pro) no disponible → usar CPT `murguia_ajustes` como sustituto
- `acf_add_local_field_group()` funciona igual

### Sistema de ajustes editables (CPT murguia_ajustes)
Cada página tiene su propio post dentro del CPT `murguia_ajustes`.
Los textos, imágenes y configuraciones se sacan con:

```php
murguia_ajuste( 'nombre_del_campo', 'fallback', 'slug-del-post-ajuste' )
```

| Página       | Slug del post | Prefijo |
|--------------|---------------|---------|
| Inicio       | pagina-de-inicio | `hp_` |
| Tienda       | tienda           | `sh_` |
| Producto     | producto         | `prod_` |
| Contacto     | contacto         | `ct_` |
| Sobre nosotros | nosotros       | `ab_` ← pendiente |

### Campos ACF activos en productos
Los siguientes campos existen en todos los productos (field group "Producto — Ajustes Globales"):
- `prod_trust_1..4` — textos trust bar
- `prod_tab_desc_label`, `prod_tab_detalles_label`, `prod_tab_cuidado_label`
- `prod_detalles_texto` — contenido pestaña Detalles técnicos
- `prod_cuidado_texto` — contenido pestaña Cuidado de la pieza
- `prod_related_cantidad` — cantidad productos relacionados (3–12)

Campos por producto individual (field group "Murguía — Datos de la pieza", sidebar):
- `murg_guia_tallas` — imagen de guía de tallas (return format: array)
- `murg_guia_tallas_titulo` — título del modal

### Atributos WooCommerce del catálogo
Los productos son tipo `simple` (no variable).
Los atributos son de taxonomía (`pa_*`), visibles, no para variaciones.
Slugs detectados en el catálogo:
- `pa_color-de-oro` — Color de oro (dorado, blanco, rosado...)
- `pa_tipo-de-producto` — Tipo (collar, anillo, arete...)
- `pa_piedra` — Tipo de piedra

### Dequeue de assets
`murguia_dequeue_unused_assets()` en `functions.php` elimina CSS/JS de WoodMart/Elementor
en nuestras páginas custom. **Leer esta función antes de agregar o quitar handles** —
ya están documentados los ~26 handles removidos con su razón.

Resultado: −82% CSS en home, −85% en shop, −56% en single product.

---

## 6. Design system

```css
/* Tokens en :root — nunca valores hardcodeados */
--murg-gold:   #C9A84C;
--murg-cream:  #F5F0E8;
--murg-black:  #0A0A0A;
--murg-ink:    #1A1A1A;
--murg-muted:  #6B6B6B;
```

**Tipografía**
- Headings: `Cormorant Garamond` (serif, 300/400/500, italic)
- Body/UI: `Inter` (sans, 200/300/400/500)

**Nomenclatura CSS**
- BEM estricto con prefijo `.murg-` siempre
- Modifier activo: `is-active`, `is-open`, `is-scrolled`, `is-visible`
- Nunca colores hardcodeados — usar variables CSS

**Breakpoints**
- Mobile: ≤767px
- Tablet: 768–1024px
- Desktop: 1025–1440px
- Wide: ≥1441px

---

## 7. Flujo de trabajo

### Antes de cada cambio
```bash
git pull origin main
```

### Validar sintaxis antes de subir
```powershell
# PHP (local con XAMPP en Windows)
C:\xampp\php\php.exe -l wp-content\themes\woodmart-child\functions.php

# JS
node -c wp-content\themes\woodmart-child\assets\js\murguia.js
```

### Subir archivos al servidor (scp)
```powershell
# Un archivo
scp wp-content/themes/woodmart-child/style.css murguia:~/public_html/wp-content/themes/woodmart-child/

# Varios archivos (mismo directorio)
scp wp-content/themes/woodmart-child/functions.php `
    wp-content/themes/woodmart-child/style.css `
    murguia:~/public_html/wp-content/themes/woodmart-child/

# Archivo en subdirectorio (scp no preserva estructura con múltiples archivos)
scp wp-content/themes/woodmart-child/assets/js/murguia.js `
    murguia:~/public_html/wp-content/themes/woodmart-child/assets/js/murguia.js
```

### Limpiar OPcache después de cambios PHP (CRÍTICO)
El servidor usa PHP-FPM con OPcache. Sin esto los cambios PHP no aparecen.

```powershell
# 1. Crear script temporal
@'
<?php
if ( function_exists( "opcache_reset" ) ) opcache_reset();
echo "ok " . time();
'@ | Set-Content opcache-reset.php

# 2. Subir
scp opcache-reset.php murguia:~/public_html/opcache-reset.php

# 3. Golpear todos los workers (6-8 requests)
for ($i=0; $i -lt 8; $i++) {
    curl.exe -sS --ssl-no-revoke -o nul "https://murguia.jamweb.space/opcache-reset.php"
}

# 4. Eliminar del servidor
ssh murguia "rm ~/public_html/opcache-reset.php"

# 5. Limpiar cache de WordPress también
ssh murguia "cd ~/public_html && wp cache flush --allow-root"
```

> ⚠️ Si después de subir un PHP los cambios no aparecen, casi siempre es OPcache.
> Primer reflejo: limpiar OPcache + Ctrl+F5 en el navegador antes de debuggear.

### Ver errores PHP del servidor
```bash
ssh murguia "tail -n 50 ~/public_html/wp-content/debug.log"
ssh murguia "tail -n 50 ~/logs/error.log"
```

### Commit y push
```bash
git add wp-content/themes/woodmart-child/
git commit -m "descripción del cambio"
git push origin main
```

> No hacer commits a medias — solo cuando el cambio está probado y funcionando.

---

## 8. Reglas importantes

1. **NUNCA editar directamente en el servidor** — local → git → scp siempre
2. **NUNCA tocar** `wp-content/themes/woodmart/` (tema padre WoodMart)
3. **SIEMPRE validar** PHP con `php -l` antes de subir — un error en `functions.php` tira todo el sitio
4. **SIEMPRE limpiar OPcache** después de cambios PHP
5. **Nunca colores hardcodeados** — usar variables CSS `--murg-*`
6. **Textos y datos en ACF**, nunca hardcodeados en PHP (excepto fallbacks con `murguia_ajuste()`)
7. **curl en PowerShell** siempre con `curl.exe --ssl-no-revoke` (el alias `curl` de PS no funciona igual)
8. **Mensajes de commit largos** guardarlos en archivo temporal — las comillas dobles rompen el parser de PS
9. Leer `CLAUDE.md` en el child theme para convenciones del código — tiene los detalles técnicos completos

---

## 9. Pendientes del proyecto

### Alta prioridad
- [ ] **Página Sobre Nosotros** — crear `page-about.php` + field group ACF con prefijo `ab_`
      Seguir el mismo patrón de `page-contact.php`.
      Registrar en `functions.php` el ACF field group.

### Media prioridad
- [ ] Cargar contenido real del cliente en campos ACF (hero, colecciones, trust bar, etc.)
- [ ] Testing responsive exhaustivo en mobile/tablet

### Opcional / futuro
- [ ] Auto-deploy git → servidor (GitHub Action o hook post-receive)
- [ ] Commit pendiente: `Footer: remover sticky toolbar móvil de WoodMart`
      (el código ya está en `functions.php`, solo falta el commit)

---

## 10. Comandos útiles de diagnóstico

```bash
# Ver productos desde CLI
ssh murguia "cd ~/public_html && wp post list --post_type=product --posts_per_page=5 --allow-root"

# Ver atributos de un producto (reemplazar ID)
ssh murguia "cd ~/public_html && wp wc product get 38275 --allow-root"

# Uso de disco
ssh murguia "du -sh ~/public_html"

# Contar CSS cargados en una URL (PowerShell)
$html = curl.exe -sS --ssl-no-revoke "https://murguia.jamweb.space/tienda/"
([regex]::Matches($html, '<link[^>]+stylesheet')).Count

# Verificar sintaxis PHP en servidor
ssh murguia "php -l ~/public_html/wp-content/themes/woodmart-child/functions.php"
```

---

## 11. Estructura de archivos del child theme

```
woodmart-child/
├── CLAUDE.md                     ← Convenciones del proyecto (leer primero)
├── HANDOFF.md                    ← Este archivo
├── style.css                     ← CSS principal (~2000 líneas)
├── functions.php                 ← Core: enqueue, CPTs, ACF, dequeue, form handler
├── front-page.php                ← Home
├── archive-product.php           ← Tienda / Shop
├── single-product.php            ← Producto individual
├── page-contact.php              ← Contacto (Template Name: Contacto)
├── woocommerce/
│   └── archive-product.php      ← Include redirect al principal
├── template-parts/
│   ├── murg-nav.php              ← Navegación (usada en todos los templates)
│   └── murg-footer.php          ← Footer (usado en todos los templates)
└── assets/
    └── js/
        └── murguia.js            ← JS vanilla: nav scroll, galería, filtros, sliders
```

---

*Última actualización: mayo 2026 — commit `a6f5336`*
