<?php get_header(); ?>
<section class="search">
    <div class="search-container">
        <h1 class="search-title">
            <span class="uppercase">Resultados de búsqueda para:</span> <?php echo get_search_query(); ?>
        </h1>
        <?php
        $s = get_search_query();
        $args = array(
            's' => $s, //Palabra clave de la búsqueda
            'post_type' => array('post', 'page', 'event'), //Buscar en entradas, páginas y eventos
        );
        $query = new WP_Query($args);
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
                ?>
                <a href="<?php the_permalink(); ?>" class="search-result">
                    <div class="search-result-content">
                        <h2><?php the_title(); ?></h2>
                        <p><?php the_excerpt(); ?></p>
                    </div>
                </a>
                <?php
            }
        } else {
            echo '<p>No se encontraron resultados.</p>';
        }
        wp_reset_postdata();
        ?>
    </div>
</section>
<?php get_footer(); ?>

