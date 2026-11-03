<?php
/**
 * Khối hero trang chủ – nhóm thay nội dung theo chủ đề.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="etc-hero" aria-labelledby="etc-hero-title">
	<div class="col-full etc-hero__inner">
		<p class="etc-hero__eyebrow">Du lịch số · Dự án E-Tourism</p>
		<h1 id="etc-hero-title" class="etc-hero__title">Khám phá Việt Nam theo cách của bạn</h1>
		<p class="etc-hero__lead">Tour, trải nghiệm và dịch vụ du lịch được chọn lọc – đặt chỗ trực tuyến, minh bạch giá, hỗ trợ 24/7.</p>
		<div class="etc-hero__actions">
			<a class="etc-btn etc-btn--primary" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Xem tour</a>
			<a class="etc-btn etc-btn--ghost" href="<?php echo esc_url( home_url( '/tu-van/' ) ); ?>">Nhận tư vấn</a>
		</div>
	</div>
</section>
