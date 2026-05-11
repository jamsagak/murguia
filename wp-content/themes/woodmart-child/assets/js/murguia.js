( function () {
	'use strict';

	/* ------------------------------------------------------------------
	   NAV — transparent on hero, solid once scrolled past 60px
	   ------------------------------------------------------------------ */
	var nav  = document.getElementById( 'murg-nav' );
	var hero = document.querySelector( '.murg-hero' );

	if ( nav ) {
		if ( hero ) {
			var onScroll = function () {
				if ( window.scrollY > 60 ) {
					nav.classList.add( 'is-scrolled' );
				} else {
					nav.classList.remove( 'is-scrolled' );
				}
			};
			window.addEventListener( 'scroll', onScroll, { passive: true } );
			onScroll();
		} else {
			nav.classList.add( 'is-scrolled' );
		}
	}

	/* ------------------------------------------------------------------
	   CONTACT FORM — show success/error message from query param
	   ------------------------------------------------------------------ */
	var params = new URLSearchParams( window.location.search );
	var cita   = params.get( 'cita' );
	var form   = document.querySelector( '.murg-form' );

	if ( form && cita ) {
		var msg = document.createElement( 'p' );
		msg.style.cssText = 'font-size:12px;letter-spacing:.1em;margin-top:16px;';

		if ( cita === 'ok' ) {
			msg.style.color  = '#B89740';
			msg.textContent  = 'Solicitud enviada — le contactaremos pronto.';
		} else {
			msg.style.color  = '#a05050';
			msg.textContent  = 'Ha ocurrido un error. Por favor intente de nuevo.';
		}

		form.appendChild( msg );

		// Clean the URL so a refresh doesn't reshow the message
		var clean = window.location.pathname + window.location.hash;
		window.history.replaceState( {}, '', clean );
	}

	/* ------------------------------------------------------------------
	   SMOOTH SCROLL — internal anchor links
	   ------------------------------------------------------------------ */
	document.querySelectorAll( 'a[href^="#"]' ).forEach( function ( anchor ) {
		anchor.addEventListener( 'click', function ( e ) {
			var id     = this.getAttribute( 'href' ).slice( 1 );
			var target = id ? document.getElementById( id ) : null;

			if ( target ) {
				e.preventDefault();
				var offset = nav ? nav.offsetHeight + 16 : 0;
				var top    = target.getBoundingClientRect().top + window.scrollY - offset;
				window.scrollTo( { top: top, behavior: 'smooth' } );
			}
		} );
	} );

	/* ------------------------------------------------------------------
	   HOME PIEZAS — product category tabs
	   ------------------------------------------------------------------ */
	var piezasTabs = Array.prototype.slice.call( document.querySelectorAll( '.murg-piezas__tab' ) );
	var piezasPanels = Array.prototype.slice.call( document.querySelectorAll( '.murg-piezas__panel' ) );

	if ( piezasTabs.length && piezasPanels.length ) {
		piezasTabs.forEach( function ( tab ) {
			tab.addEventListener( 'click', function () {
				var target = tab.dataset.target;

				piezasTabs.forEach( function ( item ) {
					var isActive = item === tab;
					item.classList.toggle( 'is-active', isActive );
					item.setAttribute( 'aria-selected', isActive ? 'true' : 'false' );
				} );

				piezasPanels.forEach( function ( panel ) {
					var isTarget = panel.id === target;
					panel.classList.toggle( 'is-active', isTarget );
					if ( isTarget ) {
						panel.removeAttribute( 'hidden' );
					} else {
						panel.setAttribute( 'hidden', '' );
					}
				} );
			} );
		} );
	}

	/* ------------------------------------------------------------------
	   HOME FEATURED — galería del producto destacado
	   ------------------------------------------------------------------ */
	var featSection = document.getElementById( 'murg-featured-slider' );
	if ( featSection ) {
		var featImgs    = Array.from( featSection.querySelectorAll( '.murg-featured__gimg' ) );
		var featDots    = Array.from( featSection.querySelectorAll( '.murg-featured__dot[data-index]' ) );
		var featCurrent = 0;

		function featGoTo( idx ) {
			if ( ! featImgs.length ) return;
			featImgs[ featCurrent ].classList.remove( 'is-active' );
			if ( featDots[ featCurrent ] ) {
				featDots[ featCurrent ].classList.remove( 'is-active' );
				featDots[ featCurrent ].setAttribute( 'aria-selected', 'false' );
			}
			featCurrent = ( idx + featImgs.length ) % featImgs.length;
			featImgs[ featCurrent ].classList.add( 'is-active' );
			if ( featDots[ featCurrent ] ) {
				featDots[ featCurrent ].classList.add( 'is-active' );
				featDots[ featCurrent ].setAttribute( 'aria-selected', 'true' );
			}
		}

		featDots.forEach( function ( dot ) {
			dot.addEventListener( 'click', function () {
				featGoTo( parseInt( dot.dataset.index, 10 ) );
			} );
		} );

		// Swipe táctil
		if ( featImgs.length > 1 ) {
			var fsStartX = 0;
			featSection.addEventListener( 'touchstart', function ( e ) {
				fsStartX = e.touches[ 0 ].clientX;
			}, { passive: true } );
			featSection.addEventListener( 'touchend', function ( e ) {
				var diff = fsStartX - e.changedTouches[ 0 ].clientX;
				if ( Math.abs( diff ) > 40 ) featGoTo( featCurrent + ( diff > 0 ? 1 : -1 ) );
			} );
		}
	}

	/* ------------------------------------------------------------------
	   PRODUCT GALLERY — thumbnail switcher on single product
	   ------------------------------------------------------------------ */
	var pdMainImg = document.getElementById( 'murg-pdg-main-img' );
	var pdThumbs  = Array.prototype.slice.call(
		document.querySelectorAll( '.murg-pdgallery__thumb' )
	);

	function pdSetActive( index ) {
		if ( ! pdThumbs[ index ] ) return;
		pdThumbs.forEach( function ( t ) { t.classList.remove( 'is-active' ); } );
		pdThumbs[ index ].classList.add( 'is-active' );
		if ( pdMainImg ) {
			pdMainImg.style.opacity = '0';
			var nextSrc = pdThumbs[ index ].dataset.full;
			setTimeout( function () {
				pdMainImg.src = nextSrc;
				pdMainImg.removeAttribute( 'srcset' );
				pdMainImg.removeAttribute( 'sizes' );
				pdMainImg.style.opacity = '1';
			}, 220 );
		}
	}

	if ( pdMainImg && pdThumbs.length ) {
		pdThumbs.forEach( function ( thumb, i ) {
			thumb.addEventListener( 'click', function () { pdSetActive( i ); } );
		} );
	}

	/* ------------------------------------------------------------------
	   PRODUCT LIGHTBOX
	   ------------------------------------------------------------------ */
	var lightbox = document.getElementById( 'murg-lightbox' );
	if ( lightbox ) {
		var lbImg     = document.getElementById( 'murg-lightbox-img' );
		var lbCaption = document.getElementById( 'murg-lightbox-caption' );
		var lbClose   = document.getElementById( 'murg-lightbox-close' );
		var lbPrev    = document.getElementById( 'murg-lightbox-prev' );
		var lbNext    = document.getElementById( 'murg-lightbox-next' );
		var lbDataEl  = document.getElementById( 'murg-lightbox-data' );
		var lbZoomBtn = document.getElementById( 'murg-pdg-zoom' );
		var lbMain    = document.querySelector( '.murg-pdgallery__main' );

		var lbImages = [];
		try { lbImages = JSON.parse( lbDataEl.textContent || '[]' ); } catch ( e ) { lbImages = []; }
		var lbIdx = 0;

		function lbRender() {
			if ( ! lbImages[ lbIdx ] ) return;
			lbImg.style.opacity = '0';
			var src = lbImages[ lbIdx ].src;
			var img = new Image();
			img.onload = function () {
				lbImg.src = src;
				lbImg.style.opacity = '1';
			};
			img.src = src;
			if ( lbCaption ) {
				lbCaption.textContent = ( lbIdx + 1 ) + ' / ' + lbImages.length;
			}
		}

		function lbOpen( fromIndex ) {
			if ( ! lbImages.length ) return;
			lbIdx = typeof fromIndex === 'number' ? fromIndex : 0;
			lightbox.classList.add( 'is-open' );
			lightbox.setAttribute( 'aria-hidden', 'false' );
			document.body.style.overflow = 'hidden';
			lbRender();
		}
		function lbCloseFn() {
			lightbox.classList.remove( 'is-open' );
			lightbox.setAttribute( 'aria-hidden', 'true' );
			document.body.style.overflow = '';
		}
		function lbStep( delta ) {
			if ( ! lbImages.length ) return;
			lbIdx = ( lbIdx + delta + lbImages.length ) % lbImages.length;
			lbRender();
		}

		if ( lbClose ) lbClose.addEventListener( 'click', lbCloseFn );
		if ( lbPrev )  lbPrev .addEventListener( 'click', function () { lbStep( -1 ); } );
		if ( lbNext )  lbNext .addEventListener( 'click', function () { lbStep(  1 ); } );

		lightbox.addEventListener( 'click', function ( e ) {
			if ( e.target === lightbox ) { lbCloseFn(); }
		} );

		document.addEventListener( 'keydown', function ( e ) {
			if ( ! lightbox.classList.contains( 'is-open' ) ) return;
			if ( e.key === 'Escape' )      lbCloseFn();
			if ( e.key === 'ArrowLeft' )   lbStep( -1 );
			if ( e.key === 'ArrowRight' )  lbStep(  1 );
		} );

		// Open from main image click or zoom button — sync index to active thumb
		function lbOpenFromMain() {
			var activeThumb = document.querySelector( '.murg-pdgallery__thumb.is-active' );
			var idx = activeThumb ? parseInt( activeThumb.dataset.index, 10 ) : 0;
			if ( isNaN( idx ) ) idx = 0;
			lbOpen( idx );
		}
		if ( lbMain )    lbMain   .addEventListener( 'click', lbOpenFromMain );
		if ( lbZoomBtn ) lbZoomBtn.addEventListener( 'click', function ( e ) {
			e.stopPropagation();
			lbOpenFromMain();
		} );
	}

	/* ------------------------------------------------------------------
	   PRODUCT TABS — Descripción / Detalles / Cuidado
	   ------------------------------------------------------------------ */
	var ptabTriggers = Array.prototype.slice.call( document.querySelectorAll( '.murg-ptabs__trigger' ) );
	var ptabPanels   = Array.prototype.slice.call( document.querySelectorAll( '.murg-ptabs__panel' ) );

	if ( ptabTriggers.length && ptabPanels.length ) {
		ptabTriggers.forEach( function ( trg ) {
			trg.addEventListener( 'click', function () {
				var target = trg.dataset.target;
				ptabTriggers.forEach( function ( t ) {
					t.classList.toggle( 'is-active', t === trg );
					t.setAttribute( 'aria-selected', t === trg ? 'true' : 'false' );
				} );
				ptabPanels.forEach( function ( p ) {
					var isTarget = p.id === 'murg-ptab-' + target;
					p.classList.toggle( 'is-active', isTarget );
					if ( isTarget ) { p.removeAttribute( 'hidden' ); }
					else            { p.setAttribute( 'hidden', '' ); }
				} );
			} );
		} );
	}

	/* ------------------------------------------------------------------
	   SIZE GUIDE MODAL — abrir/cerrar con botón, backdrop y ESC
	   ------------------------------------------------------------------ */
	var sgBtns    = Array.prototype.slice.call( document.querySelectorAll( '[data-target="murg-sizeguide"]' ) );
	var sgModal   = document.getElementById( 'murg-sizeguide' );
	var sgClosers = sgModal ? Array.prototype.slice.call( sgModal.querySelectorAll( '[data-close="murg-sizeguide"]' ) ) : [];
	var sgLastFocus = null;

	function sgOpen() {
		if ( ! sgModal ) return;
		sgLastFocus = document.activeElement;
		sgModal.classList.add( 'is-open' );
		sgModal.setAttribute( 'aria-hidden', 'false' );
		document.body.style.overflow = 'hidden';
		// Mover foco al botón de cerrar
		var close = sgModal.querySelector( '.murg-sizeguide__close' );
		if ( close ) { close.focus(); }
	}
	function sgClose() {
		if ( ! sgModal ) return;
		sgModal.classList.remove( 'is-open' );
		sgModal.setAttribute( 'aria-hidden', 'true' );
		document.body.style.overflow = '';
		if ( sgLastFocus && typeof sgLastFocus.focus === 'function' ) {
			sgLastFocus.focus();
		}
	}

	if ( sgModal && sgBtns.length ) {
		sgBtns.forEach( function ( b ) {
			b.addEventListener( 'click', sgOpen );
		} );
		sgClosers.forEach( function ( c ) {
			c.addEventListener( 'click', sgClose );
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && sgModal.classList.contains( 'is-open' ) ) {
				sgClose();
			}
		} );
	}

	/* ------------------------------------------------------------------
	   RELATED PRODUCTS SLIDER — translateX track en single-product
	   ------------------------------------------------------------------ */
	var relTrack = document.getElementById( 'murg-rel-track' );
	var relPrev  = document.getElementById( 'murg-rel-prev' );
	var relNext  = document.getElementById( 'murg-rel-next' );

	if ( relTrack && relPrev && relNext ) {
		var relItems   = relTrack.querySelectorAll( '.murg-related__item' );
		var relTotal   = relItems.length;
		var relCurrent = 0;

		function relPerView() {
			if ( window.innerWidth <= 480 )  return 1;
			if ( window.innerWidth <= 1024 ) return 2;
			return 3;
		}
		function relMaxIdx() { return Math.max( 0, relTotal - relPerView() ); }

		function relUpdate() {
			var perView = relPerView();
			if ( relCurrent > relMaxIdx() ) relCurrent = relMaxIdx();
			// percentage-based slide — each item occupies (100/perView)%
			var shift = -( 100 / perView ) * relCurrent;
			relTrack.style.transform = 'translateX(' + shift + '%)';
			relPrev.disabled = relCurrent <= 0;
			relNext.disabled = relCurrent >= relMaxIdx();
		}

		relPrev.addEventListener( 'click', function () { if ( relCurrent > 0 )          { relCurrent--; relUpdate(); } } );
		relNext.addEventListener( 'click', function () { if ( relCurrent < relMaxIdx() ) { relCurrent++; relUpdate(); } } );

		window.addEventListener( 'resize', function () {
			// keep current within bounds on resize
			relUpdate();
		} );

		relUpdate();
	}

	/* ------------------------------------------------------------------
	   SHOP FILTERS — sidebar toggle, price slider, URL-based filtering
	   ------------------------------------------------------------------ */
	var sidebar      = document.getElementById( 'murg-sidebar' );
	var filterToggle = document.getElementById( 'murg-filter-toggle' );
	var sidebarClose = document.getElementById( 'murg-sidebar-close' );

	if ( sidebar && filterToggle ) {
		var overlay = document.createElement( 'div' );
		overlay.className = 'murg-sidebar-overlay';
		document.body.appendChild( overlay );

		function openSidebar()  { sidebar.classList.add( 'is-open' );  overlay.classList.add( 'is-visible' ); }
		function closeSidebar() { sidebar.classList.remove( 'is-open' ); overlay.classList.remove( 'is-visible' ); }

		filterToggle.addEventListener( 'click', openSidebar );
		if ( sidebarClose ) sidebarClose.addEventListener( 'click', closeSidebar );
		overlay.addEventListener( 'click', closeSidebar );
	}

	// Price slider
	var priceMin   = document.getElementById( 'murg-price-min' );
	var priceMax   = document.getElementById( 'murg-price-max' );
	var priceRange = document.getElementById( 'murg-price-range' );
	var priceMinVal = document.getElementById( 'murg-price-min-val' );
	var priceMaxVal = document.getElementById( 'murg-price-max-val' );
	var priceTimer  = null;

	function updatePriceSlider() {
		if ( ! priceMin || ! priceMax || ! priceRange ) return;
		var minV  = parseInt( priceMin.value, 10 );
		var maxV  = parseInt( priceMax.value, 10 );
		var total = parseInt( priceMin.max, 10 ) - parseInt( priceMin.min, 10 );
		var base  = parseInt( priceMin.min, 10 );

		if ( minV > maxV ) {
			var tmp = minV;
			priceMin.value = maxV;
			priceMax.value = tmp;
			minV = maxV;
			maxV = tmp;
		}

		var leftPct  = ( ( minV - base ) / total ) * 100;
		var rightPct = ( ( maxV - base ) / total ) * 100;
		priceRange.style.left  = leftPct + '%';
		priceRange.style.width = ( rightPct - leftPct ) + '%';

		if ( priceMinVal ) priceMinVal.textContent = 'S/ ' + minV.toLocaleString( 'es-PE' );
		if ( priceMaxVal ) priceMaxVal.textContent = 'S/ ' + maxV.toLocaleString( 'es-PE' );
	}

	function debouncePriceFilter() {
		clearTimeout( priceTimer );
		priceTimer = setTimeout( function () {
			var minV = parseInt( priceMin.value, 10 );
			var maxV = parseInt( priceMax.value, 10 );
			var baseMin = parseInt( priceMin.min, 10 );
			var baseMax = parseInt( priceMin.max, 10 );
			var ps = new URLSearchParams( window.location.search );
			if ( minV > baseMin ) ps.set( 'min', minV ); else ps.delete( 'min' );
			if ( maxV < baseMax ) ps.set( 'max', maxV ); else ps.delete( 'max' );
			ps.delete( 'paged' );
			var base = window.location.pathname;
			window.location.href = base + ( ps.toString() ? '?' + ps.toString() : '' );
		}, 800 );
	}

	if ( priceMin && priceMax ) {
		priceMin.addEventListener( 'input', function () { updatePriceSlider(); debouncePriceFilter(); } );
		priceMax.addEventListener( 'input', function () { updatePriceSlider(); debouncePriceFilter(); } );
		updatePriceSlider();
	}

	// Global filter function
	window.murgApplyFilter = function ( param, value ) {
		var ps = new URLSearchParams( window.location.search );
		if ( value ) {
			ps.set( param, value );
		} else {
			ps.delete( param );
		}
		ps.delete( 'paged' );
		var base = window.location.pathname;
		window.location.href = base + ( ps.toString() ? '?' + ps.toString() : '' );
	};

	/* ------------------------------------------------------------------
	   BESTSELLERS SLIDER — loop infinito · drag · autoplay · teclado
	   Técnica: clona primer y último slide, hace jump silencioso en los
	   extremos para dar sensación de loop continuo sin salto visible.
	   ------------------------------------------------------------------ */
	var bsTrack   = document.getElementById( 'murg-bs-track' );
	var bsPrev    = document.getElementById( 'murg-bs-prev' );
	var bsNext    = document.getElementById( 'murg-bs-next' );
	var bsInfo    = document.getElementById( 'murg-bs-info' );
	var bsSection = document.getElementById( 'bestsellers' );

	if ( bsTrack && bsPrev && bsNext ) {
		var bsRealSlides = Array.from( bsTrack.querySelectorAll( '.murg-products__slide' ) );
		var bsRealCount  = bsRealSlides.length;
		var bsTotal      = parseInt( bsTrack.dataset.total, 10 ) ||
		                   bsTrack.querySelectorAll( '.murg-product' ).length;
		var bsPerSlide   = 3;
		var BS_INTERVAL  = 5000;
		var bsAutoTimer  = null;
		var bsJumping    = false; // true durante el jump silencioso post-loop

		// Solo activar loop si hay más de 1 slide real
		if ( bsRealCount > 1 ) {
			// Clonar último slide al inicio, primero al final
			var bsCloneFirst = bsRealSlides[0].cloneNode( true );
			var bsCloneLast  = bsRealSlides[ bsRealCount - 1 ].cloneNode( true );
			bsCloneFirst.setAttribute( 'aria-hidden', 'true' );
			bsCloneLast.setAttribute(  'aria-hidden', 'true' );
			bsTrack.insertBefore( bsCloneLast,  bsTrack.firstChild );
			bsTrack.appendChild( bsCloneFirst );
		}

		// Índice real: 0..bsRealCount-1 (los clones están en posición 0 y N+1)
		var bsAllSlides = bsTrack.querySelectorAll( '.murg-products__slide' );
		var bsTotal2    = bsAllSlides.length; // real + 2 clones
		// Empezar en índice 1 (primer slide real, los clones son 0 y last)
		var bsPos       = bsRealCount > 1 ? 1 : 0;

		/* ── Posicionar sin transición ───────────────────────────── */
		function bsJumpTo( idx ) {
			bsJumping = true;
			bsTrack.classList.add( 'is-dragging' ); // quita transición CSS
			bsPos = idx;
			bsTrack.style.transform = 'translateX(' + ( -100 * bsPos ) + '%)';
			// requestAnimationFrame doble para asegurar que el browser pintó antes de re-habilitar transición
			requestAnimationFrame( function () {
				requestAnimationFrame( function () {
					bsTrack.classList.remove( 'is-dragging' );
					bsJumping = false;
				} );
			} );
		}

		/* ── Mover con transición ────────────────────────────────── */
		function bsMoveTo( idx ) {
			bsTrack.classList.remove( 'is-dragging' );
			bsPos = idx;
			bsTrack.style.transform = 'translateX(' + ( -100 * bsPos ) + '%)';
			bsUpdateUI();
		}

		/* ── Actualizar botones e info ───────────────────────────── */
		function bsGetPerSlide() {
			// Refleja cuántos productos son visibles según el breakpoint CSS
			if ( window.innerWidth <= 768 )  return 1;
			if ( window.innerWidth <= 1024 ) return 2;
			return 3;
		}

		function bsUpdateUI() {
			var realIdx = bsRealCount > 1 ? bsPos - 1 : bsPos;
			if ( realIdx < 0 )            realIdx = bsRealCount - 1;
			if ( realIdx >= bsRealCount ) realIdx = 0;

			bsPrev.disabled = false;
			bsNext.disabled = false;

			if ( bsInfo && bsTotal ) {
				var perSlide = bsGetPerSlide();
				var from = perSlide * realIdx + 1;
				var to   = Math.min( perSlide * ( realIdx + 1 ), bsTotal );
				bsInfo.textContent = from + '\u2013' + to + ' de ' + bsTotal;
			}
		}

		/* ── Detectar llegada a clon y hacer jump ────────────────── */
		bsTrack.addEventListener( 'transitionend', function () {
			if ( bsJumping ) return;
			if ( bsRealCount <= 1 ) return;
			// Si llegamos al clon del último (pos 0) → saltar al último real
			if ( bsPos === 0 ) {
				bsJumpTo( bsRealCount );
			}
			// Si llegamos al clon del primero (pos bsRealCount+1) → saltar al primero real
			if ( bsPos === bsRealCount + 1 ) {
				bsJumpTo( 1 );
			}
		} );

		/* ── Autoplay ────────────────────────────────────────────── */
		function bsAutoStart() {
			bsAutoStop();
			if ( bsRealCount <= 1 ) return;
			bsAutoTimer = setInterval( function () {
				bsMoveTo( bsPos + 1 );
			}, BS_INTERVAL );
		}

		function bsAutoStop() {
			if ( bsAutoTimer ) { clearInterval( bsAutoTimer ); bsAutoTimer = null; }
		}

		if ( bsSection ) {
			bsSection.addEventListener( 'mouseenter', bsAutoStop );
			bsSection.addEventListener( 'mouseleave', bsAutoStart );
		}

		/* ── Botones ─────────────────────────────────────────────── */
		bsPrev.addEventListener( 'click', function () {
			bsAutoStop();
			bsMoveTo( bsPos - 1 );
			bsAutoStart();
		} );
		bsNext.addEventListener( 'click', function () {
			bsAutoStop();
			bsMoveTo( bsPos + 1 );
			bsAutoStart();
		} );

		/* ── Teclado ─────────────────────────────────────────────── */
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key !== 'ArrowLeft' && e.key !== 'ArrowRight' ) return;
			var rect = bsTrack.getBoundingClientRect();
			if ( rect.top >= window.innerHeight || rect.bottom <= 0 ) return;
			bsAutoStop();
			if ( e.key === 'ArrowLeft' )  bsMoveTo( bsPos - 1 );
			if ( e.key === 'ArrowRight' ) bsMoveTo( bsPos + 1 );
			bsAutoStart();
		} );

		/* ── Drag (mouse + touch) ────────────────────────────────── */
		var bsDragStartX   = 0;
		var bsDragOffsetX  = 0;
		var bsDragging     = false;
		var bsDragMoved    = false;
		var bsSlideW       = 0;
		var DRAG_THRESHOLD  = 8;
		var SWIPE_THRESHOLD = 60;

		// Bloquear drag nativo del navegador en imágenes y links dentro del track
		bsTrack.addEventListener( 'dragstart', function ( e ) {
			e.preventDefault();
		} );

		function bsDragStart( clientX ) {
			if ( bsJumping ) return;
			bsDragging    = true;
			bsDragMoved   = false;
			bsDragStartX  = clientX;
			bsDragOffsetX = 0;
			// Medir el slide real con getBoundingClientRect para incluir padding/box-sizing
			var firstSlide = bsTrack.querySelector( '.murg-products__slide' );
			bsSlideW = firstSlide ? firstSlide.getBoundingClientRect().width : bsTrack.parentElement.offsetWidth || 0;
			bsAutoStop();
			bsTrack.classList.add( 'is-dragging' );
			if ( bsSection ) bsSection.classList.add( 'is-dragging' );
		}

		function bsDragMove( clientX ) {
			if ( ! bsDragging || bsSlideW === 0 ) return;
			var delta = clientX - bsDragStartX;
			if ( Math.abs( delta ) > DRAG_THRESHOLD ) bsDragMoved = true;
			if ( ! bsDragMoved ) return;
			bsDragOffsetX = delta;
			var basePx  = -bsPos * bsSlideW;
			var totalPx = basePx + delta;
			bsTrack.style.transform = 'translateX(' + totalPx + 'px)';
		}

		function bsDragEnd() {
			if ( ! bsDragging ) return;
			bsDragging = false;
			if ( bsSection ) bsSection.classList.remove( 'is-dragging' );

			if ( bsDragMoved ) {
				if ( bsDragOffsetX < -SWIPE_THRESHOLD ) {
					bsMoveTo( bsPos + 1 );
				} else if ( bsDragOffsetX > SWIPE_THRESHOLD ) {
					bsMoveTo( bsPos - 1 );
				} else {
					// No llegó al umbral — volver al mismo slide con transición
					bsMoveTo( bsPos );
				}
			} else {
				bsTrack.classList.remove( 'is-dragging' );
			}
			bsAutoStart();
		}

		// Mouse
		bsTrack.addEventListener( 'mousedown', function ( e ) {
			if ( e.button !== 0 ) return;
			e.preventDefault(); // evita selección de texto durante drag
			bsDragStart( e.clientX );
		} );
		document.addEventListener( 'mousemove', function ( e ) {
			bsDragMove( e.clientX );
		} );
		document.addEventListener( 'mouseup', bsDragEnd );

		// Touch
		bsTrack.addEventListener( 'touchstart', function ( e ) {
			bsDragStart( e.touches[0].clientX );
		}, { passive: true } );
		bsTrack.addEventListener( 'touchmove', function ( e ) {
			bsDragMove( e.touches[0].clientX );
		}, { passive: true } );
		bsTrack.addEventListener( 'touchend', bsDragEnd );

		// Cancelar click en links si hubo drag
		bsTrack.addEventListener( 'click', function ( e ) {
			if ( bsDragMoved ) e.preventDefault();
		}, true );

		/* ── Init ────────────────────────────────────────────────── */
		bsJumpTo( bsPos ); // posicionar en slide 1 sin animación
		bsUpdateUI();
		bsAutoStart();
	}


	/* ===========================================================
	   HERO SLIDER — fade + contenido por slide + dots editoriales
	   =========================================================== */
	var heroSlider = document.getElementById( 'murg-hero-slider' );
	if ( heroSlider ) {
		var hsSlides  = Array.from( heroSlider.querySelectorAll( '.murg-hero__slide' ) );
		var hsDots    = Array.from( heroSlider.querySelectorAll( '.murg-hero__dot-circle' ) );
		var hsBar     = heroSlider.querySelector( '.murg-hero__progress-bar' );
		var hsCounter = document.getElementById( 'murg-hero-counter' );
		var hsCurrent = 0;
		var hsTotal   = hsSlides.length;
		var hsTimer   = null;
		var hsPaused  = false;
		var hsRemainingMs = 0;
		var hsStartTime = 0;

		if ( hsTotal > 1 ) {
			function hsGetIntervalo() {
				return parseInt( hsSlides[ hsCurrent ].dataset.intervalo, 10 ) || 5000;
			}

			function hsPad( n ) { return ( n < 10 ? '0' : '' ) + n; }

			var hsVideoFinTimer = null;
			function hsClearVideoFinTimer() {
				if ( hsVideoFinTimer ) { clearInterval( hsVideoFinTimer ); hsVideoFinTimer = null; }
			}

			function hsPauseVideo( slide ) {
				hsClearVideoFinTimer();
				var iframe = slide.querySelector( '[data-video-iframe]' );
				var mp4    = slide.querySelector( '[data-video-mp4]' );
				if ( iframe ) {
					try { iframe.contentWindow.postMessage( '{"event":"command","func":"pauseVideo","args":[]}', '*' ); } catch(e) {}
					try { iframe.contentWindow.postMessage( JSON.stringify( { method: 'pause' } ), '*' ); } catch(e) {}
				}
				if ( mp4 ) {
					mp4.removeEventListener( 'timeupdate', mp4._murgFinHandler );
					try { mp4.pause(); } catch(e) {}
				}
			}

			function hsPlayVideo( slide ) {
				hsClearVideoFinTimer();
				var iframe = slide.querySelector( '[data-video-iframe]' );
				var mp4    = slide.querySelector( '[data-video-mp4]' );
				var fin    = parseInt( slide.dataset.videoFin, 10 ) || 0;
				var inicio = parseInt( slide.dataset.videoInicio, 10 ) || 0;

				if ( iframe ) {
					// Reiniciar siempre al tiempo de inicio antes de reproducir
					var seekCmd = JSON.stringify( { event: 'command', func: 'seekTo', args: [ inicio, true ] } );
					var seekVimeo = JSON.stringify( { method: 'setCurrentTime', value: inicio } );
					try { iframe.contentWindow.postMessage( seekCmd, '*' ); } catch(e) {}
					try { iframe.contentWindow.postMessage( seekVimeo, '*' ); } catch(e) {}
					// Pequeño delay para que el seek se procese antes del play
					setTimeout( function () {
						try { iframe.contentWindow.postMessage( '{"event":"command","func":"playVideo","args":[]}', '*' ); } catch(e) {}
						try { iframe.contentWindow.postMessage( JSON.stringify( { method: 'play' } ), '*' ); } catch(e) {}
					}, 80 );

					if ( fin > 0 ) {
						var startedAt = Date.now();
						var durMs     = ( fin - inicio ) * 1000;
						hsVideoFinTimer = setInterval( function () {
							if ( hsPaused ) return;
							if ( Date.now() - startedAt >= durMs ) {
								hsClearVideoFinTimer();
								hsGoTo( hsCurrent + 1 );
							}
						}, 250 );
					}
				}

				if ( mp4 ) {
					// Reiniciar siempre al tiempo de inicio (0 si no está definido)
					try { mp4.currentTime = inicio; } catch(e) {}
					if ( fin > 0 ) {
						mp4._murgFinHandler = function () {
							if ( mp4.currentTime >= fin ) {
								mp4.removeEventListener( 'timeupdate', mp4._murgFinHandler );
								hsGoTo( hsCurrent + 1 );
							}
						};
						mp4.addEventListener( 'timeupdate', mp4._murgFinHandler );
					}
					try { mp4.play(); } catch(e) {}
				}
			}

			function hsGoTo( idx ) {
				var nextIdx = ( idx + hsTotal ) % hsTotal;
				if ( nextIdx === hsCurrent ) return; // Evitar doble click en el mismo slide

				// Detener transiciones actuales y timers
				clearTimeout( hsTimer );

				// Salida
				hsPauseVideo( hsSlides[ hsCurrent ] );
				hsSlides[ hsCurrent ].classList.remove( 'is-active' );
				hsSlides[ hsCurrent ].setAttribute( 'aria-hidden', 'true' );
				if ( hsDots[ hsCurrent ] ) {
					hsDots[ hsCurrent ].classList.remove( 'is-active' );
					hsDots[ hsCurrent ].setAttribute( 'aria-selected', 'false' );
				}

				// Cambio
				hsCurrent = nextIdx;

				// Entrada
				hsSlides[ hsCurrent ].classList.add( 'is-active' );
				hsSlides[ hsCurrent ].setAttribute( 'aria-hidden', 'false' );
				if ( hsDots[ hsCurrent ] ) {
					hsDots[ hsCurrent ].classList.add( 'is-active' );
					hsDots[ hsCurrent ].setAttribute( 'aria-selected', 'true' );
				}

				// Sincronizar contenido del overlay con el slide activo
				var activeSlide = hsSlides[ hsCurrent ];
				var titleEl = heroSlider.querySelector( '.murg-hero__title' );
				var ctaEl   = heroSlider.querySelector( '.murg-hero__cta' );
				if ( titleEl && activeSlide.dataset.titulo ) {
					titleEl.textContent = activeSlide.dataset.titulo;
				}
				if ( ctaEl ) {
					if ( activeSlide.dataset.ctaTexto ) ctaEl.textContent = activeSlide.dataset.ctaTexto;
					if ( activeSlide.dataset.ctaUrl )   ctaEl.href = activeSlide.dataset.ctaUrl;
				}

				// Sincronizar dots decorativos inline (bajo el título)
				var inlineDots = heroSlider.querySelectorAll( '.murg-hero__dot-circle' );
				inlineDots.forEach( function ( d, i ) { d.classList.toggle( 'is-active', i === hsCurrent ); } );

				if ( hsCounter ) {
					hsCounter.textContent = hsPad( hsCurrent + 1 ) + ' / ' + hsPad( hsTotal );
				}

				hsPlayVideo( hsSlides[ hsCurrent ] );

				// Reiniciar logica de tiempo
				hsRemainingMs = hsGetIntervalo();
				hsStartProgress();
				if ( ! hsPaused ) {
					hsScheduleNext( hsRemainingMs );
				}
			}

			function hsStartProgress() {
				if ( hsBar ) {
					hsBar.style.transition = 'none';
					hsBar.style.width = '0%';
					void hsBar.offsetWidth; // reflow
					if ( ! hsPaused ) {
						hsBar.style.transition = 'width ' + hsRemainingMs + 'ms linear';
						hsBar.style.width = '100%';
					}
				}
			}

			function hsStopProgress() {
				if ( hsBar ) {
					var w = window.getComputedStyle( hsBar ).width;
					var tw = window.getComputedStyle( heroSlider ).width;
					var pct = ( parseFloat( w ) / parseFloat( tw ) ) * 100;
					hsBar.style.transition = 'none';
					hsBar.style.width = pct.toFixed( 2 ) + '%';
					
					// Calcular tiempo restante exacto basado en el porcentaje visual
					hsRemainingMs = hsGetIntervalo() * ( 1 - pct / 100 );
				}
			}

			function hsResumeProgress() {
				if ( hsBar ) {
					hsBar.style.transition = 'width ' + Math.max( hsRemainingMs, 0 ) + 'ms linear';
					hsBar.style.width = '100%';
				}
			}

			function hsScheduleNext( ms ) {
				clearTimeout( hsTimer );
				hsTimer = setTimeout( function () {
					hsGoTo( hsCurrent + 1 );
				}, ms );
			}

			// Clicks en dots
			hsDots.forEach( function ( dot ) {
				dot.addEventListener( 'click', function () {
					var idx = parseInt( dot.dataset.index, 10 );
					hsGoTo( idx );
				} );
			} );

			// Pausa al hover
			heroSlider.addEventListener( 'mouseenter', function () {
				if ( hsPaused ) return;
				hsPaused = true;
				clearTimeout( hsTimer );
				hsStopProgress();
			} );
			
			heroSlider.addEventListener( 'mouseleave', function () {
				if ( ! hsPaused ) return;
				hsPaused = false;
				hsResumeProgress();
				hsScheduleNext( hsRemainingMs );
			} );

			// Swipe touch
			var hsTouchX = null;
			heroSlider.addEventListener( 'touchstart', function ( e ) {
				hsTouchX = e.touches[0].clientX;
			}, { passive: true } );
			heroSlider.addEventListener( 'touchend', function ( e ) {
				if ( hsTouchX === null ) return;
				var dx = e.changedTouches[0].clientX - hsTouchX;
				hsTouchX = null;
				if ( Math.abs( dx ) < 40 ) return;
				hsGoTo( dx < 0 ? hsCurrent + 1 : hsCurrent - 1 );
			}, { passive: true } );

			// Arrancar el primero
			hsRemainingMs = hsGetIntervalo();
			hsStartProgress();
			hsScheduleNext( hsRemainingMs );
		}
	}

	/* ------------------------------------------------------------------
	   DIAMONDS SHAPE SELECTOR — cambia stone overlay al hacer hover
	   ------------------------------------------------------------------ */
	var dmSection = document.querySelector( '.murg-diamonds' );
	if ( dmSection ) {
		var dmShapes = Array.from( dmSection.querySelectorAll( '.murg-diamonds__shape' ) );
		var dmStones = Array.from( dmSection.querySelectorAll( '.murg-diamonds__ring-img[data-shape]' ) );
		var dmLabel  = dmSection.querySelector( '.murg-diamonds__active-label' );

		function dmActivate( slug, label ) {
			dmShapes.forEach( function ( s ) {
				s.classList.toggle( 'is-active', s.dataset.shape === slug );
			} );
			dmStones.forEach( function ( s ) {
				s.classList.toggle( 'is-active', s.dataset.shape === slug );
			} );
			if ( dmLabel ) dmLabel.textContent = label;
		}

		if ( window.matchMedia && window.matchMedia( '(hover: hover) and (pointer: fine)' ).matches ) {
			dmShapes.forEach( function ( shape ) {
				shape.addEventListener( 'mouseenter', function () {
					dmActivate( shape.dataset.shape, shape.dataset.label );
				} );
				shape.addEventListener( 'focus', function () {
					dmActivate( shape.dataset.shape, shape.dataset.label );
				} );
			} );
		}
	}

	/* ------------------------------------------------------------------
	   SEARCH OVERLAY — abre input full-screen al click en "Buscar"
	   ------------------------------------------------------------------ */
	var srcModal   = document.getElementById( 'murg-search' );
	var srcOpenBtn = document.getElementById( 'murg-search-open' );
	var srcInput   = document.getElementById( 'murg-search-input' );
	var srcLastFocus = null;

	function srcOpen() {
		if ( ! srcModal ) return;
		srcLastFocus = document.activeElement;
		srcModal.classList.add( 'is-open' );
		srcModal.setAttribute( 'aria-hidden', 'false' );
		document.body.classList.add( 'murg-search-open' );
		// Foco al input despues de la transicion
		setTimeout( function () {
			if ( srcInput ) { srcInput.focus(); srcInput.select(); }
		}, 100 );
	}
	function srcClose() {
		if ( ! srcModal ) return;
		srcModal.classList.remove( 'is-open' );
		srcModal.setAttribute( 'aria-hidden', 'true' );
		document.body.classList.remove( 'murg-search-open' );
		if ( srcLastFocus && typeof srcLastFocus.focus === 'function' ) {
			srcLastFocus.focus();
		}
	}

	if ( srcModal && srcOpenBtn ) {
		srcOpenBtn.addEventListener( 'click', srcOpen );
		srcModal.querySelectorAll( '[data-close="murg-search"]' ).forEach( function ( el ) {
			el.addEventListener( 'click', srcClose );
		} );
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key === 'Escape' && srcModal.classList.contains( 'is-open' ) ) {
				srcClose();
			}
		} );
	}

	/* ------------------------------------------------------------------
	   CERTIFICACIONES (Carrusel 3D)
	   ------------------------------------------------------------------ */
	var certCarousel = document.getElementById('cert-carousel');
	if (certCarousel) {
		var certTrack = certCarousel.querySelector('.murg-certifications__track');
		if (certTrack) {
			var logos = Array.from(certTrack.children);
			var total = logos.length;
			
			if (total > 0) {
				// Clonar si hay menos de 5 logos para tener suficientes para las posiciones ocultas
				if (total < 5) {
					var toClone = total === 1 ? 4 : (total === 2 ? 3 : (total === 3 ? 2 : 1));
					for (var c = 0; c < toClone; c++) {
						logos.forEach(function(l) {
							var clone = l.cloneNode(true);
							certTrack.appendChild(clone);
						});
					}
					logos = Array.from(certTrack.children);
					total = logos.length;
				}
				
				var currentIndex = 0;
				
				function updateCertPositions() {
					logos.forEach(function(logo, i) {
						var pos = 'hidden-right'; // default
						
						if (i === currentIndex) {
							pos = 'center';
						} else if (i === (currentIndex + 1) % total) {
							pos = 'right';
						} else if (i === (currentIndex - 1 + total) % total) {
							pos = 'left';
						} else if (i === (currentIndex - 2 + total) % total) {
							pos = 'hidden-left';
						}
						
						logo.setAttribute('data-pos', pos);
					});
				}
				
				updateCertPositions();
				
				setInterval(function() {
					currentIndex = (currentIndex + 1) % total;
					updateCertPositions();
				}, 2500); // Rota cada 2.5 segundos
			}
		}
	}

	/* ===========================================================
	   SCROLL REVEAL — animaciones de entrada al hacer scroll
	   =========================================================== */
	if ( 'IntersectionObserver' in window ) {
		var revealObs = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( entry ) {
				if ( ! entry.isIntersecting ) return;
				entry.target.classList.add( 'is-revealed' );
				// Limpiar delay inline después de revelar para no afectar hovers futuros
				entry.target.addEventListener( 'transitionend', function clearDelay() {
					entry.target.style.transitionDelay = '';
					entry.target.removeEventListener( 'transitionend', clearDelay );
				}, { once: true } );
				revealObs.unobserve( entry.target );
			} );
		}, {
			threshold: 0.12,
			rootMargin: '0px 0px -40px 0px'
		} );

		// Fix #4: procesar bloques primero para que el guard closest funcione después
		document.querySelectorAll( '.murg-bestsellers, .murg-certifications, .murg-diamonds__inner, .murg-novios__panel, .murg-featured__media, .murg-qantu__gallery, .murg-visita, .murg-newsletter, .murg-footer, .murg-ac-story__media, .murg-ac-appointment__media' ).forEach( function ( el ) {
			if ( el.closest( '[aria-hidden="true"]' ) ) return;
			el.setAttribute( 'data-reveal-block', '' );
			revealObs.observe( el );
		} );

		// Elementos individuales — Fix #2+3: saltar clones (aria-hidden)
		// Fix #4: saltar elementos dentro de data-reveal-block (ya animado como bloque)
		// Fix #1: stagger por posición dentro del padre, no índice global
		var revealSelectors = [
			'.murg-section__header',
			'.murg-collection',
			'.murg-statement .murg-eyebrow',
			'.murg-statement__quote',
			'.murg-statement__attr',
			'.murg-certifications__title',
			'.murg-certifications__carousel',
			'.murg-contact__title',
			'.murg-contact__lede',
			'.murg-info-block',
			'.murg-form',
			'.murg-diamonds__header',
			'.murg-diamonds__shape',
			'.murg-novios__title',
			'.murg-novios__sub',
			'.murg-novios .murg-btn',
			'.murg-novios__logos img',
			'.murg-icon-strip__item',
			'.murg-piezas__tab',
			'.murg-pieza',
			'.murg-featured__text > *',
			'.murg-qantu__header > *',
			'.murg-brands__track img',
			'.murg-footer__col',
			'.murg-ac-hero__content',
			'.murg-ac-section-head',
			'.murg-ac-ring-tabs',
			'.murg-ac-ring-card',
			'.murg-ac-categories .murg-ac-center',
			'.murg-ac-product',
			'.murg-ac-style',
			'.murg-ac-benefit',
			'.murg-ac-story__copy',
			'.murg-ac-appointment__copy',
			'.murg-ac-testimonial',
			// .murg-product excluido — está dentro de .murg-bestsellers (data-reveal-block)
		];

		revealSelectors.forEach( function ( sel ) {
			document.querySelectorAll( sel ).forEach( function ( el ) {
				// Saltar clones del slider
				if ( el.closest( '[aria-hidden="true"]' ) ) return;
				// Saltar elementos dentro de un bloque que ya se anima completo
				if ( el.closest( '[data-reveal-block]' ) ) return;

				// Stagger por posición dentro del mismo padre (Fix #1)
				var siblings = el.parentElement
					? Array.from( el.parentElement.querySelectorAll( sel ) )
					: [];
				var idx = siblings.indexOf( el );
				var isIcon = el.matches( '.murg-icon-strip__item, .murg-diamonds__shape, .murg-brands__track img, .murg-ac-product' );
				el.setAttribute( 'data-reveal', isIcon ? 'scale' : 'soft' );
				el.style.transitionDelay = Math.max( 0, idx % 4 ) * 0.08 + 's';
				revealObs.observe( el );
			} );
		} );

	} else {
		// Fallback — mostrar todo sin animación
		document.querySelectorAll( '[data-reveal], [data-reveal-block]' ).forEach( function ( el ) {
			el.classList.add( 'is-revealed' );
		} );
	}


	/* ------------------------------------------------------------------
	   RING CONFIGURATOR — selectores para anillos de compromiso
	   ------------------------------------------------------------------ */
	var rcConfig = document.getElementById( 'murg-ring-config' );

	if ( rcConfig ) {
		var rcShapeVal  = document.getElementById( 'murg-rc-shape-val' );
		var rcCaratVal  = document.getElementById( 'murg-rc-carat-val' );
		var rcCaratIn   = document.getElementById( 'murg-rc-carat' );
		var rcCaratFill = document.getElementById( 'murg-rc-carat-fill' );
		var rcMetalVal  = document.getElementById( 'murg-rc-metal-val' );
		var rcOriginVal = document.getElementById( 'murg-rc-origin-val' );

		// Shape labels for display
		var rcShapeLabels = {
			'redondo': 'Redondo', 'oval': 'Oval', 'esmeralda': 'Esmeralda',
			'cojin': 'Cojín', 'pera': 'Pera', 'radiante': 'Radiante',
			'princesa': 'Princesa', 'marquesa': 'Marquesa', 'asscher': 'Asscher',
			'corazon': 'Corazón'
		};

		// Shape selector
		rcConfig.querySelectorAll( '.murg-rc-shape' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				rcConfig.querySelectorAll( '.murg-rc-shape' ).forEach( function ( b ) {
					b.classList.remove( 'is-active' );
				} );
				btn.classList.add( 'is-active' );
				var shape = btn.dataset.shape;
				if ( rcShapeVal ) rcShapeVal.textContent = rcShapeLabels[ shape ] || shape;
			} );
		} );

		// Carat slider
		function rcUpdateCarat() {
			if ( ! rcCaratIn ) return;
			var val  = parseFloat( rcCaratIn.value );
			var min  = parseFloat( rcCaratIn.min );
			var max  = parseFloat( rcCaratIn.max );
			var pct  = ( ( val - min ) / ( max - min ) ) * 100;
			if ( rcCaratFill ) rcCaratFill.style.width = pct + '%';
			if ( rcCaratVal )  rcCaratVal.textContent  = val.toFixed( 2 ) + ' Ct';
		}
		if ( rcCaratIn ) {
			rcCaratIn.addEventListener( 'input', rcUpdateCarat );
			rcUpdateCarat();
		}

		// Metal selector
		rcConfig.querySelectorAll( '.murg-rc-metal' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				rcConfig.querySelectorAll( '.murg-rc-metal' ).forEach( function ( b ) {
					b.classList.remove( 'is-active' );
				} );
				btn.classList.add( 'is-active' );
				if ( rcMetalVal ) rcMetalVal.textContent = btn.dataset.label;
			} );
		} );

		// Origin toggle
		rcConfig.querySelectorAll( '.murg-rc-origin__btn' ).forEach( function ( btn ) {
			btn.addEventListener( 'click', function () {
				rcConfig.querySelectorAll( '.murg-rc-origin__btn' ).forEach( function ( b ) {
					b.classList.remove( 'is-active' );
				} );
				btn.classList.add( 'is-active' );
				var origin = btn.dataset.origin;
				if ( rcOriginVal ) {
					rcOriginVal.textContent = origin === 'laboratorio' ? 'Lab Grown' : 'Natural';
				}
			} );
		} );
	}

	/* ------------------------------------------------------------------
	   ADD TO CART — feedback visual en el botón
	   ------------------------------------------------------------------ */
	var atcWrap = document.querySelector( '.murg-product-detail__atc' );
	if ( atcWrap ) {
		var atcBtn = atcWrap.querySelector( '.single_add_to_cart_button' );
		if ( atcBtn ) {
			var atcOrigText = atcBtn.textContent;

			// WooCommerce AJAX add-to-cart triggers jQuery events
			if ( window.jQuery ) {
				jQuery( document.body ).on( 'added_to_cart', function () {
					atcBtn.classList.add( 'murg-atc--added' );
					atcBtn.textContent = 'Producto añadido al carrito ✓';
					setTimeout( function () {
						atcBtn.classList.remove( 'murg-atc--added' );
						atcBtn.textContent = atcOrigText;
					}, 3000 );
				} );
			}

			// Fallback: form submit (non-AJAX / variable products)
			var atcForm = atcWrap.querySelector( 'form.cart' );
			if ( atcForm ) {
				atcForm.addEventListener( 'submit', function () {
					atcBtn.classList.add( 'murg-atc--loading' );
					atcBtn.textContent = 'Añadiendo...';
				} );
			}
		}
	}

} )();
