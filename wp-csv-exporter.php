<?php
/*
Plugin Name: WP CSV Exporter
Plugin URI: http://www.kigurumi.asia/imake/3603/
Description:
Author: Nakashima Masahiro
Version: 1.0
Author URI: http://www.kigurumi.asia
License: GPLv2
Text Domain: wce
Domain Path: /languages/
 */
define( 'WCE_VERSION', '1.0' );
define( 'WCE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'WCE_PLUGIN_NAME', trim( dirname( WCE_PLUGIN_BASENAME ), '/' ) );
define( 'WCE_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
define( 'WCE_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );

class WP_CSV_Exporter {
	private $textdomain = 'wce';

	public function __construct() {

		add_action( 'init', array( $this, 'init', ) );

		// 管理メニューに追加するフック
		add_action( 'admin_menu', array( $this, 'admin_menu', ) );

		// css, js
		add_action( 'admin_print_styles', array( $this, 'head_css', ) );
		add_action( 'admin_print_scripts', array( $this, "head_js", ) );

		// プラグインが有効・無効化されたとき
		register_activation_hook( __FILE__, array( $this, 'activationHook', ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivationHook', ) );
	}

	function init() {

	}

	// 上のフックに対するaction関数
	function admin_menu() {
		add_submenu_page( 'tools.php', 'CSVエクスポート', 'CSVエクスポート', 'level_7', WCE_PLUGIN_NAME, array( $this, 'show_options_page', ) );
	}

	function show_options_page() {
		require_once WCE_PLUGIN_DIR . '/admin/admin.php';
	}

	/**
	 * カスタムフィールドリストを取得
	 */
	function get_custom_field_list( $type ) {
		global $wpdb;
		$value_parameter = esc_html($type);
		$query = <<< EOL
SELECT DISTINCT meta_key
FROM $wpdb->postmeta
LEFT JOIN $wpdb->posts
        ON $wpdb->posts.id = $wpdb->postmeta.post_id
WHERE $wpdb->posts.post_type = '%s'
AND $wpdb->postmeta.meta_key NOT LIKE '\_%'
EOL;
		return $wpdb->get_results( $wpdb->prepare($query, $value_parameter), ARRAY_A );
	}

	/**
	 * 管理画面CSS追加
	 */
	function head_css() {
		if ( $_REQUEST["page"] == WCE_PLUGIN_NAME ) {
			wp_enqueue_style( "wce_css", WCE_PLUGIN_URL . '/css/style.css' );
			wp_enqueue_style( "jquery-ui_css", WCE_PLUGIN_URL . '/js/jquery-ui/jquery-ui.css' );
		}
	}

	/*
	 * 管理画面JS追加
	 */
	function head_js() {
		if ( $_REQUEST["page"] == WCE_PLUGIN_NAME ) {
			wp_enqueue_script( "wce_js", WCE_PLUGIN_URL . '/js/scripts.js', array(
					"jquery",
				) );

			wp_enqueue_script( "jquery-ui", WCE_PLUGIN_URL . '/js/jquery-ui/jquery-ui.js' );
		}
	}

	/**
	 * esc_htmlの配列対応
	 */
	function esc_htmls( $str ) {
		if ( is_array( $str ) ) {
			return array_map( "esc_html", $str );
		}else {
			return esc_html( $str );
		}
	}

	/**
	 * プラグインが有効化されたときに実行
	 */
	function activationHook() {
		if ( !get_option( 'aac_options' ) ) {
			update_option( 'aac_options', $this->aac_defalt_options );
		}
	}

	/**
	 * 無効化ときに実行
	 */
	function deactivationHook() {
		// delete_option( 'aac_options' );
	}

	/**
	 * 翻訳用
	 */
	public function e( $text, $ja = null ) {
		_e( $text, $this->textdomain );
	}
	public function _( $text, $ja = null ) {
		return __( $text, $this->textdomain );
	}
}
$wp_csv_exporter = new WP_CSV_Exporter();
