<?php
/* Template Name: Noticias */
get_header();
?>

<div class="presentation">
    <div class="presentation-container">
        <h1>Ponte al día en La Zubia</h1>
    </div>
</div>

<?php
$args = array(
    'post_type'      => 'post',
    'category_name'  => 'noticias',
    'posts_per_page' => 10,
);

$query = new WP_Query($args);

if ($query->have_posts()) : ?>
    <div class="news">
        <div class="news-container">
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <a class="news-link" href="<?php the_permalink(); ?>">
                    <div class="news-item">
                        <div class="news-img">
                            <?php if (has_post_thumbnail()) {
                                the_post_thumbnail('medium', array('class' => 'post-thumbnail'));
                            } ?>
                        </div>
                        <div class="news-content">
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
    echo '<p>No hay noticias disponibles.</p>';
endif;
?>


<?php
get_footer();
?>