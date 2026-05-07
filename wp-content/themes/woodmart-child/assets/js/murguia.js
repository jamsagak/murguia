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
	   BESTSELLERS SLIDER — translateX track
	   ------------------------------------------------------------------ */
	var bsTrack = document.getElementById( 'murg-bs-track' );
	var bsPrev  = document.getElementById( 'murg-bs-prev' );
	var bsNext  = document.getElementById( 'murg-bs-next' );
	var bsInfo  = document.getElementById( 'murg-bs-info' );

	if ( bsTrack && bsPrev && bsNext ) {
		var bsSlideEls = bsTrack.querySelectorAll( '.murg-products__slide' );
		var bsSlides   = bsSlideEls.length;
		var bsTotal    = parseInt( bsTrack.dataset.total, 10 ) ||
		                 bsTrack.querySelectorAll( '.murg-product' ).length;
		var bsPerSlide = 3;
		var bsCurrent  = 0;

		function bsUpdate() {
			bsTrack.style.transform = 'translateX(' + ( -100 * bsCurrent ) + '%)';
			bsPrev.disabled = bsCurrent === 0;
			bsNext.disabled = bsCurrent >= bsSlides - 1;
			if ( bsInfo && bsTotal ) {
				var from = bsPerSlide * bsCurrent + 1;
				var to   = Math.min( bsPerSlide * ( bsCurrent + 1 ), bsTotal );
				bsInfo.textContent = from + '–' + to + ' de ' + bsTotal;
			}
		}

		bsPrev.addEventListener( 'click', function () {
			if ( bsCurrent > 0 ) { bsCurrent--; bsUpdate(); }
		} );
		bsNext.addEventListener( 'click', function () {
			if ( bsCurrent < bsSlides - 1 ) { bsCurrent++; bsUpdate(); }
		} );

		// Optional: keyboard navigation when slider is in view
		document.addEventListener( 'keydown', function ( e ) {
			if ( e.key !== 'ArrowLeft' && e.key !== 'ArrowRight' ) return;
			var rect = bsTrack.getBoundingClientRect();
			var inView = rect.top < window.innerHeight && rect.bottom > 0;
			if ( ! inView ) return;
			if ( e.key === 'ArrowLeft'  && bsCurrent > 0 )            { bsCurrent--; bsUpdate(); }
			if ( e.key === 'ArrowRight' && bsCurrent < bsSlides - 1 ) { bsCurrent++; bsUpdate(); }
		} );

		bsUpdate();
	}

} )();
