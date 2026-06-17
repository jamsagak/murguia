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
