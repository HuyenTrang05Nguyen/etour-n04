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

/**
 * Thêm section "Tour Khuyến mãi & Nổi bật" vào trang chủ sau hero.
 */
add_action( 'storefront_before_content', function () {
	if ( ! is_front_page() || ! function_exists( 'wc_get_products' ) ) {
		return;
	}

	$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

	// Lấy tour đang sale (tối đa 6)
	$on_sale = wc_get_products( array(
		'status'  => 'publish',
		'on_sale' => true,
		'limit'   => 6,
		'orderby' => 'rand',
	) );

	// Nếu ít hơn 3, bổ sung bằng tour mới nhất
	if ( count( $on_sale ) < 3 ) {
		$extra = wc_get_products( array(
			'status'  => 'publish',
			'limit'   => 6 - count( $on_sale ),
			'orderby' => 'date',
			'order'   => 'DESC',
			'exclude' => array_map( function( $p ) { return $p->get_id(); }, $on_sale ),
		) );
		$on_sale = array_merge( $on_sale, $extra );
	}

	if ( empty( $on_sale ) ) return;

	$tour_count = count( wc_get_products( array( 'status' => 'publish', 'limit' => -1, 'return' => 'ids' ) ) );
	?>
	<section class="etc-featured-tours" aria-labelledby="etc-featured-title">
		<div class="etc-featured-tours__header">
			<h2 id="etc-featured-title" class="etc-featured-tours__title">Tour Khuyến mãi &amp; Nổi bật</h2>
			<div class="etc-featured-tours__actions">
				<select class="etc-featured-sort" aria-label="Lựa chọn khác" onchange="if(this.value) window.location=this.value">
					<option value="">Lựa chọn khác</option>
					<option value="<?php echo esc_url( add_query_arg( 'orderby', 'price', $shop_url ) ); ?>">Giá thấp nhất</option>
					<option value="<?php echo esc_url( add_query_arg( 'orderby', 'price-desc', $shop_url ) ); ?>">Giá cao nhất</option>
					<option value="<?php echo esc_url( add_query_arg( 'orderby', 'popularity', $shop_url ) ); ?>">Phổ biến nhất</option>
				</select>
				<a href="<?php echo esc_url( $shop_url ); ?>" class="etc-view-all-btn">
					Xem toàn bộ <?php echo esc_html( $tour_count ); ?> tour →
				</a>
			</div>
		</div>

		<div class="etc-featured-tours__grid">
		<?php foreach ( array_slice( $on_sale, 0, 6 ) as $product ) :
			$img_id  = $product->get_image_id();
			$img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'woocommerce_thumbnail' ) : '';
			$cats    = wp_get_post_terms( $product->get_id(), 'product_cat', array( 'fields' => 'names' ) );
			$cat     = ! empty( $cats ) ? implode( ', ', $cats ) : '';
			$sale    = $product->is_on_sale();
			$sku     = $product->get_sku();
			// Tháng gợi ý từ bảng tour_months
			global $wpdb;
			$months_raw = $wpdb->get_col( $wpdb->prepare(
				"SELECT month FROM {$wpdb->prefix}etour_tour_months WHERE product_sku = %s ORDER BY month",
				$sku
			) );
			$months_str = ! empty( $months_raw )
				? 'Tháng: ' . implode( ', ', array_map( function( $m ) { return 'T' . $m; }, $months_raw ) )
				: '';
		?>
			<article class="etc-tour-card" itemscope itemtype="https://schema.org/Product">
				<div class="etc-tour-card__img">
					<?php if ( $sale ) : ?>
						<span class="etc-tour-card__badge">GIẢM GIÁ!</span>
					<?php endif; ?>
					<?php if ( $sku ) : ?>
						<span class="etc-tour-card__sku"><?php echo esc_html( $sku ); ?><?php echo $cat ? ' · ' . esc_html( $cat ) : ''; ?></span>
					<?php endif; ?>
					<?php if ( $img_url ) : ?>
						<img src="<?php echo esc_url( $img_url ); ?>"
						     alt="<?php echo esc_attr( $product->get_name() ); ?>"
						     loading="lazy" itemprop="image">
					<?php else : ?>
						<div class="etc-tour-card__img-placeholder"></div>
					<?php endif; ?>
					<?php if ( $months_str ) : ?>
						<span class="etc-tour-card__months"><?php echo esc_html( 'Mekong Miệt Vườn & Chợ Nổi · ' . $months_str ); ?></span>
					<?php endif; ?>
				</div>

				<div class="etc-tour-card__body">
					<?php if ( $cat ) : ?>
						<span class="etc-tour-card__cat"><?php echo esc_html( $cat ); ?></span>
					<?php endif; ?>

					<h3 class="etc-tour-card__title" itemprop="name">
						<a href="<?php echo esc_url( $product->get_permalink() ); ?>" itemprop="url">
							<?php echo esc_html( $product->get_name() ); ?>
						</a>
					</h3>

					<div class="etc-tour-card__tags">
						<span class="etc-tour-card__tag"><?php echo $product->is_type( 'variable' ) ? 'Sản phẩm biến thể' : 'Sản phẩm đơn'; ?></span>
						<?php if ( $months_str ) : ?>
							<span class="etc-tour-card__tag">Mùa đẹp: <?php echo esc_html( implode( ', ', array_map( function($m){ return 'T'.$m; }, array_slice( $months_raw, 0, 4 ) ) ) ) . ( count($months_raw) > 4 ? '…' : '' ); ?></span>
						<?php endif; ?>
					</div>

					<div class="etc-tour-card__price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
						<?php if ( $sale && $product->get_regular_price() ) : ?>
							<span class="etc-tour-card__regular"><?php echo wc_price( $product->get_regular_price() ); ?></span>
						<?php endif; ?>
						<span class="etc-tour-card__sale"><?php echo wc_price( $product->get_price() ); ?></span>
					</div>
				</div>
			</article>
		<?php endforeach; ?>
		</div>
	</section>
	<?php
}, 6 );

/** Dòng ghi chú bắt buộc ở chân trang (YC10). */
add_action( 'storefront_footer', function () {
	// Footer 3 cột theo mockup
	?>
	<div class="etc-footer-widgets">
		<div class="etc-footer-widgets__inner">

			<!-- Cột 1: Giới thiệu nhóm -->
			<div class="etc-footer-col">
				<h3 class="etc-footer-col__title">Mekong Miệt Vườn &amp; Chợ Nổi – Nhóm N04</h3>
				<p>Chủ đề CD04: Du lịch miệt vườn và chợ nổi Đồng bằng sông Cửu Long. Học phần Lập trình ứng dụng web trong du lịch 1 (CSE703072).</p>
			</div>

			<!-- Cột 2: Danh mục điểm đến -->
			<div class="etc-footer-col">
				<h3 class="etc-footer-col__title">Danh mục Điểm đến ĐBSCL</h3>
				<ul>
					<li><a href="<?php echo esc_url( add_query_arg( 'product_cat', 'can-tho', get_permalink( wc_get_page_id( 'shop' ) ) ) ); ?>">Cần Thơ (Chợ nổi Cái Răng, Còn Sơn)</a></li>
					<li><a href="<?php echo esc_url( add_query_arg( 'product_cat', 'an-giang', get_permalink( wc_get_page_id( 'shop' ) ) ) ); ?>">An Giang – Đồng Tháp (Tràm Chim, Làng hoa Sa Đéc)</a></li>
					<li><a href="<?php echo esc_url( add_query_arg( 'product_cat', 'tien-giang', get_permalink( wc_get_page_id( 'shop' ) ) ) ); ?>">Tiền Giang – Bến Tre (Cù lao Thới Sơn, Cái Mơn)</a></li>
					<li>Vĩnh Long &amp; Cà Mau – Bạc Liêu</li>
				</ul>
			</div>

			<!-- Cột 3: Kiến trúc kỹ thuật -->
			<div class="etc-footer-col">
				<h3 class="etc-footer-col__title">Kiến trúc Kỹ thuật</h3>
				<p>WordPress + WooCommerce + Child Theme <code>etour-child</code> + Plugin <code>etour-cd04</code>. Vanilla JS (Fetch API), CSS Grid 3 breakpoints, chuẩn WCAG 2.2 AA.</p>
			</div>

		</div>
		<hr class="etc-footer-divider">
	</div>
	<p class="etc-disclaimer"><?php echo esc_html__( 'Website phục vụ học tập – dữ liệu mô phỏng – không phát sinh giao dịch thật.', 'etour-child' ); ?></p>
	<?php
}, 30 );

/**
 * Ẩn sidebar Storefront mặc định (bài viết, bình luận, lưu trữ, danh mục, tìm kiếm)
 * trên tất cả các trang: shop, cart, checkout, page, archive.
 * Thay thế bằng sidebar tùy chỉnh (đồng hồ đếm ngược + form tư vấn).
 * Dùng after_setup_theme priority 100 để chạy SAU khi Storefront đăng ký hook.
 */
add_action( 'after_setup_theme', function () {
	remove_action( 'storefront_sidebar', 'storefront_get_sidebar' );
}, 100 );

add_action( 'storefront_sidebar', function () {
	// Không hiển thị sidebar tuỳ chỉnh trên trang chủ và trang đơn sản phẩm
	if ( is_front_page() || is_singular( 'product' ) ) {
		return;
	}

	// Ngày kết thúc ưu đãi: cuối năm hiện tại
	$sale_end = gmdate( 'Y' ) . '-12-31T23:59:59+07:00';
	if ( function_exists( 'wc_get_products' ) ) {
		$on_sale = wc_get_products( array(
			'status'  => 'publish',
			'on_sale' => true,
			'limit'   => 1,
			'orderby' => 'date',
			'order'   => 'DESC',
		) );
		if ( ! empty( $on_sale ) ) {
			$to = $on_sale[0]->get_date_on_sale_to();
			if ( $to ) {
				$sale_end = $to->date( DATE_ATOM );
			}
		}
	}

	// Danh sách tour cho form tư vấn
	$tours = array();
	if ( function_exists( 'wc_get_products' ) ) {
		$tours = wc_get_products( array(
			'status'  => 'publish',
			'limit'   => -1,
			'orderby' => 'title',
			'order'   => 'ASC',
		) );
	}

	$notice = isset( $_GET['etour_cd04'] ) ? sanitize_key( wp_unslash( $_GET['etour_cd04'] ) ) : '';

	// Enqueue CSS/JS cần thiết
	wp_enqueue_style( 'etour-cd04' );
	wp_enqueue_script( 'etour-cd04' );
	?>
	<aside class="etour-global-sidebar" role="complementary" aria-label="Sidebar tư vấn">

		<!-- Hộp đếm ngược -->
		<div class="etour-sidebar-box etour-countdown-box">
			<h3 class="etour-sidebar-title">⏳ Ưu đãi đặt tour sớm</h3>
			<p class="etour-countdown-desc">Giá ưu đãi kết thúc sau:</p>
			<div class="etour-countdown-timer"
			     data-countdown="<?php echo esc_attr( $sale_end ); ?>"
			     aria-live="polite">
				<div class="etour-countdown-unit">
					<span class="etour-countdown-num" data-countdown-days>--</span>
					<span class="etour-countdown-label">Ngày</span>
				</div>
				<div class="etour-countdown-sep">:</div>
				<div class="etour-countdown-unit">
					<span class="etour-countdown-num" data-countdown-hours>--</span>
					<span class="etour-countdown-label">Giờ</span>
				</div>
				<div class="etour-countdown-sep">:</div>
				<div class="etour-countdown-unit">
					<span class="etour-countdown-num" data-countdown-mins>--</span>
					<span class="etour-countdown-label">Phút</span>
				</div>
				<div class="etour-countdown-sep">:</div>
				<div class="etour-countdown-unit">
					<span class="etour-countdown-num" data-countdown-secs>--</span>
					<span class="etour-countdown-label">Giây</span>
				</div>
			</div>
			<p class="etour-countdown-note">* Áp dụng cho tour đặt trước mùa cao điểm</p>
		</div>

		<!-- Form tư vấn -->
		<div class="etour-sidebar-box etour-consult-box">
			<h3 class="etour-sidebar-title">📋 Đăng ký tư vấn tour</h3>

			<?php if ( 'ok' === $notice ) : ?>
				<p class="etour-alert etour-alert--ok" role="status">✅ Đã ghi nhận! Chúng tôi liên hệ trong 24 giờ.</p>
			<?php elseif ( 'error' === $notice ) : ?>
				<p class="etour-alert etour-alert--err" role="alert">❌ Thông tin chưa hợp lệ. Thử lại.</p>
			<?php endif; ?>

			<form class="etour-consult-form" method="post"
			      action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
			      novalidate>
				<input type="hidden" name="action" value="etour_cd04_consult">
				<?php wp_nonce_field( 'etour_cd04_consult', 'etour_cd04_nonce' ); ?>

				<div class="etour-field">
					<label for="esb-name">Họ tên <span aria-hidden="true">*</span></label>
					<input id="esb-name" name="full_name" type="text" maxlength="120"
					       placeholder="Nguyễn Văn A" required autocomplete="name">
				</div>
				<div class="etour-field">
					<label for="esb-phone">Số điện thoại <span aria-hidden="true">*</span></label>
					<input id="esb-phone" name="phone" type="tel" pattern="0[0-9]{9}"
					       placeholder="0901234567" required autocomplete="tel">
				</div>
				<div class="etour-field">
					<label for="esb-month">Tháng muốn đi <span aria-hidden="true">*</span></label>
					<select id="esb-month" name="expected_month" required>
						<option value="">— Chọn tháng —</option>
						<?php for ( $m = 1; $m <= 12; $m++ ) : ?>
							<option value="<?php echo esc_attr( $m ); ?>">Tháng <?php echo esc_html( $m ); ?></option>
						<?php endfor; ?>
					</select>
				</div>
				<div class="etour-field">
					<label for="esb-guests">Số khách <span aria-hidden="true">*</span></label>
					<input id="esb-guests" name="guests" type="number" min="1" max="50" value="2" required>
				</div>
				<?php if ( ! empty( $tours ) ) : ?>
				<div class="etour-field">
					<label for="esb-tour">Tour quan tâm</label>
					<select id="esb-tour" name="season_item">
						<option value="">— Tuỳ chọn —</option>
						<?php foreach ( $tours as $t ) : ?>
							<option value="<?php echo esc_attr( $t->get_name() ); ?>">
								<?php echo esc_html( $t->get_name() ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>
				<?php endif; ?>
				<div class="etour-field etour-consent" style="flex-direction:row;align-items:flex-start;gap:.4rem">
					<input id="esb-consent" name="consent" type="checkbox" value="1" required style="width:auto;margin-top:.2rem">
					<label for="esb-consent" style="font-size:.82rem">Tôi đồng ý cho website xử lý thông tin để tư vấn tour.</label>
				</div>
				<button type="submit" class="etour-consult-submit">Gửi yêu cầu tư vấn</button>
			</form>
		</div>

	</aside>
	<?php
}, 10 );

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
		$urls[] = array( 'href' => 'https://fonts.gstatic.com', 'crossorigin' => true );
	}
	return $urls;
}, 10, 2 );

/**
 * Khắc phục lỗi ảnh sản phẩm WooCommerce bị co thành 1x1 px (tích hợp chuẩn vào Child Theme)
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

