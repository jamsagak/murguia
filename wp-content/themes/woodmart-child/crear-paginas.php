<?php
/**
 * Script de una sola ejecución: crear páginas institucionales.
 * Ejecutar visitando: /wp-content/themes/woodmart-child/crear-paginas.php
 * Eliminar después de ejecutar.
 */
require_once dirname( __DIR__, 3 ) . '/wp-load.php';

if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( 'Debes estar logueado como admin.' );
}

$pages = [
	[
		'title'    => 'Nosotros',
		'slug'     => 'nosotros',
		'template' => 'page-nosotros.php',
		'content'  => '',
	],
	[
		'title'    => 'Política de Privacidad',
		'slug'     => 'politica-de-privacidad',
		'template' => 'page-legal.php',
		'content'  => '<h2>¿Qué datos personales recogemos?</h2>
<p>En cumplimiento de la Ley Nº 29733 – Ley de Protección de Datos, los datos personales que recogemos figuran en los formularios de la página web. Adicionalmente se almacena la dirección IP para mantener un registro del origen.</p>
<p>Esta información se utiliza únicamente para comunicarse o para el propósito del formulario. Existen tres formularios:</p>
<ol>
<li><strong>Formulario de contacto:</strong> Identificación y comunicación por comentarios o consultas</li>
<li><strong>Formulario de facturación y despacho:</strong> Necesario para facturación y despacho</li>
<li><strong>Formulario de libro de reclamaciones:</strong> Recopilación para el propósito del formulario</li>
</ol>

<h2>¿A dónde se envía o almacena tu información personal?</h2>
<p>La información viaja a través de internet hacia el servidor ubicado en Estados Unidos de América. El procesamiento ocurre así:</p>
<ul>
<li>Comentarios del formulario de contacto o libro de reclamaciones: Enviados a correo empresarial, no almacenados en base de datos</li>
<li>Formulario de facturación y despacho: Guardado en base de datos web y enviado a correo empresarial</li>
</ul>

<h2>¿Cuánto tiempo conservamos tus datos personales?</h2>
<p>Los datos proporcionados serán conservados indefinidamente por motivos administrativos y de seguridad.</p>

<h2>¿Qué derechos tienes sobre tus datos?</h2>
<p>Puedes solicitar: archivo de exportación de datos personales o eliminación de cualquier dato personal. Esto no incluye datos que deben conservarse con fines administrativos, legales o de seguridad.</p>

<h2>Confidencialidad de los datos personales</h2>
<p>Los datos serán tratados con total confidencialidad, adoptando todas las medidas de seguridad necesarias.</p>

<h2>Seguridad de los datos personales</h2>
<p>Se han adoptado medidas legales, organizativas y técnicas apropiadas para garantizar la seguridad, evitando alteración, pérdida, tratamiento indebido o acceso no autorizado. Solo se realiza tratamiento de datos almacenados en repositorios que reúnan condiciones de seguridad exigidas por la normativa vigente.</p>

<h2>Sobre el ejercicio de derechos como titular de datos personales</h2>
<p>Las personas pueden ejercer derechos de acceso, rectificación, cancelación, oposición, impedir suministro, oposición al tratamiento u objetivo de datos, conforme a legislación peruana vigente.</p>
<p>Para ejercer estos derechos, dirigir solicitud a: <a href="mailto:info@joyeriamurguia.com">info@joyeriamurguia.com</a> con asunto "Protección de Datos Personales", consignando datos, acreditando identidad y motivos de solicitud.</p>

<h2>Vigencia y Modificación de la Política de Privacidad</h2>
<p>Joyería Murguía se reserva el derecho a modificar esta Política de Privacidad ante cambios en legislación vigente. Cualquier cambio será publicado en la plataforma. Se recomienda verificar regularmente este documento.</p>',
	],
	[
		'title'    => 'Política de Cookies',
		'slug'     => 'politica-de-cookies',
		'template' => 'page-legal.php',
		'content'  => '<h2>1. Introducción</h2>
<p>Nuestra web utiliza cookies y otras tecnologías relacionadas. Las cookies también son colocadas por terceros a los que hemos contratado.</p>

<h2>2. ¿Qué son las cookies?</h2>
<p>Una cookie es un pequeño archivo simple que se envía junto con las páginas de esta web y que tu navegador almacena en el disco duro de tu ordenador o de otro dispositivo.</p>

<h2>3. ¿Qué son los scripts?</h2>
<p>Un script es un fragmento de código de programa que se utiliza para hacer que nuestra web funcione correctamente y de forma interactiva.</p>

<h2>4. ¿Qué es una baliza web?</h2>
<p>Una baliza web es una pequeña e invisible pieza de texto o imagen en una web que se utiliza para hacer seguimiento del tráfico en una web.</p>

<h2>5. Cookies</h2>
<h3>5.1 Cookies técnicas o funcionales</h3>
<p>Algunas cookies aseguran que ciertas partes de la web funcionen correctamente y que tus preferencias de usuario sigan recordándose.</p>

<h3>5.2 Cookies de estadísticas</h3>
<p>Usamos cookies de estadísticas para optimizar la experiencia en la web para nuestros usuarios.</p>

<h3>5.3 Cookies de marketing/seguimiento</h3>
<p>Las cookies de marketing/seguimiento son cookies usadas para crear perfiles de usuario para mostrar publicidad.</p>

<h3>5.4 Botones de medios sociales</h3>
<p>En nuestra web hemos incluido botones para Facebook e Instagram para promocionar páginas o compartirlas.</p>

<h2>6. Consentimiento</h2>
<p>Cuando visites nuestra web por primera vez, te mostraremos una ventana emergente con una explicación sobre las cookies.</p>

<h2>7. Tus derechos con respecto a los datos personales</h2>
<ul>
<li>Derecho a saber por qué se necesitan tus datos personales</li>
<li>Derecho de acceso</li>
<li>Derecho de rectificación</li>
<li>Derecho de cesión de tus datos</li>
<li>Derecho de oposición</li>
</ul>

<h2>8. Activación, desactivación y eliminación de cookies</h2>
<p>Puedes utilizar tu navegador de Internet para eliminar las cookies de forma automática o manual.</p>

<h2>9. Detalles de contacto</h2>
<p>Joyería Murguía<br>Av. Pardo y Aliaga 572, San Isidro, Lima, Perú<br>Email: <a href="mailto:info@joyeriamurguia.com">info@joyeriamurguia.com</a><br>Teléfono: +51(01)7195364</p>',
	],
	[
		'title'    => 'Términos y Condiciones',
		'slug'     => 'terminos-y-condiciones',
		'template' => 'page-legal.php',
		'content'  => '<h2>I. Aviso Legal</h2>
<p>Joyería Murguía, con razón social Novios Murguia S.A.C. y RUC 20605052194, opera este sitio web. La empresa se dedica a la promoción y comercialización de productos de joyería en oro de 18Kt con piedras preciosas, relojería y artículos de decoración. Aceptar estos términos es obligatorio para registro y compra. Contacto: <a href="mailto:info@joyeriamurguia.com">info@joyeriamurguia.com</a> o 01-7195364.</p>

<h2>II. Condiciones de Uso</h2>
<p>Los usuarios deben usar el sitio conforme a la ley y buena fe. No se permitirán conductas contra la ley, derechos de terceros, moral o costumbres.</p>

<h2>III. Capacidad Legal</h2>
<p>Los servicios están disponibles para personas con capacidad legal para contratar. Los menores o incapaces deben actuar mediante padres o tutores. Los usuarios deben registrarse proporcionando información verdadera, actualizada y completa.</p>

<h2>IV. Límites a Responsabilidad</h2>
<p>Joyería Murguía no responde por: información de terceros en foros y redes sociales, caída del sitio por prestadores de internet, problemas en pasarelas de pago, ni veracidad de información de fabricantes.</p>

<h2>V. Modificaciones</h2>
<p>Joyería Murguía se reserva el derecho de cambiar, modificar o agregar contenido en cualquier momento sin aviso previo.</p>

<h2>VI. Enlaces a Otros Sitios</h2>
<p>El sitio puede contener enlaces a servicios no operados por Joyería Murguía. Las políticas descritas no aplican a terceros, y los enlaces no implican aprobación.</p>

<h2>VII. Propiedad Intelectual</h2>
<p>Joyería Murguía es titular de todos los derechos de propiedad intelectual de contenidos (textos, fotos, videos, imágenes). No pueden reproducirse sin autorización expresa.</p>

<h2>VIII. Ley Aplicable y Jurisdicción</h2>
<p>Los términos se rigen por la legislación peruana. Los usuarios renuncian a cualquier fuero y se someten a autoridades competentes del Perú.</p>

<h2>IX. Condiciones sobre Adquisición</h2>

<h3>Medios de Pago</h3>
<p>Se aceptan: tarjetas de crédito/débito, transferencias bancarias y pago en efectivo en locales.</p>

<h3>Disponibilidad</h3>
<p>Productos están sujetos a disponibilidad. El usuario recibirá correo de confirmación. Joyería Murguía puede rescindir o cancelar, total o parcialmente cualquier transacción hasta antes de entrega y reembolsará el dinero.</p>

<h3>Política de Entrega</h3>
<p>Los productos pueden recogerse en tres tiendas sin costo, tres días hábiles después de compra. Entregas a domicilio cuestan US$10, gratis para compras superiores a US$350. Se entregan en tres días hábiles. Los envíos los realiza Bonnet Servicios Logísticos SAC.</p>

<h3>Política de Cambios</h3>
<p>Se pueden cambiar productos dentro de siete días hábiles de la entrega, si no han sido usados, están en perfecto estado y en estuche original. Enviar correo a <a href="mailto:info@joyeriamurguia.com">info@joyeriamurguia.com</a> con asunto "Cambio de producto".</p>

<h3>Política de Devoluciones</h3>
<p>Si un producto presenta defectos de fabricación, se puede cambiar por otro (sujeto a disponibilidad) o solicitar devolución de dinero dentro de siete días hábiles de recepción. Joyería Murguía asume gastos de envío y recojo.</p>

<h3>Garantía de Joyas</h3>
<p>Las joyas tienen garantía de 6 meses que cubre solo fallas del producto e incluye revisión, limpieza, pulido y lustrado anual (sin baño de rodio). No cubre: daños por desgaste natural, descuido, mal uso, golpes fuertes o exposición a químicos.</p>',
	],
	[
		'title'    => 'Recojos y Envíos',
		'slug'     => 'recojos-y-envios',
		'template' => 'page-legal.php',
		'content'  => '<h2>Política de envíos y recojos de pedidos</h2>

<p>Los productos adquiridos mediante la página web o canales digitales pueden recogerse en cualquiera de las tres sucursales sin costo adicional tras coordinación previa. La disponibilidad ocurre tres días hábiles después de la compra.</p>

<h3>Entregas a domicilio</h3>
<p>Para entregas domiciliarias aplica tarifa de US$10, con envío gratuito en compras superiores a US$350. El plazo es de tres días hábiles contados desde el día siguiente de la compra. Los envíos los realiza Bonnet Servicios Logísticos SAC.</p>

<h3>Zonas de entrega en Lima</h3>
<p>Barranco, Breña, Jesús María, La Victoria, Lince, Magdalena del Mar, Miraflores, Pueblo Libre, San Borja, San Isidro, San Luis, San Miguel, Santiago de Surco, Surquillo, Rímac, Independencia, San Martín de Porres, Ate Vitarte, El Agustino, Los Olivos, Santa Anita, Callao, Ventanilla, Carmen de la Legua, Bellavista, La Perla, La Punta, Villa El Salvador, Villa María del Triunfo, Chorrillos, La Molina, Ancón, Chaclacayo, Cieneguilla, Comas, Lurigancho, Lurín, Pachacamac, Pucusana, Puente Piedra, Punta Hermosa, Punta Negra, San Bartolo, San Juan de Miraflores y Santa Rosa.</p>

<h3>Envíos a provincia</h3>
<p>Para envíos a provincia contactar mediante WhatsApp al +51 934 413 662.</p>

<h3>Envíos internacionales</h3>
<p>Se realizan cotizaciones especiales contactando por WhatsApp. Se envía a todo el mundo excepto Cuba y Rusia.</p>

<h3>Responsabilidad</h3>
<p>La persona que recibe los productos solicitados da cuenta de que el paquete contiene los productos solicitados en correctas condiciones. La empresa no asume responsabilidad por retrasos derivados de casos fortuitos o fuerza mayor.</p>

<h3>Sucursales para recojo</h3>
<ul>
<li><strong>San Isidro:</strong> Av. Pardo y Aliaga 572</li>
<li><strong>Miraflores:</strong> Av. La Paz 1198</li>
<li><strong>Jockey Plaza:</strong> Av. Javier Prado Este 4200, Surco</li>
</ul>',
	],
	[
		'title'    => 'Libro de Reclamaciones',
		'slug'     => 'libro-de-reclamaciones',
		'template' => 'page-libro-reclamaciones.php',
		'content'  => '',
	],
];

echo "<h1>Creando páginas institucionales...</h1><pre>\n";

foreach ( $pages as $page ) {
	$existing = get_page_by_path( $page['slug'] );
	if ( $existing ) {
		echo "SKIP: '{$page['title']}' ya existe (ID {$existing->ID})\n";
		// Asegurar que tenga el template correcto
		update_post_meta( $existing->ID, '_wp_page_template', $page['template'] );
		echo "  → Template actualizado a {$page['template']}\n";
		continue;
	}

	$id = wp_insert_post( [
		'post_title'   => $page['title'],
		'post_name'    => $page['slug'],
		'post_content' => $page['content'],
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_author'  => get_current_user_id(),
	] );

	if ( is_wp_error( $id ) ) {
		echo "ERROR: '{$page['title']}' — {$id->get_error_message()}\n";
		continue;
	}

	update_post_meta( $id, '_wp_page_template', $page['template'] );
	echo "OK: '{$page['title']}' creada (ID {$id}) con template {$page['template']}\n";
}

echo "\n¡Listo! Ahora elimina este archivo del servidor.\n";
echo "</pre>";
