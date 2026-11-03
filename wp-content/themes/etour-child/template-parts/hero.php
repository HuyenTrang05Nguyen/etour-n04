<?php
/**
 * Template part: Hero Banner Trang chủ
 * Chủ đề CD04: Miệt Vườn Xanh Tour
 * Chuẩn SEO (1 thẻ H1) và Khả năng tiếp cận WCAG 2.2
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<section class="cd04-hero" aria-labelledby="cd04-hero-title">
    <div class="cd04-hero__overlay">
        <div class="cd04-hero__container">
            <span class="cd04-hero__badge"><?php echo esc_html__( 'DU LỊCH SINH THÁI TÂY NAM BỘ', 'etour-child' ); ?></span>
            
            <h1 id="cd04-hero-title" class="cd04-hero__title">
                <?php echo esc_html__( 'Khám Phá Miệt Vườn & Sông Nước Nam Bộ', 'etour-child' ); ?>
            </h1>
            
            <p class="cd04-hero__desc">
                <?php echo esc_html__( 'Trải nghiệm tự tay hái trái cây chín cây, chèo xuồng ba lá len lỏi rạch dừa và hòa mình vào nhịp sống chợ nổi đậm chất miền Tây.', 'etour-child' ); ?>
            </p>

            <!-- Hàng nút bấm hành động (CTA Buttons) -->
            <div class="cd04-hero__actions">
                <a href="<?php echo esc_url( home_url( '/tour/' ) ); ?>" class="button cd04-btn cd04-btn--primary">
                    <?php echo esc_html__( 'Xem danh sách tour', 'etour-child' ); ?>
                </a>
                <a href="<?php echo esc_url( home_url( '/di-mien-tay-thang-nao/' ) ); ?>" class="button cd04-btn cd04-btn--outline">
                    <?php echo esc_html__( 'Đi miền Tây tháng nào?', 'etour-child' ); ?>
                </a>
            </div>

            <!-- Form tìm kiếm tour nhanh bằng WooCommerce -->
            <div class="cd04-hero__search">
                <form role="search" method="get" class="cd04-search-box" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                    <input type="hidden" name="post_type" value="product" />
                    <label for="cd04-search-field" class="screen-reader-text"><?php echo esc_html__( 'Tìm tour miệt vườn', 'etour-child' ); ?></label>
                    <input type="search" id="cd04-search-field" class="cd04-search-input" placeholder="<?php echo esc_attr__( 'Nhập tên tour, miệt vườn cần tìm...', 'etour-child' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
                    <button type="submit" class="cd04-search-submit">
                        <?php echo esc_html__( 'Tìm kiếm', 'etour-child' ); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>