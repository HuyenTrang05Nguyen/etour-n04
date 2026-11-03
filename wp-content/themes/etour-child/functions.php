<?php
/**
 * E-Tourism Child – functions.php
 * Nạp CSS/JS của nhóm theo đúng thứ tự phụ thuộc và bổ sung các phần giao diện chung.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ETC_VERSION', '1.0.0' );

add_action( 'wp_enqueue_scripts', function () {
	$uri = get_stylesheet_directory_uri();
	// Storefront đã tự nạp style.css của theme cha; child theme chỉ nạp tệp của nhóm.
	wp_enqueue_style( 'etc-tokens', $uri . '/assets/css/tokens.css', array( 'storefront-style' ), ETC_VERSION );
	wp_enqueue_style( 'etc-layout', $uri . '/assets/css/layout.css', array( 'etc-tokens' ), ETC_VERSION );
	wp_enqueue_style( 'etc-components', $uri . '/assets/css/components.css', array( 'etc-layout' ), ETC_VERSION );
	// JS của nhóm: không khai báo phụ thuộc 'jquery'; nạp ở cuối trang (in_footer = true).
	wp_enqueue_script( 'etc-main', $uri . '/assets/js/main.js', array(), ETC_VERSION, true );
}, 20 );

/** Phông chữ hệ thống ưu tiên Roboto (nạp từ Google Fonts với display=swap để không chặn kết xuất). */
add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'etc-font', 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap', array(), null );
}, 5 );

/** Khối giới thiệu (hero) chỉ hiển thị ở trang chủ. */
add_action( 'storefront_before_content', function () {
	if ( ! is_front_page() ) {
		return;
	}
	get_template_part( 'template-parts/hero' );
}, 5 );

/** Dòng ghi chú bắt buộc ở chân trang (YC10). */
add_action( 'storefront_footer', function () {
	echo '<p class="etc-disclaimer">' . esc_html__( 'Website phục vụ học tập – dữ liệu mô phỏng – không phát sinh giao dịch thật.', 'etour-child' ) . '</p>';
}, 30 );

/** Ẩn phiên bản WordPress khỏi mã nguồn trang (giảm lộ thông tin – YC09). */
remove_action( 'wp_head', 'wp_generator' );

/** Định dạng ngày cho bộ đếm khuyến mãi: in thuộc tính data-countdown trên sản phẩm đang giảm giá có hạn. */
add_action( 'woocommerce_single_product_summary', function () {
	global $product;
	if ( ! $product || ! $product->is_on_sale() ) {
		return;
	}
	$to = $product->get_date_on_sale_to();
	if ( $to ) {
		printf(
			'<p class="etc-countdown" data-countdown="%s" aria-live="polite">Ưu đãi kết thúc sau: <strong data-countdown-text>…</strong></p>',
			esc_attr( $to->date( DATE_ATOM ) )
		);
	}
}, 11 );

/** Biểu tượng trang (favicon) mặc định khi chưa thiết lập Site Icon – tránh lỗi 404 /favicon.ico trong Console. */
add_action( 'wp_head', function () {
	if ( has_site_icon() ) {
		return;
	}
	$uri = get_stylesheet_directory_uri() . '/assets/img/';
	echo '<link rel="icon" type="image/png" sizes="32x32" href="' . esc_url( $uri . 'favicon-32.png' ) . '">' . "\n";
	echo '<link rel="apple-touch-icon" href="' . esc_url( $uri . 'favicon.png' ) . '">' . "\n";
}, 2 );

/** Thẻ mô tả meta cho SEO (Lighthouse: "Tài liệu không có phần mô tả meta"). */
add_action( 'wp_head', function () {
	if ( is_singular() && has_excerpt() ) {
		$desc = get_the_excerpt();
	} elseif ( function_exists( 'is_product_category' ) && is_product_category() ) {
		$desc = term_description();
	} else {
		$desc = get_bloginfo( 'description' );
	}
	$desc = wp_trim_words( wp_strip_all_tags( (string) $desc ), 30, '…' );
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
}, 1 );

/** Kết nối sớm tới máy chủ phông chữ để giảm thời gian chặn hiển thị. */
add_filter( 'wp_resource_hints', function ( $urls, $relation ) {
	if ( 'preconnect' === $relation ) {
		$urls[] = 'https://fonts.googleapis.com';
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' );
	}
	return $urls;
}, 10, 2 );
