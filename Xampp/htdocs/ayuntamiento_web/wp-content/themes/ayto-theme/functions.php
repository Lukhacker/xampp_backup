<?php
function cargar_estilos_pagina() {
    //Estilo general para todo el sitio
    wp_enqueue_style('header-styles', get_template_directory_uri() . '/assets/css/header.css');
    wp_enqueue_style('footer-styles', get_template_directory_uri() . '/assets/css/footer.css');

    if (is_front_page()) {
        wp_enqueue_style('inicio-styles', get_template_directory_uri() . '/assets/css/inicio.css');
    } elseif (is_page('contacto')) {
        wp_enqueue_style('contacto-styles', get_template_directory_uri() . '/assets/css/contacto.css');
    } elseif (is_page('eventos')) {
        wp_enqueue_style('eventos-styles', get_template_directory_uri() . '/assets/css/eventos.css');
    } elseif (is_page('noticias')) {
        wp_enqueue_style('noticias-styles', get_template_directory_uri() . '/assets/css/noticias.css');
    } elseif (is_page('areas')) {
        wp_enqueue_style('areas-styles', get_template_directory_uri() . '/assets/css/areas.css');
    } elseif (is_search()) {
        wp_enqueue_style('search-styles', get_template_directory_uri() . '/assets/css/search.css');
    }

    //Estilos para entradas del blog
    if (is_single()) {
        wp_enqueue_style('blog-styles', get_template_directory_uri() . '/assets/css/single.css');
    }
}
add_action('wp_enqueue_scripts', 'cargar_estilos_pagina');

function cargar_scripts_personalizados() {
    //Script general para el menú
    wp_enqueue_script(
        'menu-script', 
        get_template_directory_uri() . '/assets/js/menu.js', 
        array(),
        '1.0',
        true
    );

    //Script para entradas individuales (single post)
    if (is_single()) {
        wp_enqueue_script(
            'custom-scripts',
            get_template_directory_uri() . '/assets/js/custom.js',
            array(),
            null,
            true
        );
    }

    if (is_page('contacto')) {
        wp_enqueue_script(
            'contacto-script',
            get_template_directory_uri() . '/assets/js/contacto.js',
            array(),
            null,
            true
        );
    }

    if (is_page('areas')) {
        wp_enqueue_script(
            'slider-script',
            get_template_directory_uri() . '/assets/js/slider.js',
            array(),
            null,
            true
        );
    }
}
add_action('wp_enqueue_scripts', 'cargar_scripts_personalizados');


//Registro las páginas de la web
function registrar_paginas_personalizadas() {
    //Defino las páginas a registrar
    $paginas = array(
        'Eventos'   => 'page-eventos.php',
        'Contacto'  => 'page-contacto.php',
        'Noticias'  => 'page-noticias.php',
        'Áreas'     => 'page-areas.php',
    );

    //Recorro las páginas y verifico si ya existen
    foreach ($paginas as $titulo => $template) {
        $page_check = get_page_by_title($titulo);

        //Si la página no existe, la creo
        if (!$page_check) {
            wp_insert_post(array(
                'post_title'    => $titulo,
                'post_content'  => '',
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'page_template' => $template,
            ));
        }
    }
}
add_action('after_setup_theme', 'registrar_paginas_personalizadas');

function activar_imagenes_destacadas() {
    add_theme_support('post-thumbnails');
}
add_action('after_setup_theme', 'activar_imagenes_destacadas');




//CALENDARIO DE EVENTOS

add_action('wp_ajax_obtener_eventos_fecha', 'obtener_eventos_fecha');
add_action('wp_ajax_nopriv_obtener_eventos_fecha', 'obtener_eventos_fecha');

function obtener_eventos_fecha() {
    if (!isset($_POST['fecha'])) {
        wp_send_json_error('Falta la fecha');
    }

    $fecha = sanitize_text_field($_POST['fecha']);

    $eventos = eo_get_events(array(
        'showpastevents' => true,
        'event_start_after' => $fecha,
        'event_start_before' => $fecha,
        'group_events_by' => 'series'
    ));

    if ($eventos) {
        ob_start(); // Capturamos HTML

        foreach ($eventos as $evento) {
            ?>
            <div class="evento" style="opacity: 0; transition: opacity 0.5s;">
              <h2><?php echo esc_html($evento->post_title); ?></h2>
              <p><?php echo esc_html($evento->post_excerpt); ?></p>
              <?php echo get_the_post_thumbnail($evento->ID, 'medium'); ?>
            </div>
            <?php
        }

        $html = ob_get_clean();
        wp_send_json_success($html);
    } else {
        wp_send_json_success('<p>No hay eventos para esta fecha.</p>');
    }
}



add_action('wp_enqueue_scripts', 'cargar_calendar_js');
function cargar_calendar_js() {
    wp_enqueue_script(
        'calendar-custom',
        get_template_directory_uri() . '/assets/js/calendar.js',
        array('jquery'),
        null,
        true
    );

    wp_localize_script('calendar-custom', 'calendarData', array(
        'ajaxurl' => admin_url('admin-ajax.php')
    ));
}

/*FIESTAS*/

function mostrar_ultimos_eventos_fiestas() {
    ob_start(); // Iniciar el buffer de salida

    $args = array(
        'post_type' => 'event',
        'posts_per_page' => 2,
        'orderby' => 'eventstart',
        'order' => 'DESC',
    );

    $event_query = new WP_Query($args);

    if ($event_query->have_posts()) :
        while ($event_query->have_posts()) : $event_query->the_post(); ?>
            <div class="evento-item">
                <div class="evento-imagen">
                    <?php if (has_post_thumbnail()) : ?>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('medium'); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <div class="evento-info">
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <p class="evento-fecha">
                        <?php echo eo_get_the_start('j F Y, H:i'); ?>
                    </p>
                    <p><?php echo get_the_excerpt(); ?></p>
                </div>
            </div>
        <?php endwhile;
        wp_reset_postdata();
    else :
        echo '<p>No hay eventos próximos en este momento.</p>';
    endif;

    return ob_get_clean(); // Devolver el contenido del buffer
}
add_shortcode('ultimos_eventos_fiestas', 'mostrar_ultimos_eventos_fiestas');

function zubia_override_event_css() {
    if ( is_singular('event') ) {
        ?>
        <style>
            .eventorganiser-event-meta {
                background-color: var(--white);
                color: var(--black);
                font-size: clamp(1.5rem, 1.333rem + 0.556vw, 2rem);
                border-radius: 10px;
                margin-bottom: 2rem;
                line-height: 1.6;
            }

            .eventorganiser-event-meta h4 {
                display: none;
                font-size: clamp(1.5rem, 1.333rem + 0.556vw, 2rem);
                margin-bottom: 1rem;
                color: var(--black);
                padding: 1rem;
            }

            .eventorganiser-event-meta ul {
                list-style: none;
                padding: 1rem;
                margin: 0;
            }

            .post-body {
                background-color: var(--white);
                border-radius: 10px;
                p {
                    font-family: Montserrat, Calibri;
                    text-align: justify;
                    word-spacing: .2rem;
                    color: var(--black);
                    font-size: clamp(1rem, 0.933rem + 0.222vw, 1.2rem);
                }
            }

            .html-div {
                font-family: Montserrat, Calibri;
                text-align: justify;
                word-spacing: .2rem;
                color: var(--black);
                font-size: clamp(1rem, 0.933rem + 0.222vw, 1.2rem);
            }

            div[dir="auto"] {
                font-family: Montserrat, Calibri;
                text-align: justify;
                word-spacing: 0.2rem;
                color: var(--black);
                font-size: clamp(1rem, 0.933rem + 0.222vw, 1.2rem);
                margin-bottom: .5rem;
            }

        </style>
        <?php
    }
}
add_action('wp_head', 'zubia_override_event_css');

function zubia_custom_event_date_label( $text ) {
    if ( 'Date' === $text ) {
        return 'Fecha';
    }
    return $text;
}
add_filter( 'gettext', 'zubia_custom_event_date_label' );
