<?php
/**
 * Phục hồi dữ liệu về trạng thái cũ:
 * Chuyển toàn bộ sản phẩm trong thùng rác (trash) trở lại trạng thái publish
 */
add_action( 'init', function () {
	global $wpdb;
	$wpdb->query(
		"UPDATE {$wpdb->posts}
		 SET post_status = 'publish'
		 WHERE post_status = 'trash'
		   AND post_type IN ('product', 'product_variation')"
	);
	delete_transient( 'wc_count_comments' );
	delete_transient( 'wc_products_onsale' );
	delete_transient( 'wc_featured_products' );
	if ( function_exists( 'wc_delete_product_transients' ) ) {
		wc_delete_product_transients();
	}
} );

/**
 * Nạp hệ thống thiết kế giao diện hiện đại (Modern Eco-Tourism Design)
 */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'etour-modern-ui', content_url( 'mu-plugins/etour-modern.css' ), array(), '1.2.0' );
}, 99 );

/**
 * Khắc phục triệt để lỗi ảnh sản phẩm bị co thành 1x1 px trên trang cửa hàng
 */
add_filter( 'wp_get_attachment_image_attributes', function ( $attr ) {
	if ( isset( $attr['width'] ) && 1 == $attr['width'] ) {
		$attr['width'] = 324;
	}
	if ( isset( $attr['height'] ) && 1 == $attr['height'] ) {
		$attr['height'] = 324;
	}
	return $attr;
}, 10, 1 );
