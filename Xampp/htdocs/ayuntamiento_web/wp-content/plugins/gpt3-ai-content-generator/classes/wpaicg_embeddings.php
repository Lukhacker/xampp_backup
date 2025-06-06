<?php
namespace WPAICG;
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('\\WPAICG\\WPAICG_Embeddings')) {
    class WPAICG_Embeddings
    {
        private static  $instance = null ;
        public $wpaicg_max_file_size = 10485760;

        public static function get_instance()
        {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public function __construct()
        {
            add_action('wp_ajax_wpaicg_embeddings',[$this,'wpaicg_embeddings']);
            add_action( 'admin_menu', array( $this, 'wpaicg_menu' ) );
            add_action('init',[$this,'wpaicg_cron_job'],9999);
            add_action('wp_ajax_wpaicg_builder_reindex',[$this,'wpaicg_builder_reindex']);
            add_action('wp_ajax_wpaicg_builder_delete',[$this,'wpaicg_builder_delete']);
            add_action('wp_ajax_wpaicg_reindex_embeddings',[$this,'wpaicg_reindex_embeddings']);
            add_action('wp_ajax_wpaicg_delete_embeddings',[$this,'wpaicg_delete_embeddings']);
            add_action('wp_ajax_wpaicg_reindex_builder_data',[$this,'wpaicg_reindex_builder_data']);
            $wpaicg_instant_embedding = get_option('wpaicg_instant_embedding','yes');
            if($wpaicg_instant_embedding == 'yes'){
                add_action('manage_posts_extra_tablenav',[$this,'wpaicg_instant_embedding_button']);
                add_action('admin_footer',[$this,'wpaicg_instant_embedding_footer']);
                add_action('wp_ajax_wpaicg_instant_embedding',[$this,'wpaicg_instant_embedding']);
            }
            /*Pinecone sync Indexes*/
            add_action('wp_ajax_wpaicg_pinecone_indexes',[$this,'wpaicg_pinecone_indexes']);
            add_action( 'wp_ajax_gpt4_pagination', array( $this, 'gpt4_ajax_pagination' ) );
            add_action( 'wp_ajax_nopriv_gpt4_pagination', 'gpt4_ajax_pagination');
            add_action('wp_ajax_reload_items_embeddings', array($this, 'reload_items_embeddings'));
            add_action('wp_ajax_search_embeddings_content', array($this, 'search_embeddings_content'));
            // wpaicg_delete_all_embeddings
            add_action('wp_ajax_wpaicg_delete_all_embeddings', array($this, 'wpaicg_delete_all_embeddings'));
            add_action('wp_ajax_set_results_per_page', array($this, 'set_results_per_page'));
            add_action('wp_ajax_wpaicg_save_revised_answer', [$this, 'wpaicg_save_revised_answer']);
            add_action('wp_ajax_wpaicg_get_all_posts_for_embeddings', [$this, 'wpaicg_get_all_posts_for_embeddings']);

        }

        // =========== NEW: Provide a list of posts/pages/CPT for the "Add my data" button in knowledge modal =========== //
        public function wpaicg_get_all_posts_for_embeddings() {
            // Verify nonce
            if (!isset($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'wpaicg_save_ai_engine_nonce')) {
                wp_send_json_error(array('message' => __('Nonce verification failed.', 'gpt3-ai-content-generator')));
                return;
            }
            
            if(!current_user_can('manage_options')){
                wp_send_json_error(['message' => 'Permission denied.']);
            }
        
            $post_types = get_post_types(['public' => true], 'names');
            if(isset($post_types['attachment'])) {
                unset($post_types['attachment']);
            }
        
            $all_posts_by_type = [];
            foreach($post_types as $pt) {
                $args = [
                    'post_type' => $pt,
                    'post_status' => 'publish',
                    'posts_per_page' => -1
                ];
                $posts = get_posts($args);
                $list = [];
                foreach($posts as $p) {
                    $list[] = [
                        'ID' => $p->ID,
                        'post_title' => $p->post_title
                    ];
                }
                // Even if zero posts found, $list = [] is still an array.
                // Make sure you assign $all_posts_by_type[$pt] = $list, not just a single object.
                $all_posts_by_type[$pt] = $list;
            }
        
            wp_send_json_success(['data' => $all_posts_by_type]);
        }

        public function wpaicg_save_revised_answer() {
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            
            if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'wpaicg_embeddings_save' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = esc_html__('Nonce verification failed','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
        
            if(isset($_POST['question']) && !empty($_POST['question']) && isset($_POST['content']) && !empty($_POST['content'])) {
                $question = wp_kses_post(wp_strip_all_tags($_POST['question']));
                $content = wp_kses_post(wp_strip_all_tags($_POST['content']));
                
                // Remove "User: " prefix from the question
                $question = preg_replace('/^User:\s*/', '', $question);
        
                if(!empty($question) && !empty($content)) {
                    // Format the content to save without "Question:" and "Answer:"
                    $formatted_content = $question . "\n" . $content;
                    // Save the formatted content
                    $wpaicg_result = $this->wpaicg_save_embedding($formatted_content);
                } else {
                    $wpaicg_result['msg'] = esc_html__('Please insert both question and content','gpt3-ai-content-generator');
                }
            } else {
                $wpaicg_result['msg'] = esc_html__('Please provide all necessary data','gpt3-ai-content-generator');
            }
        
            wp_send_json($wpaicg_result);
        }
        
        
        public function calculate_estimated_cost($post_id) {
            $token = get_post_meta($post_id, 'wpaicg_embedding_token', true);
            $wpaicg_emb_model = get_post_meta($post_id, 'wpaicg_model', true);
        
            // Default cost per token
            $costPerToken = 0.00010 / 1000; // Default cost for 'text-embedding-ada-002'
        
            // Adjust cost per token based on model
            switch ($wpaicg_emb_model) {
                case 'text-embedding-3-small':
                    $costPerToken = 0.00002 / 1000;
                    break;
                case 'text-embedding-3-large':
                    $costPerToken = 0.00013 / 1000;
                    break;
                case 'embedding-001':
                    $costPerToken = 0.0002 / 1000;
                    break;
                case 'text-embedding-004':
                    $costPerToken = 0.0002 / 1000;
                    break;
                case 'text-embedding-005':
                    $costPerToken = 0.0002 / 1000;
                    break;
            }
        
            // Calculate estimated cost
            $estimatedCost = !empty($token) ? number_format((int)$token * $costPerToken, 8) . '$' : '--';
            return $estimatedCost;
        }

        public function generate_table_row($post) {
            $title = strlen($post->post_title) > 20 ? esc_html(substr($post->post_title, 0, 20)) . '...' : esc_html($post->post_title);
            $token = get_post_meta($post->ID, 'wpaicg_embedding_token', true); // Fetch the token value for each post
            $estimatedCost = $this->calculate_estimated_cost($post->ID); // Calculate estimated cost
            $postContent = htmlentities(wp_kses_post(get_post_field('post_content', $post->ID)), ENT_QUOTES, 'UTF-8'); // Safely encode the post content
            $wpaicg_embedding_type = get_post_meta($post->ID,'wpaicg_embedding_type',true);
            $wpaicg_embedding_status = get_post_meta($post->ID,'wpaicg_embeddings_reindex',true);
            $wpaicg_provider = get_post_meta($post->ID, 'wpaicg_provider', true);
            $wpaicg_index = get_post_meta($post->ID, 'wpaicg_index', true);
            // Define allowed HTML tags for wp_kses
            $allowed_html = array(
                'div' => array(
                    'style' => array()
                ),
                'strong' => array(),
                'br' => array(),
            );
            $wpaicg_emb_model = get_post_meta($post->ID, 'wpaicg_model', true);

            // Determine the source based on post_type
            $source = 'Unknown'; // Default source
            if ($post->post_type == 'wpaicg_embeddings') {
                $source = 'Manual';
            } elseif ($post->post_type == 'wpaicg_pdfadmin') {
                $source = 'PDF';
            } elseif ($post->post_type == 'wpaicg_builder') {
                $source = 'Auto-Scan';
            }

            // Display empty or placeholder if fields are not available
            $wpaicg_provider_display = !empty($wpaicg_provider) ? esc_html($wpaicg_provider) : '';
            
            $wpaicg_emb_model_display = !empty($wpaicg_emb_model) ? esc_html($wpaicg_emb_model) : 'text-embedding-ada-002';

            $postdate = wp_date('y-m-d H:i', strtotime($post->post_date));
            // Combine all information into a detailed string
            $details = "<div style='font-size: 90%;white-space: break-spaces;'>";
            if (!empty($wpaicg_index)) {
                $dbProvider = (strpos($wpaicg_index, 'pinecone.io') !== false) ? 'Pinecone' : 'Qdrant';
                $details .= "<strong>DB:</strong> " . esc_html($dbProvider) . "<br>";
                if ($dbProvider == 'Pinecone') {
                    $parts = explode('-', $wpaicg_index);
                    $indexName = '';
                    $svc_pos = strpos($wpaicg_index, '.svc'); // Assuming '.svc' is a relevant marker in this context too.
                
                    if ($svc_pos !== false) {
                        // Find the last "-" before ".svc" by looking backwards from the position of ".svc"
                        $sub_string_up_to_svc = substr($wpaicg_index, 0, $svc_pos);
                        $last_dash_before_svc = strrpos($sub_string_up_to_svc, '-');
                
                        // Ensure there's a "-" before ".svc"
                        if ($last_dash_before_svc !== false) {
                            // Extract everything before the last "-" before ".svc"
                            $indexName = substr($wpaicg_index, 0, $last_dash_before_svc);
                        } else {
                            // No "-" found before ".svc", assume the whole part before ".svc" is the index name
                            $indexName = $sub_string_up_to_svc;
                        }
                    } else {
                        // ".svc" not found, handle the error or use a default
                        $indexName = null;  // Or handle this case as needed
                    }
                    $projectName = substr($parts[1], 0, strpos($parts[1], '.svc'));
                    $details .= "<strong>Project:</strong> " . esc_html($projectName) . "<br>" . "<strong>Index:</strong> " . esc_html($indexName) . "<br>";
                } else {
                    $details .= "<strong>Collection:</strong> " . esc_html($wpaicg_index) . "<br>";
                }
            }

            // Include Model, AI Provider, Estimated Cost, and Token
            $details .= "<strong>Model:</strong> " . esc_html($wpaicg_emb_model_display) . "<br>" .
                        "<strong>AI:</strong> " . esc_html($wpaicg_provider_display) . "<br>" .
                        "<strong>Cost:</strong> " . esc_html($estimatedCost) . "<br>" .
                        "<strong>Token:</strong> " . esc_html($token) . "<br>" .
                        "</div>";

            // Adjust the table row to include the new "Details" column
            return "<tr id='post-row-{$post->ID}'>
                        <td class='column-id'>" . esc_html($post->ID) . "</td>
                        <td class='column-content'>
                            <a href='javascript:void(0);' class='wpaicg-embedding-content' data-content='{$postContent}'>" . $title . "</a>
                        </td>
                        <td class='column-details'>" . $details . "</td>
                        <td class='column-source'>" . esc_html($source) . "</td>
                        <td class='column-date' style='white-space: break-spaces;'>" . esc_html($postdate) . "</td>
                        <td class='column-action'><button class='button btn-delete-post' data-post-id='{$post->ID}'>" . esc_html__('Delete', 'gpt3-ai-content-generator') . "</button></td>
                    </tr>";
        }
        public function search_embeddings_content() {
            global $wpdb; // Access the global database object
            check_ajax_referer('gpt4_ajax_pagination_nonce', 'nonce');
        
            $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';
            $results_per_page = isset($_POST['results_per_page']) ? intval($_POST['results_per_page']) : get_option('wpaicg_knowledge_builder_page', 3);
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $offset = ($page - 1) * $results_per_page;
        
            // Construct the basic query with LIKE clause for search within post_content
            $posts = $wpdb->get_results( $wpdb->prepare(
                "SELECT ID, post_title, post_date, post_type FROM {$wpdb->posts}
                WHERE post_type IN ('wpaicg_embeddings', 'wpaicg_pdfadmin','wpaicg_builder') AND post_status = 'publish'
                AND post_content LIKE %s
                ORDER BY post_date DESC
                LIMIT %d, %d",
                '%' . $wpdb->esc_like($search_term) . '%', $offset, $results_per_page
            ) );
                
            // Prepare content HTML
            $output = '';
            foreach ($posts as $post) {
                $output .= $this->generate_table_row($post);
            }
        
            // Get total posts for pagination
            $total_posts = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->posts}
                WHERE post_type IN ('wpaicg_embeddings', 'wpaicg_pdfadmin','wpaicg_builder') AND post_status = 'publish'
                AND post_content LIKE %s",
                '%' . $wpdb->esc_like($search_term) . '%'
            ) );
            $total_pages = ceil($total_posts / $results_per_page);
        
            // Generate pagination HTML
            $updated_pagination_html = $this->generate_smart_pagination($page, $total_pages);
        
            // Return the filtered results and updated pagination
            wp_send_json_success(array(
                'content' => $output,
                'pagination' => $updated_pagination_html,
            ));
        }
        
        public function gpt4_ajax_pagination() {
            global $wpdb;
            // Check for nonce security
            if ( ! wp_verify_nonce( $_POST['nonce'], 'gpt4_ajax_pagination_nonce' ) ) {
                wp_send_json_error(['msg' => esc_html__('Nonce verification failed', 'gpt3-ai-content-generator')]);
            }
        
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $results_per_page = isset($_POST['results_per_page']) ? intval($_POST['results_per_page']) : get_option('wpaicg_knowledge_builder_page', 3);
            $offset = ($page - 1) * $results_per_page;
            $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';
        
            // Calculate total number of posts
            $total_posts = $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) FROM {$wpdb->posts} 
                WHERE post_type IN ('wpaicg_embeddings', 'wpaicg_pdfadmin','wpaicg_builder')
                AND post_content LIKE %s", 
                '%' . $wpdb->esc_like($search_term) . '%'
            ));
            $total_pages = ceil($total_posts / $results_per_page);

            $posts = $wpdb->get_results($wpdb->prepare("
                SELECT ID, post_title, post_status, post_mime_type, post_type, post_date
                FROM {$wpdb->posts} 
                WHERE post_type IN ('wpaicg_embeddings', 'wpaicg_pdfadmin','wpaicg_builder')
                AND post_content LIKE %s
                ORDER BY post_date DESC 
                LIMIT %d, %d", 
                '%' . $wpdb->esc_like($search_term) . '%', $offset, $results_per_page
            ));
        
            $output = '';
            foreach ( $posts as $post ) {
                $output .= $this->generate_table_row($post);
            }
        
            // Revised to generate smarter pagination
            $pagination_html = $this->generate_smart_pagination($page, $total_pages);

            // Send back both the table content and pagination HTML
            wp_send_json_success(['content' => $output, 'pagination' => $pagination_html]);
        
            die();
        }

        public function generate_smart_pagination($current_page, $total_pages) {
            $html = '<div class="gpt4-pagination">';
            $range = 2; // Adjust as needed. This will show two pages before and after the current page.
            $showEllipses = false;
        
            for ($i = 1; $i <= $total_pages; $i++) {
                // Always show the first page, the last page, and the current page with $range pages on each side.
                if ($i == 1 || $i == $total_pages || ($i >= $current_page - $range && $i <= $current_page + $range)) {
                    $html .= sprintf('<a href="#" data-page="%d">%d</a> ', $i, $i);
                    $showEllipses = true;
                } elseif ($showEllipses) {
                    $html .= '... ';
                    $showEllipses = false;
                }
            }
        
            $html .= '</div>';
            return $html;
        }

        public function reload_items_embeddings() {
            global $wpdb;
            // Check for nonce security
            if ( ! wp_verify_nonce( $_POST['nonce'], 'gpt4_ajax_pagination_nonce' ) ) {
                wp_send_json_error(['msg' => esc_html__('Nonce verification failed', 'gpt3-ai-content-generator')]);
            }
        
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $results_per_page = isset($_POST['results_per_page']) ? intval($_POST['results_per_page']) : get_option('wpaicg_knowledge_builder_page', 3);
            $offset = ($page - 1) * $results_per_page;
            $search_term = isset($_POST['search_term']) ? sanitize_text_field($_POST['search_term']) : '';
        
            // Calculate total number of posts
            $total_posts = $wpdb->get_var($wpdb->prepare("
                SELECT COUNT(*) FROM {$wpdb->posts} 
                WHERE post_type IN ('wpaicg_embeddings', 'wpaicg_pdfadmin','wpaicg_builder')
                AND post_content LIKE %s", 
                '%' . $wpdb->esc_like($search_term) . '%'
            ));
            $total_pages = ceil($total_posts / $results_per_page);
        
            $posts = $wpdb->get_results($wpdb->prepare("
                SELECT ID, post_title, post_status, post_mime_type, post_type, post_date
                FROM {$wpdb->posts} 
                WHERE post_type IN ('wpaicg_embeddings', 'wpaicg_pdfadmin','wpaicg_builder')
                AND post_content LIKE %s
                ORDER BY post_date DESC 
                LIMIT %d, %d", 
                '%' . $wpdb->esc_like($search_term) . '%', $offset, $results_per_page
            ));
    
            $output = '';
            foreach ( $posts as $post ) {
                $output .= $this->generate_table_row($post);
            }

            // Generate and return pagination HTML as before
            $pagination_html = $this->generate_smart_pagination($page, $total_pages);

            // Send back both the table content and pagination HTML
            wp_send_json_success(['content' => $output, 'pagination' => $pagination_html]);

            die();
        }

        public function set_results_per_page() {
            check_ajax_referer('gpt4_ajax_pagination_nonce', 'nonce');
        
            $results_per_page = isset($_POST['results_per_page']) ? intval($_POST['results_per_page']) : 3;
        
            if (update_option('wpaicg_knowledge_builder_page', $results_per_page)) {
                wp_send_json_success();
            } else {
                wp_send_json_error(['msg' => esc_html__('Failed to set results per page', 'gpt3-ai-content-generator')]);
            }
        
            die();
        }

        public function wpaicg_pinecone_indexes()
        {
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                die(esc_html__('Nonce verification failed','gpt3-ai-content-generator'));
            }
            if (!current_user_can('manage_options')) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = esc_html__('You do not have permission for this action.','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            $indexes = sanitize_text_field(str_replace("\\",'',$_REQUEST['indexes']));
            update_option('wpaicg_pinecone_indexes',$indexes);
            if(isset($_REQUEST['api_key']) && !empty($_REQUEST['api_key'])){
                update_option('wpaicg_pinecone_api', sanitize_text_field($_REQUEST['api_key']));
            }

        }

        public function wpaicg_instant_embedding()
        {
            $wpaicg_result = array('status' => 'error','msg' => esc_html__('Missing ID request','gpt3-ai-content-generator'));
            if(!current_user_can('manage_options')){
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = esc_html__('You do not have permission for this action.','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = esc_html__('Nonce verification failed','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            if(isset($_REQUEST['id']) && !empty($_REQUEST['id'])){
                $id = sanitize_text_field($_REQUEST['id']);
                $wpaicg_data = get_post($id);
                if($wpaicg_data){
                    $result = $this->wpaicg_builder_data($wpaicg_data);
                    if($result == 'success'){
                        $wpaicg_result['status'] = 'success';
                    }
                    else{
                        $wpaicg_result['msg'] = $result;
                    }
                }
                else{
                    $wpaicg_result['msg'] = esc_html__('Data not found','gpt3-ai-content-generator');
                }
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_instant_embedding_footer()
        {
            ?>
            <script>
                jQuery(document).ready(function ($){
                    let wpaicgInstantAjax = false;
                    let wpaicgInstantWorking = true;
                    let wpaicgInstantSuccess = 0;
                    $(document).on('click', '.wpaicg-instant-embedding-cancel', function (){
                        wpaicgInstantWorking = false;
                        if(wpaicgInstantAjax){
                            wpaicgInstantAjax.abort();
                        }
                        let pendings = $('.wpaicg-instant-pending');
                        pendings.find('.wpaicg-instant-embedding-status').html('<?php echo esc_html__('Cancelled','gpt3-ai-content-generator')?>');
                        pendings.find('.wpaicg-instant-embedding-status').css({
                            'font-style': 'normal',
                            'font-weight': 'bold',
                            'color': '#e30000'
                        })
                        $('.wpaicg_modal_close').show();
                        $('.wpaicg-instant-embedding-cancel').hide();
                    });
                    function wpaicgInstantEmbedding(start,ids){
                        let id = ids[start];
                        let nextId = start+1;
                        let embedding = $('#wpaicg-instant-embedding-'+id);
                        if(wpaicgInstantWorking) {
                            wpaicgInstantAjax = $.ajax({
                                url: '<?php echo esc_js( admin_url('admin-ajax.php') ); ?>',
                                data: {action: 'wpaicg_instant_embedding', id: id,'nonce': '<?php echo esc_js( wp_create_nonce('wpaicg-ajax-nonce') ); ?>'},
                                type: 'POST',
                                dataType: 'JSON',
                                success: function (res) {
                                    if (res.status === 'success') {
                                        wpaicgInstantSuccess += 1;
                                        $('.wpaicg-embedding-remain').html(wpaicgInstantSuccess+'/'+ids.length);
                                        embedding.css({
                                            'background-color': '#cde5dd'
                                        });
                                        embedding.removeClass('wpaicg-instant-pending');
                                        embedding.find('.wpaicg-instant-embedding-status').html('<?php echo esc_html__('Indexed','gpt3-ai-content-generator')?>');
                                        embedding.find('.wpaicg-instant-embedding-status').css({
                                            'font-style': 'normal',
                                            'font-weight': 'bold',
                                            'color': '#008917'
                                        })
                                    } else {
                                        embedding.css({
                                            'background-color': '#e5cdcd'
                                        });
                                        embedding.find('.wpaicg-instant-embedding-status').html('<?php echo esc_html__('Error','gpt3-ai-content-generator')?>');
                                        embedding.find('.wpaicg-instant-embedding-status').css({
                                            'font-style': 'normal',
                                            'font-weight': 'bold',
                                            'color': '#e30000'
                                        })
                                        embedding.append('<div style="color: #e30000;font-size: 12px;">' + res.msg + '</div>');
                                    }
                                    if (nextId < ids.length) {
                                        wpaicgInstantEmbedding(nextId, ids);
                                    } else {
                                        $('.wpaicg_modal_close').show();
                                        $('.wpaicg-instant-embedding-cancel').hide();
                                    }
                                },
                                error: function () {
                                    embedding.css({
                                        'background-color': '#e5cdcd'
                                    });
                                    embedding.find('.wpaicg-instant-embedding-status').html('<?php echo esc_html__('Error','gpt3-ai-content-generator')?>');
                                    embedding.find('.wpaicg-instant-embedding-status').css({
                                        'font-style': 'normal',
                                        'font-weight': 'bold',
                                        'color': '#e30000'
                                    })
                                    embedding.append('<div style="color: #e30000;font-size: 12px;"><?php echo esc_html__('Either something went wrong or you cancelled it.','gpt3-ai-content-generator')?></div>');
                                    if (nextId < ids.length) {
                                        wpaicgInstantEmbedding(nextId, ids);
                                    } else {
                                        $('.wpaicg_modal_close').show();
                                        $('.wpaicg-instant-embedding-cancel').hide();
                                    }
                                }
                            })
                        }
                    }
                    $('.wpaicg-instan-embedding-btn').click(function (){
                        let form = $(this).closest('#posts-filter');
                        let ids = [];
                        let titles = {};
                        form.find('.wp-list-table th.check-column input[type=checkbox]:checked').each(function (idx, item){
                            let post_id = $(item).val();
                            ids.push(post_id);
                            let row = form.find('#post-'+post_id);
                            let post_name = row.find('.column-title .row-title').text();
                            if(post_name === ''){
                                post_name = row.find('.column-name .row-title').text();
                            }
                            titles[post_id] = post_name.trim();
                        });
                        if(ids.length === 0){
                            alert('<?php echo esc_html__('Please select data to index','gpt3-ai-content-generator')?>');
                        }
                        else{
                            let html = '';
                            wpaicgInstantWorking = true;
                            wpaicgInstantSuccess = 0;
                            $('.wpaicg_modal_title').html('<?php echo esc_html__('Instant Embedding','gpt3-ai-content-generator')?><span style="font-weight: bold;font-size: 16px;background: #fba842;padding: 1px 5px;border-radius: 3px;display: inline-block;margin-left: 6px;color: #222;" class="wpaicg-embedding-remain">0/'+ids.length+'</span>');
                            $('.wpaicg_modal').css({
                                top: '5%',
                                height: '90%'
                            })
                            $('.wpaicg_modal_content').css({
                                'max-height': 'calc(100% - 103px)',
                                'overflow-y': 'auto'
                            })
                            $.each(ids, function(idx, id){
                                html += '<div class="wpaicg-instant-pending" id="wpaicg-instant-embedding-'+id+'" style="background: #ebebeb;border-radius: 3px;padding: 5px;margin-bottom: 5px;border: 1px solid #dfdfdf;"><div style="display: flex; justify-content: space-between;"><span>'+titles[id]+'</span><span style="font-style: italic" class="wpaicg-instant-embedding-status"><?php echo esc_html__('Indexing...','gpt3-ai-content-generator')?></span></div></div>';
                            });
                            html += '<div style="text-align: center"><button class="button button-link-delete wpaicg-instant-embedding-cancel"><?php echo esc_html__('Cancel','gpt3-ai-content-generator')?></button></div>';
                            $('.wpaicg_modal_content').html(html);
                            $('.wpaicg-overlay').show();
                            $('.wpaicg_modal').show();
                            $('.wpaicg_modal_close').hide();
                            wpaicgInstantEmbedding(0,ids);
                        }
                    })
                })
            </script>
            <?php
        }

        public function wpaicg_instant_embedding_button($which)
        {
            global $post_type;
            $post_types = array('post','page','product');
            if(wpaicg_util_core()->wpaicg_is_pro()) {
                $wpaicg_all_post_types = get_post_types(array(
                    'public' => true,
                    '_builtin' => false,
                ), 'array');
                $post_types = wp_parse_args($post_types, array_keys($wpaicg_all_post_types));
            }
            if(in_array($post_type,$post_types)):
                if(current_user_can('manage_options')):
            ?>
            <div class="alignleft actions">
                <a style="height: 32px" href="javascript:void(0)" class="button button-primary wpaicg-instan-embedding-btn"><?php echo esc_html__('Instant Embedding','gpt3-ai-content-generator')?></a>
            </div>
            <?php
                endif;
            endif;
        }

        public function wpaicg_reindex_builder_data()
        {
            if(!current_user_can('manage_options')){
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = esc_html__('You do not have permission for this action.','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = esc_html__('Nonce verification failed','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            $ids = wpaicg_util_core()->sanitize_text_or_array_field($_REQUEST['ids']);
            if(count($ids)){
                foreach ($ids as $id){
                    update_post_meta($id,'wpaicg_indexed','reindex');
                }
            }
        }

        public function wpaicg_delete_embeddings()
        {
            $wpaicg_result = array('status' => 'success');
            if(!current_user_can('manage_options')){
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = esc_html__('You do not have permission for this action.','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = esc_html__('Nonce verification failed','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }

            $ids = wpaicg_util_core()->sanitize_text_or_array_field($_REQUEST['ids']);
            $this->wpaicg_delete_embeddings_ids($ids);
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_reindex_embeddings_ids($ids)
        {
            foreach($ids as $id){
                update_post_meta($id,'wpaicg_embeddings_reindex',1);
            }
        }

        public function wpaicg_delete_all_embeddings()
        {

            if (!wp_verify_nonce($_POST['nonce'], 'wpaicg-ajax-nonce')) {
                wp_send_json_error(['message' => 'Nonce verification failed.']);
                return;
            }

            if (!current_user_can('manage_options')) {
                wp_send_json_error(['message' => 'You do not have permission for this action.']);
                return;
            }
        
            global $wpdb;
            $ids = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type IN ('wpaicg_embeddings', 'wpaicg_pdfadmin','wpaicg_builder')");
        
            if (empty($ids)) {
                wp_send_json_error(['message' => 'No posts found to delete.']);
                return;
            }
        
            $this->wpaicg_delete_embeddings_ids($ids);
            // Clean up postmeta
            $meta_keys = ['wpaicg_indexed', 'wpaicg_source', 'wpaicg_parent', 'wpaicg_error_msg'];
            foreach ($meta_keys as $meta_key) {
                $wpdb->delete($wpdb->postmeta, ['meta_key' => $meta_key]);
            }
            
            wp_send_json_success(['message' => 'All embeddings have been deleted.']);
        }

        public function wpaicg_delete_embeddings_ids($ids)
        {
            global $wpdb;
            // Common settings
            $wpaicg_qdrant_api_key = get_option('wpaicg_qdrant_api_key', '');
            $wpaicg_qdrant_endpoint = rtrim(get_option('wpaicg_qdrant_endpoint', ''), '/') . '/collections';
            $wpaicg_pinecone_api = get_option('wpaicg_pinecone_api', '');
            $wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment', '');
        
            foreach ($ids as $id) {
                $wpaicg_index = get_post_meta($id, 'wpaicg_index', true);

                // Check if the index belongs to Pinecone or Qdrant
                if (empty($wpaicg_index) || strpos($wpaicg_index, 'pinecone.io') !== false) {
                    // Determine index host
                    $index_host = '';
                    if (!empty($wpaicg_index) && strpos($wpaicg_index, 'pinecone.io') !== false) {
                        $index_host = $wpaicg_index;
                    } else {
                        $index_host = $wpaicg_pinecone_environment;
                    }

                    $index_host_url = 'https://' . $index_host . '/vectors/delete';

                    // Pinecone deletion logic
                    try {
                        $headers = array(
                            'Content-Type' => 'application/json',
                            'Api-Key' => $wpaicg_pinecone_api
                        );
                        $body = json_encode([
                            'deleteAll' => 'false',
                            'ids' => [$id]
                        ]);
                        $response = wp_remote_post($index_host_url, array(
                            'headers' => $headers,
                            'body' => $body
                        ));

                        if (is_wp_error($response)) {

                            error_log(print_r($response, true));
                        }
                    } catch (\Exception $exception) {
                        error_log(print_r($exception->getMessage(), true));
                    }
                } else {
                    // Qdrant deletion logic
                    $collection_name = $wpaicg_index; // Assuming wpaicg_index contains the collection name
                    $endpoint = $wpaicg_qdrant_endpoint . '/' . urlencode($collection_name) . '/points/delete?wait=true';
                    $id = intval($id);
                    $points = json_encode(['points' => [$id]]);
        
                    $response = wp_remote_request($endpoint, [
                        'method' => 'POST',
                        'headers' => ['api-key' => $wpaicg_qdrant_api_key, 'Content-Type' => 'application/json'],
                        'body' => $points,
                    ]);
                    if (is_wp_error($response)) {
                        error_log(print_r($response, true));
                    }
                }
                // get wpaicg_parent from meta and find parent id and delete wpaicg_indexed meta key
                $parent_id = get_post_meta($id, 'wpaicg_parent', true);
                if (!empty($parent_id)) {
                    delete_post_meta($parent_id, 'wpaicg_indexed');
                }
                // Delete post after vector deletion
                wp_delete_post($id);
                
            }
        }
        

        public function wpaicg_reindex_embeddings()
        {
            $wpaicg_result = array('status' => 'success');
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = esc_html__('Nonce verification failed','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            $ids = wpaicg_util_core()->sanitize_text_or_array_field($_REQUEST['ids']);
            $this->wpaicg_reindex_embeddings_ids($ids);
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_builder_delete()
        {
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong', 'gpt3-ai-content-generator'));
            if(!current_user_can('manage_options')){
                $wpaicg_result['msg'] = esc_html__('You do not have permission for this action.', 'gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            if (!wp_verify_nonce($_POST['nonce'], 'wpaicg-ajax-nonce')) {
                $wpaicg_result['msg'] = esc_html__('Nonce verification failed', 'gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                $id = sanitize_text_field($_POST['id']);
                $wpaicg_index = get_post_meta($id, 'wpaicg_index', true);
                
                // Determine the vector DB provider from the post meta
                if (empty($wpaicg_index) || strpos($wpaicg_index, 'pinecone.io') !== false) {
                    // Determine index host
                    $index_host = '';
                    $wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment', '');
                    if (!empty($wpaicg_index) && strpos($wpaicg_index, 'pinecone.io') !== false) {
                        $index_host = $wpaicg_index;
                    } else {
                        $index_host = $wpaicg_pinecone_environment;
                    }

                    $index_host_url = 'https://' . $index_host . '/vectors/delete';

                    $wpaicg_pinecone_api = get_option('wpaicg_pinecone_api', '');
                    

                    if (empty($wpaicg_pinecone_api) || empty($wpaicg_pinecone_environment)) {
                        $wpaicg_result['msg'] = esc_html__('Missing Pinecone API Settings', 'gpt3-ai-content-generator');
                    } else {
                        $headers = [
                            'Content-Type' => 'application/json',
                            'Api-Key' => $wpaicg_pinecone_api
                        ];
                        $body = json_encode([
                            'deleteAll' => 'false',
                            'ids' => [$id]
                        ]);
                        $response = wp_remote_post($index_host_url, [
                            'headers' => $headers,
                            'body' => $body
                        ]);
                    }
                } else {
                    // Qdrant deletion logic
                    $collection_name = $wpaicg_index; // Assuming wpaicg_index contains the collection name for Qdrant
                    $wpaicg_qdrant_api_key = get_option('wpaicg_qdrant_api_key', '');
                    $wpaicg_qdrant_endpoint = rtrim(get_option('wpaicg_qdrant_endpoint', ''), '/') . '/collections/' . $collection_name . '/points/delete?wait=true';
                    $id = intval($id); // Cast $id to integer
                    $points = json_encode(['points' => [$id]]);
                    $response = wp_remote_request($wpaicg_qdrant_endpoint, [
                        'method' => 'POST',
                        'headers' => ['api-key' => $wpaicg_qdrant_api_key, 'Content-Type' => 'application/json'],
                        'body' => $points,
                    ]);
                }
        
                // Handle response
                if (is_wp_error($response)) {
                    $wpaicg_result['msg'] = $response->get_error_message();
                } else {
                    $response_code = wp_remote_retrieve_response_code($response);
                    if ($response_code !== 200) {
                        $wpaicg_result['msg'] = wp_remote_retrieve_body($response);
                    } else {
                        wp_delete_post($id);
                        $wpaicg_result['status'] = 'success';
                    }
                }
            }
            wp_send_json($wpaicg_result);
        }
        

        public function wpaicg_builder_reindex()
        {
            $wpaicg_result = array('status' => 'error','msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if(!current_user_can('manage_options')){
                $wpaicg_result['msg'] = esc_html__('You do not have permission for this action.','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            if ( ! wp_verify_nonce( $_POST['nonce'], 'wpaicg-ajax-nonce' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = esc_html__('Nonce verification failed','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            if(isset($_POST['id']) && !empty($_POST['id'])){
                $id = sanitize_text_field($_POST['id']);
                $parent_id = get_post_meta($id,'wpaicg_parent',true);
                if($id){
                    update_post_meta($id,'wpaicg_indexed','reindex');
                    update_post_meta($parent_id,'wpaicg_indexed','reindex');
                    $wpaicg_result['status'] = 'success';
                }
                else{
                    $wpaicg_result['msg'] = esc_html__('The content intended for re-indexing cannot be found or may have been removed. Please verify the content exists or has not been deleted before attempting to re-index.', 'gpt3-ai-content-generator');
                }
            }
            wp_send_json($wpaicg_result);
        }

        public function wpaicg_cron_job()
        {
            if(isset($_SERVER['argv']) && is_array($_SERVER['argv']) && count($_SERVER['argv'])){
                foreach( $_SERVER['argv'] as $arg ) {
                    $e = explode( '=', $arg );
                    if($e[0] == 'wpaicg_builder') {
                        if (count($e) == 2)
                            $_GET[$e[0]] = sanitize_text_field($e[1]);
                        else
                            $_GET[$e[0]] = 0;
                    }
                }
            }
            if(isset($_GET['wpaicg_builder']) && sanitize_text_field($_GET['wpaicg_builder']) == 'yes'){
                // Initialize WP Filesystem
                global $wp_filesystem;
                if ( empty( $wp_filesystem ) ) {
                    require_once ABSPATH . '/wp-admin/includes/file.php';
                    WP_Filesystem();
                }
                // Check if WP_Filesystem was initialized successfully
                if ( ! $wp_filesystem ) {
                    // Handle error: WP_Filesystem could not be initialized
                    // Maybe log an error or exit gracefully. For minimal change, just proceed, but note this risk.
                    // For now, we'll let the code proceed and potentially fail below if $wp_filesystem is null.
                }


                $wpaicg_running = WPAICG_PLUGIN_DIR.'/wpaicg_builder.txt';

                // Use WP_Filesystem->exists()
                if ( ! $wp_filesystem || ! $wp_filesystem->exists($wpaicg_running) ) {
                    // Use WP_Filesystem->put_contents() instead of fopen/fwrite/fclose
                    $write_result = $wp_filesystem ? $wp_filesystem->put_contents($wpaicg_running, 'running', FS_CHMOD_FILE) : false; // FS_CHMOD_FILE defines default permissions

                    if ( $write_result ) {
                        try {
                            $_SERVER["REQUEST_METHOD"] = 'GET';
                            // Use WP_Filesystem->chmod()
                            if ($wp_filesystem) {
                                $wp_filesystem->chmod($wpaicg_running, 0755);
                            }
                            $this->wpaicg_builer();
                        }
                        catch (\Exception $exception){
                            $wpaicg_error = WPAICG_PLUGIN_DIR.'wpaicg_error.txt';
                            $txt = $exception->getMessage();
                            // Use WP_Filesystem->put_contents() for error log (overwrites/creates)
                            // If appending is strictly necessary, it's more complex:
                            // $current_error = $wp_filesystem ? $wp_filesystem->get_contents($wpaicg_error) : '';
                            // $new_error_content = $current_error . PHP_EOL . $txt;
                            // if ($wp_filesystem) $wp_filesystem->put_contents($wpaicg_error, $new_error_content, FS_CHMOD_FILE);
                            if ($wp_filesystem) {
                                $wp_filesystem->put_contents($wpaicg_error, $txt, FS_CHMOD_FILE);
                            }

                        }
                        // Use WP_Filesystem->delete() instead of unlink()
                        if ($wp_filesystem) {
                            $wp_filesystem->delete($wpaicg_running);
                        }
                    } else {
                        // Handle error: Could not write the running file
                        // Maybe log this failure
                    }

                }
                exit;
            }
        }

        public function wpaicg_print_array($arr, $pad = 0, $padStr = "\t")
        {
            $outerPad = $pad;
            $innerPad = $pad + 1;
            $out = '[';
            foreach ($arr as $k => $v) {
                if (is_array($v)) {
                    $out .= str_repeat($padStr, $innerPad) . $k . ': ' . $this->wpaicg_print_array($v, $innerPad);
                } else {
                    $out .= str_repeat($padStr, $innerPad) . $k . ': ' . $v;
                }
            }
            $out .= str_repeat($padStr, $outerPad) . ']';
            return $out;
        }

        public function wpaicg_custom_post_type($content, $post)
        {
            if(!in_array($post->post_type, array('post','page','product'))){
                $wpaicg_custom_post_fields = get_option('wpaicg_builder_custom_'.$post->post_type,'');
                $new_content = '';
                if(!empty($wpaicg_custom_post_fields)){
                    $exs = explode('||',$wpaicg_custom_post_fields);
                    foreach($exs as $ex){
                        $item = explode('##',$ex);
                        if($item && is_array($item) && count($item) == 2){
                            $key = $item[0];
                            $name = $item[1];
                            /*Check is standard field*/
                            if(substr($key,0,8) == 'wpaicgp_'){
                                $post_key = str_replace('wpaicgp_','',$key);
                                if($post_key == 'post_content'){
                                    $post_value = $content;
                                }
                                elseif($post_key == 'post_date'){
                                    $post_value = get_the_date('', $post->ID);
                                }
                                elseif($post_key == 'post_parent'){
                                    $post_value = get_the_title($post->post_parent);
                                }
                                elseif($post_key == 'permalink'){
                                    $post_value = get_permalink($post->ID);
                                }
                                else{
                                    $post_value = $post->$post_key;
                                }
                                $new_content .= (empty($new_content) ? '': "\n"). $name.': '.$post_value;
                            }
                            /*Check if Custom Meta*/
                            if(substr($key,0,9) == 'wpaicgcf_'){
                                $meta_key = str_replace('wpaicgcf_','',$key);
                                $meta_value = get_post_meta($post->ID,$meta_key,true);
                                $meta_value = apply_filters('wpaicg_meta_value_embedding',$meta_value,$post,$meta_key);
                                if(is_array($meta_value)){
                                    $meta_value = $this->wpaicg_print_array($meta_value);
                                }
                                $new_content .= (empty($new_content) ? '': "\n"). $name.': '.$meta_value;
                            }
                            /*Check if is author fields*/
                            if(substr($key,0,13) == 'wpaicgauthor_'){
                                $user_key = str_replace('wpaicgauthor_','',$key);
                                $author = get_user_by('ID',$post->post_author);
                                $new_content .= (empty($new_content) ? '': "\n"). $name.': '.$author->$user_key;
                            }
                            /*Check Taxonomies*/
                            if(substr($key,0,9) == 'wpaicgtx_'){
                                $taxonomy = str_replace('wpaicgtx_','',$key);
                                $terms = get_the_terms($post->ID,$taxonomy);
                                if(!is_wp_error($terms)){
                                    $terms_string = join(', ', wp_list_pluck($terms, 'name'));
                                    if(!empty($terms_string)){
                                        $new_content .= (empty($new_content) ? '': "\n"). $name.': '.$terms_string;
                                    }
                                }
                            }
                        }
                    }
                    if(empty($new_content)){
                        $new_content .= esc_html__('Post Title','gpt3-ai-content-generator').': '.$post->post_title;
                        $new_content .= "\n".esc_html__('Post Content','gpt3-ai-content-generator').': '.$content;
                    }
                }
                else{
                    $new_content .= esc_html__('Post Title','gpt3-ai-content-generator').': '.$post->post_title;
                    $new_content .= "\n".esc_html__('Post Content','gpt3-ai-content-generator').': '.$content;
                }
                $content = $new_content;
            }
            return $content;
        }

        public function wpaicg_builder_data($wpaicg_data)
        {
            global $wpdb;
            $wpaicg_content = $wpaicg_data->post_content;
            preg_match_all('@\[([^<>&/\[\]\x00-\x20=]++)@', $wpaicg_content, $matches);
            if ($matches && is_array($matches) && count($matches)) {
                $pattern = get_shortcode_regex($matches[1]);
                $wpaicg_content = preg_replace_callback("/$pattern/", 'strip_shortcode_tag', $wpaicg_content);
            }
            $wpaicg_content = trim($wpaicg_content);
            $wpaicg_content = preg_replace("/<((?:style)).*>.*<\/style>/si", ' ',$wpaicg_content);
            $wpaicg_content = preg_replace("/<((?:script)).*>.*<\/script>/si", ' ',$wpaicg_content);
            $wpaicg_content = preg_replace('/<a(.*)href="([^"]*)"(.*)>(.*?)<\/a>/i', '$2', $wpaicg_content);
            $wpaicg_content = wp_strip_all_tags($wpaicg_content);
            $wpaicg_content = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $wpaicg_content);
            $wpaicg_content = trim($wpaicg_content);
            if (empty($wpaicg_content)) {
                update_post_meta($wpaicg_data->ID, 'wpaicg_indexed', 'skip');
                return 'Empty content or probably a shortcode. Skipped.';
            } else {
                /*Check If is Re-Index*/
                $check = $wpdb->get_row($wpdb->prepare("SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key='wpaicg_parent' AND meta_value=%d",$wpaicg_data->ID));
                $wpaicg_old_builder = false;
                if ($check) {
                    $wpaicg_old_builder = $check->post_id;

                    /*Check if old index exist*/
                    $wpaicg_old_index_builder = get_post($check->post_id);
                    if(!$wpaicg_old_index_builder){
                        $wpaicg_old_builder = false;
                    }
                }
                /*For Post*/
                if($wpaicg_data->post_type == 'post'){
                    $wpaicg_new_content = esc_html__('Post Title','gpt3-ai-content-generator').': '.$wpaicg_data->post_title."\n";
                    $wpaicg_new_content .= esc_html__('Post Content','gpt3-ai-content-generator').': '.$wpaicg_content."\n";
                    $wpaicg_new_content .= esc_html__('Post URL','gpt3-ai-content-generator').': '.get_permalink($wpaicg_data->ID);
                    /*Categories*/
                    $categories_name = wp_get_post_categories($wpaicg_data->ID, array('fields' => 'names'));
                    if($categories_name && is_array($categories_name) && count($categories_name)){
                        $wpaicg_new_content .= "\n".esc_html__('Post Categories','gpt3-ai-content-generator').": ".implode(',',$categories_name);
                    }
                    $wpaicg_content = $wpaicg_new_content;
                }
                /*For Page*/
                if($wpaicg_data->post_type == 'page'){
                    $wpaicg_new_content = esc_html__('Page Title','gpt3-ai-content-generator').': '.$wpaicg_data->post_title."\n";
                    $wpaicg_new_content .= esc_html__('Page Content','gpt3-ai-content-generator').': '.$wpaicg_content."\n";
                    $wpaicg_new_content .= esc_html__('Page URL','gpt3-ai-content-generator').': '.get_permalink($wpaicg_data->ID);
                    $wpaicg_content = $wpaicg_new_content;
                }
                /*For Product*/
                if($wpaicg_data->post_type == 'product' && class_exists('WC_Product_Factory')){
                    $wooFac = new \WC_Product_Factory();
                    $wpaicg_product = $wooFac->get_product($wpaicg_data->ID);
                    if($wpaicg_product) {
                        $wpaicg_content_product = '';
                        $product_sku = $wpaicg_product->get_sku();
                        if (!empty($product_sku)) {
                            $wpaicg_content_product .= esc_html__('Product SKU','gpt3-ai-content-generator').': ' . $product_sku . "\n";
                        }
                        $product_title = $wpaicg_product->get_title();
                        $wpaicg_content_product .= esc_html__('Product Name','gpt3-ai-content-generator').': ' . $product_title . "\n";
                        $wpaicg_content_product .= esc_html__('Product Description','gpt3-ai-content-generator').': ' . $wpaicg_content . "\n";
                        if(!empty($wpaicg_data->post_excerpt)){
                            $wpaicg_content_product .= esc_html__('Product Short Description','gpt3-ai-content-generator').': ' . $wpaicg_data->post_excerpt . "\n";
                        }
                        $product_url = $wpaicg_product->get_permalink();
                        $wpaicg_content_product .= esc_html__('Product URL','gpt3-ai-content-generator').': ' . $product_url . "\n";
                        $product_regular_price = $wpaicg_product->get_regular_price();
                        if (!empty($product_regular_price)) {
                            $wpaicg_content_product .= esc_html__('Product Regular Price','gpt3-ai-content-generator').": " . $product_regular_price.' '.get_option('woocommerce_currency','USD') . "\n";
                        }
                        $product_sale_price = $wpaicg_product->get_sale_price();
                        if (!empty($product_sale_price)) {
                            $wpaicg_content_product .= esc_html__('Product Sale Price','gpt3-ai-content-generator').': ' . $product_sale_price.' '.get_option('woocommerce_currency','USD') . "\n";
                        }
                        $product_tax_status = $wpaicg_product->get_tax_status();
                        if (!empty($product_tax_status)) {
                            $wpaicg_content_product .= esc_html__('Tax Status','gpt3-ai-content-generator').': ' . $product_tax_status . "\n";
                        }
                        $product_tax_class = $wpaicg_product->get_tax_class();
                        if (!empty($product_tax_class)) {
                            $wpaicg_content_product .= esc_html__('Tax Class','gpt3-ai-content-generator').': ' . $product_tax_class . "\n";
                        }
                        $product_external_url = '';
                        if ($wpaicg_product->get_type() == 'external') {
                            $product_external_url = $product_url;
                        }
                        if (!empty($product_external_url)) {
                            $wpaicg_content_product .= esc_html__('External Product URL','gpt3-ai-content-generator').': ' . $product_tax_class . "\n";
                        }
                        $product_shipping_weight = $wpaicg_product->get_weight();
                        if (!empty($product_shipping_weight)) {
                            $wpaicg_content_product .= esc_html__('Shipping Weight','gpt3-ai-content-generator').': ' . $product_shipping_weight .' '.get_option('woocommerce_weight_unit','oz'). "\n";
                        }
                        $product_dimensions = '';
                        if (!empty($wpaicg_product->get_length()) || !empty($wpaicg_product->get_width()) || !empty($wpaicg_product->get_height())) {
                            $dimension_unit = get_option('woocommerce_dimension_unit','cm');
                            $product_dimensions = $wpaicg_product->get_length() .$dimension_unit. ', ' . $wpaicg_product->get_width().$dimension_unit . ', ' . $wpaicg_product->get_height().$dimension_unit;
                        }
                        if (!empty($product_dimensions)) {
                            $wpaicg_content_product .= esc_html__('Dimensions','gpt3-ai-content-generator').': ' . $product_dimensions . "\n";
                        }
                        $product_stock_status = $wpaicg_product->get_stock_status();
                        $stock_status_options = wc_get_product_stock_status_options();
                        if(isset($stock_status_options[$product_stock_status]) && !empty($stock_status_options[$product_stock_status])){
                            $wpaicg_content_product .= esc_html__('Stock Status','gpt3-ai-content-generator').': '.$stock_status_options[$product_stock_status]."\n";
                        }
                        $product_attributes = $wpaicg_product->get_attributes();
                        if ($product_attributes && is_array($product_attributes) && count($product_attributes)) {
                            $wpaicg_content_product .= esc_html__('Custom Product Attributes','gpt3-ai-content-generator').': ';
                            foreach ($product_attributes as $keyx => $att) {
                                $options = $att->get_options();
                                $wpaicg_content_product .= $att->get_name() . ': ';
                                foreach ($options as $key => $option) {
                                    $wpaicg_content_product .= $key == 0 ? $option : ',' . $option;
                                }
                                if ($key + 1 == count($options)) {
                                    $wpaicg_content_product .= '; ';
                                }
                            }
                            $wpaicg_content_product .= "\n";
                        }
                        $wpaicg_content = $wpaicg_content_product;
                    }
                }
                /*For custom post type*/
                $wpaicg_content = $this->wpaicg_custom_post_type($wpaicg_content,$wpaicg_data);
                $wpaicg_content = apply_filters('wpaicg_embedding_content_custom_post_type',$wpaicg_content,$wpaicg_data);
                /*End for custom post_type*/
                $wpaicg_result = $this->wpaicg_save_embedding($wpaicg_content, 'wpaicg_builder', $wpaicg_data->post_title, $wpaicg_old_builder);
                if ($wpaicg_result && is_array($wpaicg_result) && isset($wpaicg_result['status'])) {
                    if ($wpaicg_result['status'] == 'error') {
                        /*
                         * If save embedding error
                         * */
                        if ($wpaicg_old_builder) {
                            $embedding_id = $wpaicg_old_builder;
                        } else {
                            $embedding_data = array(
                                'post_type' => 'wpaicg_builder',
                                'post_title' => $wpaicg_data->post_title,
                                'post_content' => $wpaicg_content,
                                'post_status' => 'publish'
                            );
                            $embedding_id = wp_insert_post($embedding_data);
                        }
                        update_post_meta($wpaicg_data->ID, 'wpaicg_indexed', 'error');
                        update_post_meta($embedding_id, 'wpaicg_indexed', 'error');
                        update_post_meta($embedding_id, 'wpaicg_source', $wpaicg_data->post_type);
                        update_post_meta($embedding_id, 'wpaicg_parent', $wpaicg_data->ID);
                        update_post_meta($embedding_id, 'wpaicg_error_msg', $wpaicg_result['msg']);

                        $wpaicg_provider = get_option('wpaicg_provider', 'OpenAI');
                        $wpaicg_vector_db_provider = get_option('wpaicg_vector_db_provider', 'pinecone');
                        $wpaicg_emb_index = get_option('wpaicg_pinecone_environment', '');
                        if ($wpaicg_vector_db_provider === 'qdrant') {
                            $wpaicg_emb_index = get_option('wpaicg_qdrant_default_collection', '');
                        }
                        $wpaicg_emb_model = $wpaicg_provider === 'OpenAI' ? get_option('wpaicg_openai_embeddings', 'text-embedding-ada-002') : ($wpaicg_provider === 'Google' ? get_option('wpaicg_google_embeddings', 'embedding-001') : get_option('wpaicg_azure_embeddings', 'text-embedding-ada-002'));

                        $main_embedding_model = get_option('wpaicg_main_embedding_model', '');
                        if (!empty($main_embedding_model)) {
                            $model_parts = explode(':', $main_embedding_model);
                            if (count($model_parts) === 2) {
                                $wpaicg_emb_model = $model_parts[1];
                                $wpaicg_provider = $model_parts[0];
                            }
                        }
                        
                        update_post_meta($embedding_id, 'wpaicg_provider', $wpaicg_provider);
                        update_post_meta($embedding_id, 'wpaicg_index', $wpaicg_emb_index);
                        update_post_meta($embedding_id, 'wpaicg_model', $wpaicg_emb_model);
                        return $wpaicg_result['msg'];
                    } else {
                        update_option('wpaicg_crojob_builder_content',time());
                        wp_update_post(array(
                            'ID' => $wpaicg_result['id'],
                            'post_content' => $wpaicg_content
                        ));
                        update_post_meta($wpaicg_data->ID, 'wpaicg_indexed', 'yes');
                        update_post_meta($wpaicg_result['id'], 'wpaicg_indexed', 'yes');
                        update_post_meta($wpaicg_result['id'], 'wpaicg_source', $wpaicg_data->post_type);
                        update_post_meta($wpaicg_result['id'], 'wpaicg_parent', $wpaicg_data->ID);

                        $wpaicg_provider = get_option('wpaicg_provider', 'OpenAI');
                        $wpaicg_vector_db_provider = get_option('wpaicg_vector_db_provider', 'pinecone');
                        $wpaicg_emb_index = get_option('wpaicg_pinecone_environment', '');
                        if ($wpaicg_vector_db_provider === 'qdrant') {
                            $wpaicg_emb_index = get_option('wpaicg_qdrant_default_collection', '');
                        }

                        $wpaicg_emb_model = $wpaicg_provider === 'OpenAI' ? get_option('wpaicg_openai_embeddings', 'text-embedding-ada-002') : ($wpaicg_provider === 'Google' ? get_option('wpaicg_google_embeddings', 'embedding-001') : get_option('wpaicg_azure_embeddings', 'text-embedding-ada-002'));

                        $main_embedding_model = get_option('wpaicg_main_embedding_model', '');
                        if (!empty($main_embedding_model)) {
                            $model_parts = explode(':', $main_embedding_model);
                            if (count($model_parts) === 2) {
                                $wpaicg_emb_model = $model_parts[1];
                                $wpaicg_provider = $model_parts[0];
                            }
                        }

                        update_post_meta($wpaicg_result['id'], 'wpaicg_provider', $wpaicg_provider);
                        update_post_meta($wpaicg_result['id'], 'wpaicg_index', $wpaicg_emb_index);
                        update_post_meta($wpaicg_result['id'], 'wpaicg_model', $wpaicg_emb_model);
                        return 'success';
                    }
                } else {
                    if ($wpaicg_old_builder) {
                        $embedding_id = $wpaicg_old_builder;
                    } else {
                        $embedding_data = array(
                            'post_type' => 'wpaicg_builder',
                            'post_title' => $wpaicg_data->post_title,
                            'post_content' => $wpaicg_content,
                            'post_status' => 'publish'
                        );
                        $embedding_id = wp_insert_post($embedding_data);
                    }
                    update_post_meta($embedding_id, 'wpaicg_source', $wpaicg_data->post_type);
                    update_post_meta($embedding_id, 'wpaicg_parent', $wpaicg_data->ID);
                    update_post_meta($wpaicg_data->ID, 'wpaicg_indexed', 'error');
                    update_post_meta($embedding_id, 'wpaicg_indexed', 'error');
                    update_post_meta($embedding_id, 'wpaicg_error_msg', esc_html__('Something went wrong','gpt3-ai-content-generator'));
                    $wpaicg_provider = get_option('wpaicg_provider', 'OpenAI');
                    $wpaicg_vector_db_provider = get_option('wpaicg_vector_db_provider', 'pinecone');
                    $wpaicg_emb_index = get_option('wpaicg_pinecone_environment', '');
                    if ($wpaicg_vector_db_provider === 'qdrant') {
                        $wpaicg_emb_index = get_option('wpaicg_qdrant_default_collection', '');
                    }
                    $wpaicg_emb_model = $wpaicg_provider === 'OpenAI' ? get_option('wpaicg_openai_embeddings', 'text-embedding-ada-002') : ($wpaicg_provider === 'Google' ? get_option('wpaicg_google_embeddings', 'embedding-001') : get_option('wpaicg_azure_embeddings', 'text-embedding-ada-002'));                    
                    
                    $main_embedding_model = get_option('wpaicg_main_embedding_model', '');
                    if (!empty($main_embedding_model)) {
                        $model_parts = explode(':', $main_embedding_model);
                        if (count($model_parts) === 2) {
                            $wpaicg_emb_model = $model_parts[1];
                            $wpaicg_provider = $model_parts[0];
                        }
                    }
                    
                    update_post_meta($embedding_id, 'wpaicg_provider', $wpaicg_provider);
                    update_post_meta($embedding_id, 'wpaicg_index', $wpaicg_emb_index);
                    update_post_meta($embedding_id, 'wpaicg_model', $wpaicg_emb_model);
                    return esc_html__('Something went wrong','gpt3-ai-content-generator');
                }
            }
        }

        public function wpaicg_builer()
        {
            global $wpdb;
            $wpaicg_cron_added = get_option( 'wpaicg_cron_builder_added', '' );
            if(empty($wpaicg_cron_added)){
                update_option( 'wpaicg_cron_builder_added', time() );
            }
            else {
                $wpaicg_has_builder_run = false;
                update_option( 'wpaicg_crojob_builder_last_time', time() );
                $wpaicg_builder_types = get_option('wpaicg_builder_types', []);
                $wpaicg_builder_enable = get_option('wpaicg_builder_enable', '');
                if ($wpaicg_builder_enable == 'yes' && is_array($wpaicg_builder_types) && count($wpaicg_builder_types)) {
                    $commaDelimitedPlaceholders = implode(',', array_fill(0, count($wpaicg_builder_types), '%s'));
                    $wpaicg_data = $wpdb->get_row( $wpdb->prepare(
                        "SELECT p.ID,p.post_title, p.post_content,p.post_type,p.post_excerpt,p.post_date,p.post_parent,p.post_status,p.post_author FROM " . $wpdb->posts . " p LEFT JOIN " . $wpdb->postmeta . " m ON m.post_id=p.ID AND m.meta_key='wpaicg_indexed' WHERE (m.meta_value IS NULL OR m.meta_value='' OR m.meta_value='reindex') AND p.post_content!='' AND p.post_type IN ($commaDelimitedPlaceholders) AND p.post_status = 'publish' ORDER BY p.ID ASC LIMIT 1",
                        $wpaicg_builder_types
                    ) );
                    if($wpaicg_data) {
                        $wpaicg_has_builder_run = true;
                        $this->wpaicg_builder_data($wpaicg_data);
                    }
                }
                if(!$wpaicg_has_builder_run){
                    // wpaicg_embeddings_reindex
                    $wpaicg_embedding_data = $wpdb->get_row("SELECT p.* FROM ".$wpdb->posts." p LEFT JOIN ".$wpdb->postmeta." m ON m.post_id=p.ID WHERE p.post_type='wpaicg_embeddings' AND m.meta_key='wpaicg_embeddings_reindex' AND m.meta_value=1");
                    if($wpaicg_embedding_data){
                        $wpaicg_result = $this->wpaicg_save_embedding($wpaicg_embedding_data->post_content,'','', $wpaicg_embedding_data->ID);
                        if($wpaicg_result['status'] == 'success'){
                            delete_post_meta($wpaicg_embedding_data->ID,'wpaicg_embeddings_reindex');
                        }
                    }
                }
            }
        }

        public function wpaicg_menu()
        {
            $module_settings = get_option('wpaicg_module_settings');
            if ($module_settings === false) {
                $module_settings = array_map(function() { return true; }, \WPAICG\WPAICG_Util::get_instance()->wpaicg_modules);
            }
        
            $modules = \WPAICG\WPAICG_Util::get_instance()->wpaicg_modules;
            if (isset($module_settings['training']) && $module_settings['training']) {
                // --- FIX: Use the literal string for the title ---
                // Get the correct literal title from the $wpaicg_modules array ('Training')
                $training_page_title = esc_html__('Training', 'gpt3-ai-content-generator');
                $training_menu_title = esc_html__('Training', 'gpt3-ai-content-generator');
                // --- END FIX ---

                add_submenu_page(
                    'wpaicg',
                    $training_page_title,  // Use the prepared variable
                    $training_menu_title, // Use the prepared variable
                    $modules['training']['capability'], // Keep dynamic
                    $modules['training']['menu_slug'],  // Keep dynamic
                    array($this, $modules['training']['callback']), // Keep dynamic
                    $modules['training']['position'] // Keep dynamic
                );
            }
        }

        public function wpaicg_main()
        {
            include WPAICG_PLUGIN_DIR.'admin/views/embeddings/index.php';
        }

        public function wpaicg_save_embedding($content, $post_type = '', $title = '', $embeddings_id = false)
        {
            global $wpdb;
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            $wpaicg_provider = get_option('wpaicg_provider', 'OpenAI');
            $openai = WPAICG_OpenAI::get_instance()->openai();
            // Get the AI engine.
            try {
                $openai = WPAICG_Util::get_instance()->initialize_ai_engine();
            } catch (\Exception $e) {
                $wpaicg_result['msg'] = $e->getMessage();
                wp_send_json($wpaicg_result);
            }

            $wpaicg_main_embedding_model = get_option('wpaicg_main_embedding_model', '');
            if (!empty($wpaicg_main_embedding_model)) {
                $wpaicg_main_embedding_model = explode(':', $wpaicg_main_embedding_model);
                $wpaicg_embedding_provider = $wpaicg_main_embedding_model[0];
                try {
                    $openai = WPAICG_Util::get_instance()->initialize_embedding_engine($wpaicg_embedding_provider, $wpaicg_provider);
                } catch (\Exception $e) {
                    $wpaicg_result['msg'] = $e->getMessage();
                    wp_send_json($wpaicg_result);
                }
            } 

            $content = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $content);
            
            if($openai){
                // Determine the model based on the provider
                $wpaicg_provider = get_option('wpaicg_provider', 'OpenAI');
                // Retrieve the embedding model based on the provider
                switch ($wpaicg_provider) {
                    case 'OpenAI':
                        $wpaicg_model = get_option('wpaicg_openai_embeddings', 'text-embedding-ada-002');
                        break;
                    case 'Azure':
                        $wpaicg_model = get_option('wpaicg_azure_embeddings', '');
                        break;
                    case 'Google':
                        $wpaicg_model = get_option('wpaicg_google_embeddings', 'embedding-001');
                        break;
                }

                // check to see if wpaicg_main_embedding_model exists and not empty if yes then get the provider amd model from there: OpenAI:text-embedding-3-large
                $wpaicg_main_embedding_model = get_option('wpaicg_main_embedding_model', '');
                if (!empty($wpaicg_main_embedding_model)) {
                    $wpaicg_main_embedding_model = explode(':', $wpaicg_main_embedding_model);
                    $wpaicg_model = $wpaicg_main_embedding_model[1];
                }

                // Prepare the API call parameters
                $apiParams = [
                    'input' => $content,
                    'model' => $wpaicg_model
                ];

                // Make the API call
                $response = $openai->embeddings($apiParams);
                $response = json_decode($response,true);
                if(isset($response['error']) && !empty($response['error'])) {
                    $wpaicg_result['msg'] = $response['error']['message'];
                    if(empty($wpaicg_result['msg']) && isset($response['error']['code']) && $response['error']['code'] == 'invalid_api_key'){
                        $wpaicg_result['msg'] = 'Incorrect API key provided. You can find your API key at https://platform.openai.com/account/api-keys.';
                    }
                }
                else{
                    $embedding = $response['data'][0]['embedding'];
                    if(empty($embedding)){
                        $wpaicg_result['msg'] = esc_html__('No data returned','gpt3-ai-content-generator');
                    }
                    else{
                        
                        if(!$embeddings_id) {
                            $embedding_title = empty($title) ? mb_substr($content,0,50,'utf-8') : $title;
                            $embedding_data = array(
                                'post_type' => 'wpaicg_embeddings',
                                'post_title' => $embedding_title,
                                'post_content' => $content,
                                'post_status' => 'publish'
                            );
                            if (!empty($post_type)) {
                                $embedding_data['post_type'] = $post_type;
                            }
                            $wpaicg_vector_db_provider = get_option('wpaicg_vector_db_provider', 'pinecone');
                            $wpaicg_emb_index = get_option('wpaicg_pinecone_environment', '');
                            if ($wpaicg_vector_db_provider === 'qdrant') {
                                $wpaicg_emb_index = get_option('wpaicg_qdrant_default_collection', '');
                            }

                            $wpaicg_emb_model = $wpaicg_provider === 'OpenAI' ? 
                            get_option('wpaicg_openai_embeddings', 'text-embedding-ada-002') : 
                            ($wpaicg_provider === 'Google' ? 
                            get_option('wpaicg_google_embeddings', 'embedding-001') : 
                            get_option('wpaicg_azure_embeddings', 'text-embedding-ada-002'));

                            $wpaicg_main_embedding_model = get_option('wpaicg_main_embedding_model', '');
                            if (!empty($wpaicg_main_embedding_model)) {
                                $wpaicg_main_embedding_model = explode(':', $wpaicg_main_embedding_model);
                                $wpaicg_emb_model = $wpaicg_main_embedding_model[1];
                                $wpaicg_provider = $wpaicg_main_embedding_model[0];
                            }
                            
                            $embeddings_id = wp_insert_post($embedding_data,true);
                            if(is_wp_error($embeddings_id)) {
                                $wpaicg_result['msg'] = $embeddings_id->get_error_message();
                                $wpaicg_result['status'] = 'error';
                                return $wpaicg_result;
                            }
                            if(isset($_REQUEST['type']) && !empty($_REQUEST['type'])){
                                add_post_meta($embeddings_id,'wpaicg_embedding_type',sanitize_text_field($_REQUEST['type']));
                            }
                            add_post_meta($embeddings_id, 'wpaicg_provider', $wpaicg_provider);
                            add_post_meta($embeddings_id, 'wpaicg_index', $wpaicg_emb_index);
                            add_post_meta($embeddings_id, 'wpaicg_model', $wpaicg_emb_model);
                        }
                        if(is_wp_error($embeddings_id)){
                            $wpaicg_result['msg'] = $embeddings_id->get_error_message();
                        }
                        else {
                            update_post_meta($embeddings_id,'wpaicg_start',time());
                            $usage_tokens = $response['usage']['total_tokens'];
                            add_post_meta($embeddings_id, 'wpaicg_embedding_token', $usage_tokens);
                            $vectors = array(
                                array(
                                    'id' => (string)$embeddings_id,
                                    'values' => $embedding
                                )
                            );
                            $wpaicg_vector_db_provider = get_option('wpaicg_vector_db_provider', 'pinecone');
                            if ($wpaicg_vector_db_provider === 'pinecone') {
                                $wpaicg_pinecone_api = get_option('wpaicg_pinecone_api','');
                                $wpaicg_pinecone_environment = get_option('wpaicg_pinecone_environment','');
                                $headers = array(
                                    'Content-Type' => 'application/json',
                                    'Api-Key' => $wpaicg_pinecone_api
                                );
                                /*Check Pinecone API*/
                                $response = wp_remote_get('https://api.pinecone.io/indexes',array(
                                    'headers' => $headers
                                ));
                                if(is_wp_error($response)){
                                    $wpaicg_result['msg'] = $response->get_error_message();
                                    return $wpaicg_result;
                                }
                
                                $response_code = $response['response']['code'];
                                if($response_code !== 200){
                                    $wpaicg_result['msg'] = $response['response']['message'];
                                    return $wpaicg_result;
                                }
                                $pinecone_url = 'https://' . $wpaicg_pinecone_environment . '/vectors/upsert';
                                $response = wp_remote_post($pinecone_url, array(
                                    'headers' => $headers,
                                    'body' => json_encode(array('vectors' => $vectors))
                                ));
                                if(is_wp_error($response)){
                                    $wpaicg_result['msg'] = $response->get_error_message();
                                    wp_delete_post($embeddings_id);
                                    $wpdb->delete($wpdb->postmeta, array(
                                        'meta_value' => $embeddings_id,
                                        'meta_key' => 'wpaicg_parent'
                                    ));
                                }
                                else{
                                    $body = json_decode($response['body'],true); 
                                    if($body){
                                        if(isset($body['code']) && isset($body['message'])){
                                            $wpaicg_result['msg'] = wp_strip_all_tags($body['message']);
                                            wp_delete_post($embeddings_id);
                                            $wpdb->delete($wpdb->postmeta, array(
                                                'meta_value' => $embeddings_id,
                                                'meta_key' => 'wpaicg_parent'
                                            ));
                                        }
                                        else{
                                            $wpaicg_result['status'] = 'success';
                                            $wpaicg_result['id'] = $embeddings_id;
                                            update_post_meta($embeddings_id,'wpaicg_completed',time());
                                        }
                                    }
                                    else{
                                        $wpaicg_result['msg'] = esc_html__('No data returned','gpt3-ai-content-generator');
                                        wp_delete_post($embeddings_id);
                                        $wpdb->delete($wpdb->postmeta, array(
                                            'meta_value' => $embeddings_id,
                                            'meta_key' => 'wpaicg_parent'
                                        ));
                                    }
                                }
                            } else {

                                $qdrant_endpoint = rtrim(get_option('wpaicg_qdrant_endpoint', ''), '/') . '/collections';
                                $qdrant_default_collection = get_option('wpaicg_qdrant_default_collection', '');
                                $qdrant_url = $qdrant_endpoint . '/' . $qdrant_default_collection . '/points?wait=true';
                                $qdrant_api_key = get_option('wpaicg_qdrant_api_key', '');

                                $group_id = 'default';

                                // Format for Qdrant
                                $formatted_vector = array(
                                    'id' => $embeddings_id,
                                    'vector' => $embedding,
                                    'payload' => array('group_id' => $group_id)
                                );

                                $vectors = array('points' => array($formatted_vector));
                            
                                // Prepare the request for Qdrant
                                $response = wp_remote_request($qdrant_url, array(
                                    'method'    => 'PUT',
                                    'headers' => ['api-key' => $qdrant_api_key, 'Content-Type' => 'application/json'],
                                    'body'      => json_encode($vectors)
                                ));
                                if(is_wp_error($response)){
                                    $wpaicg_result['msg'] = $response->get_error_message();
                                    wp_delete_post($embeddings_id);
                                    $wpdb->delete($wpdb->postmeta, array(
                                        'meta_value' => $embeddings_id,
                                        'meta_key' => 'wpaicg_parent'
                                    ));
                                }
                                else{
                                    $body = json_decode($response['body'],true);
                                    if($body){
                                        if ($body['status'] === 'ok') {
                                            $wpaicg_result['status'] = 'success';
                                            $wpaicg_result['id'] = $embeddings_id;
                                            update_post_meta($embeddings_id,'wpaicg_completed',time());
                                        }
                                        else{
                                            // Initialize an empty error message
                                            $error_message = 'Unknown error occurred';

                                            // Check if error is directly available or nested inside 'status'
                                            if (isset($body['error'])) {
                                                $error_message = $body['error'];
                                            } elseif (isset($body['status']['error'])) {
                                                $error_message = $body['status']['error'];
                                            }

                                            // Set the error message in the result array
                                            $wpaicg_result['msg'] = "Response from API: " . $error_message;
                                            wp_delete_post($embeddings_id);
                                            $wpdb->delete($wpdb->postmeta, array(
                                                'meta_value' => $embeddings_id,
                                                'meta_key' => 'wpaicg_parent'
                                            ));
                                        }
                                    }
                                    else{
                                        $wpaicg_result['msg'] = esc_html__('No data returned','gpt3-ai-content-generator');
                                        wp_delete_post($embeddings_id);
                                        $wpdb->delete($wpdb->postmeta, array(
                                            'meta_value' => $embeddings_id,
                                            'meta_key' => 'wpaicg_parent'
                                        ));
                                    }
                                }
                            }

                        }
                    }
                }
            }
            else{
                $wpaicg_result['msg'] = esc_html__('Missing API details.','gpt3-ai-content-generator');
            }
            return $wpaicg_result;
        }

        public function wpaicg_embeddings()
        {
            $wpaicg_result = array('status' => 'error', 'msg' => esc_html__('Something went wrong','gpt3-ai-content-generator'));
            if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'wpaicg_embeddings_save' ) ) {
                $wpaicg_result['status'] = 'error';
                $wpaicg_result['msg'] = esc_html__('Nonce verification failed','gpt3-ai-content-generator');
                wp_send_json($wpaicg_result);
            }
            if(isset($_POST['content']) && !empty($_POST['content'])){
                $content = wp_kses_post(wp_strip_all_tags($_POST['content']));
                if(!empty($content)){
                    $wpaicg_result = $this->wpaicg_save_embedding($content);
                }
                else $wpaicg_result['msg'] = esc_html__('Please insert content','gpt3-ai-content-generator');
            }
            wp_send_json($wpaicg_result);
        }
    }
    WPAICG_Embeddings::get_instance();
}
