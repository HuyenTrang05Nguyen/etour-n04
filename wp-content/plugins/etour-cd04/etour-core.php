<?php
/**
 * Plugin Name:       E-Tourism Core (Starter Kit)
 * Description:       Module mẫu cho Dự án E-Tourism – CSE703072: tạo bảng tùy biến, form shortcode xử lý an toàn, trang quản trị, REST API và Fetch. Nhóm đổi tên thành etour-<mã chủ đề> và phát triển theo chủ đề.
 * Version:           1.0.0
 * Requires at least: 6.5
 * Requires PHP:      8.1
 * Author:            CSE703072 Starter Kit
 * Text Domain:       etour-core
 * License:           GPL-2.0-or-later
 */

// Chặn truy cập trực tiếp tệp (không qua WordPress).
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ETOUR_VERSION', '1.0.0' );
define( 'ETOUR_DB_VERSION', '1.0' );
define( 'ETOUR_FILE', __FILE__ );
define( 'ETOUR_DIR', plugin_dir_path( __FILE__ ) );
define( 'ETOUR_URL', plugin_dir_url( __FILE__ ) );

require_once ETOUR_DIR . 'includes/class-etour-db.php';
require_once ETOUR_DIR . 'includes/class-etour-form.php';
require_once ETOUR_DIR . 'includes/class-etour-admin.php';
require_once ETOUR_DIR . 'includes/class-etour-rest.php';

// Tầng dữ liệu: tạo/cập nhật bảng khi kích hoạt plugin.
register_activation_hook( __FILE__, array( 'Etour_DB', 'install' ) );

add_action( 'plugins_loaded', array( 'Etour_DB', 'maybe_upgrade' ) );
add_action( 'init', array( 'Etour_Form', 'register' ) );
add_action( 'admin_menu', array( 'Etour_Admin', 'register_menu' ) );
add_action( 'rest_api_init', array( 'Etour_REST', 'register_routes' ) );

// Nạp tài nguyên phía người dùng chỉ khi trang có shortcode của module.
add_action( 'wp_enqueue_scripts', function () {
	wp_register_style( 'etour-core', ETOUR_URL . 'assets/css/etour-core.css', array(), ETOUR_VERSION );
	wp_register_script( 'etour-core', ETOUR_URL . 'assets/js/etour-core.js', array(), ETOUR_VERSION, true );
	wp_localize_script( 'etour-core', 'ETOUR', array(
		'restUrl' => esc_url_raw( rest_url( 'etour/v1/' ) ),
		'nonce'   => wp_create_nonce( 'wp_rest' ),
	) );
} );
