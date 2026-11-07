<?php
/**
 * Plugin Name: E-Tourism CD04 - Lịch mùa vụ & Gợi ý tour miệt vườn
 * Description: Module tra cứu mùa vụ trái cây, lễ hội miền Tây và gợi ý tour theo tháng (CD04).
 * Version: 1.0.1
 * Author: Nhóm N04
 * Text Domain: etour-cd04
 */

// Chặn truy cập trực tiếp tệp (không qua WordPress).
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Dùng defined() để tránh fatal error khi plugin bị load nhiều lần.
if ( ! defined( 'ETOUR_VERSION' ) )         define( 'ETOUR_VERSION',         '1.0.1' );
if ( ! defined( 'ETOUR_DB_VERSION' ) )      define( 'ETOUR_DB_VERSION',      '1.0' );
if ( ! defined( 'ETOUR_CD04_DB_VERSION' ) ) define( 'ETOUR_CD04_DB_VERSION', '1.0' );
if ( ! defined( 'ETOUR_FILE' ) )            define( 'ETOUR_FILE',            __FILE__ );
if ( ! defined( 'ETOUR_DIR' ) )             define( 'ETOUR_DIR',             plugin_dir_path( __FILE__ ) );
if ( ! defined( 'ETOUR_URL' ) )             define( 'ETOUR_URL',             plugin_dir_url( __FILE__ ) );
if ( ! defined( 'ETOUR_CD04_DIR' ) )        define( 'ETOUR_CD04_DIR',        plugin_dir_path( __FILE__ ) );

require_once ETOUR_DIR . 'includes/class-etour-db.php';
require_once ETOUR_DIR . 'includes/class-etour-form.php';
require_once ETOUR_DIR . 'includes/class-etour-admin.php';
require_once ETOUR_DIR . 'includes/class-etour-rest.php';
require_once ETOUR_DIR . 'includes/class-etour-cd04-db.php';
require_once ETOUR_DIR . 'includes/class-etour-cd04-form.php';    // Shortcode [etour_seasons_page]
require_once ETOUR_DIR . 'includes/class-etour-cd04-rest.php';
require_once ETOUR_DIR . 'includes/class-etour-cd04-admin.php';

// Tạo/nâng cấp bảng CSDL khi kích hoạt plugin.
register_activation_hook( __FILE__, array( 'Etour_DB',      'install' ) );
register_activation_hook( __FILE__, array( 'Etour_Cd04_DB', 'install' ) );

add_action( 'plugins_loaded', array( 'Etour_DB',       'maybe_upgrade' ) );
add_action( 'plugins_loaded', array( 'Etour_Cd04_DB',  'maybe_upgrade' ) );
add_action( 'init',           array( 'Etour_Form',     'register' ) );
add_action( 'init',           array( 'Etour_Cd04_Form', 'register' ) ); // Shortcode [etour_seasons_page]
// admin_post_* hooks phải được đăng ký trên 'init' (chạy sớm hơn admin_init)
// để admin-post.php xử lý được POST request trước khi trang admin tải.
add_action( 'init',           array( 'Etour_Admin',    'register_actions' ) );
add_action( 'admin_menu',     array( 'Etour_Admin',    'register_menu' ) );
add_action( 'admin_menu',     array( 'Etour_Cd04_Admin', 'register_menu' ) );
add_action( 'rest_api_init',  array( 'Etour_REST',     'register_routes' ) );
add_action( 'rest_api_init',  array( 'Etour_Cd04_REST','register_routes' ) );

// Đăng ký (chưa enqueue) tài nguyên CSS/JS; shortcode sẽ enqueue khi cần.
add_action( 'wp_enqueue_scripts', function () {
	wp_register_style(  'etour-core', ETOUR_URL . 'assets/css/etour-core.css', array(), ETOUR_VERSION );
	wp_register_script( 'etour-core', ETOUR_URL . 'assets/js/etour-core.js',   array(), ETOUR_VERSION, true );
	wp_register_style(  'etour-cd04', ETOUR_URL . 'assets/css/etour-cd04.css', array( 'etour-core' ), ETOUR_VERSION );
	wp_register_script( 'etour-cd04', ETOUR_URL . 'assets/js/etour-cd04.js',   array(), ETOUR_VERSION, true );

	wp_localize_script( 'etour-core', 'ETOUR', array(
		'restUrl' => esc_url_raw( rest_url( 'etour/v1/' ) ),
		'nonce'   => wp_create_nonce( 'wp_rest' ),
	) );
	wp_localize_script( 'etour-cd04', 'ETOUR_CD04', array(
		'restUrl' => esc_url_raw( rest_url( 'etour/v1/' ) ),
		'nonce'   => wp_create_nonce( 'wp_rest' ),
	) );
} );



