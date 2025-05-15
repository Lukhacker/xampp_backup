<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo wp_get_document_title(); ?></title>
    <?php wp_head(); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body <?php body_class(); ?>>
    <header role="banner" id="nav">
        <nav role="navigation" class="main-nav">
        <div class="logo-secreto">
            <a href="<?php echo home_url('/'); ?>">
                <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo.png" alt="Logo">
            </a>
        </div>
        <form action="<?php echo site_url('/'); ?>" method="get" class="secret-search-form search-form">
            <input type="text" name="s" placeholder="Buscar..." value="<?php the_search_query(); ?>">
            <button type="submit" class="search-button">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>

            <input type="checkbox" id="menu-toggle" class="menu-toggle" />
            <label for="menu-toggle" class="hamburger">
                <div class="bar"></div>
                <div class="bar"></div>
                <div class="bar"></div>
            </label>
            <div class="list-nav">
                <div class="logo">
                    <a href="<?php echo home_url('/'); ?>">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/img/logo.png" alt="Logo">
                    </a>                   
                </div>
                <form action="<?php echo site_url('/'); ?>" method="get" class="search-form">
                    <input type="text" name="s" placeholder="Buscar..." value="<?php the_search_query(); ?>">
                    <button type="submit" class="search-button">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>

                <div class="nav-left">
                    <ul>
                        <li><a href="<?php echo home_url('/'); ?>" class="nav-section">INICIO</a></li>
                        <li><a href="<?php echo site_url('/noticias/'); ?>" class="nav-section">NOTICIAS</a></li>
                        <li><a href="<?php echo site_url('/areas/'); ?>" class="nav-section">ÁREAS</a></li>
                        <li><a href="<?php echo site_url('/eventos/'); ?>" class="nav-section">EVENTOS</a></li>
                        <li><a href="<?php echo site_url('/contacto/'); ?>" class="nav-section">CONTACTO</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <a href="#nav" class="scroll-to-top">
        <i class="fa fa-arrow-up"></i>
    </a>