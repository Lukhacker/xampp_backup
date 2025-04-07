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

    // Script para entradas individuales (single post)
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

