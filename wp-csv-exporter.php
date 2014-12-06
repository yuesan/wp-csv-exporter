<?php
/*
Plugin Name: WP CSV Exporter
Plugin URI: http://www.kigurumi.asia
Description: You can export posts in CSV format for each post type. It is compatible with posts' custom fields and custom taxonomies. It is also possible to set the number or date range of posts to download.
Author: Nakashima Masahiro
Version: 1.0.0
Author URI: http://www.kigurumi.asia
License: GPLv2 or later
Text Domain: wce
Domain Path: /languages/
 */
require('classes/base.php');

define( 'WCE_VERSION', '1.0.0' );
define( 'WCE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'WCE_PLUGIN_NAME', trim( dirname( WCE_PLUGIN_BASENAME ), '/' ) );
define( 'WCE_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
define( 'WCE_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );

class WP_CSV_Exporter extends Base{
	protected $textdomain = 'wce';
	private $wce_keys = array(
		'gumroad' => '5a9228e90e6b405a6db2fa95f0c8cb0af973e21d3e95e67e9b06e4e932bff3fa',
		'storesjp' => '8ddafa75927b750cf51d51e070a90a8ba1a801ac501bc68e0ff3a8928942d4d1',
	);

	public function __construct() {
		$this->init();

		// 管理メニューに追加するフック
		add_action( 'admin_menu', array( $this, 'admin_menu', ) );

		// css, js
		add_action( 'admin_print_styles', array( $this, 'head_css', ) );
		add_action( 'admin_print_scripts', array( $this, "head_js", ) );

	}


	public function init() {
		//他言語化
		load_plugin_textdomain( $this->textdomain, false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * メニューを表示
	 */
	public function admin_menu() {
		add_submenu_page( 'tools.php', $this->_( 'CSV Export', 'CSVエクスポート' ), $this->_( 'CSV Export', 'CSVエクスポート' ), 'level_7', WCE_PLUGIN_NAME, array( $this, 'show_options_page', ) );
	}

    /**
     * プラグインのメインページ
     */
	public function show_options_page() {
		require_once WCE_PLUGIN_DIR . '/admin/index.php';
	}

    /**
     * Get admin panel URL
     */
    public function setting_url($view = ''){
        $query = array(
            'page' => 'wp-csv-exporter',
        );
        if( $view ){
            $query['view'] = $view;
        }
        return admin_url('tools.php?'.http_build_query($query));
    }

	/**
	 * 管理画面CSS追加
	 */
	public function head_css() {
		if ( $_REQUEST["page"] == WCE_PLUGIN_NAME ) {
			wp_enqueue_style( "wce_css", WCE_PLUGIN_URL . '/css/style.css' );
			wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
		}
	}

	/*
	 * 管理画面JS追加
	 */
	public function head_js() {
		if ( $_REQUEST["page"] == WCE_PLUGIN_NAME && $_REQUEST["view"] != 'setting' ) {
			wp_enqueue_script('jquery');
			wp_enqueue_script( "jquery-ui-core" );
			wp_enqueue_script( "jquery-ui-datepicker" );
			wp_enqueue_script( "wce_admin_js", WCE_PLUGIN_URL . '/js/admin.js', array('jquery'), '', true );
		}
	}

	/**
	 * カスタムフィールドリストを取得
	 */
	public function get_custom_field_list( $type ) {
		global $wpdb;
		$value_parameter = esc_html( $type );
		$pattern = "\_%";
		$query = <<< EOL
SELECT DISTINCT meta_key
FROM $wpdb->postmeta
INNER JOIN $wpdb->posts
        ON $wpdb->posts.ID = $wpdb->postmeta.post_id
WHERE $wpdb->posts.post_type = '%s'
AND $wpdb->postmeta.meta_key NOT LIKE '%s'
EOL;
		return $wpdb->get_results( $wpdb->prepare( $query, array( $value_parameter, $pattern ) ), ARRAY_A );
	}


	/**
	 * ライセンスキーの確認
	 */
	public function verify_license_key( $license_key ) {
		$license_key_sha256 = hash_hmac( 'sha256' , $license_key , false );
		foreach ( $this->wce_keys as $key => $value ) {
			if ( $value == $license_key_sha256 ) {
				$wce_options['license_key'] = $license_key_sha256;
				update_option( 'wce_options', $wce_options );
				return true;
			}
		}
		update_option( 'wce_options', $license_key );
		return false;
	}

	/**
	 * 認証確認
	 *
	 * @return boolean [description]
	 */
	public function is_certified() {
		$wce_options = get_option( 'wce_options' );
		if ( !empty( $wce_options ) && isset( $wce_options['license_key'] ) ) {
			foreach ( $this->wce_keys as $key => $value ) {
				if ( $wce_options['license_key'] == $value ) {
					return true;
				}
			}
		}
		return false;
	}

}
$wp_csv_exporter = new WP_CSV_Exporter();
