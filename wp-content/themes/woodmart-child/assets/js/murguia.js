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

	if ( pdMainImg && pdThumbs.length ) {
		pdThumbs.forEach( function ( thumb ) {
			thumb.addEventListener( 'click', function () {
				pdThumbs.forEach( function ( t ) { t.classList.remove( 'is-active' ); } );
				thumb.classList.add( 'is-active' );
				pdMainImg.style.opacity = '0';
				var nextSrc = thumb.dataset.full;
				setTimeout( function () {
					pdMainImg.src        = nextSrc;
					pdMainImg.style.opacity = '1';
				}, 300 );
			} );
		} );
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
