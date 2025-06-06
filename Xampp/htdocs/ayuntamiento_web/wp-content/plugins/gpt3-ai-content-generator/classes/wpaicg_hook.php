<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_Hook')) {
    class WPAICG_Hook
    {
        private static $instance = null;

        public static function get_instance()
        {
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct()
        {
            add_action( 'admin_menu', array( $this, 'wpaicg_change_menu_name' ) );
            add_action( 'admin_head', array( $this, 'wpaicg_hooks_admin_header' ) );
            add_action('wp_footer',[$this,'wpaicg_footer'],1);
            add_action('wp_head',[$this,'wpaicg_head_seo'],1);
            add_action( 'admin_enqueue_scripts', 'wp_enqueue_media' );
            add_action('admin_footer',array($this,'wpaicg_admin_footer'));
            add_editor_style(WPAICG_PLUGIN_URL.'admin/css/editor.css');
            add_action( 'admin_enqueue_scripts', [$this,'wpaicg_enqueue_scripts'] );
            add_action( 'wp_enqueue_scripts', [$this,'wp_enqueue_scripts_hook'] );
        }


        public function wpaicg_enqueue_scripts()
        {
            wp_enqueue_script('wpaicg-jquery-datepicker',WPAICG_PLUGIN_URL.'admin/js/jquery.datetimepicker.full.min.js',array(),null);
            wp_enqueue_script('wpaicg-init',WPAICG_PLUGIN_URL.'public/js/wpaicg-init.js',array(),null,true);
            wp_localize_script( 'wpaicg-init', 'wpaicgParams', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'search_nonce' => wp_create_nonce( 'wpaicg-chatbox' ),
                'logged_in' => is_user_logged_in() ? 1 : 0,
                'languages' => array(
                    'source' => esc_html__('Sources','gpt3-ai-content-generator'),
                    'no_result' => esc_html__('No result found','gpt3-ai-content-generator'),
                    'wrong' => esc_html__('Something went wrong','gpt3-ai-content-generator'),
                    'prompt_strength' => esc_html__('Please enter a valid prompt strength value.', 'gpt3-ai-content-generator'),
                    'num_inference_steps' => esc_html__('Please enter a valid number of inference steps.', 'gpt3-ai-content-generator'),
                    'guidance_scale' => esc_html__('Please enter a valid guidance scale value.', 'gpt3-ai-content-generator'),
                    'error_image' => esc_html__('Please select least one image for generate', 'gpt3-ai-content-generator'),
                    'save_image_success' => esc_html__('Save images to media successfully','gpt3-ai-content-generator'),
                    'select_all' => esc_html__('Select All', 'gpt3-ai-content-generator'),
                    'unselect' => esc_html__('Unselect', 'gpt3-ai-content-generator'),
                    'select_save_error' => esc_html__('Please select least one image to save', 'gpt3-ai-content-generator'),
                    'alternative' => esc_html__('Alternative Text','gpt3-ai-content-generator'),
                    'title' => esc_html__('Title','gpt3-ai-content-generator'),
                    'caption' => esc_html__('Caption','gpt3-ai-content-generator'),
                    'description' => esc_html__('Description','gpt3-ai-content-generator'),
                    'edit_image' => esc_html__('Edit Image','gpt3-ai-content-generator'),
                    'save' => esc_html__('Save','gpt3-ai-content-generator'),
                    'removed_pdf' => esc_html__('Your pdf session is cleared','gpt3-ai-content-generator')
                ),
                'katex_enabled' => get_option('wpaicg_form_katex', false) ? 1 : 0,
            ));
            wp_enqueue_script('wpaicg-chat-shortcode',WPAICG_PLUGIN_URL.'public/js/wpaicg-chat.js',array(),null,true);
            wp_enqueue_script('wpaicg-chat-shortcode',WPAICG_PLUGIN_URL.'public/js/marked.js',array(),null,true);
            wp_enqueue_script('wpaicg-chat-recorder',WPAICG_PLUGIN_URL.'public/js/recorder.js',array(),null,true);

            // Only load jsPDF on the Forms admin page for Pro users
            if (\WPAICG\wpaicg_util_core()->wpaicg_is_pro() && isset($_GET['page']) && $_GET['page'] === 'wpaicg_forms') {
            
                $jspdf_path = WPAICG_PLUGIN_DIR . 'lib/js/jspdf.umd.min.js';
                if (file_exists($jspdf_path)) {
                    wp_enqueue_script(
                        'wpaicg-jspdf',
                        WPAICG_PLUGIN_URL . 'lib/js/jspdf.umd.min.js',
                        array(),
                        null,
                        true
                    );
                }
            }

            // *** Check if KaTeX option is enabled before loading KaTeX resources ***
            $katex_enabled = get_option('wpaicg_form_katex', false);
            if ($katex_enabled) {
                // 1. CSS
                wp_enqueue_style(
                    'katex-css',
                    WPAICG_PLUGIN_URL . 'public/css/katex.min.css', // Use local file
                    array(),
                    '0.16.4',
                    'all'
                );
                // 2. Main KaTeX JS
                wp_enqueue_script(
                    'katex-main',
                    WPAICG_PLUGIN_URL . 'public/js/katex.min.js', // Use local file
                    array(), // Keep dependencies if needed, KaTeX main usually doesn't have WP script deps
                    '0.16.4',
                    true
                );
                // 3. Auto-render
                wp_enqueue_script(
                    'katex-auto-render',
                    WPAICG_PLUGIN_URL . 'public/js/auto-render.min.js', // Use local file
                    array('katex-main'), // Ensure dependency on local 'katex-main' is correct
                    '0.16.4',
                    true
                );
            }
            wp_enqueue_style('wpaicg-extra-css',WPAICG_PLUGIN_URL.'admin/css/wpaicg_extra.css',array(),null);
            wp_enqueue_style('wpaicg-jquery-datepicker-css',WPAICG_PLUGIN_URL.'admin/css/jquery.datetimepicker.min.css',array(),null);
            wp_enqueue_style('wpaicg-rtl-css',WPAICG_PLUGIN_URL.'public/css/wpaicg-rtl.css',array(),null);
        }

        public function wpaicg_admin_footer()
        {
            ?>
            <div class="wpaicg-overlay" style="display: none">
                <div class="wpaicg_modal">
                    <div class="wpaicg_modal_head">
                        <span class="wpaicg_modal_title"><?php echo esc_html__('GPT3 Modal','gpt3-ai-content-generator')?></span>
                        <span class="wpaicg_modal_close">&times;</span>
                    </div>
                    <div class="wpaicg_modal_content"></div>
                </div>
            </div>
            <div class="wpaicg-overlay-second" style="display: none">
                <div class="wpaicg_modal_second">
                    <div class="wpaicg_modal_head_second">
                        <span class="wpaicg_modal_title_second"><?php echo esc_html__('GPT3 Modal','gpt3-ai-content-generator')?></span>
                        <span class="wpaicg_modal_close_second">&times;</span>
                    </div>
                    <div class="wpaicg_modal_content_second"></div>
                </div>
            </div>
            <div class="wpcgai_lds-ellipsis" style="display: none">
                <div class="wpaicg-generating-title"><?php echo esc_html__('Generating content..','gpt3-ai-content-generator')?></div>
                <div class="wpaicg-generating-process"></div>
                <div class="wpaicg-timer"></div>
            </div>
            <script>
                let wpaicg_ajax_url = '<?php echo esc_js( admin_url('admin-ajax.php') ); ?>';
            </script>
            <?php
        }

        public function wp_enqueue_scripts_hook()
        {
            wp_enqueue_script('wpaicg-init',WPAICG_PLUGIN_URL.'public/js/wpaicg-init.js',array(),null,true);
            wp_localize_script( 'wpaicg-init', 'wpaicgParams', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'search_nonce' => wp_create_nonce( 'wpaicg-chatbox' ),
                'logged_in' => is_user_logged_in() ? 1 : 0,
                'languages' => array(
                    'source' => esc_html__('Sources','gpt3-ai-content-generator'),
                    'no_result' => esc_html__('No result found','gpt3-ai-content-generator'),
                    'wrong' => esc_html__('Something went wrong','gpt3-ai-content-generator'),
                    'prompt_strength' => esc_html__('Please enter a valid prompt strength value.', 'gpt3-ai-content-generator'),
                    'num_inference_steps' => esc_html__('Please enter a valid number of inference steps value.', 'gpt3-ai-content-generator'),
                    'guidance_scale' => esc_html__('Please enter a valid guidance scale value.', 'gpt3-ai-content-generator'),
                    'error_image' => esc_html__('Please select least one image for generate', 'gpt3-ai-content-generator'),
                    'save_image_success' => esc_html__('Save images to media successfully','gpt3-ai-content-generator'),
                    'select_all' => esc_html__('Select All', 'gpt3-ai-content-generator'),
                    'unselect' => esc_html__('Unselect', 'gpt3-ai-content-generator'),
                    'select_save_error' => esc_html__('Please select least one image to save', 'gpt3-ai-content-generator'),
                    'alternative' => esc_html__('Alternative Text','gpt3-ai-content-generator'),
                    'title' => esc_html__('Title','gpt3-ai-content-generator'),
                    'edit_image' => esc_html__('Edit Image','gpt3-ai-content-generator'),
                    'caption' => esc_html__('Caption','gpt3-ai-content-generator'),
                    'description' => esc_html__('Description','gpt3-ai-content-generator'),
                    'save' => esc_html__('Save','gpt3-ai-content-generator'),
                    'removed_pdf' => esc_html__('Your pdf session is cleared','gpt3-ai-content-generator')
                ),
                'katex_enabled' => get_option('wpaicg_form_katex', false) ? 1 : 0,
            ));
            wp_enqueue_script('wpaicg-chat-script',WPAICG_PLUGIN_URL.'public/js/wpaicg-chat.js',null,null,true);
            wp_enqueue_script('wpaicg-markdown-script',WPAICG_PLUGIN_URL.'public/js/marked.js',null,null,true);
            wp_enqueue_script('wpaicg-chat-recorder',WPAICG_PLUGIN_URL.'public/js/recorder.js',null,null,true);

            // *** Check if KaTeX option is enabled before loading KaTeX resources ***
            $katex_enabled = get_option('wpaicg_form_katex', false);
            if ($katex_enabled) {
                // 1. CSS
                wp_enqueue_style(
                    'katex-css',
                    WPAICG_PLUGIN_URL . 'public/css/katex.min.css', // Use local file
                    array(),
                    '0.16.4',
                    'all'
                );
                // 2. Main KaTeX JS
                wp_enqueue_script(
                    'katex-main',
                    WPAICG_PLUGIN_URL . 'public/js/katex.min.js', // Use local file
                    array(), // Keep dependencies if needed, KaTeX main usually doesn't have WP script deps
                    '0.16.4',
                    true
                );
                // 3. Auto-render
                wp_enqueue_script(
                    'katex-auto-render',
                    WPAICG_PLUGIN_URL . 'public/js/auto-render.min.js', // Use local file
                    array('katex-main'), // Ensure dependency on local 'katex-main' is correct
                    '0.16.4',
                    true
                );
            }
        }

        public function wpaicg_head_seo()
        {
            global $wpdb;
            $wpaicg_chat_widget = get_option('wpaicg_chat_widget',[]);
            /*Check Custom Widget For Page Post*/
            $current_context_ID = get_the_ID();
            $wpaicg_bot_content = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".$wpdb->postmeta." WHERE meta_key=%s",'wpaicg_widget_page_'.$current_context_ID));
            if($wpaicg_bot_content && isset($wpaicg_bot_content->post_id)){
                $wpaicg_bot = get_post($wpaicg_bot_content->post_id);
                if($wpaicg_bot) {
                    if(strpos($wpaicg_bot->post_content,'\"') !== false) {
                        $wpaicg_bot->post_content = str_replace('\"', '&quot;', $wpaicg_bot->post_content);
                    }
                    if(strpos($wpaicg_bot->post_content,"\'") !== false) {
                        $wpaicg_bot->post_content = str_replace('\\', '', $wpaicg_bot->post_content);
                    }
                    $wpaicg_chat_widget = json_decode($wpaicg_bot->post_content, true);
                }
            }
            /*End check*/
            $wpaicg_chat_icon = isset($wpaicg_chat_widget['icon']) && !empty($wpaicg_chat_widget['icon']) ? $wpaicg_chat_widget['icon'] : 'default';
            $wpaicg_chat_icon_url = isset($wpaicg_chat_widget['icon_url']) && !empty($wpaicg_chat_widget['icon_url']) ? $wpaicg_chat_widget['icon_url'] : '';
            $wpaicg_chat_status = isset($wpaicg_chat_widget['status']) && !empty($wpaicg_chat_widget['status']) ? $wpaicg_chat_widget['status'] : '';
            $wpaicg_chat_fontsize = isset($wpaicg_chat_widget['fontsize']) && !empty($wpaicg_chat_widget['fontsize']) ? $wpaicg_chat_widget['fontsize'] : '13';
            $wpaicg_chat_fontcolor = isset($wpaicg_chat_widget['fontcolor']) && !empty($wpaicg_chat_widget['fontcolor']) ? $wpaicg_chat_widget['fontcolor'] : '#ffffff';
            $wpaicg_input_font_color = isset($wpaicg_chat_widget['input_font_color']) && !empty($wpaicg_chat_widget['input_font_color']) ? $wpaicg_chat_widget['input_font_color'] : '#000000';
            $wpaicg_chat_bgcolor = isset($wpaicg_chat_widget['bgcolor']) && !empty($wpaicg_chat_widget['bgcolor']) ? $wpaicg_chat_widget['bgcolor'] : '#f8f9fa';
            $wpaicg_bg_text_field = isset($wpaicg_chat_widget['bg_text_field']) && !empty($wpaicg_chat_widget['bg_text_field']) ? $wpaicg_chat_widget['bg_text_field'] : '#ffffff';
            $wpaicg_send_color = isset($wpaicg_chat_widget['send_color']) && !empty($wpaicg_chat_widget['send_color']) ? $wpaicg_chat_widget['send_color'] : '#d1e8ff';
            $wpaicg_footer_color = isset($wpaicg_chat_widget['footer_color']) && !empty($wpaicg_chat_widget['footer_color']) ? $wpaicg_chat_widget['footer_color'] : '#ffffff';
            $wpaicg_border_text_field = isset($wpaicg_chat_widget['border_text_field']) && !empty($wpaicg_chat_widget['border_text_field']) ? $wpaicg_chat_widget['border_text_field'] : '#ced4da';
            $wpaicg_chat_width = isset($wpaicg_chat_widget['width']) && !empty($wpaicg_chat_widget['width']) ? $wpaicg_chat_widget['width'] : '40%';
            $wpaicg_chat_height = isset($wpaicg_chat_widget['height']) && !empty($wpaicg_chat_widget['height']) ? $wpaicg_chat_widget['height'] : '40%';
            $wpaicg_chat_position = isset($wpaicg_chat_widget['position']) && !empty($wpaicg_chat_widget['position']) ? $wpaicg_chat_widget['position'] : 'left';
            $wpaicg_chat_tone = isset($wpaicg_chat_widget['tone']) && !empty($wpaicg_chat_widget['tone']) ? $wpaicg_chat_widget['tone'] : 'friendly';
            $wpaicg_chat_proffesion = isset($wpaicg_chat_widget['proffesion']) && !empty($wpaicg_chat_widget['proffesion']) ? $wpaicg_chat_widget['proffesion'] : 'none';
            $wpaicg_chat_remember_conversation = isset($wpaicg_chat_widget['remember_conversation']) && !empty($wpaicg_chat_widget['remember_conversation']) ? $wpaicg_chat_widget['remember_conversation'] : 'yes';
            $wpaicg_chat_content_aware = isset($wpaicg_chat_widget['content_aware']) && !empty($wpaicg_chat_widget['content_aware']) ? $wpaicg_chat_widget['content_aware'] : 'yes';
            $wpaicg_include_footer = (isset($wpaicg_chat_widget['footer_text']) && !empty($wpaicg_chat_widget['footer_text'])) ? 5 : 0;
            $wpaicg_text_rounded = isset($wpaicg_chat_widget['text_rounded']) && !empty($wpaicg_chat_widget['text_rounded']) ? $wpaicg_chat_widget['text_rounded'] : 5;
            $wpaicg_text_height = isset($wpaicg_chat_widget['text_height']) && !empty($wpaicg_chat_widget['text_height']) ? $wpaicg_chat_widget['text_height'] : 40;
            $wpaicg_user_bg_color = isset($wpaicg_chat_widget['user_bg_color']) && !empty($wpaicg_chat_widget['user_bg_color']) ? $wpaicg_chat_widget['user_bg_color'] : '#ccf5e1';
            ?>

            <?php
                global $post;

                // Check if the post content has .toc_post_list class
                if (isset($post->post_content) && strpos($post->post_content, 'toc_post_list') !== false) {
                    ?>
                    <style>
                        .toc_post_list h2{
                            margin-bottom: 20px;
                        }
                        .toc_post_list{
                            list-style: none;
                            margin: 0 0 30px 0!important;
                            padding: 0!important;
                        }
                        .toc_post_list li{}
                        .toc_post_list li ul{
                            list-style: decimal;
                        }
                        .toc_post_list a{}
                    </style>
                    <?php
                }
            ?>
            <?php
                global $wpdb, $post;

                // Flag to determine whether to print CSS
                $should_print_css = false;

                // Get the current page ID if $post is defined and is an object
                $current_page_id = (is_object($post)) ? $post->ID : null;

                // Check if the post content has the [wpaicg_chatgpt] shortcode, with or without attributes
                if (isset($post->post_content) && preg_match('/\[wpaicg_chatgpt[^\]]*\]/', $post->post_content)) {
                    $should_print_css = true;
                }

                // Query the wp_posts table for posts of type 'wpaicg_chatbot'
                $chatbots = $wpdb->get_results("SELECT post_content FROM $wpdb->posts WHERE post_type = 'wpaicg_chatbot'");

                // Iterate through the chatbots and check the conditions
                foreach ($chatbots as $chatbot) {
                    $data = json_decode($chatbot->post_content, true); // Decode the JSON data

                    // Check if the type is 'widget'. If current_page_id is not null, check in pages attribute, else true
                    if (isset($data['type']) && $data['type'] == 'widget' && ($current_page_id === null || in_array($current_page_id, explode(',', $data['pages'])))) {
                        $should_print_css = true;
                        break; // Exit the loop since we found a match
                    }
                }

                $wpaicg_chat_widget_option = get_option('wpaicg_chat_widget');

                // Check if the option exists
                if ($wpaicg_chat_widget_option) {
                    $wpaicg_chat_data = maybe_unserialize($wpaicg_chat_widget_option);

                    // If the status is active, then set the flag to true
                    if (isset($wpaicg_chat_data['status']) && $wpaicg_chat_data['status'] == 'active') {
                        $should_print_css = true;
                    }
                }

                // Print the CSS based on the flag
                if ($should_print_css) {
                    ?>
                    <!-- Chat Bot CSS -->
                    <style>
                        .wpaicg_chat_widget{
                            position: fixed;
                        }
                        .wpaicg_widget_left{
                            bottom: 15px;
                            left: 15px;
                        }
                        .wpaicg_widget_right{
                            bottom: 15px;
                            right: 15px;
                        }
                        .wpaicg_widget_right .wpaicg_chat_widget_content{
                            right: 0;
                        }
                        .wpaicg_widget_left .wpaicg_chat_widget_content{
                            left: 0;
                        }
                        .wpaicg_chat_widget_content .wpaicg-chatbox{
                            height: 100%;
                            border-radius: 5px;
                            border: none;
                        }
                        .wpaicg_chat_widget_content {
                            /* Initial state of the chat window - hidden */
                            opacity: 0;
                            transform: scale(0.9);
                            visibility: hidden;
                            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0s linear 0.3s;
                        }

                        .wpaicg_widget_open .wpaicg_chat_widget_content {
                            /* Visible state of the chat window */
                            opacity: 1;
                            transform: scale(1);
                            visibility: visible;
                            transition-delay: 0s;
                        }

                        /* Updated shining light effect for hover without background */
                        @keyframes shine {
                            0% {
                                background-position: -150px;
                            }
                            50% {
                                background-position: 150px;
                            }
                            100% {
                                background-position: -150px;
                            }
                        }

                        .wpaicg_chat_widget .wpaicg_toggle {
                            position: relative;
                            overflow: hidden;
                            transition: box-shadow 0.3s ease;
                        }

                        .wpaicg_chat_widget .wpaicg_toggle::before {
                            content: '';
                            position: absolute;
                            top: -50%;
                            left: -50%;
                            width: 200%;
                            height: 200%;
                            /* Ensure gradient is completely transparent except for the shine */
                            background: linear-gradient(to right, transparent, rgba(255,255,255,0.8) 50%, transparent) no-repeat;
                            transform: rotate(30deg);
                            /* Start with the shine outside of the visible area */
                            background-position: -150px;
                        }

                        .wpaicg_chat_widget .wpaicg_toggle:hover::before {
                            /* Apply the animation only on hover */
                            animation: shine 2s infinite;
                        }

                        .wpaicg_chat_widget .wpaicg_toggle img {
                            display: block;
                            transition: opacity 0.3s ease;
                        }

                        .wpaicg_chat_widget_content{
                            position: absolute;
                            bottom: calc(100% + 15px);
                            overflow: hidden;
                        }
                        .wpaicg_widget_open .wpaicg_chat_widget_content{
                            overflow: visible;
                        }
                        .wpaicg_widget_open .wpaicg_chat_widget_content .wpaicg-chatbox{
                            top: 0;
                        }
                        .wpaicg_chat_widget_content .wpaicg-chatbox{
                            position: absolute;
                            top: 100%;
                            left: 0;
                            transition: top 300ms cubic-bezier(0.17, 0.04, 0.03, 0.94);
                        }

                        .wpaicg-chatbox-footer{
                            font-size: 0.75rem;
                            padding: 12px 20px;
                        }
                        /* inherit for hyperlink */
                        .wpaicg-chatbox-footer a{
                            color: inherit;
                        }

                        textarea.wpaicg-chat-shortcode-typing,textarea.wpaicg-chatbox-typing {
                            flex: 1;
                            resize: vertical;
                            padding-left: 1em;
                        }

                        /* Updated shining light effect for hover without background */
                        @keyframes shine {
                            0% {
                                background-position: -150px;
                            }
                            50% {
                                background-position: 150px;
                            }
                            100% {
                                background-position: -150px;
                            }
                        }

                        .wpaicg_chat_widget .wpaicg_toggle {
                            position: relative;
                            overflow: hidden;
                            transition: box-shadow 0.3s ease;
                        }

                        .wpaicg_chat_widget .wpaicg_toggle::before {
                            content: '';
                            position: absolute;
                            top: -50%;
                            left: -50%;
                            width: 200%;
                            height: 200%;
                            /* Ensure gradient is completely transparent except for the shine */
                            background: linear-gradient(to right, transparent, rgba(255,255,255,0.8) 50%, transparent) no-repeat;
                            transform: rotate(30deg);
                            /* Start with the shine outside of the visible area */
                            background-position: -150px;
                        }

                        .wpaicg_chat_widget .wpaicg_toggle:hover::before {
                            /* Apply the animation only on hover */
                            animation: shine 2s infinite;
                        }

                        .wpaicg_chat_widget .wpaicg_toggle img {
                            display: block;
                            transition: opacity 0.3s ease;
                        }

                        .wpaicg_chat_widget .wpaicg_toggle{
                            cursor: pointer;
                        }
                        .wpaicg_chat_widget .wpaicg_toggle img{
                            width: 75px;
                            height: 75px;
                        }
                        .wpaicg-chat-shortcode-type,.wpaicg-chatbox-type{
                            position: relative;
                        }
                        .wpaicg-mic-icon{
                            cursor: pointer;
                        }
                        .wpaicg-mic-icon svg{
                            width: 16px;
                            height: 16px;
                            fill: currentColor;
                        }
                        .wpaicg-img-icon{
                            cursor: pointer;
                        }

                        .wpaicg-pdf-icon svg{
                            width: 16px;
                            height: 16px;
                            fill: currentColor;
                        }
                        .wpaicg_chat_additions span{
                            cursor: pointer;
                            margin-right: 10px;
                        }
                        .wpaicg_chat_additions span:last-of-type{
                            margin-right: 0.5em;
                        }
                        .wpaicg-pdf-loading{
                            width: 16px;
                            height: 16px;
                            border: 2px solid #FFF;
                            border-bottom-color: transparent;
                            border-radius: 50%;
                            display: inline-block;
                            box-sizing: border-box;
                            animation: wpaicg_rotation 1s linear infinite;
                        }
                        @keyframes wpaicg_rotation {
                            0% {
                                transform: rotate(0deg);
                            }
                            100% {
                                transform: rotate(360deg);
                            }
                        }
                        .wpaicg-chat-message code{
                            padding: 3px 5px 2px;
                            background: rgb(0 0 0 / 20%);
                            font-size: 13px;
                            font-family: Consolas,Monaco,monospace;
                            direction: ltr;
                            unicode-bidi: embed;
                            display: block;
                            margin: 5px 0px;
                            border-radius: 4px;
                            white-space: pre-wrap;
                            max-width: fit-content;
                        }
                        .wpaicg_chatbox_line{
                            overflow: hidden;
                            text-align: center;
                            display: block!important;
                            font-size: 12px;
                        }
                        .wpaicg_chatbox_line:after,.wpaicg_chatbox_line:before{
                            background-color: rgb(255 255 255 / 26%);
                            content: "";
                            display: inline-block;
                            height: 1px;
                            position: relative;
                            vertical-align: middle;
                            width: 50%;
                        }
                        .wpaicg_chatbox_line:before {
                            right: 0.5em;
                            margin-left: -50%;
                        }

                        .wpaicg_chatbox_line:after {
                            left: 0.5em;
                            margin-right: -50%;
                        }
                        .wpaicg-chat-shortcode-typing::-webkit-scrollbar,.wpaicg-chatbox-typing::-webkit-scrollbar{
                            width: 5px
                        }
                        .wpaicg-chat-shortcode-typing::-webkit-scrollbar-track,.wpaicg-chatbox-typing::-webkit-scrollbar-track{
                            -webkit-box-shadow:inset 0 0 6px rgba(0, 0, 0, 0.15);border-radius:5px;
                        }
                        .wpaicg-chat-shortcode-typing::-webkit-scrollbar-thumb,.wpaicg-chatbox-typing::-webkit-scrollbar-thumb{
                            border-radius:5px;
                            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.75);
                        }
                    </style>
                    <?php
                }
            ?>

            <?php
            if(is_single()){
                $wpaicg_meta_description = get_post_meta(get_the_ID(),'_wpaicg_meta_description',true);
                $_wpaicg_seo_meta_tag = get_option('_wpaicg_seo_meta_tag',false);
                $wpaicg_seo_option = false;
                $wpaicg_seo_plugin = wpaicg_util_core()->seo_plugin_activated();
                if($wpaicg_seo_plugin) {
                    $wpaicg_seo_option = get_option($wpaicg_seo_plugin, false);
                }
                if(!empty($wpaicg_meta_description) && $_wpaicg_seo_meta_tag && !$wpaicg_seo_option){
                    ?>
                    <!--- This meta description generated by AI Power Plugin --->
                    <meta name="description" content="<?php echo esc_html($wpaicg_meta_description)?>">
                    <meta name="og:description" content="<?php echo esc_html($wpaicg_meta_description)?>">
                    <?php
                }
            }
        }

        public function wpaicg_footer()
        {
            include WPAICG_PLUGIN_DIR.'admin/extra/wpaicg_chat_widget.php';
            ?>
            <?php
        }

        public function wpaicg_hooks_admin_header()
        {
            ?>
            <style>
                .wp-block .toc_post_list h2{
                    margin-bottom: 20px;
                }
                .wp-block .toc_post_list{
                    list-style: none;
                    margin: 0 0 30px 0!important;
                    padding: 0!important;
                }
                .wp-block .toc_post_list li{}
                .wp-block .toc_post_list li ul{
                    list-style: decimal;
                }
                .wp-block .toc_post_list a{}
                .wpaicg-chat-shortcode-type,.wpaicg-chatbox-type{
                    position: relative;
                }
                .wpaicg-mic-icon{
                    cursor: pointer;
                }
                .wpaicg-mic-icon svg{
                    width: 16px;
                    height: 16px;
                    fill: currentColor;
                }
                .wpaicg-img-icon{
                    cursor: pointer;
                }
                .wpaicg-pdf-icon svg{
                    width: 16px;
                    height: 16px;
                    fill: currentColor;
                }
                .wpaicg-pdf-loading{
                    width: 16px;
                    height: 16px;
                    border: 2px solid #FFF;
                    border-bottom-color: transparent;
                    border-radius: 50%;
                    display: inline-block;
                    box-sizing: border-box;
                    animation: wpaicg_rotation 1s linear infinite;
                }
                @keyframes wpaicg_rotation {
                    0% {
                        transform: rotate(0deg);
                    }
                    100% {
                        transform: rotate(360deg);
                    }
                }
                .wpaicg_chat_additions span{
                    cursor: pointer;
                    margin-right: 10px;
                }
                .wpaicg_chat_additions span:last-of-type{
                    margin-right: 0.5em;
                }
                .wp-picker-container{
                    z-index: 99999;
                }

                .gpt-ai-power_page_wpaicg_help .ui-state-default,
                .gpt-ai-power_page_wpaicg_help .ui-widget-content .ui-state-default,
                .gpt-ai-power_page_wpaicg_help .ui-widget-header .ui-state-default,
                .gpt-ai-power_page_wpaicg_help .ui-button,
                html .gpt-ai-power_page_wpaicg_help  .ui-button.ui-state-disabled:hover,
                html .gpt-ai-power_page_wpaicg_help  .ui-button.ui-state-disabled:active,
                .wpcgai_container .ui-state-default,
                .wpcgai_container .ui-widget-content .ui-state-default,
                .wpcgai_container .ui-widget-header .ui-state-default,
                .wpcgai_container .ui-button,
                html .wpcgai_container .ui-button.ui-state-disabled:hover,
                html .wpcgai_container .ui-button.ui-state-disabled:active
                {
                    border: 1px solid #c5c5c5;
                    background: #f6f6f6;
                    font-weight: normal;
                    color: #454545;
                    border-bottom-width: 0;
                }
                .gpt-ai-power_page_wpaicg_help .ui-state-hover,
                .gpt-ai-power_page_wpaicg_help .ui-widget-content .ui-state-hover,
                .gpt-ai-power_page_wpaicg_help .ui-widget-header .ui-state-hover,
                .gpt-ai-power_page_wpaicg_help .ui-state-focus,
                .gpt-ai-power_page_wpaicg_help .ui-widget-content .ui-state-focus,
                .gpt-ai-power_page_wpaicg_help .ui-widget-header .ui-state-focus,
                .gpt-ai-power_page_wpaicg_help .ui-button:hover, .ui-button:focus,
                .wpcgai_container .ui-state-hover,
                .wpcgai_container .ui-widget-content .ui-state-hover,
                .wpcgai_container .ui-widget-header .ui-state-hover,
                .wpcgai_container .ui-state-focus,
                .wpcgai_container .ui-widget-content .ui-state-focus,
                .wpcgai_container .ui-widget-header .ui-state-focus,
                .wpcgai_container .ui-button:hover, .ui-button:focus
                {
                    border: 1px solid #cccccc;
                    background: #ededed;
                    font-weight: normal;
                    color: #2b2b2b;
                }

                .gpt-ai-power_page_wpaicg_help .ui-state-active,
                .gpt-ai-power_page_wpaicg_help .ui-widget-content .ui-state-active,
                .gpt-ai-power_page_wpaicg_help .ui-widget-header .ui-state-active,
                .gpt-ai-power_page_wpaicg_help a.ui-button:active,
                .gpt-ai-power_page_wpaicg_help .ui-button:active,
                .gpt-ai-power_page_wpaicg_help .ui-button.ui-state-active:hover
                .wpcgai_container .ui-state-active,
                .wpcgai_container .ui-widget-content .ui-state-active,
                .wpcgai_container .ui-widget-header .ui-state-active,
                .wpcgai_container a.ui-button:active,
                .wpcgai_container .ui-button:active,
                .wpcgai_container .ui-button.ui-state-active:hover{
                    border: 1px solid #003eff;
                    background: #007fff;
                    font-weight: normal;
                    color: #ffffff;
                }
                .wpaicg-overlay-second {
                    position: fixed;
                    width: 100%;
                    height: 100%;
                    z-index: 99999;
                    background: rgb(0 0 0 / 20%);
                    top: 0;
                    direction: ltr;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }
                .wpaicg_modal_second {
                    width: 400px;
                    min-height: 100px;
                    background: #fff;
                    border-radius: 5px;
                    max-height: 500px;
                    overflow-y: auto;
                }
                .wpaicg_modal_head_second {
                    min-height: 30px;
                    border-bottom: 1px solid #ccc;
                    display: flex;
                    align-items: center;
                    padding: 6px 12px;
                    position: relative;
                }
                .wpaicg_modal_content_second {
                    max-height: calc(100% - 103px);
                    overflow-y: auto;
                }
                .wpaicg_modal_title_second {
                    font-size: 18px;
                }
                .wpaicg_modal_close_second {
                    position: absolute;
                    top: 10px;
                    right: 10px;
                    font-size: 30px;
                    font-weight: bold;
                    cursor: pointer;
                }
                .wpaicg-chat-message code{
                    padding: 3px 5px 2px;
                    background: rgb(0 0 0 / 20%);
                    font-size: 13px;
                    font-family: Consolas,Monaco,monospace;
                    direction: ltr;
                    unicode-bidi: embed;
                    display: block;
                    margin: 5px 0px;
                    border-radius: 4px;
                    white-space: pre-wrap;
                    max-width: fit-content;
                }

                .wpaicg_chatbox_line{
                    overflow: hidden;
                    text-align: center;
                    display: block!important;
                    font-size: 12px;
                }
                .wpaicg_chatbox_line:after,.wpaicg_chatbox_line:before{
                    background-color: rgb(255 255 255 / 26%);
                    content: "";
                    display: inline-block;
                    height: 1px;
                    position: relative;
                    vertical-align: middle;
                    width: 50%;
                }
                .wpaicg_chatbox_line:before {
                    right: 0.5em;
                    margin-left: -50%;
                }

                .wpaicg_chatbox_line:after {
                    left: 0.5em;
                    margin-right: -50%;
                }
                .wpaicg-chat-shortcode-typing::-webkit-scrollbar,.wpaicg-chatbox-typing::-webkit-scrollbar{
                    width: 5px
                }
                .wpaicg-chat-shortcode-typing::-webkit-scrollbar-track,.wpaicg-chatbox-typing::-webkit-scrollbar-track{
                    -webkit-box-shadow:inset 0 0 6px rgba(0, 0, 0, 0.15);border-radius:5px;
                }
                .wpaicg-chat-shortcode-typing::-webkit-scrollbar-thumb,.wpaicg-chatbox-typing::-webkit-scrollbar-thumb{
                    border-radius:5px;
                    -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.75);
                }
                .wpaicg-woocommerce-content-btn.button-primary{
                    background: #d63638;
                    border-color: #d63638;
                }
                .wpaicg-woocommerce-content-btn.button-primary:hover{
                    background: #8d0000;
                    border-color: #8d0000;
                }
                .wpcgai_form_row label{
                    vertical-align: top;
                }
            </style>
            <?php
        }

        public function wpaicg_change_menu_name()
        {
            global  $menu ;
            global  $submenu ;
            if(isset($submenu['wpaicg'])) {
                if ($submenu['wpaicg'][0][2] == 'wpaicg') {
                    $submenu['wpaicg'][0][0] = 'Dashboard';
                }
            }
        }
    }
    WPAICG_Hook::get_instance();
}
