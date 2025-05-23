<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
$wpaicg_pexels_api = get_option( 'wpaicg_pexels_api', '' );
$wpaicg_pixabay_api = get_option( 'wpaicg_pixabay_api', '' );
if ( strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post-new.php' ) ) {
    $mode = 'NEW';
    global $wpdb;
    $table = $wpdb->prefix . 'wpaicg';
    $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE name = %s", 'wpaicg_settings' ) );
    $value = '';
    $_wporg_preview_title = '';
    $_wporg_language = $result->wpai_language;
    $_wporg_number_of_heading = $result->wpai_number_of_heading;
    $_wporg_writing_style = $result->wpai_writing_style;
    $_wporg_writing_tone = $result->wpai_writing_tone;
    $_wporg_heading_tag = $result->wpai_heading_tag;
    $_wporg_target_url = '';
    $_wporg_cta_pos = $result->wpai_cta_pos;
    $_wporg_target_url_cta = '';
    $_wporg_keywords = "";
    $_wporg_add_keywords_bold = $result->wpai_add_keywords_bold;
    $_wporg_words_to_avoid = '';
    $_wporg_modify_headings = $result->wpai_modify_headings;
    $_wporg_add_img = $result->wpai_add_img;
    $_wporg_add_tagline = $result->wpai_add_tagline;
    $_wporg_add_intro = $result->wpai_add_intro;
    $_wporg_add_faq = $result->wpai_add_faq;
    $_wporg_add_conclusion = $result->wpai_add_conclusion;
    $_wporg_anchor_text = '';
    $_wporg_generated_text = '';
    $_wpaicg_post_tags = '';
    $_wporg_img_size = $result->img_size;
    $_wporg_img_style = get_option( '_wpaicg_image_style', '' );
    $_wpaicg_seo_meta_desc = get_option( '_wpaicg_seo_meta_desc', false );
    $wpaicg_toc = get_option( 'wpaicg_toc', false );
    $wpaicg_toc_title = get_option( 'wpaicg_toc_title', '' );
    $wpaicg_toc_title_tag = get_option( 'wpaicg_toc_title_tag', 'h2' );
    $wpaicg_intro_title_tag = get_option( 'wpaicg_intro_title_tag', 'h2' );
    $wpaicg_conclusion_title_tag = get_option( 'wpaicg_conclusion_title_tag', 'h2' );
    $wpaicg_conclusion_title_tag = get_option( 'wpaicg_conclusion_title_tag', 'h2' );
    $wpaicg_image_source = get_option( 'wpaicg_image_source', '' );
    $wpaicg_featured_image_source = get_option( 'wpaicg_featured_image_source', '' );
    $wpaicg_pexels_orientation = get_option( 'wpaicg_pexels_orientation', '' );
    $wpaicg_pexels_size = get_option( 'wpaicg_pexels_size', '' );
    $wpaicg_custom_image_settings = get_option( 'wpaicg_custom_image_settings', [] );
    $wpaicg_custom_prompt_enable = get_option( 'wpaicg_content_custom_prompt_enable', false );
    $wpaicg_custom_prompt = get_option( 'wpaicg_content_custom_prompt', '' );
    $wpaicg_hide_conclusion = get_option( 'wpaicg_hide_conclusion', false );
    $wpaicg_hide_introduction = get_option( 'wpaicg_hide_introduction', false );
    if ( empty( $wpaicg_custom_prompt ) ) {
        $wpaicg_custom_prompt = \WPAICG\WPAICG_Custom_Prompt::get_instance()->wpaicg_default_custom_prompt;
    }
    $wpaicg_pixabay_language = get_option( 'wpaicg_pixabay_language', 'en' );
    $wpaicg_pixabay_type = get_option( 'wpaicg_pixabay_type', 'all' );
    $wpaicg_pixabay_order = get_option( 'wpaicg_pixabay_order', 'popular' );
    $wpaicg_pixabay_orientation = get_option( 'wpaicg_pixabay_orientation', 'all' );
    $wpaicg_pixabay_enable_prompt = get_option( 'wpaicg_pixabay_enable_prompt', false );
    $wpaicg_pixabay_custom_prompt = get_option( 'wpaicg_pixabay_custom_prompt', \WPAICG\WPAICG_Generator::get_instance()->wpaicg_pixabay_custom_prompt );
    $wpaicg_pexels_enable_prompt = get_option( 'wpaicg_pexels_enable_prompt', false );
    $wpaicg_pexels_custom_prompt = get_option( 'wpaicg_pexels_custom_prompt', \WPAICG\WPAICG_Generator::get_instance()->wpaicg_pexels_custom_prompt );
} else {
    if ( strstr( $_SERVER['REQUEST_URI'], 'wp-admin/post.php' ) ) {
        $mode = 'EDIT';
        $value = get_post_meta( $post->ID, '_wporg_meta_key', true );
        $_wporg_preview_title = get_post_meta( $post->ID, '_wporg_preview_title', true );
        $_wporg_number_of_heading = get_post_meta( $post->ID, '_wporg_number_of_heading', true );
        $_wporg_add_img = get_post_meta( $post->ID, '_wporg_add_img', true );
        $_wporg_language = get_post_meta( $post->ID, '_wporg_language', true );
        $_wporg_add_intro = get_post_meta( $post->ID, '_wporg_add_intro', true );
        $_wporg_add_conclusion = get_post_meta( $post->ID, '_wporg_add_conclusion', true );
        $_wporg_writing_style = get_post_meta( $post->ID, '_wporg_writing_style', true );
        $_wporg_writing_tone = get_post_meta( $post->ID, '_wporg_writing_tone', true );
        $_wporg_keywords = get_post_meta( $post->ID, '_wporg_keywords', true );
        $_wporg_add_keywords_bold = get_post_meta( $post->ID, '_wporg_add_keywords_bold', true );
        $_wporg_heading_tag = get_post_meta( $post->ID, '_wporg_heading_tag', true );
        $_wporg_words_to_avoid = get_post_meta( $post->ID, '_wporg_words_to_avoid', true );
        $_wporg_add_tagline = get_post_meta( $post->ID, '_wporg_add_tagline', true );
        $_wporg_add_faq = get_post_meta( $post->ID, '_wporg_add_faq', true );
        $_wporg_target_url = get_post_meta( $post->ID, '_wporg_target_url', true );
        $_wporg_anchor_text = get_post_meta( $post->ID, '_wporg_anchor_text', true );
        $_wporg_generated_text = get_post_meta( $post->ID, '_wporg_generated_text', true );
        $_wporg_cta_pos = get_post_meta( $post->ID, '_wporg_cta_pos', true );
        $_wporg_target_url_cta = get_post_meta( $post->ID, '_wporg_target_url_cta', true );
        $_wporg_modify_headings = get_post_meta( $post->ID, '_wporg_modify_headings', true );
        $_wporg_img_size = get_post_meta( $post->ID, '_wporg_img_size', true );
        $_wporg_img_style = get_post_meta( $post->ID, '_wporg_img_style', true );
        $_wpaicg_seo_meta_desc = get_post_meta( $post->ID, '_wpaicg_seo_meta_desc', true );
        $_wpaicg_post_tags = get_post_meta( $post->ID, '_wpaicg_post_tags', true );
        $wpaicg_toc = get_post_meta( $post->ID, 'wpaicg_toc', true );
        $wpaicg_toc = ( empty( $wpaicg_toc ) ? false : $wpaicg_toc );
        $wpaicg_toc_title = get_post_meta( $post->ID, 'wpaicg_toc_title', true );
        $wpaicg_toc_title_tag = get_post_meta( $post->ID, 'wpaicg_toc_title_tag', true );
        $wpaicg_intro_title_tag = get_post_meta( $post->ID, 'wpaicg_intro_title_tag', true );
        $wpaicg_conclusion_title_tag = get_post_meta( $post->ID, 'wpaicg_conclusion_title_tag', true );
        $wpaicg_image_source = get_post_meta( $post->ID, 'wpaicg_image_source', true );
        $wpaicg_featured_image_source = get_post_meta( $post->ID, 'wpaicg_featured_image_source', true );
        $wpaicg_pexels_orientation = get_post_meta( $post->ID, 'wpaicg_pexels_orientation', true );
        $wpaicg_pexels_size = get_post_meta( $post->ID, 'wpaicg_pexels_size', true );
        $wpaicg_custom_image_settings = get_post_meta( $post->ID, 'wpaicg_custom_image_settings', true );
        $wpaicg_custom_prompt_enable = get_post_meta( $post->ID, 'wpaicg_custom_prompt_enable', true );
        $wpaicg_custom_prompt = get_post_meta( $post->ID, 'wpaicg_custom_prompt', true );
        $wpaicg_hide_conclusion = get_post_meta( $post->ID, 'wpaicg_hide_conclusion', true );
        $wpaicg_hide_introduction = get_post_meta( $post->ID, 'wpaicg_hide_introduction', true );
        $wpaicg_pixabay_language = get_post_meta( $post->ID, 'wpaicg_pixabay_language', true );
        if ( empty( $wpaicg_pixabay_language ) ) {
            $wpaicg_pixabay_language = 'en';
        }
        $wpaicg_pixabay_type = get_post_meta( $post->ID, 'wpaicg_pixabay_type', true );
        $wpaicg_pixabay_order = get_post_meta( $post->ID, 'wpaicg_pixabay_order', true );
        $wpaicg_pixabay_orientation = get_post_meta( $post->ID, 'wpaicg_pixabay_orientation', true );
        if ( empty( $wpaicg_custom_prompt ) ) {
            $wpaicg_custom_prompt = \WPAICG\WPAICG_Custom_Prompt::get_instance()->wpaicg_default_custom_prompt;
        }
        $wpaicg_pixabay_enable_prompt = get_post_meta( $post->ID, 'wpaicg_pixabay_enable_prompt', true );
        $wpaicg_pixabay_custom_prompt = get_post_meta( $post->ID, 'wpaicg_pixabay_custom_prompt', true );
        $wpaicg_pexels_enable_prompt = get_post_meta( $post->ID, 'wpaicg_pexels_enable_prompt', true );
        $wpaicg_pexels_custom_prompt = get_post_meta( $post->ID, 'wpaicg_pexels_custom_prompt', true );
        if ( empty( $wpaicg_pixabay_enable_prompt ) ) {
            $wpaicg_pixabay_enable_prompt = false;
        }
        if ( empty( $wpaicg_pexels_enable_prompt ) ) {
            $wpaicg_pexels_enable_prompt = false;
        }
        if ( empty( $wpaicg_pexels_custom_prompt ) ) {
            $wpaicg_pexels_custom_prompt = \WPAICG\WPAICG_Generator::get_instance()->wpaicg_pexels_custom_prompt;
        }
        if ( empty( $wpaicg_pixabay_custom_prompt ) ) {
            $wpaicg_pexels_custom_prompt = \WPAICG\WPAICG_Generator::get_instance()->wpaicg_pixabay_custom_prompt;
        }
    }
}
$wpaicg_custom_image_settings_default = array(
    'artist'            => 'None',
    'photography_style' => 'None',
    'lighting'          => 'Ambient',
    'subject'           => 'None',
    'camera_settings'   => 'Aperture',
    'composition'       => 'Rule of Thirds',
    'resolution'        => '4K (3840x2160)',
    'color'             => 'RGB',
    'special_effects'   => 'Cinemagraph',
);
$wpaicg_custom_image_settings = wp_parse_args( $wpaicg_custom_image_settings, $wpaicg_custom_image_settings_default );
?>

<table width="100%" id="wpaicg-post-form">
    <tr>
        <td><label style="font-weight: bold;" for="label_title"><?php 
echo esc_html( __( "Language", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <select name="_wporg_language" id="wpai_language">
                <option value="en" <?php 
echo ( esc_html( $_wporg_language ) == 'en' ? 'selected' : '' );
?>>English</option>
                <option value="af" <?php 
echo ( esc_html( $_wporg_language ) == 'af' ? 'selected' : '' );
?>>Afrikaans</option>
                <option value="ar" <?php 
echo ( esc_html( $_wporg_language ) == 'ar' ? 'selected' : '' );
?>>Arabic</option>
                <option value="an" <?php 
echo ( esc_html( $_wporg_language ) == 'an' ? 'selected' : '' );
?>>Armenian</option>
                <option value="bs" <?php 
echo ( esc_html( $_wporg_language ) == 'bs' ? 'selected' : '' );
?>>Bosnian</option>
                <option value="bg" <?php 
echo ( esc_html( $_wporg_language ) == 'bg' ? 'selected' : '' );
?>>Bulgarian</option>
                <option value="zh" <?php 
echo ( esc_html( $_wporg_language ) == 'zh' ? 'selected' : '' );
?>>Chinese (Simplified)</option>
                <option value="zt" <?php 
echo ( esc_html( $_wporg_language ) == 'zt' ? 'selected' : '' );
?>>Chinese (Traditional)</option>
                <option value="hr" <?php 
echo ( esc_html( $_wporg_language ) == 'hr' ? 'selected' : '' );
?>>Croatian</option>
                <option value="cs" <?php 
echo ( esc_html( $_wporg_language ) == 'cs' ? 'selected' : '' );
?>>Czech</option>
                <option value="da" <?php 
echo ( esc_html( $_wporg_language ) == 'da' ? 'selected' : '' );
?>>Danish</option>
                <option value="nl" <?php 
echo ( esc_html( $_wporg_language ) == 'nl' ? 'selected' : '' );
?>>Dutch</option>
                <option value="et" <?php 
echo ( esc_html( $_wporg_language ) == 'et' ? 'selected' : '' );
?>>Estonian</option>
                <option value="fil" <?php 
echo ( esc_html( $_wporg_language ) == 'fil' ? 'selected' : '' );
?>>Filipino</option>
                <option value="fi" <?php 
echo ( esc_html( $_wporg_language ) == 'fi' ? 'selected' : '' );
?>>Finnish</option>
                <option value="fr" <?php 
echo ( esc_html( $_wporg_language ) == 'fr' ? 'selected' : '' );
?>>French</option>
                <option value="de" <?php 
echo ( esc_html( $_wporg_language ) == 'de' ? 'selected' : '' );
?>>German</option>
                <option value="el" <?php 
echo ( esc_html( $_wporg_language ) == 'el' ? 'selected' : '' );
?>>Greek</option>
                <option value="he" <?php 
echo ( esc_html( $_wporg_language ) == 'he' ? 'selected' : '' );
?>>Hebrew</option>
                <option value="hi" <?php 
echo ( esc_html( $_wporg_language ) == 'hi' ? 'selected' : '' );
?>>Hindi</option>
                <option value="hu" <?php 
echo ( esc_html( $_wporg_language ) == 'hu' ? 'selected' : '' );
?>>Hungarian</option>
                <option value="id" <?php 
echo ( esc_html( $_wporg_language ) == 'id' ? 'selected' : '' );
?>>Indonesian</option>
                <option value="it" <?php 
echo ( esc_html( $_wporg_language ) == 'it' ? 'selected' : '' );
?>>Italian</option>
                <option value="ja" <?php 
echo ( esc_html( $_wporg_language ) == 'ja' ? 'selected' : '' );
?>>Japanese</option>
                <option value="ko" <?php 
echo ( esc_html( $_wporg_language ) == 'ko' ? 'selected' : '' );
?>>Korean</option>
                <option value="lv" <?php 
echo ( esc_html( $_wporg_language ) == 'lv' ? 'selected' : '' );
?>>Latvian</option>
                <option value="lt" <?php 
echo ( esc_html( $_wporg_language ) == 'lt' ? 'selected' : '' );
?>>Lithuanian</option>
                <option value="ms" <?php 
echo ( esc_html( $_wporg_language ) == 'ms' ? 'selected' : '' );
?>>Malay</option>
                <option value="no" <?php 
echo ( esc_html( $_wporg_language ) == 'no' ? 'selected' : '' );
?>>Norwegian</option>
                <option value="fa" <?php 
echo ( esc_html( $_wporg_language ) == 'fa' ? 'selected' : '' );
?>>Persian</option>
                <option value="pl" <?php 
echo ( esc_html( $_wporg_language ) == 'pl' ? 'selected' : '' );
?>>Polish</option>
                <option value="pt" <?php 
echo ( esc_html( $_wporg_language ) == 'pt' ? 'selected' : '' );
?>>Portuguese</option>
                <option value="ro" <?php 
echo ( esc_html( $_wporg_language ) == 'ro' ? 'selected' : '' );
?>>Romanian</option>
                <option value="ru" <?php 
echo ( esc_html( $_wporg_language ) == 'ru' ? 'selected' : '' );
?>>Russian</option>
                <option value="sr" <?php 
echo ( esc_html( $_wporg_language ) == 'sr' ? 'selected' : '' );
?>>Serbian</option>
                <option value="sk" <?php 
echo ( esc_html( $_wporg_language ) == 'sk' ? 'selected' : '' );
?>>Slovak</option>
                <option value="sl" <?php 
echo ( esc_html( $_wporg_language ) == 'sl' ? 'selected' : '' );
?>>Slovenian</option>
                <option value="es" <?php 
echo ( esc_html( $_wporg_language ) == 'es' ? 'selected' : '' );
?>>Spanish</option>
                <option value="sv" <?php 
echo ( esc_html( $_wporg_language ) == 'sv' ? 'selected' : '' );
?>>Swedish</option>
                <option value="th" <?php 
echo ( esc_html( $_wporg_language ) == 'th' ? 'selected' : '' );
?>>Thai</option>
                <option value="tr" <?php 
echo ( esc_html( $_wporg_language ) == 'tr' ? 'selected' : '' );
?>>Turkish</option>
                <option value="uk" <?php 
echo ( esc_html( $_wporg_language ) == 'uk' ? 'selected' : '' );
?>>Ukranian</option>
                <option value="vi" <?php 
echo ( esc_html( $_wporg_language ) == 'vi' ? 'selected' : '' );
?>>Vietnamese</option>
            </select>
        </td>
    <tr>
        <td><label style="font-weight: bold;" for="label_title"><?php 
echo esc_html( __( "Title", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td><input type="text" id="wpai_preview_title" rows="20" cols="20" placeholder="e.g. Artificial Intelligence" class="wpcgai_input" name="_wporg_preview_title" value="<?php 
echo esc_html( $_wporg_preview_title );
?>"></td>
    </tr>
    <?php 
?>
    <tr>
        <td><label style="font-weight: bold;" for="label_keywords"><?php 
echo esc_html( __( "Add Keywords? (Use comma to seperate keywords)", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <?php 
?>
                <input type="text" class="wpcgai_input" disabled placeholder="<?php 
echo esc_html__( 'Available in Pro', 'gpt3-ai-content-generator' );
?>">
                <?php 
?>
        </td>
    </tr>
    <tr>
        <td><label style="font-weight: bold;" for="label_keywords_bold"><?php 
echo esc_html( __( "Make Keywords Bold?", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <?php 
?>
                <input type="checkbox" disabled id="wpai_add_keywords_bold" class="wpai-content-title-input" name="_wporg_add_keywords_bold" value="0"><?php 
echo esc_html__( 'Available in Pro', 'gpt3-ai-content-generator' );
?>
                <?php 
?>
        </td>
    </tr>
    <tr>
        <td><label style="font-weight: bold;" for="label_words_to_avoid"><?php 
echo esc_html( __( "Keywords to Avoid? (Use comma to seperate keywords)", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <?php 
?>
                <input type="text" class="wpcgai_input" disabled placeholder="<?php 
echo esc_html__( 'Available in Pro', 'gpt3-ai-content-generator' );
?>">
                <?php 
?>
        </td>
    </tr>

    <tr>
        <td><label style="font-weight: bold;" for="label_title"><?php 
echo esc_html( __( "Headings?", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <select id="wpai_number_of_heading" name="_wporg_number_of_heading">
                <?php 
for ($i = 1; $i < 16; $i++) {
    echo '<option' . (( $_wporg_number_of_heading == $i ? ' selected' : '' )) . ' value="' . esc_html( $i ) . '">' . esc_html( $i ) . '</option>';
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td><label style="font-weight: bold;" for="label_title"><?php 
echo esc_html( __( "Outline Editor", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <input type="checkbox" id="wpai_modify_headings2" name="_wporg_modify_headings2" class="wpai-content-title-input"
                   value="<?php 
echo ( esc_html( $_wporg_modify_headings ) == 1 ? "1" : "0" );
?>" <?php 
echo ( esc_html( $_wporg_modify_headings ) == 1 ? "checked" : "" );
?> />

            <input type="hidden" id="wpai_modify_headings" name="_wporg_modify_headings" class="wpai-content-title-input" value="<?php 
echo ( esc_html( $_wporg_modify_headings ) == 1 ? "1" : "0" );
?>" />

            <input type="hidden" id="hfHeadings" name="hfHeadings" />
            <input type="hidden" id="is_generate_continue" name="is_generate_continue" value='0' />
        </td>
    </tr>
    <tr>
        <td><label style="font-weight: bold;" for="label_title"><?php 
echo esc_html( __( "Heading Tag", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <select name="_wporg_heading_tag" id="wpai_heading_tag">
                <option value="h1" <?php 
echo ( esc_html( $_wporg_heading_tag ) == 'h1' ? 'selected' : '' );
?>>h1</option>
                <option value="h2" <?php 
echo ( esc_html( $_wporg_heading_tag ) == 'h2' ? 'selected' : '' );
?>>h2</option>
                <option value="h3" <?php 
echo ( esc_html( $_wporg_heading_tag ) == 'h3' ? 'selected' : '' );
?>>h3</option>
                <option value="h4" <?php 
echo ( esc_html( $_wporg_heading_tag ) == 'h4' ? 'selected' : '' );
?>>h4</option>
                <option value="h5" <?php 
echo ( esc_html( $_wporg_heading_tag ) == 'h5' ? 'selected' : '' );
?>>h5</option>
                <option value="h6" <?php 
echo ( esc_html( $_wporg_heading_tag ) == 'h6' ? 'selected' : '' );
?>>h6</option>
            </select>
        </td>
    </tr>
    <tr>
        <td><label style="font-weight: bold;" for="label_style"><?php 
echo esc_html( __( "Style", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <select name="_wporg_writing_style" id="wpai_writing_style">
                <option value="infor" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'infor' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Informative', 'gpt3-ai-content-generator' );
?></option>
                <option value="acade" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'acade' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Academic', 'gpt3-ai-content-generator' );
?></option>
                <option value="analy" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'analy' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Analytical', 'gpt3-ai-content-generator' );
?></option>
                <option value="anect" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'anect' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Anecdotal', 'gpt3-ai-content-generator' );
?></option>
                <option value="argum" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'argum' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Argumentative', 'gpt3-ai-content-generator' );
?></option>
                <option value="artic" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'artic' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Articulate', 'gpt3-ai-content-generator' );
?></option>
                <option value="biogr" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'biogr' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Biographical', 'gpt3-ai-content-generator' );
?></option>
                <option value="blog" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'blog' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Blog', 'gpt3-ai-content-generator' );
?></option>
                <option value="casua" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'casua' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Casual', 'gpt3-ai-content-generator' );
?></option>
                <option value="collo" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'collo' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Colloquial', 'gpt3-ai-content-generator' );
?></option>
                <option value="compa" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'compa' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Comparative', 'gpt3-ai-content-generator' );
?></option>
                <option value="conci" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'conci' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Concise', 'gpt3-ai-content-generator' );
?></option>
                <option value="creat" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'creat' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Creative', 'gpt3-ai-content-generator' );
?></option>
                <option value="criti" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'criti' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Critical', 'gpt3-ai-content-generator' );
?></option>
                <option value="descr" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'descr' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Descriptive', 'gpt3-ai-content-generator' );
?></option>
                <option value="detai" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'detai' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Detailed', 'gpt3-ai-content-generator' );
?></option>
                <option value="dialo" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'dialo' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Dialogue', 'gpt3-ai-content-generator' );
?></option>
                <option value="direct" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'direct' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Direct', 'gpt3-ai-content-generator' );
?></option>
                <option value="drama" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'drama' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Dramatic', 'gpt3-ai-content-generator' );
?></option>
                <option value="emoti" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'emoti' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Emotional', 'gpt3-ai-content-generator' );
?></option>
                <option value="evalu" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'evalu' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Evaluative', 'gpt3-ai-content-generator' );
?></option>
                <option value="expos" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'expos' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Expository', 'gpt3-ai-content-generator' );
?></option>
                <option value="ficti" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'ficti' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Fiction', 'gpt3-ai-content-generator' );
?></option>
                <option value="histo" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'histo' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Historical', 'gpt3-ai-content-generator' );
?></option>
                <option value="journ" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'journ' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Journalistic', 'gpt3-ai-content-generator' );
?></option>
                <option value="lette" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'lette' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Letter', 'gpt3-ai-content-generator' );
?></option>
                <option value="metaph" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'metaph' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Metaphorical', 'gpt3-ai-content-generator' );
?></option>
                <option value="monol" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'monol' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Monologue', 'gpt3-ai-content-generator' );
?></option>
                <option value="narra" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'narra' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Narrative', 'gpt3-ai-content-generator' );
?></option>
                <option value="news" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'news' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'News', 'gpt3-ai-content-generator' );
?></option>
                <option value="objec" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'objec' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Objective', 'gpt3-ai-content-generator' );
?></option>
                <option value="lyric" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'lyric' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Lyrical', 'gpt3-ai-content-generator' );
?></option>
                <option value="pasto" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'pasto' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Pastoral', 'gpt3-ai-content-generator' );
?></option>
                <option value="perso" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'perso' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Personal', 'gpt3-ai-content-generator' );
?></option>
                <option value="persu" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'persu' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Persuasive', 'gpt3-ai-content-generator' );
?></option>
                <option value="poeti" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'poeti' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Poetic', 'gpt3-ai-content-generator' );
?></option>
                <option value="refle" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'refle' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Reflective', 'gpt3-ai-content-generator' );
?></option>
                <option value="rheto" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'rheto' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Rhetorical', 'gpt3-ai-content-generator' );
?></option>
                <option value="satir" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'satir' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Satirical', 'gpt3-ai-content-generator' );
?></option>
                <option value="senso" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'senso' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Sensory', 'gpt3-ai-content-generator' );
?></option>
                <option value="simpl" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'simpl' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Simple', 'gpt3-ai-content-generator' );
?></option>
                <option value="techn" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'techn' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Technical', 'gpt3-ai-content-generator' );
?></option>
                <option value="theore" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'theore' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Theoretical', 'gpt3-ai-content-generator' );
?></option>
                <option value="vivid" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'vivid' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Vivid', 'gpt3-ai-content-generator' );
?></option>
                <option value="busin" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'busin' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Business', 'gpt3-ai-content-generator' );
?></option>
                <option value="repor" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'repor' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Report', 'gpt3-ai-content-generator' );
?></option>
                <option value="resea" <?php 
echo ( esc_html( $_wporg_writing_style ) == 'resea' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Research', 'gpt3-ai-content-generator' );
?></option>
            </select>
        </td>
    <tr>
    <tr>
        <td><label style="font-weight: bold;" for="label_tone"><?php 
echo esc_html( __( "Tone", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <select name="_wporg_writing_tone" id="wpai_writing_tone">
                <option value="formal" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'formal' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Formal', 'gpt3-ai-content-generator' );
?></option>
                <option value="asser" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'asser' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Assertive', 'gpt3-ai-content-generator' );
?></option>
                <option value="authoritative" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'authoritative' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Authoritative', 'gpt3-ai-content-generator' );
?></option>
                <option value="cheer" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'cheer' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Cheerful', 'gpt3-ai-content-generator' );
?></option>
                <option value="confident" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'confident' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Confident', 'gpt3-ai-content-generator' );
?></option>
                <option value="conve" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'conve' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Conversational', 'gpt3-ai-content-generator' );
?></option>
                <option value="factual" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'factual' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Factual', 'gpt3-ai-content-generator' );
?></option>
                <option value="friendly" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'friendly' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Friendly', 'gpt3-ai-content-generator' );
?></option>
                <option value="humor" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'humor' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Humorous', 'gpt3-ai-content-generator' );
?></option>
                <option value="informal" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'informal' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Informal', 'gpt3-ai-content-generator' );
?></option>
                <option value="inspi" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'inspi' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Inspirational', 'gpt3-ai-content-generator' );
?></option>
                <option value="neutr" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'neutr' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Neutral', 'gpt3-ai-content-generator' );
?></option>
                <option value="nostalgic" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'nostalgic' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Nostalgic', 'gpt3-ai-content-generator' );
?></option>
                <option value="polite" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'polite' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Polite', 'gpt3-ai-content-generator' );
?></option>
                <option value="profe" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'profe' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Professional', 'gpt3-ai-content-generator' );
?></option>
                <option value="romantic" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'romantic' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Romantic', 'gpt3-ai-content-generator' );
?></option>
                <option value="sarca" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'sarca' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Sarcastic', 'gpt3-ai-content-generator' );
?></option>
                <option value="scien" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'scien' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Scientific', 'gpt3-ai-content-generator' );
?></option>
                <option value="sensit" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'sensit' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Sensitive', 'gpt3-ai-content-generator' );
?></option>
                <option value="serious" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'serious' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Serious', 'gpt3-ai-content-generator' );
?></option>
                <option value="sincere" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'sincere' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Sincere', 'gpt3-ai-content-generator' );
?></option>
                <option value="skept" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'skept' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Skeptical', 'gpt3-ai-content-generator' );
?></option>
                <option value="suspenseful" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'suspenseful' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Suspenseful', 'gpt3-ai-content-generator' );
?></option>
                <option value="sympathetic" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'sympathetic' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Sympathetic', 'gpt3-ai-content-generator' );
?></option>
                <option value="curio" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'curio' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Curious', 'gpt3-ai-content-generator' );
?></option>
                <option value="disap" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'disap' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Disappointed', 'gpt3-ai-content-generator' );
?></option>
                <option value="encou" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'encou' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Encouraging', 'gpt3-ai-content-generator' );
?></option>
                <option value="optim" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'optim' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Optimistic', 'gpt3-ai-content-generator' );
?></option>
                <option value="surpr" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'surpr' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Surprised', 'gpt3-ai-content-generator' );
?></option>
                <option value="worry" <?php 
echo ( esc_html( $_wporg_writing_tone ) == 'worry' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Worried', 'gpt3-ai-content-generator' );
?></option>
            </select>
        </td>
    <tr>
    <tr>
        <td><label style="font-weight: bold;" for="label_img"><?php 
echo esc_html( __( "Image Source", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <select class="regular-text" id="wpaicg_image_source" name="wpaicg_image_source" >
                <option value=""><?php 
echo esc_html__( 'None', 'gpt3-ai-content-generator' );
?></option>
                <option<?php 
echo ( $wpaicg_image_source == 'dalle' || $wpaicg_image_source == 'pexels' && empty( $wpaicg_pexels_api ) ? ' selected' : '' );
?> value="dalle"><?php 
echo esc_html__( 'DALL-E', 'gpt3-ai-content-generator' );
?></option>
                <option<?php 
echo ( !empty( $wpaicg_pexels_api ) && $wpaicg_image_source == 'pexels' ? ' selected' : '' );
echo ( empty( $wpaicg_pexels_api ) ? ' disabled' : '' );
?> value="pexels"><?php 
echo esc_html__( 'Pexels', 'gpt3-ai-content-generator' );
?></option>
                <option<?php 
echo ( !empty( $wpaicg_pixabay_api ) && $wpaicg_image_source == 'pixabay' ? ' selected' : '' );
echo ( empty( $wpaicg_pixabay_api ) ? ' disabled' : '' );
?> value="pixabay"><?php 
echo esc_html__( 'Pixabay', 'gpt3-ai-content-generator' );
?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td><label style="font-weight: bold;" for="label_img"><?php 
echo esc_html( __( "Featured Image Source", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <select class="regular-text" id="wpaicg_featured_image_source" name="wpaicg_featured_image_source" >
                <option value=""><?php 
echo esc_html__( 'None', 'gpt3-ai-content-generator' );
?></option>
                <option<?php 
echo ( $wpaicg_featured_image_source == 'dalle' || $wpaicg_featured_image_source == 'pexels' && empty( $wpaicg_pexels_api ) ? ' selected' : '' );
?> value="dalle"><?php 
echo esc_html__( 'DALL-E', 'gpt3-ai-content-generator' );
?></option>
                <option<?php 
echo ( !empty( $wpaicg_pexels_api ) && $wpaicg_featured_image_source == 'pexels' ? ' selected' : '' );
echo ( empty( $wpaicg_pexels_api ) ? ' disabled' : '' );
?> value="pexels"><?php 
echo esc_html__( 'Pexels', 'gpt3-ai-content-generator' );
?></option>
                <option<?php 
echo ( !empty( $wpaicg_pixabay_api ) && $wpaicg_featured_image_source == 'pixabay' ? ' selected' : '' );
echo ( empty( $wpaicg_pixabay_api ) ? ' disabled' : '' );
?> value="pixabay"><?php 
echo esc_html__( 'Pixabay', 'gpt3-ai-content-generator' );
?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td><b><u><?php 
echo esc_html__( 'DALL-E', 'gpt3-ai-content-generator' );
?></b></u></td>
    </tr>
    <tr>
        <td><label style="font-weight: bold;" for="_wporg_img_size"><?php 
echo esc_html( __( "Image Size?", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <select class="regular-text" id="_wporg_img_size" name="_wporg_img_size" >
                <option value="256x256"<?php 
echo ( esc_html( $_wporg_img_size ) == '256x256' ? ' selected' : '' );
?>><?php 
echo esc_html__( 'Small (256x256)', 'gpt3-ai-content-generator' );
?></option>
                <option value="512x512"<?php 
echo ( esc_html( $_wporg_img_size ) == '512x512' ? ' selected' : '' );
?>><?php 
echo esc_html__( 'Medium (512x512)', 'gpt3-ai-content-generator' );
?></option>
                <option value="1024x1024"<?php 
echo ( esc_html( $_wporg_img_size ) == '1024x1024' ? ' selected' : '' );
?>><?php 
echo esc_html__( 'Big (1024x1024)', 'gpt3-ai-content-generator' );
?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td><label style="font-weight: bold;" for="_wporg_img_style"><?php 
echo esc_html( __( "Image Style", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <select class="regular-text" id="_wporg_img_style" name="_wporg_img_style" >
                <option value=""><?php 
echo esc_html__( 'None', 'gpt3-ai-content-generator' );
?></option>
                <option<?php 
echo ( esc_html( $_wporg_img_style ) == 'abstract' ? ' selected' : '' );
?> value="abstract"><?php 
echo esc_html__( 'Abstract', 'gpt3-ai-content-generator' );
?></option>
                <option<?php 
echo ( esc_html( $_wporg_img_style ) == 'modern' ? ' selected' : '' );
?> value="modern"><?php 
echo esc_html__( 'Modern', 'gpt3-ai-content-generator' );
?></option>
                <option<?php 
echo ( esc_html( $_wporg_img_style ) == 'impressionist' ? ' selected' : '' );
?> value="impressionist"><?php 
echo esc_html__( 'Impressionist', 'gpt3-ai-content-generator' );
?></option>
                <option<?php 
echo ( esc_html( $_wporg_img_style ) == 'popart' ? ' selected' : '' );
?> value="popart"><?php 
echo esc_html__( 'Pop Art', 'gpt3-ai-content-generator' );
?></option>
                <option<?php 
echo ( esc_html( $_wporg_img_style ) == 'cubism' ? ' selected' : '' );
?> value="cubism"><?php 
echo esc_html__( 'Cubism', 'gpt3-ai-content-generator' );
?></option>
                <option<?php 
echo ( esc_html( $_wporg_img_style ) == 'surrealism' ? ' selected' : '' );
?> value="surrealism"><?php 
echo esc_html__( 'Surrealism', 'gpt3-ai-content-generator' );
?></option>
                <option<?php 
echo ( esc_html( $_wporg_img_style ) == 'contemporary' ? ' selected' : '' );
?> value="contemporary"><?php 
echo esc_html__( 'Contemporary', 'gpt3-ai-content-generator' );
?></option>
                <option<?php 
echo ( esc_html( $_wporg_img_style ) == 'cantasy' ? ' selected' : '' );
?> value="cantasy"><?php 
echo esc_html__( 'Fantasy', 'gpt3-ai-content-generator' );
?></option>
                <option<?php 
echo ( esc_html( $_wporg_img_style ) == 'graffiti' ? ' selected' : '' );
?> value="graffiti"><?php 
echo esc_html__( 'Graffiti', 'gpt3-ai-content-generator' );
?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <?php 
$wpaicg_art_file = WPAICG_PLUGIN_DIR . 'admin/data/art.json';
$wpaicg_painter_data = file_get_contents( $wpaicg_art_file );
$wpaicg_painter_data = json_decode( $wpaicg_painter_data, true );
$wpaicg_style_data = file_get_contents( $wpaicg_art_file );
$wpaicg_style_data = json_decode( $wpaicg_style_data, true );
$wpaicg_photo_file = WPAICG_PLUGIN_DIR . 'admin/data/photo.json';
$wpaicg_photo_data = file_get_contents( $wpaicg_photo_file );
$wpaicg_photo_data = json_decode( $wpaicg_photo_data, true );
?>
            <div class="wpaicg_more_image_settings" style="display: none">
                <div class="mb-5">
                    <label for="artist" class="wpaicg-form-label"><?php 
echo esc_html__( 'Artist', 'gpt3-ai-content-generator' );
?>:</label>
                    <select class="regular-text" name="wpaicg_custom_image_settings[artist]" id="artist">
                        <?php 
foreach ( $wpaicg_painter_data['painters'] as $key => $value ) {
    echo '<option' . (( isset( $wpaicg_custom_image_settings['artist'] ) && $wpaicg_custom_image_settings['artist'] == $value || (!isset( $wpaicg_custom_image_settings['artist'] ) && $value) == 'None' ? ' selected' : '' )) . ' value="' . esc_html( $value ) . '">' . esc_html( $value ) . '</option>';
}
?>
                    </select>
                </div>
                <div class="mb-5">
                    <?php 
echo '<label for="photography_style" class="wpaicg-form-label">' . esc_html__( 'Photography', 'gpt3-ai-content-generator' ) . ':</label>';
echo '<select class="regular-text" name="wpaicg_custom_image_settings[photography_style]" id="photography_style">';
foreach ( $wpaicg_photo_data['photography_style'] as $key => $value ) {
    echo '<option' . (( isset( $wpaicg_custom_image_settings['photography_style'] ) && $wpaicg_custom_image_settings['photography_style'] == $value || !isset( $wpaicg_custom_image_settings['photography_style'] ) && $value == 'Landscape' ? ' selected' : '' )) . ' value="' . esc_html( $value ) . '">' . esc_html( $value ) . '</option>';
}
echo '</select>';
?>
                </div>
                <div class="mb-5">
                    <?php 
echo '<label for="lighting" class="wpaicg-form-label">' . esc_html__( 'Lighting', 'gpt3-ai-content-generator' ) . ':</label>';
echo '<select class="regular-text" name="wpaicg_custom_image_settings[lighting]" id="lighting">';
foreach ( $wpaicg_photo_data['lighting'] as $key => $value ) {
    echo '<option' . (( isset( $wpaicg_custom_image_settings['lighting'] ) && $wpaicg_custom_image_settings['lighting'] == $value ? ' selected' : '' )) . ' value="' . esc_html( $value ) . '">' . esc_html( $value ) . '</option>';
}
echo '</select>';
?>
                </div>
                <div class="mb-5">
                    <?php 
echo '<label for="subject" class="wpaicg-form-label">' . esc_html__( 'Subject', 'gpt3-ai-content-generator' ) . ':</label>';
echo '<select class="regular-text" name="wpaicg_custom_image_settings[subject]" id="subject">';
foreach ( $wpaicg_photo_data['subject'] as $key => $value ) {
    echo '<option' . (( isset( $wpaicg_custom_image_settings['subject'] ) && $wpaicg_custom_image_settings['subject'] == $value || !isset( $wpaicg_custom_image_settings['subject'] ) && $value == 'None' ? ' selected' : '' )) . ' value="' . esc_html( $value ) . '">' . esc_html( $value ) . '</option>';
}
echo '</select>';
?>
                </div>
                <div class="mb-5">
                    <?php 
echo '<label for="camera_settings" class="wpaicg-form-label">' . esc_html__( 'Camera', 'gpt3-ai-content-generator' ) . ':</label>';
echo '<select class="regular-text" name="wpaicg_custom_image_settings[camera_settings]" id="camera_settings">';
foreach ( $wpaicg_photo_data['camera_settings'] as $key => $value ) {
    echo '<option' . (( isset( $wpaicg_custom_image_settings['camera_settings'] ) && $wpaicg_custom_image_settings['camera_settings'] == $value ? ' selected' : '' )) . ' value="' . esc_html( $value ) . '">' . esc_html( $value ) . '</option>';
}
echo '</select>';
?>
                </div>
                <div class="mb-5">
                    <?php 
echo '<label for="composition" class="wpaicg-form-label">' . esc_html__( 'Composition', 'gpt3-ai-content-generator' ) . ':</label>';
echo '<select class="regular-text" name="wpaicg_custom_image_settings[composition]" id="composition">';
foreach ( $wpaicg_photo_data['composition'] as $key => $value ) {
    echo '<option' . (( isset( $wpaicg_custom_image_settings['composition'] ) && $wpaicg_custom_image_settings['composition'] == $value ? ' selected' : '' )) . ' value="' . esc_html( $value ) . '">' . esc_html( $value ) . '</option>';
}
echo '</select>';
?>
                </div>
                <div class="mb-5">
                    <?php 
echo '<label for="resolution" class="wpaicg-form-label">' . esc_html__( 'Resolution', 'gpt3-ai-content-generator' ) . ':</label>';
echo '<select class="regular-text" name="wpaicg_custom_image_settings[resolution]" id="resolution">';
foreach ( $wpaicg_photo_data['resolution'] as $key => $value ) {
    echo '<option' . (( isset( $wpaicg_custom_image_settings['resolution'] ) && $wpaicg_custom_image_settings['resolution'] == $value ? ' selected' : '' )) . ' value="' . esc_html( $value ) . '">' . esc_html( $value ) . '</option>';
}
echo '</select>';
?>
                </div>
                <div class="mb-5">
                    <?php 
echo '<label for="color" class="wpaicg-form-label">' . esc_html__( 'Color', 'gpt3-ai-content-generator' ) . ':</label>';
echo '<select class="regular-text" name="wpaicg_custom_image_settings[color]" id="color">';
foreach ( $wpaicg_photo_data['color'] as $key => $value ) {
    echo '<option' . (( isset( $wpaicg_custom_image_settings['color'] ) && $wpaicg_custom_image_settings['color'] == $value ? ' selected' : '' )) . ' value="' . esc_html( $value ) . '">' . esc_html( $value ) . '</option>';
}
echo '</select>';
?>
                </div>
                <div class="mb-5">
                    <?php 
echo '<label for="special_effects" class="wpaicg-form-label">' . esc_html__( 'Special Effects', 'gpt3-ai-content-generator' ) . ':</label>';
echo '<select class="regular-text" name="wpaicg_custom_image_settings[special_effects]" id="special_effects">';
foreach ( $wpaicg_photo_data['special_effects'] as $key => $value ) {
    echo '<option' . (( isset( $wpaicg_custom_image_settings['special_effects'] ) && $wpaicg_custom_image_settings['special_effects'] == $value ? ' selected' : '' )) . ' value="' . esc_html( $value ) . '">' . esc_html( $value ) . '</option>';
}
echo '</select>';
?>
                </div>
            </div>
            <div class="mb-5">
                <a href="javascript:void(0)" class="wpaicg_show_image_settings">[<?php 
echo esc_html__( '+ More Settings', 'gpt3-ai-content-generator' );
?>]</a>
            </div>
            <script>
                jQuery(document).ready(function ($){
                    $('.wpaicg_show_image_settings').click(function (){
                        $(this).toggleClass('wpaig_opened');
                        $('.wpaicg_more_image_settings').slideToggle();
                        if($(this).hasClass('wpaig_opened')){
                            $(this).html('[<?php 
echo esc_html__( '- Hide Settings', 'gpt3-ai-content-generator' );
?>]');
                        }
                        else{
                            $(this).html('[<?php 
echo esc_html__( '+ More Settings', 'gpt3-ai-content-generator' );
?>]');
                        }
                    })
                })
            </script>
        </td>
    </tr>
    <tr>
    <td><b><u><?php 
echo esc_html__( 'Pexels', 'gpt3-ai-content-generator' );
?></b></u></td>
    </tr>
    <tr>
        <td>
            <label class="wpaicg-form-label"><?php 
echo esc_html__( 'Orientation', 'gpt3-ai-content-generator' );
?>:</label>
            <select class="regular-text" id="wpaicg_pexels_orientation" name="wpaicg_pexels_orientation" >
                <option value=""><?php 
echo esc_html__( 'None', 'gpt3-ai-content-generator' );
?></option>
                <option<?php 
echo ( strtolower( $wpaicg_pexels_orientation ) == 'landscape' ? ' selected' : '' );
?> value="landscape"><?php 
echo esc_html__( 'Landscape', 'gpt3-ai-content-generator' );
?></option>
                <option<?php 
echo ( strtolower( $wpaicg_pexels_orientation ) == 'portrait' ? ' selected' : '' );
?> value="portrait"><?php 
echo esc_html__( 'Portrait', 'gpt3-ai-content-generator' );
?></option>
                <option<?php 
echo ( strtolower( $wpaicg_pexels_orientation ) == 'square' ? ' selected' : '' );
?> value="square"><?php 
echo esc_html__( 'Square', 'gpt3-ai-content-generator' );
?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <div class="wpaicg-mb-10">
                <label class="wpaicg-form-label"><?php 
echo esc_html__( 'Size', 'gpt3-ai-content-generator' );
?>:</label>
                <select class="regular-text" id="wpaicg_pexels_size" name="wpaicg_pexels_size" >
                    <option value=""><?php 
echo esc_html__( 'None', 'gpt3-ai-content-generator' );
?></option>
                    <option<?php 
echo ( strtolower( $wpaicg_pexels_size ) == 'large' ? ' selected' : '' );
?> value="large"><?php 
echo esc_html__( 'Large', 'gpt3-ai-content-generator' );
?></option>
                    <option<?php 
echo ( strtolower( $wpaicg_pexels_size ) == 'medium' ? ' selected' : '' );
?> value="medium"><?php 
echo esc_html__( 'Medium', 'gpt3-ai-content-generator' );
?></option>
                    <option<?php 
echo ( strtolower( $wpaicg_pexels_size ) == 'small' ? ' selected' : '' );
?> value="small"><?php 
echo esc_html__( 'Small', 'gpt3-ai-content-generator' );
?></option>
                </select>
            </div>
            <div class="wpaicg-mb-10">
                <label class="wpaicg-form-label"><?php 
echo esc_html__( 'Use Keyword [Beta]', 'gpt3-ai-content-generator' );
?>:</label>
                <input<?php 
echo ( $wpaicg_pexels_enable_prompt ? ' checked' : '' );
?> type="checkbox" name="wpaicg_pexels_enable_prompt" value="1" id="wpaicg_pexels_enable_prompt">
            </div>
            <div class="wpaicg-mb-10 wpaicg_pexels_custom_prompt" style="display:none">
                <label style="vertical-align:top" class="wpaicg-form-label">
                    <?php 
echo esc_html__( 'Custom Prompt', 'gpt3-ai-content-generator' );
?>:
                    <small style="display: block;font-weight: normal">
                        <?php 
// --- FIX: Add translators comment ---
// translators: %s: The placeholder string "[title]" wrapped in <code> tags.
echo sprintf( esc_html__( 'Ensure %s is included in your prompt.', 'gpt3-ai-content-generator' ), '<code>[title]</code>' );
?>
                    </small>
                </label>
                <textarea id="wpaicg_pexels_custom_prompt" rows="5" name="wpaicg_pexels_custom_prompt"><?php 
echo esc_html( $wpaicg_pexels_custom_prompt );
?></textarea>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <b><u><?php 
echo esc_html__( 'Pixabay', 'gpt3-ai-content-generator' );
?></b></u>
            <div class="wpaicg-mb-10">
                <label class="wpaicg-form-label"><?php 
echo esc_html__( 'Language', 'gpt3-ai-content-generator' );
?>:</label>
                <select class="regular-text" name="wpaicg_pixabay_language" id="wpaicg_pixabay_language">
                    <?php 
foreach ( \WPAICG\WPAICG_Generator::get_instance()->pixabay_languages as $key => $pixabay_language ) {
    echo '<option' . (( $wpaicg_pixabay_language == $key ? ' selected' : '' )) . ' value="' . esc_html( $key ) . '">' . esc_html( $pixabay_language ) . '</option>';
}
?>
                </select>
            </div>
            <div class="wpaicg-mb-10">
                <label class="wpaicg-form-label"><?php 
echo esc_html__( 'Image Type', 'gpt3-ai-content-generator' );
?>:</label>
                <select class="regular-text" name="wpaicg_pixabay_type" id="wpaicg_pixabay_type">
                    <option<?php 
echo ( $wpaicg_pixabay_type == 'all' ? ' selected' : '' );
?> value="all"><?php 
echo esc_html__( 'All', 'gpt3-ai-content-generator' );
?></option>
                    <option<?php 
echo ( $wpaicg_pixabay_type == 'photo' ? ' selected' : '' );
?> value="photo"><?php 
echo esc_html__( 'Photo', 'gpt3-ai-content-generator' );
?></option>
                    <option<?php 
echo ( $wpaicg_pixabay_type == 'illustration' ? ' selected' : '' );
?> value="illustration"><?php 
echo esc_html__( 'Illustration', 'gpt3-ai-content-generator' );
?></option>
                    <option<?php 
echo ( $wpaicg_pixabay_type == 'vector' ? ' selected' : '' );
?> value="vector"><?php 
echo esc_html__( 'Vector', 'gpt3-ai-content-generator' );
?></option>
                </select>
            </div>
            <div class="wpaicg-mb-10">
                <label class="wpaicg-form-label"><?php 
echo esc_html__( 'Orientation', 'gpt3-ai-content-generator' );
?>:</label>
                <select class="regular-text" name="wpaicg_pixabay_orientation" id="wpaicg_pixabay_orientation" >
                    <option<?php 
echo ( $wpaicg_pixabay_orientation == 'all' ? ' selected' : '' );
?> value="all"><?php 
echo esc_html__( 'All', 'gpt3-ai-content-generator' );
?></option>
                    <option<?php 
echo ( $wpaicg_pixabay_orientation == 'horizontal' ? ' selected' : '' );
?> value="horizontal"><?php 
echo esc_html__( 'Horizontal', 'gpt3-ai-content-generator' );
?></option>
                    <option<?php 
echo ( $wpaicg_pixabay_orientation == 'vertical' ? ' selected' : '' );
?> value="vertical"><?php 
echo esc_html__( 'Vertical', 'gpt3-ai-content-generator' );
?></option>
                </select>
            </div>
            <div class="wpaicg-mb-10">
                <label class="wpaicg-form-label"><?php 
echo esc_html__( 'Order', 'gpt3-ai-content-generator' );
?>:</label>
                <select class="regular-text" name="wpaicg_pixabay_order" id="wpaicg_pixabay_order">
                    <option<?php 
echo ( $wpaicg_pixabay_order == 'popular' ? ' selected' : '' );
?> value="popular"><?php 
echo esc_html__( 'Popular', 'gpt3-ai-content-generator' );
?></option>
                    <option<?php 
echo ( $wpaicg_pixabay_order == 'latest' ? ' selected' : '' );
?> value="latest"><?php 
echo esc_html__( 'Latest', 'gpt3-ai-content-generator' );
?></option>
                </select>
            </div>
            <div class="wpaicg-mb-10">
                <label class="wpaicg-form-label"><?php 
echo esc_html__( 'Use Keyword [Beta]', 'gpt3-ai-content-generator' );
?>:</label>
                <input<?php 
echo ( $wpaicg_pixabay_enable_prompt ? ' checked' : '' );
?> type="checkbox" name="wpaicg_pixabay_enable_prompt" value="1" id="wpaicg_pixabay_enable_prompt">
            </div>
            <div class="wpaicg-mb-10 wpaicg_pixabay_custom_prompt" style="display:none">
                <label style="vertical-align:top" class="wpaicg-form-label">
                    <?php 
echo esc_html__( 'Custom Prompt', 'gpt3-ai-content-generator' );
?>:
                    <small style="display: block;font-weight: normal">
                        <?php 
// --- FIX: Add translators comment ---
// translators: %s: The placeholder string "[title]" wrapped in <code> tags.
echo sprintf( esc_html__( 'Ensure %s is included in your prompt.', 'gpt3-ai-content-generator' ), '<code>[title]</code>' );
?>
                    </small>
                </label>
                <textarea id="wpaicg_pixabay_custom_prompt" rows="5" name="wpaicg_pixabay_custom_prompt"><?php 
echo esc_html( $wpaicg_pixabay_custom_prompt );
?></textarea>
            </div>
        </td>
    </tr>

    <tr>
        <td><label style="font-weight: bold;" for="label_tagline"><?php 
echo esc_html( __( "Add Tagline?", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <input type="checkbox" id="wpai_add_tagline2"  name="_wporg_add_tagline2" class="wpai-content-title-input"
                   value="<?php 
echo ( esc_html( $_wporg_add_tagline ) == 1 ? "1" : "0" );
?>" <?php 
echo ( esc_html( $_wporg_add_tagline ) == 1 ? "checked" : "" );
?> />
            <input type="hidden" id="wpai_add_tagline" name="_wporg_add_tagline" class="wpai-content-title-input" value="<?php 
echo ( esc_html( $_wporg_add_tagline ) == 1 ? "1" : "0" );
?>" />
        </td>
    </tr>
    <tr>
        <td><label style="font-weight: bold;" for="label_intro"><?php 
echo esc_html( __( "Add Introduction?", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <input type="checkbox" id="wpai_add_intro2" name="_wporg_add_intro2" class="wpai-content-title-input"
                   value="<?php 
echo ( esc_html( $_wporg_add_intro ) == 1 ? "1" : "0" );
?>" <?php 
echo ( esc_html( $_wporg_add_intro ) == 1 ? "checked" : "" );
?> />
            <input type="hidden" id="wpai_add_intro" name="_wporg_add_intro" class="wpai-content-title-input"
                   value="<?php 
echo ( esc_html( $_wporg_add_intro ) == 1 ? "1" : "0" );
?>" />
        </td>
    </tr>
    <tr>
        <td>
            <label class="wpaicg-form-label" for="wpaicg_intro_title_tag"><?php 
echo esc_html( __( "Intro Title Tag", "gpt3-ai-content-generator" ) );
?></label>
        </td>
    </tr>
    <tr>
        <td>
            <select name="wpaicg_intro_title_tag" id="wpaicg_intro_title_tag">
                <option value="h1" <?php 
echo ( $wpaicg_intro_title_tag == 'h1' ? 'selected' : '' );
?>>H1</option>
                <option value="h2" <?php 
echo ( $wpaicg_intro_title_tag == 'h2' ? 'selected' : '' );
?>>H2</option>
                <option value="h3" <?php 
echo ( $wpaicg_intro_title_tag == 'h3' ? 'selected' : '' );
?>>H3</option>
                <option value="h4" <?php 
echo ( $wpaicg_intro_title_tag == 'h4' ? 'selected' : '' );
?>>H4</option>
                <option value="h5" <?php 
echo ( $wpaicg_intro_title_tag == 'h5' ? 'selected' : '' );
?>>H5</option>
                <option value="h6" <?php 
echo ( $wpaicg_intro_title_tag == 'h6' ? 'selected' : '' );
?>>H6</option>
            </select>
        </td>
    </tr>
    <tr>
        <td><label class="wpaicg-form-label"><?php 
echo esc_html( __( "Hide Introduction Title", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td><input id="wpaicg_hide_introduction" type="checkbox" name="wpaicg_hide_introduction" value="1"<?php 
echo ( $wpaicg_hide_introduction ? " checked" : "" );
?>/></td>
    </tr>
    <tr> <!-- add text PREMIUM FEATURES -->
        <td><label style="font-weight: bold;" for="label_faq"><?php 
echo esc_html( __( "Add Q&A?", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <?php 
?>
                <input type="checkbox" value="0" disabled><?php 
echo esc_html__( 'Available in Pro', 'gpt3-ai-content-generator' );
?>
                <?php 
?>

        </td>

    </tr>
    <?php 
?>
    <tr>
        <td><label style="font-weight: bold;" for="label_conclusion"><?php 
echo esc_html( __( "Add Conclusion?", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <input type="checkbox" id="wpai_add_conclusion2" name="_wporg_add_conclusion2" class="wpai-content-title-input"
                   value="<?php 
echo ( esc_html( $_wporg_add_conclusion ) == 1 ? "1" : "0" );
?>" <?php 
echo ( esc_html( $_wporg_add_conclusion ) == 1 ? "checked" : "" );
?> />
            <input type="hidden" id="wpai_add_conclusion" name="_wporg_add_conclusion" class="wpai-content-title-input" value="<?php 
echo ( esc_html( $_wporg_add_conclusion ) == 1 ? "1" : "0" );
?>" />
        </td>
    </tr>
    <tr>
        <!-- wpaicg_conclusion_title_tag -->
        <td>
            <label class="wpaicg-form-label" for="wpaicg_conclusion_title_tag"><?php 
echo esc_html( __( "Conclusion Title Tag", "gpt3-ai-content-generator" ) );
?></label>
        </td>
    </tr>
    <tr>
        <td>
            <select name="wpaicg_conclusion_title_tag" id="wpaicg_conclusion_title_tag">
                <option value="h1" <?php 
echo ( $wpaicg_conclusion_title_tag == 'h1' ? 'selected' : '' );
?>>H1</option>
                <option value="h2" <?php 
echo ( $wpaicg_conclusion_title_tag == 'h2' ? 'selected' : '' );
?>>H2</option>
                <option value="h3" <?php 
echo ( $wpaicg_conclusion_title_tag == 'h3' ? 'selected' : '' );
?>>H3</option>
                <option value="h4" <?php 
echo ( $wpaicg_conclusion_title_tag == 'h4' ? 'selected' : '' );
?>>H4</option>
                <option value="h5" <?php 
echo ( $wpaicg_conclusion_title_tag == 'h5' ? 'selected' : '' );
?>>H5</option>
                <option value="h6" <?php 
echo ( $wpaicg_conclusion_title_tag == 'h6' ? 'selected' : '' );
?>>H6</option>
            </select>
        </td>
    </tr>
    <tr>
        <td><label class="wpaicg-form-label"><?php 
echo esc_html( __( "Hide Conclusion Title", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td><input type="checkbox" id="wpaicg_hide_conclusion" name="wpaicg_hide_conclusion" value="1"<?php 
echo ( $wpaicg_hide_conclusion ? " checked" : "" );
?>/></td>
    </tr>
    <tr>
        <td><label style="font-weight: bold;" for="label_anchor_text"><?php 
echo esc_html( __( "Anchor Text?", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <input type="text" id="wpai_anchor_text" placeholder="e.g. battery life" class="wpcgai_input" name="_wporg_anchor_text" value="<?php 
echo esc_html( $_wporg_anchor_text );
?>">
        </td>
    </tr>
    <tr>
        <td><label style="font-weight: bold;" for="label_target_url"><?php 
echo esc_html( __( "Target URL?", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <input type="url" id="wpai_target_url" placeholder="https://..." class="wpcgai_input" name="_wporg_target_url" value="<?php 
echo esc_html( $_wporg_target_url );
?>">
        </td>
    </tr>
    <tr>
        <td><label style="font-weight: bold;" for="label_cta"><?php 
echo esc_html( __( "Add Call-to-Action? Enter target URL.", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <input type="url" id="wpai_target_url_cta" placeholder="https://..." class="wpcgai_input" name="_wporg_target_url_cta" value="<?php 
echo esc_html( $_wporg_target_url_cta );
?>">
        </td>
    </tr>
    <tr>
        <td><label style="font-weight: bold;" for="label_cta_pos"><?php 
echo esc_html( __( "Call-to-Action Position?", "gpt3-ai-content-generator" ) );
?></label></td>
    </tr>
    <tr>
        <td>
            <select name="_wporg_cta_pos" id="wpai_cta_pos">
                <option value="beg" <?php 
echo ( esc_html( $_wporg_cta_pos ) == 'beg' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'Beginning', 'gpt3-ai-content-generator' );
?></option>
                <option value="end" <?php 
echo ( esc_html( $_wporg_cta_pos ) == 'end' ? 'selected' : '' );
?>><?php 
echo esc_html__( 'End', 'gpt3-ai-content-generator' );
?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <label class="wpaicg-form-label" for="wpaicg_toc"><?php 
echo esc_html( __( "Table of Content?", "gpt3-ai-content-generator" ) );
?></label>
        </td>
    </tr>
    <tr>
        <td>
            <input<?php 
echo ( $wpaicg_toc ? ' checked' : '' );
?> type="checkbox" name="wpaicg_toc" id="wpaicg_toc" value="1" />
        </td>
    </tr>
    <tr>
        <td>
            <label class="wpaicg-form-label" for="wpaicg_toc_title"><?php 
echo esc_html( __( "ToC Title", "gpt3-ai-content-generator" ) );
?></label>
        </td>
    </tr>
    <tr>
        <td>
            <input type="text" name="wpaicg_toc_title" id="wpaicg_toc_title" class="regular-text" value="<?php 
echo esc_html( $wpaicg_toc_title );
?>" />
        </td>
    </tr>
    <tr>
        <td>
            <label class="wpaicg-form-label" for="wpaicg_toc_title_tag"><?php 
echo esc_html( __( "ToC Title Tag", "gpt3-ai-content-generator" ) );
?></label>
        </td>
    </tr>
    <tr>
        <td>
            <select name="wpaicg_toc_title_tag" id="wpaicg_toc_title_tag">
                <option value="h1" <?php 
echo ( $wpaicg_toc_title_tag == 'h1' ? 'selected' : '' );
?>>H1</option>
                <option value="h2" <?php 
echo ( $wpaicg_toc_title_tag == 'h2' ? 'selected' : '' );
?>>H2</option>
                <option value="h3" <?php 
echo ( $wpaicg_toc_title_tag == 'h3' ? 'selected' : '' );
?>>H3</option>
                <option value="h4" <?php 
echo ( $wpaicg_toc_title_tag == 'h4' ? 'selected' : '' );
?>>H4</option>
                <option value="h5" <?php 
echo ( $wpaicg_toc_title_tag == 'h5' ? 'selected' : '' );
?>>H5</option>
                <option value="h6" <?php 
echo ( $wpaicg_toc_title_tag == 'h6' ? 'selected' : '' );
?>>H6</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>
            <label class="wpaicg-form-label" for="wpaicg_seo_meta_desc"><?php 
echo esc_html( __( "Meta Description", "gpt3-ai-content-generator" ) );
?></label>
        </td>
    </tr>
    <tr>
        <td>
            <input<?php 
echo ( $_wpaicg_seo_meta_desc ? ' checked' : '' );
?> type="checkbox" name="wpaicg_seo_meta_desc" id="wpaicg_seo_meta_desc" class="wpai-content-title-input" value="1" />
        </td>
    </tr>
    <tr>
        <td>
            <label class="wpaicg-form-label" for="wpaicg_seo_meta_desc"><?php 
echo esc_html( __( "Tags", "gpt3-ai-content-generator" ) );
?></label>
        </td>
    </tr>
    <tr>
        <td>
            <input style="width: 100%;" type="text" name="wpaicg_post_tags" id="wpaicg_post_tags" class="wpcgai_input" value="<?php 
echo esc_html( $_wpaicg_post_tags );
?>" />
            <p class="wpaicg-help-text"><?php 
echo esc_html__( '(Use comma to seperate tags)', 'gpt3-ai-content-generator' );
?></p>
        </td>
    </tr>
    <tr>
        <td><label class="wpaicg-form-label">Custom Prompt</label></td>
    </tr>
    <tr>
        <td>
            <div class="mb-5">
                <label><input<?php 
echo ( isset( $wpaicg_custom_prompt_enable ) && $wpaicg_custom_prompt_enable ? ' checked' : '' );
?> type="checkbox" class="wpaicg_meta_custom_prompt_enable" name="wpaicg_custom_prompt_enable">&nbsp;Enable</label>
            </div>
            <div class="wpaicg_meta_custom_prompt_box" style="<?php 
echo ( isset( $wpaicg_custom_prompt_enable ) && $wpaicg_custom_prompt_enable ? '' : 'display:none' );
?>">
                <label><?php 
echo esc_html__( 'Custom Prompt', 'gpt3-ai-content-generator' );
?></label>
                <textarea rows="20" class="wpaicg_meta_custom_prompt" name="wpaicg_custom_prompt"><?php 
echo esc_html( $wpaicg_custom_prompt );
?></textarea>
                <?php 
if ( \WPAICG\wpaicg_util_core()->wpaicg_is_pro() ) {
    ?>
                                        <div>
                        <?php 
    // --- FIX: Add translators comment and use ordered placeholders ---
    // translators: %1$s: Placeholder [title]. %2$s: Placeholder [keywords_to_include]. %3$s: Placeholder [keywords_to_avoid]. All wrapped in <code> tags.
    echo sprintf(
        esc_html__( 'Make sure to include %1$s in your prompt. You can also incorporate %2$s and %3$s to further customize your prompt.', 'gpt3-ai-content-generator' ),
        '<code>[title]</code>',
        // Corresponds to %1$s
        '<code>[keywords_to_include]</code>',
        // Corresponds to %2$s
        '<code>[keywords_to_avoid]</code>'
    );
    // --- END FIX ---
    ?>
                    </div>
                <?php 
} else {
    ?>
                                        <div>
                        <?php 
    // --- FIX: Add translators comment ---
    // translators: %s: The placeholder string "[title]" wrapped in <code> tags.
    echo sprintf( esc_html__( 'Ensure %s is included in your prompt.', 'gpt3-ai-content-generator' ), '<code>[title]</code>' );
    ?>
                    </div>
                <?php 
}
?>
                <button style="color: #fff;background: #df0707;border-color: #df0707;" data-prompt="<?php 
echo esc_html( \WPAICG\WPAICG_Custom_Prompt::get_instance()->wpaicg_default_custom_prompt );
?>" class="button wpaicg_meta_custom_prompt_reset" type="button"><?php 
echo esc_html__( 'Reset', 'gpt3-ai-content-generator' );
?></button>
                <div class="wpaicg_meta_custom_prompt_auto_error"></div>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <?php 
$wpaicg_post_excerpt = get_post_meta( $post->ID, '_wpaicg_meta_description', true );
?>
            <div class="wpaicg-tabs">
                <ul>
                    <li id="wpaicg-seo-tab-content" data-target="wpaicg-tab-generated-text" class="wpaicg-active<?php 
echo ( !empty( $_wporg_generated_text ) ? ' wpaicg-has-seo' : '' );
?>"><?php 
echo esc_html__( 'Content', 'gpt3-ai-content-generator' );
?></li>
                    <li id="wpaicg-seo-tab-item" data-target="wpaicg-seo-tab" class="<?php 
echo ( !empty( $wpaicg_post_excerpt ) ? 'wpaicg-has-seo' : '' );
?>"><?php 
echo esc_html__( 'SEO', 'gpt3-ai-content-generator' );
?></li>
                </ul>
                <div class="wpaicg-tab-content">
                    <div id="wpaicg-tab-generated-text">
                        <textarea id="wpcgai_preview_box" name="_wporg_generated_text" rows="20" cols="20" class="wpai-content-generator-textarea"><?php 
echo esc_html( $_wporg_generated_text );
?></textarea>
                    </div>
                    <div id="wpaicg-seo-tab" style="display: none">
                        <p><?php 
echo esc_html__( 'Meta Description', 'gpt3-ai-content-generator' );
?></p>
                        <textarea id="wpaicg-meta-description" name="_wpaicg_meta_description" rows="20" cols="20"><?php 
echo esc_html( $wpaicg_post_excerpt );
?></textarea>
                    </div>
                </div>
            </div>
        </td>
    </tr>
    <tr>
        <td>
            <div class="btn-group" style="display:flex; gap:20px;">
                <button type="submit" name="get_preview" id="wpcgai_load_plugin_settings" class="button button-primary button-large"><?php 
echo esc_html__( 'Generate', 'gpt3-ai-content-generator' );
?></button>
                <!-- <input type="hidden" name="_save_draft" value="draft">   -->
                <button type="button" style="display:none;" name="action_save_draft" id="wpcgai_save_draft_post_action" class="button button-large"><?php 
echo esc_html__( 'Save Draft', 'gpt3-ai-content-generator' );
?></button>
            </div>
        </td>
    </tr>
</table>

<!-- Modal -->
<div class="modal-wpaicg fade" id="myModal" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-wpaicg-dialog">

        <!-- Modal content-->
        <div class="wpcgai_modal-content">
            <div class="wpcgai_modal-header">
                <!--<button type="button" class="close" data-dismiss="modal">&times;</button>-->
                <h4 class="wpcgai_modal-title"><?php 
echo esc_html__( 'Outline Editor', 'gpt3-ai-content-generator' );
?></h4>
                <span><?php 
echo esc_html__( 'You can modify, sort, add or delete headings.', 'gpt3-ai-content-generator' );
?></span>
            </div>
            <div class="wpcgai_modal-body">
                <ol class="wpcgai_menu_editor"></ol>
                <a href="javascript:;" id="wpcgai_add_new_heading">+ <?php 
echo esc_html__( 'Add new heading', 'gpt3-ai-content-generator' );
?></a>
            </div>
            <div class="wpcgai_modal-footer">
                <button type="button" class="button button-secondary button-large m_close"><?php 
echo esc_html__( 'CANCEL', 'gpt3-ai-content-generator' );
?></button>
                <button type="button" class="button button-primary button-large m_generate"><?php 
echo esc_html__( 'GENERATE', 'gpt3-ai-content-generator' );
?></button>
            </div>
        </div>

    </div>
</div>

<script>
    jQuery("#wpai_modify_headings2").change(function()
    {
        if(this.checked)
            jQuery('#wpai_modify_headings').attr('value', 1);
        else
            jQuery('#wpai_modify_headings').attr('value', 0);
    });

    jQuery("#wpai_add_img2").change(function()
    {
        if(this.checked)
            jQuery('#wpai_add_img').attr('value', 1);
        else
            jQuery('#wpai_add_img').attr('value', 0);
    });

    jQuery("#wpai_add_tagline2").change(function()
    {
        if(this.checked)
            jQuery('#wpai_add_tagline').attr('value', 1);
        else
            jQuery('#wpai_add_tagline').attr('value', 0);
    });

    jQuery("#wpai_add_intro2").change(function()
    {
        if(this.checked)
            jQuery('#wpai_add_intro').attr('value', 1);
        else
            jQuery('#wpai_add_intro').attr('value', 0);
    });

    jQuery("#wpai_add_faq2").change(function()
    {
        if(this.checked)
            jQuery('#wpai_add_faq').attr('value', 1);
        else
            jQuery('#wpai_add_faq').attr('value', 0);
    });

    jQuery("#wpai_add_conclusion2").change(function()
    {
        if(this.checked)
            jQuery('#wpai_add_conclusion').attr('value', 1);
        else
            jQuery('#wpai_add_conclusion').attr('value', 0);
    });

    jQuery("#wpai_add_keywords_bold2").change(function()
    {
        if(this.checked)
            jQuery('#wpai_add_keywords_bold').attr('value', 1);
        else
            jQuery('#wpai_add_keywords_bold').attr('value', 0);
    });

    jQuery(".m_generate").on("click", function(e)
    {
        var menuholder = new Array();
        var menuholder2 = new Array();

        var menu_data = jQuery(".wpcgai_menu_editor").children();
        var firstli = menu_data;

        firstli.each(function ()
        {
            var menus_html = jQuery(this).children();

            var identifier = jQuery(this).find("#identifier").text();
            var text = jQuery(this).find("#text").val();

            if(text == '')
            {
                menuholder = new Array();
                menuholder2 = new Array();
                alert('<?php 
echo esc_html__( 'Heading input can not be blank!', 'gpt3-ai-content-generator' );
?>');
            }
            else
            {
                var menuObj = new Object();
                menuObj['Identifier'] = identifier;
                menuObj['Text'] = text;

                menuholder.push(menuObj);
                menuholder2.push(text);
            }
        });

        if(menuholder.length > 0)
        {
            jQuery('#wpai_number_of_heading').val(menuholder.length);

            jQuery("#hfHeadings2").val(JSON.stringify(menuholder));

            jQuery("#hfHeadings").val(menuholder2.join('||'));

            jQuery("#is_generate_continue").val(1);

            jQuery('#myModal').fadeOut('wpcgai_hide');
            jQuery('.modal-backdrop').hide();

            jQuery('#wpcgai_load_plugin_settings').click();
        }
        else if(firstli.length == 0)
        {
            alert('<?php 
echo esc_html__( 'No heading found.', 'gpt3-ai-content-generator' );
?>');
        }
    });

    jQuery(".m_close").on("click", function(e)
    {
        jQuery('#myModal').fadeOut('wpcgai_hide');
        jQuery('.modal-backdrop').hide();
        jQuery('.wpcgai_lds-ellipsis').hide();
        clearTimeout(window['wpaicgTimer']);
        jQuery('#wpcgai_load_plugin_settings').removeAttr('disabled');
        jQuery('#wpcgai_load_plugin_settings .spinner').remove();
        e.stopPropagation();
    });

    jQuery("#wpcgai_add_new_heading").on("click", function(e)
    {
        if(jQuery('#myModal .wpcgai_menu_editor li').length >= 10){
            alert('<?php 
echo esc_html__( 'Limited 10 headings', 'gpt3-ai-content-generator' );
?>')
        }
        else{
            var randomnum = Math.floor((Math.random() * 100000) + 1);

            var itemTemplate = "<li><div>";

            itemTemplate += "<input type='text' id='text' value='' placeholder='<?php 
echo esc_html__( 'Type heading text...', 'gpt3-ai-content-generator' );
?>' style='width: 90%;'/>";

            itemTemplate += "<span class='wpcgai_sort_heading'><i class='fa fa-bars'></i></span>";

            itemTemplate += "<span id='wpcgai_remove_heading'><i class='fa fa-trash-o'></i></span>";

            itemTemplate += "<div style='display: none;'><span id='identifier'>" + randomnum + "</span>";
            itemTemplate += "</div>";
            itemTemplate += "</div></li>";
            jQuery(".wpcgai_menu_editor").append(itemTemplate);
        }
    });

    jQuery(document).ready(function ()
    {
        var menuHolder = jQuery('.wpcgai_menu_editor');
        menuHolder.sortable({
            handle: 'div',
            items: 'li',
            toleranceElement: '> div',
            maxLevels: 2,
            isTree: true,
            tolerance: 'pointer'
        });

        jQuery("body").on('click', '#wpcgai_remove_heading', function ()
        {
            var p = jQuery(this).parent().parent();
            jQuery(p).remove();
        });
    });
</script>
