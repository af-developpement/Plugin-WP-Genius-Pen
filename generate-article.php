<?php
/*
Plugin Name: Generate Article
Description: En fonction du titre de l'article et des options choisis, cela génère un article.
Author: A&F Dev
Version: 1.0
Author URI: https://af-developpement.com
*/

if (!function_exists('is_plugin_active')) {
	include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

// PAGE D'ADMIN POUR LE TOKEN -------------------------------------------

function af_generate_article_deactivate() {
	unregister_setting('af_generate_article_options_group', 'af_generate_article_token');
}
register_deactivation_hook( __FILE__, 'af_generate_article_deactivate' );

function af_generate_article_setting_page() {
	add_options_page('Paramètrage plugin : A&F Générate Article', 'A&F Générate Article', 'manage_options', 'af-generate-article', 'af_generate_article_page_html_form');
}
add_action('admin_menu', 'af_generate_article_setting_page');

function af_generate_article_register_settings() {
	register_setting('af_generate_article_options_group', 'af_generate_article_token', ['sanitize_callback' => "af_generate_article_settings_validate"]);
}
add_action('admin_init', 'af_generate_article_register_settings');

function af_generate_article_page_html_form() {
	include_once(plugin_dir_path( __FILE__ ) . 'template-parts/option-page.php');
}

function af_generate_article_template($post) {
	if($post->post_type === "post" && current_user_can( 'edit_posts' )) {
		include_once(plugin_dir_path( __FILE__ ) . 'template-parts/options.php');
	}
}

function af_generate_article_metabox() {
	add_meta_box('generate-article', 'Générez votre article', 'af_generate_article_template', 'post', 'side');
}
add_action('add_meta_boxes', 'af_generate_article_metabox');

function af_generate_article_register_styles_and_scripts() {
	$page = get_current_screen();

	if(is_admin() && $page->id === "settings_page_af-generate-article") {
		wp_enqueue_script('token-verification', plugin_dir_url(__FILE__) . 'assets/js/token.js', ['jquery'], '1.0', true);
	}

	if(is_admin() && $page->id === "post") {
		wp_enqueue_script('generate-article', plugin_dir_url(__FILE__) . 'assets/js/article.js', ['jquery'], '1.0', true);
	}
}
add_action( 'admin_enqueue_scripts', 'af_generate_article_register_styles_and_scripts' );

function af_generate_article_settings_validate($args){
	if(!isset($args) || !preg_match('/^[0-9a-zA-Z]*$/', $args)){
		$args = '';
		add_settings_error('af_generate_article_settings', 'af_generate_article_invalid_token', 'Le token n\'a pas le bon format', $type = 'error');
	}

	return $args;
}


// GESTION VERIFICATION DU TOKEN --------------------------------

add_action( 'wp_ajax_token_verification', 'af_generate_article_token_verification_response' );
add_action( 'wp_ajax_nopriv_token_verification', 'af_generate_article_token_verification_response' );

function af_generate_article_token_verification_response() {
    if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'token_verification_nonce')) {
        $token = sanitize_text_field($_POST['token']);

        $url = "https://wp-ai-writter.com/verification";
        $response = wp_remote_post($url, [
            'method'    => 'POST',
            'timeout'   => 30,
            'sslverify' => false,
            'body' => [
                'token' => $token,
                'url' => $_SERVER['SERVER_NAME']
            ],
        ]);

        if (is_wp_error($response)) {
            wp_send_json_error(['success' => false, 'message' => $response["response"]["message"]], 500);
            return;
        }


        if ($response["response"]["code"] === 404) {
            wp_send_json_error(['success' => false, 'message' => $response["response"]["message"]], 404);
            return;
        }


        wp_send_json_success(['success' => true]);
    } else {
        wp_send_json_error(['success' => false, 'message' => 'Nonce invalide ou expiré'], 401);
    }
}


// GESTION DE LA GENERATION D'ARTICLES --------------------------------

add_action( 'wp_ajax_form_content_generator', 'af_generate_article_form_content_generator_response' );
add_action( 'wp_ajax_nopriv_form_content_generator', 'af_generate_article_form_content_generator_response' );

function af_generate_article_form_content_generator_response() {
    if (isset($_POST['nonce']) && wp_verify_nonce($_POST['nonce'], 'generate_article_nonce')) {
        $url = "https://wp-genius-pen.com/api/send";
        $response = wp_remote_get($url, [
            'method'    => 'GET',
            'timeout'   => 30,
            'sslverify' => false,
            'body'      => [
                'token'     => get_option('af_generate_article_token'),
                'url'       => $_SERVER['SERVER_NAME'],
                'title'     => $_POST['title'],
                'nbrTokens' => absint($_POST['nbrTokens']),
	            'keyWords' => sanitize_text_field($_POST['keyWords'])
            ],
        ]);


        if (is_wp_error($response)) {
            wp_send_json_error(['success' => false, 'message' => $response["response"]["message"]], 500);
            return;
        }

        if ($response["response"]["code"] === 402) {
            wp_send_json_error(['success' => false, 'message' => $response["response"]["message"]], 402);
            return;
        }

        if ($response["response"]["code"] === 403) {
            wp_send_json_error(['success' => false, 'message' => $response["response"]["message"]], 403);
            return;
        }

        if ($response["response"]["code"] === 404) {
            wp_send_json_error(['success' => false, 'message' => $response["response"]["message"]], 404);
            return;
        }

        $return = [
            'success' => true,
            'isClassicEditor' => is_plugin_active('classic-editor/classic-editor.php'),
            'content' => $response["body"],
        ];

        wp_send_json_success($return);
    } else {
        wp_send_json_error(['success' => false, 'message' => 'Invalid or expired nonce provided.'], 401);
    }
}

