<?php
/**
 * Hero Banner Trang chủ – CD04 Miệt Vườn Xanh Tour
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Đếm số tour đang publish
$tour_count = 0;
if ( function_exists( 'wc_get_products' ) ) {
    $tour_count = count( wc_get_products( array( 'status' => 'publish', 'limit' => -1, 'return' => 'ids' ) ) );
}
$tour_label = $tour_count > 0 ? $tour_count . ' Tour' : '24 Tour';

// URL trang shop + trang mùa vụ
$shop_url    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/cua-hang/' );
$seasons_url = get_permalink( get_page_by_path( 'di-mien-tay-thang-nao' ) ) ?: home_url( '/di-mien-tay-thang-nao/' );
?>
<section class="cd04-hero" aria-labelledby="cd04-hero-title">
    <div class="cd04-hero__overlay">
        <div class="cd04-hero__container">

            <span class="cd04-hero__badge">DU LỊCH SỐ · DỰ ÁN E-TOURISM CD04</span>

            <h1 id="cd04-hero-title" class="cd04-hero__title">
                Khám phá Miệt vườn &amp; Chợ nổi Đồng bằng sông Cửu Long theo mùa
            </h1>

            <p class="cd04-hero__desc">
                Mỗi tháng miền Tây lại có một mùa trái chín, mùa nước nổi hay lễ hội văn hóa đặc sắc.
                Tra cứu lịch mùa vụ 12 tháng để chọn ngay hành trình xuồng máy hoặc xe khách phù hợp nhất cho gia đình bạn.
            </p>

            <div class="cd04-hero__actions">
                <a href="<?php echo esc_url( $seasons_url ); ?>"
                   class="cd04-btn cd04-btn--primary">
                    Tra cứu Đi miền Tây tháng nào?
                </a>
                <a href="<?php echo esc_url( $shop_url ); ?>"
                   class="cd04-btn cd04-btn--outline">
                    Xem tất cả <?php echo esc_html( $tour_label ); ?> miệt vườn
                </a>
            </div>

        </div>
    </div>
</section>
