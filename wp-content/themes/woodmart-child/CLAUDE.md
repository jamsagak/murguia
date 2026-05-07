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
- Tipografía: Cormorant Garamond (headings) + Inter (body/UI)
- Design tokens: --gold: #C9A84C | --cream: #F5F0E8 | --black: #0A0A0A
- Sin page builders — todo PHP + ACF custom fields

## Convenciones de código
- CPTs con prefijo: murguia_
- ACF field groups por CPT
- BEM para clases CSS con prefijo .murg-
- Variables CSS en :root, nunca valores hardcodeados
- WooCommerce: overrides en /woocommerce/
- Hooks sobre template overrides cuando sea posible

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

### Páginas registradas
- [x] Página de Inicio — front-page.php · slug: pagina-de-inicio · field prefix: hp_
- [x] Tienda — archive-product.php · slug: tienda · field prefix: sh_
- [x] Producto — single-product.php · slug: producto · field prefix: prod_
- [x] Contacto — page-contact.php (Template Name: Contacto) · slug: contacto · field prefix: ct_
- [ ] Sobre Nosotros — page-about.php · slug: nosotros · field prefix: ab_

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

## Especificaciones de Imágenes y Media

### Reglas generales
- Formato preferido: WebP | Fallback: JPG para fotos, PNG para transparencias
- SVG obligatorio para logos e íconos
- Nunca: BMP, TIFF
- Comprimir siempre antes de subir (Squoosh o TinyPNG)
- loading="lazy" en todo excepto imágenes above-the-fold (loading="eager")
- wp_get_attachment_image() con srcset siempre
- ACF return format: array en todos los campos de imagen

### Breakpoints del theme
- Mobile: hasta 767px
- Tablet: 768px — 1024px
- Desktop: 1025px — 1440px
- Wide: 1441px+

### Protocolo para definir tamaños
Cada vez que se crea o actualiza un field group ACF con campos de imagen:
1. Analizar el PHP y CSS de esa sección para determinar el tamaño real renderizado
2. Calcular el tamaño óptimo según el breakpoint más grande donde aparece
3. Definir si necesita versión mobile separada (cuando el recorte cambia significativamente)
4. Escribir en el campo Instructions de ACF:
   - Formato recomendado
   - Dimensiones exactas deducidas del diseño
   - Peso máximo recomendado (referencia: 1px = ~0.5-1byte en WebP comprimido)
   - Si requiere versión mobile: indicarlo con nombre del campo _mobile

### Imágenes responsive
- Si una imagen cambia de ratio entre mobile y desktop: crear campo _mobile adicional
- Si solo escala proporcionalmente: un solo campo es suficiente, srcset lo maneja

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
- [x] CLAUDE.md
