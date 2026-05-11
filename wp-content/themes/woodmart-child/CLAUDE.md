# Proyecto: Joyería Murguía
URL referencia: https://joyeriamurguia.com

## Stack
- WordPress 6.x + WooCommerce
- WoodMart child theme
- Secure Custom Fields (SCF) — fork gratuito de ACF 6.x, API completamente compatible
- PHP 8.x, CSS custom, JS vanilla
- Sin page builders — todo PHP + SCF/ACF custom fields

## Estilo
- Minimalista, elegante, dark/luxury
- Design tokens: --gold: #C9A84C | --cream: #F5F0E8 | --black: #0A0A0A | --murg-forest: #192e1d | --murg-parchment: #f4eee5
- **Tipografía CORRECTA (fuente Figma):**
  - Headings: `Copperplate` (archivo local `assets/fonts/Copperplate.otf`)
  - Body / UI / botones / labels: `Vesper Libre` (Google Fonts) ← CRÍTICO, NO Inter
  - Precios: `Tiro Bangla` (Google Fonts)
  - Inter y Cormorant Garamond NO se usan — eliminar si aparecen

## Convenciones de código
- CPTs con prefijo: murguia_
- ACF field groups por CPT
- BEM para clases CSS con prefijo .murg-
- Variables CSS en :root, nunca valores hardcodeados
- WooCommerce: overrides en /woocommerce/
- Hooks sobre template overrides cuando sea posible

---

## Accesos y Credenciales

> ⚠️ Este archivo es LOCAL y no se sube a git. Tratar con confidencialidad.

### Figma — ARCHIVO ACCESIBLE (usar este)
- **Archivo (copia accesible):** https://www.figma.com/design/R8OtgybAGkJwTQEcXI0sba/Joyeria-Murquia--Copy-
- **File key:** `R8OtgybAGkJwTQEcXI0sba`
- **Propietario:** `javier20185984@gmail.com` (cuenta del usuario)
- **Personal Access Token:** `REDACTED — guardado localmente fuera del repo`

### Figma — Archivo original (SIN acceso directo)
- **File key:** `89FSfpdwWjfuAkxD0xqFZH`
- **Propietario:** `soporte@peadlnag.edu.pe` (cuenta ajena, no accesible)
- No usar este file key con la REST API — dará 403

### Figma MCP (CONFIGURADO y FUNCIONAL)
- **Servidor:** MCP remoto oficial de Figma `https://mcp.figma.com/mcp`
- **Transporte:** HTTP (OAuth)
- **Autenticado como:** `javier20185984@gmail.com` con plan Full seat
- **Estado:** ACTIVO — ya completó OAuth en sesión anterior
- **Herramienta:** `mcp__Figma__get_design_context`, `mcp__Figma__get_screenshot`, `mcp__Figma__get_metadata`
- **SIEMPRE usar MCP en lugar de REST API** — el REST API tiene límite de 6 llamadas/MES en plan gratuito (ya agotado en sesión anterior)
- **Cómo activar si no responde:** El usuario debe abrir Figma Desktop con el archivo abierto

### REST API Figma — RATE LIMIT CRÍTICO
- El endpoint `/v1/files/{key}` tiene límite de **6 llamadas por MES** en el plan gratuito/colaborador
- Las 6 llamadas del mes actual ya fueron consumidas en sesiones anteriores
- **NO usar REST API para extraer datos de diseño** — usar el Figma MCP
- Si por alguna razón se necesita REST, usar vía SSH al servidor de producción:
```bash
ssh -i "C:/Users/USUARIO/.ssh/claude-murguia" -o StrictHostKeyChecking=no murguiajamweb@murguia.jamweb.space \
  "curl -s -H 'X-Figma-Token: REDACTED_FIGMA_TOKEN' \
  'https://api.figma.com/v1/files/R8OtgybAGkJwTQEcXI0sba?depth=2'"
```

### WordPress / Local by Flywheel
- **URL local:** http://murgia.local
- **Panel admin:** http://murgia.local/wp-admin
- **Ruta Local Sites:** `C:\Users\USUARIO\Local Sites\murgia\app\public\wp-content\themes\woodmart-child\`
- **Ruta de trabajo (edición):** `C:\Users\USUARIO\murguia\wp-content\themes\woodmart-child\`

### SSH — Servidor de Producción (VERIFICADO y FUNCIONAL)
- **Host:** `murguia.jamweb.space`
- **Puerto:** 22
- **Usuario:** `murguiajamweb` ← (NO admin_4swcs8ur, ese es solo cPanel)
- **Llave privada:** `C:\Users\USUARIO\.ssh\claude-murguia`
- **Llave pública:** `ssh-ed25519 AAAAC3NzaC1lZDI1NTE5AAAAIFtimzjAq5zQ2tLvT1UhM9+OyyQWMyShVvwGN3uswwE8 claude-murguia`
- **WordPress producción:** `/home/murguiajamweb/public_html/`
- **Theme producción:** `/home/murguiajamweb/public_html/wp-content/themes/woodmart-child/`
- **PHP versión:** 8.1.34
- **cPanel:** https://murguia.jamweb.space:2083 (user: admin_4swcs8ur — solo para panel web)

### Comando SSH base
```bash
ssh -i "C:/Users/USUARIO/.ssh/claude-murguia" -o StrictHostKeyChecking=no murguiajamweb@murguia.jamweb.space "COMANDO"
```

### Protocolo de sincronización (Local → Local Sites)
Después de editar cualquier archivo en la ruta de trabajo, sincronizar a Local Sites con PowerShell:
```powershell
$src = "C:\Users\USUARIO\murguia\wp-content\themes\woodmart-child"
$dst = "C:\Users\USUARIO\Local Sites\murgia\app\public\wp-content\themes\woodmart-child"
Copy-Item "$src\style.css"            "$dst\style.css"            -Force
Copy-Item "$src\front-page.php"       "$dst\front-page.php"       -Force
Copy-Item "$src\functions.php"        "$dst\functions.php"        -Force
Copy-Item "$src\assets\js\murguia.js" "$dst\assets\js\murguia.js" -Force
```
Agregar/quitar archivos según lo que se haya editado en esa sesión.

**NUNCA hacer git push automático.** Solo cuando el usuario diga explícitamente "push", "sube" o "despliega".

### Flujo completo de trabajo
```
1. Editar en:  C:\Users\USUARIO\murguia\wp-content\themes\woodmart-child\
       ↓ (automático después de cada cambio)
2. Sincronizar a Local Sites con PowerShell → revisar en http://murgia.local/
       ↓ (solo con orden explícita: "push" / "sube" / "despliega")
3. git add + git commit + git push al repo remoto
       ↓ (el servidor hace pull desde git automáticamente)
4. Producción actualizada en https://joyeriamurguia.com
```

### Comando de deploy a producción (cuando el usuario lo pida)
```bash
# Desde la ruta de trabajo local:
git add -A
git commit -m "descripción del cambio"
git push

# El servidor de producción tiene configurado git pull automático (webhook/hook)
# No se necesita SSH manual para deploy — solo para diagnóstico
```

---

## CPTs del proyecto
- murguia_ajustes — Ajustes de Diseño (menú padre admin)
- murguia_coleccion — Colecciones
- murguia_pieza — Piezas destacadas
- Todos los CPTs aparecen como submenús bajo WoodMart en el admin

## Sistema de Ajustes de Diseño

### Arquitectura
- CPT padre: murguia_ajustes (Ajustes de Diseño)
- Cada página del theme tiene su propio item dentro de ese CPT
- Cada item tiene un field group ACF con todos los campos editables de esa página
- Los PHP nunca tienen textos ni imágenes hardcodeadas — todo viene de ACF

### Helper functions (en functions.php)
```php
murguia_ajuste_id('slug')    // devuelve el post ID del ajuste por slug
murguia_ajuste('key', $fallback) // devuelve el valor ACF del ajuste activo
```

### Páginas registradas
- [x] Página de Inicio — front-page.php · slug: pagina-de-inicio · field prefix: hp_
- [x] Tienda — archive-product.php · slug: tienda · field prefix: sh_
- [x] Producto — single-product.php · slug: producto · field prefix: prod_
- [x] Contacto — page-contact.php (Template Name: Contacto) · slug: contacto · field prefix: ct_
- [ ] Sobre Nosotros — page-about.php · slug: nosotros · field prefix: ab_ ← PENDIENTE

### Protocolo cuando se actualiza un diseño
Cuando el usuario suba un nuevo zip de Claude Design para una página:
1. Leer el nuevo HTML/CSS
2. Comparar con el PHP existente
3. Actualizar el PHP con los cambios visuales
4. Revisar si hay secciones nuevas o eliminadas
5. Sincronizar el field group ACF correspondiente — agregar campos nuevos, eliminar obsoletos
6. Actualizar el checklist de Páginas registradas en este CLAUDE.md

### Convención de field groups
- Nombre: [Página] — [Área]
- Location rule: `post == murguia_ajuste_id('slug')` (específico por post, no por post_type)
- Prefijo de fields por página: hp_ (inicio), sh_ (tienda), prod_ (producto), ct_ (contacto), ab_ (about)
- Ejemplos: hp_hero_titulo, sh_por_pagina, prod_badge_nuevo, ct_direccion

### Nota sobre SCF vs ACF Pro
- `relationship` no existe en SCF — usar `post_object` con `multiple:1` en su lugar
- `acf_add_local_field_group()` funciona igual en SCF
- Options Pages (ACF Pro) no disponible — usar CPT murguia_ajustes como sustituto

### Secciones dinámicas con productos WooCommerce
Las secciones que muestran productos NO tienen contenido hardcodeado ni en ACF.
Los productos, precios e imágenes vienen siempre de WooCommerce.

Lo que SÍ se controla desde ACF en estas secciones:
- Título y subtítulo de la sección
- Cantidad de productos a mostrar (número)
- Categoría o tag de WC a consultar (taxonomy field)
- Criterio de ordenamiento: más vendidos / recientes / destacados / manual (select field)
- Texto y link del botón CTA
- Layout de la grilla: columnas en desktop/tablet/mobile (select field)

Para queries de productos usar siempre WC_Product_Query o get_posts
con post_type=product — nunca WP_Query directo.
Respetar stock, visibilidad y estado published de WooCommerce en cada query.

---

## Tipografía

### Fuentes — MAPA DEFINITIVO EXTRAÍDO DE FIGMA

**IMPORTANTE:** El CSS actual puede tener `Inter` y `Cormorant Garamond` — ambas deben ser reemplazadas.
Las fuentes correctas son:

| Fuente | Uso en diseño | Cómo cargar |
|--------|--------------|-------------|
| `Copperplate` | Headings, marcas, títulos de sección, botones uppercase de marca | Archivo local: `assets/fonts/Copperplate.otf` |
| `Vesper Libre` | Body, párrafos, botones CTA, tabs, subtítulos, footer | Google Fonts: `family=Vesper+Libre:wght@400;700` |
| `Tiro Bangla` | Precios de productos | Google Fonts: `family=Tiro+Bangla` |

### @font-face Copperplate (en style.css)
```css
@font-face {
  font-family: 'Copperplate';
  src: url('assets/fonts/Copperplate.otf') format('opentype');
  font-weight: 100 900;   /* rango completo — crítico para evitar fallback */
  font-style: normal;
  font-display: swap;
}
```
**IMPORTANTE:** El rango `font-weight: 100 900` es obligatorio. Si se limita a un peso (ej. 400), el browser cae a fallback cuando un elemento pide `font-weight: 300`.

### Tipografía completa — Figma a CSS (canvas 1728px)

| Elemento | Fuente Figma | Tamaño Figma | Color | Notas |
|----------|-------------|--------------|-------|-------|
| Topbar banner | Copperplate:Light | 20px | white | uppercase, tracking normal |
| Nav items | Copperplate:Light | 20px | white | uppercase |
| ES/EN toggle | Copperplate:Light | 22px | white | uppercase |
| Hero title | Copperplate:Light | 27px | white | uppercase |
| Hero CTA "Descubrir Colección" | Vesper Libre Regular | 20px | white | letter-spacing: 3.2px, uppercase |
| "Anillos de compromiso" section title | Copperplate:Light | 36px | #192e1d | uppercase |
| "Novios" section title | Copperplate:Light | 36px | #192e1d | uppercase |
| Diamond filter (Oval, Pear…) | Vesper Libre Regular | 20px | #192e1d | — |
| "forjada a mano" quote | Vesper Libre Regular | 24px | #a7a7a7 | line-height: 40px |
| "Ver Colecciónes" button | Vesper Libre Regular | 20px | white | letter-spacing: 3.2px, uppercase |
| Novios subtitle párrafo | Vesper Libre Regular | 24px | #474747 | line-height: 40px |
| Category tabs (Anillos/Aretes…) | Vesper Libre Regular | 24px | black | — |
| Product names | Vesper Libre Bold | 24px | black | font-weight: 700 |
| Product prices | Tiro Bangla Regular | 29px | black | — |
| "Ver tienda completa" / "Ver Colección" | Vesper Libre Regular | 20px | white | letter-spacing: 3.2px, uppercase |
| "Piezas que Destacan" title | Copperplate:Light | 36px | #192e1d | uppercase |
| Featured piece title | Vesper Libre Regular | 35px | black | — |
| Featured subtitle (COLLAR EN ORO…) | Vesper Libre Regular | 20px | black | — |
| Statement quote (fondo oscuro) | Vesper Libre Regular | 24px | white | line-height: 40px |
| "CASA MURGUÍA, FUNDADA EN 1910" | Vesper Libre Regular | 21px | #949494 | letter-spacing: 3.99px |
| "Colección QANTU" title | Copperplate:Light | 50px | #192e1d | uppercase, centrado |
| "Donde florece el tiempo" | Vesper Libre Regular | 31px | #a4a4a4 | line-height: 40px, centrado |
| "Agenda tu visita" title | Copperplate:Light | 36px | #192e1d | uppercase |
| Visit info body text | Vesper Libre Regular | 19px | #5c5a5a | line-height: 40px |
| "Reservar cita" / "WHATSAPP" buttons | Vesper Libre Regular | 16px | white | letter-spacing: 2.56px, uppercase |
| "-10% en tu primera compra" | Copperplate:Light | 36px | white | uppercase |
| Newsletter input/subscribe | Vesper Libre Regular | 19px | rgba(181,181,181,0.66) | letter-spacing: 2.09px |
| Footer section headings | Tiro Bangla / Vesper Libre | 19px | #192e1d | line-height: 40px |
| Footer body / links | Vesper Libre Regular | 19px | #5c5a5a | line-height: 40px |

### Conversión Figma px → CSS responsive (canvas 1728px)
```
vw_value = figma_px / 1728 * 100
css = clamp(min_mobile, {vw_value}vw, {figma_px}px)
```

| Figma px | vw equiv | Clamp sugerido |
|----------|----------|----------------|
| 14px | 0.81vw | clamp(11px, 0.81vw, 14px) |
| 16px | 0.93vw | clamp(12px, 0.93vw, 16px) |
| 19px | 1.10vw | clamp(13px, 1.10vw, 19px) |
| 20px | 1.16vw | clamp(14px, 1.16vw, 20px) |
| 21px | 1.22vw | clamp(14px, 1.22vw, 21px) |
| 22px | 1.27vw | clamp(14px, 1.27vw, 22px) |
| 24px | 1.39vw | clamp(16px, 1.39vw, 24px) |
| 27px | 1.56vw | clamp(16px, 1.56vw, 27px) |
| 29px | 1.68vw | clamp(17px, 1.68vw, 29px) |
| 31px | 1.79vw | clamp(18px, 1.79vw, 31px) |
| 35px | 2.03vw | clamp(20px, 2.03vw, 35px) |
| 36px | 2.08vw | clamp(20px, 2.08vw, 36px) |
| 50px | 2.89vw | clamp(28px, 2.89vw, 50px) |

NO escalar: letter-spacing (ya es relativo), line-height (usar ratio), border-radius pequeños.

---

## Especificaciones de Imágenes y Media

### Reglas generales
- Formato preferido: WebP | Fallback: JPG para fotos, PNG para transparencias
- SVG obligatorio para logos e íconos
- Nunca: BMP, TIFF
- Comprimir siempre antes de subir (Squoosh o TinyPNG)
- loading="lazy" en todo excepto imágenes above-the-fold (loading="eager")
- wp_get_attachment_image() con srcset siempre
- ACF return format: array en todos los campos de imagen

### Imágenes fallback
Las imágenes de fallback (usadas cuando ACF no tiene contenido) se alojan en WordPress Uploads:
```php
$img_upload = content_url('uploads/2026/05/');
// Ejemplo: $img_upload . 'hero-1.jpg'
```

### Breakpoints del theme
- Mobile: hasta 767px
- Tablet: 768px — 1024px
- Desktop: 1025px — 1440px
- Wide: 1441px+

---

## Hero Slider — Arquitectura actual

### Estructura HTML (front-page.php)
El hero tiene separación entre slides (fondo) y contenido (overlay):
```
<section.murg-hero #murg-hero-slider>
  <!-- Slides: solo fondo, datos en data-* -->
  <div.murg-hero__slide [data-titulo] [data-cta-texto] [data-cta-url] [data-intervalo]>
    <div.murg-hero__bg>   ← imagen o video
    <div.murg-hero__vignette>
  </div>
  ...más slides...

  <!-- Overlay de contenido: hijo directo de section, z-index 10 -->
  <div.murg-hero__content>
    <h1.murg-hero__title>
    <div.murg-hero__dots-inline>  ← dots decorativos bajo el título
      <span.murg-hero__dot-circle>
    </div>
    <a.murg-hero__cta>
    <span.murg-hero__cta-line>
  </div>

  <!-- Nav dots (si >1 slide) -->
  <div.murg-hero__nav-dots>
    <button.murg-hero__nav-dot [data-index]>
  </div>
  <div.murg-hero__progress>
    <div.murg-hero__progress-bar>
  </div>
</section>
```

### JS Slider (murguia.js)
- El overlay `.murg-hero__content` se actualiza en cada cambio de slide leyendo los `data-*` del slide entrante
- `.murg-hero__nav-dot` = botones de navegación (bottom bar)
- `.murg-hero__dot-circle` = dots decorativos inline bajo el título (se activan con clase `is-active`)
- Soporta: autoplay, pause on hover, swipe touch, video (YouTube iframe + mp4)
- La función `hsGoTo(idx)` actualiza: `.murg-hero__title`, `.murg-hero__cta` texto y href, dots activos

### ACF fields del Hero (prefix: hp_)
- `hp_hero_slides` — repeater con: titulo, imagen, video_url, video_type, cta_texto, cta_link, intervalo_ms
- `hp_hero_titulo` — título global (fallback si slide no tiene)
- `hp_hero_cta_txt` / `hp_hero_cta_url` — CTA global (fallback)

---

## Archivos completados
- [x] style.css
- [x] functions.php
- [x] front-page.php
- [x] archive-product.php
- [x] single-product.php
- [x] page-contact.php
- [x] template-parts/murg-nav.php
- [x] template-parts/murg-footer.php
- [x] assets/js/murguia.js
- [x] assets/fonts/Copperplate.otf
- [x] CLAUDE.md

---

## PENDIENTES — Próxima sesión

### ALTA PRIORIDAD: Corrección tipográfica
1. **Eliminar `Inter` y `Cormorant Garamond`** de `functions.php` (Google Fonts enqueue) y `style.css`
2. **Agregar `Vesper Libre` y `Tiro Bangla`** a Google Fonts en `functions.php`:
   ```php
   // Reemplazar la línea de Inter/Cormorant con:
   wp_enqueue_style(
     'murguia-google-fonts',
     'https://fonts.googleapis.com/css2?family=Vesper+Libre:wght@400;700&family=Tiro+Bangla&display=swap',
     [], null
   );
   ```
3. **Actualizar variables CSS** en `style.css`:
   ```css
   :root {
     --font-heading: 'Copperplate', 'Copperplate Gothic Light', serif;
     --font-body:    'Vesper Libre', Georgia, serif;
     --font-price:   'Tiro Bangla', serif;
   }
   ```
4. **Reemplazar todas las referencias** a `font-family: 'Inter'` → `var(--font-body)` en `style.css`
5. **Reemplazar** `font-family: 'Cormorant Garamond'` → `var(--font-body)` en `style.css`
6. **Aplicar tamaños** de la tabla de tipografía a cada sección (ver tabla arriba)

### MEDIA PRIORIDAD
- [ ] Crear `page-about.php` — slug: nosotros · field prefix: ab_
- [ ] Verificar en browser local que todos los slides del hero funcionan con la nueva arquitectura JS

### BAJA PRIORIDAD
- [ ] Revisar alineación de footer con Figma
- [ ] Optimizar imágenes de fallback (convertir a WebP)
