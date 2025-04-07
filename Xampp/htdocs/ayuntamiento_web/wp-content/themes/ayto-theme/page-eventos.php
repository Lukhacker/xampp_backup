<?php
/* Template Name: Eventos */
get_header();
?>

<div class="presentation">
    <div class="presentation-container">
        <h1>Nuestros próximos eventos</h1>
    </div>
</div>

<?php
$args = array(
    'post_type'      => 'post',
    'category_name'  => 'eventos',
    'posts_per_page' => 10,
);

$query = new WP_Query($args);

if ($query->have_posts()) : ?>
    <div class="events">
        <div class="events-container">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <a class="events-link" href="<?php the_permalink(); ?>">
                    <div class="events-item">
                        <div class="events-img">
                            <?php if (has_post_thumbnail()) {
                                the_post_thumbnail('medium', array('class' => 'post-thumbnail'));
                            } ?>
                        </div>
                        <div class="events-content">
                            <h2><?php the_title(); ?></h2>
                            <p><?php echo wp_trim_words(get_the_excerpt(), 20, '...'); ?></p>
                        </div>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
    <?php wp_reset_postdata();
else :
    echo '<p>No hay eventos disponibles.</p>';
endif;
?>

<?php
get_footer();
?>
