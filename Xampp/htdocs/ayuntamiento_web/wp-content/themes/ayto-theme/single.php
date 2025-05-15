<?php get_header(); ?>

<main class="post">
    <div class="post-container">
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post(); ?>
                <article class="post">
                    <h1><?php the_title(); ?></h1>
                    <div class="post-meta">
                        <span>Publicado el <?php echo get_the_date(); ?></span>
                    </div>
                    <?php
                    if (has_post_thumbnail()) {
                        the_post_thumbnail('large', array('class' => 'post-thumbnail'));
                    }
                    ?>
                    <div class="post-body">
                        <?php the_content(); ?>
                    </div>
                </article>
        <?php endwhile;
        else :
            echo '<p>No se encontró la entrada.</p>';
        endif;
        ?>
    </div>
</main>

<?php get_footer(); ?>
