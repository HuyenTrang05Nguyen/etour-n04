<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Etour_Cd04_Admin {
	public static function register_menu() {
		add_menu_page( 'Mùa vụ CD04', 'Mùa vụ CD04', 'etour_manage', 'etour-cd04', array( __CLASS__, 'render_page' ), 'dashicons-palmtree', 26 );
	}
	public static function register_actions() {}
	public static function render_page() {
		echo '<div class="wrap"><h1>Quản lý Mùa vụ CD04 Miệt Vườn</h1><p>Hệ thống dữ liệu đã sẵn sàng.</p></div>';
	}
}