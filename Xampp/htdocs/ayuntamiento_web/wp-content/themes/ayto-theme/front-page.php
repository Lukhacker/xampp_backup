<?php
get_header();
?>

<section class="hero" id="hero">
    <div class="hero-container">
        <div class="hero-text">
            <h1 class="hero-title">Conoce La Zubia, un lugar lleno de historia y encanto</h1>
            <div class="hero-buttons">
                <button class="hero-button"><a href="noticias">Noticias <i class="fa-solid fa-chevron-right"></i></a></button>
                <button class="hero-button"><a href="eventos">Eventos <i class="fa-solid fa-chevron-right"></i></a></button>
            </div>
        </div>
    </div>
</section>

<section class="news">
    <div class="news-container">
        <h1>Lo último en La Zubia</h1>
        <span class="white-line"></span>

        <div class="news-row">
            <?php
            $args = array(
                'post_type'      => 'post',             // Tipo de contenido (entradas)
                'posts_per_page' => 4,                  // Número de entradas a mostrar (4 para que haya 2 filas de 2 noticias)
                'orderby'        => 'date',             // Ordenado por fecha
                'order'          => 'DESC',             // Más recientes primero
                'category_name'  => 'noticias'          // Filtrar por la categoría 'noticias'
            );
            $query = new WP_Query($args);

            if ($query->have_posts()) : 
                $counter = 0;  //Contador para las filas
                while ($query->have_posts()) : $query->the_post(); ?>
                    <!-- Inicio de la noticia -->
                    <div class="news-item">
                        <a href="<?php the_permalink(); ?>" class="news-link"> <!-- El enlace abarca todo el artículo -->
                            <div class="news-image">
                                <?php 
                                if (has_post_thumbnail()) {
                                    the_post_thumbnail('medium', array('class' => 'post-thumbnail'));
                                } else {
                                    echo '<img src="' . get_template_directory_uri() . '/img/default.jpg" alt="Noticia">';
                                }
                                ?>
                            </div>
                            <div class="news-text">
                                <h2><?php the_title(); ?></h2>
                                <p><?php echo wp_trim_words(get_the_content(), 20, '...'); ?></p> <!-- Mostrar resumen -->
                            </div>
                        </a>
                    </div>
                    <!-- Fin de la noticia -->
                    
                    <?php
                    $counter++;
                    if ($counter % 2 == 0) {
                        echo '</div><div class="news-row">'; //Cierro una fila y abro una nueva
                    }
                endwhile; 
                wp_reset_postdata();
            else : 
                echo '<p>No hay noticias disponibles.</p>';
            endif;
            ?>
        </div>
    </div>
</section>

<section class="intro">
    <div class="intro-container">
        <h1>Un pueblo con historia</h1>
        <span class="white-line"></span>
        <div class="intro-row intro-row1">
            <div class="intro-text">
                <p>
                    La Zubia es un <strong>municipio</strong> situado en la comarca del <strong>Área Metropolitana de Granada</strong>, en el sur de España. Su historia se remonta a tiempos de la <strong>Al-Ándalus</strong>, aunque fue durante la Edad Media cuando comenzó a consolidarse como un núcleo habitado.
                </p>
                <p>
                    En la época musulmana, la zona fue un lugar estratégico debido a su proximidad a la ciudad de Granada, lo que le permitió prosperar y desarrollarse. La Zubia se convirtió en un importante centro comercial y cultural durante el siglo XVIII, cuando se estableció la <strong>Casa de la Cultura</strong> en la iglesia de Nuestra Señora de la Asunción. 
                </p>
            </div>
            <div class="intro-img1 intro-img"></div>
        </div>
        <div class="intro-row intro-row2">
            <div class="intro-img2 intro-img"></div>
            <div class="intro-text">
                <p>
                    En cuanto a su patrimonio, La Zubia alberga varios <strong>monumentos</strong> de interés. La <strong>Iglesia de Nuestra Señora de la Asunción</strong>, construida en el siglo XVI sobre una antigua mezquita, es uno de los principales símbolos religiosos del municipio. Además, La Zubia cuenta con la <strong>Iglesia de San Pedro y San Pablo</strong>, de estilo <strong>renacentista</strong>, que destaca por su fachada impresionante y su interior decorado con pinturas y esculturas de gran valor artístico.
                </p>
                <p>
                    La cercanía al <strong>Parque Natural de la Sierra de Huétor</strong> también ofrece paisajes naturales de gran belleza, convirtiendo a La Zubia en un lugar de interés tanto histórico como natural.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="visitar">
    <div class="visitar-container">
        <h1>¿Por qué visitar La Zubia?</h1>
        <div class="white-line"></div>
        <div class="visitar-items">
            <a class="visitar-link" href="https://turismo.lazubia.org/que-hacer/?tour_types=44&page=0" target="_blank">
                <div class="visitar-item visitar-item1">
                    <div class="visitar-img visitar-img1"></div>
                    <div class="visitar-text">
                        <h2>Senderismo</h2>
                        <p>
                        <strong>Explora</strong> rutas naturales y <strong>disfruta</strong> de paisajes impresionantes mientras te sumerges en la belleza de La Zubia. <strong>Aventúrate</strong> en senderos que conectan tradición y naturaleza.
                        </p>
                    </div>
                </div>
            </a>
            
            <a class="visitar-link" href="https://turismo.lazubia.org/que-hacer/?tour_types=33&page=0" target="_blank">
                <div class="visitar-item visitar-item2">
                    <div class="visitar-img visitar-img2"></div>
                    <div class="visitar-text">
                        <h2>Fiestas locales</h2>
                        <p>
                        <strong>Vive</strong> la emoción de nuestras fiestas, <strong>comparte</strong> la alegría de celebraciones únicas y <strong>experimenta</strong> la calidez de la comunidad en cada evento.
                        </p>
                    </div>
                </div>
            </a>

            <a class="visitar-link" href="https://turismo.lazubia.org/que-hacer/?tour_types=59&page=0" target="_blank">
                <div class="visitar-item visitar-item3">
                    <div class="visitar-img visitar-img3"></div>
                    <div class="visitar-text">
                        <h2>Gastronomía local</h2>
                        <p>
                        <strong>Degusta</strong> platos auténticos y <strong>saborea</strong> ingredientes de la tierra, <strong>descubre</strong> la rica tradición culinaria que hace de La Zubia un destino único.
                        </p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>

<section class="eventos">
    <div class="eventos-container">
        <h1>Eventos y Actividades</h1>
        <span class="white-line"></span>
        <?php echo do_shortcode('[eo_calendar]'); ?>
        <div id="eventos-del-dia"></div>
    </div>
</section>

<section class="galeria">
    <div class="galeria-container">
        <h1>Galería</h1>
        <span class="white-line"></span>
    </div>
</section>

<?php
get_footer();
?>
