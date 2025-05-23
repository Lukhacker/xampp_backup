<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$wpaicg_provider = get_option('wpaicg_provider', 'OpenAI');

if($wpaicg_provider != 'OpenAI'){
    ?>
    <div>
        <p></p>
        <p>Audio Converter is available for OpenAI only. Please go to Settings - AI Engine and switch to OpenAI to use this feature.</p>
    </div>
    <?php
    exit; // Stop the script if the provider is Azure
} else {
    $wpaicg_action = isset($_GET['action']) && !empty($_GET['action']) ? sanitize_text_field($_GET['action']) : '';
    // Assuming $checkRole will be a URL string if redirect is needed, or false/null otherwise
    $checkRole = \WPAICG\wpaicg_roles()->user_can('wpaicg_audio', empty($wpaicg_action) ? 'converter' : $wpaicg_action);

    // Only attempt redirect if $checkRole is a non-empty string (likely a URL)
    if(is_string($checkRole) && !empty($checkRole)){
        // Escape the URL for safe embedding within the JavaScript string.
        echo '<script>window.location.href="'.esc_js($checkRole).'"</script>';
        exit;
    }
    ?>

    <style>
    .wpaicg_notice_text_rw {
        padding: 10px;
        background-color: #F8DC6F;
        text-align: left;
        margin-bottom: 12px;
        color: #000;
        box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
    }
    </style>

    <div class="wrap fs-section">
        <h2 class="nav-tab-wrapper">
            <?php
            if(empty($wpaicg_action)){
                $wpaicg_action = 'converter';
            }
            \WPAICG\wpaicg_util_core()->wpaicg_tabs('wpaicg_audio', array(
                'converter' => esc_html__('Audio Converter','gpt3-ai-content-generator'),
                'logs' => esc_html__('Logs','gpt3-ai-content-generator')
            ), $wpaicg_action);
            if(!$wpaicg_action || $wpaicg_action == 'converter'){
                $wpaicg_action = '';
            }
            ?>
        </h2>
    </div>
    <div id="poststuff">
        <?php
        if(empty($wpaicg_action)){
            include __DIR__.'/converter.php';
        }
        if($wpaicg_action == 'logs'){
            include __DIR__.'/logs.php';
        }
        ?>
    </div>

    <?php
}
?>
