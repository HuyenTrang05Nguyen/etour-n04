<?php
/**
 * Shortcode [etour_seasons_page] – Lịch mùa vụ & Gợi ý tour miệt vườn CD04.
 * Layout theo mockup: tiêu đề mô tả → thanh 12 tháng → tip → meta → 2 cột (mùa vụ | tour) → form tư vấn.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Etour_Cd04_Form {

	public static function register() {
		add_shortcode( 'etour_seasons_page', array( __CLASS__, 'render_seasons_page' ) );
		add_action( 'admin_post_nopriv_etour_cd04_consult', array( __CLASS__, 'handle_consult' ) );
		add_action( 'admin_post_etour_cd04_consult',        array( __CLASS__, 'handle_consult' ) );
	}

	/* --------------------------------------------------------
	   Xử lý POST form tư vấn
	   -------------------------------------------------------- */
	public static function handle_consult() {
		$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );

		if ( ! isset( $_POST['etour_cd04_nonce'] ) ||
		     ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['etour_cd04_nonce'] ) ), 'etour_cd04_consult' ) ) {
			wp_safe_redirect( add_query_arg( 'etour_cd04', 'error', $back ) );
			exit;
		}

		$consent = isset( $_POST['consent'] ) && '1' === $_POST['consent'];
		if ( ! $consent ) {
			wp_safe_redirect( add_query_arg( 'etour_cd04', 'error', $back ) );
			exit;
		}

		$full_name      = isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '';
		$email          = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$phone          = isset( $_POST['phone'] ) ? preg_replace( '/\D/', '', wp_unslash( $_POST['phone'] ) ) : '';
		$expected_month = isset( $_POST['expected_month'] ) ? absint( $_POST['expected_month'] ) : 0;
		$guests         = isset( $_POST['guests'] ) ? absint( $_POST['guests'] ) : 1;
		$season_item    = isset( $_POST['season_item'] ) ? sanitize_text_field( wp_unslash( $_POST['season_item'] ) ) : '';
		$note           = isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '';

		if ( mb_strlen( $full_name ) < 2 || ! is_email( $email ) ||
		     ! preg_match( '/^0\d{9}$/', $phone ) ||
		     $expected_month < 1 || $expected_month > 12 || $guests < 1 ) {
			wp_safe_redirect( add_query_arg( 'etour_cd04', 'error', $back ) );
			exit;
		}

		$id = Etour_Cd04_DB::insert_request( array(
			'full_name'      => $full_name,
			'email'          => $email,
			'phone'          => $phone,
			'expected_month' => $expected_month,
			'season_item'    => $season_item,
			'guests'         => $guests,
			'note'           => $note,
		) );

		wp_safe_redirect( add_query_arg( 'etour_cd04', $id ? 'ok' : 'error', $back ) );
		exit;
	}

	/* --------------------------------------------------------
	   Shortcode [etour_seasons_page]
	   -------------------------------------------------------- */
	public static function render_seasons_page( $atts = array() ) {

		wp_enqueue_style( 'etour-cd04' );
		wp_enqueue_script( 'etour-cd04' );

		$current_month = (int) gmdate( 'n' );

		// Truyền consultUrl vào JS để nút "Tư vấn mùa này" scroll đúng form
		wp_localize_script( 'etour-cd04', 'ETOUR_CD04', array(
			'restUrl'    => esc_url_raw( rest_url( 'etour/v1/' ) ),
			'nonce'      => wp_create_nonce( 'wp_rest' ),
			'consultUrl' => '#etour-consult-form',
		) );

		// Danh sách tour cho form
		$tours = array();
		if ( function_exists( 'wc_get_products' ) ) {
			$tours = wc_get_products( array( 'status' => 'publish', 'limit' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
		}

		$notice        = isset( $_GET['etour_cd04'] ) ? sanitize_key( wp_unslash( $_GET['etour_cd04'] ) ) : '';
		$prefill_month = isset( $_GET['month'] ) ? absint( $_GET['month'] ) : $current_month;
		$prefill_month = ( $prefill_month >= 1 && $prefill_month <= 12 ) ? $prefill_month : $current_month;
		$prefill_item  = isset( $_GET['season'] ) ? sanitize_text_field( wp_unslash( $_GET['season'] ) ) : '';

		$shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );

		ob_start();
		?>

		<!-- ===================== HEADER MÔ TẢ ===================== -->
		<div class="etour-seasons-header">
			<p class="etour-seasons-header__sub">Chọn tháng từ 1 đến 12 để xem trái cây, lễ hội, cảnh quan đang vào mùa (hỗ trợ <strong>mùa vắt qua năm</strong> như Tháng 11 → Tháng 2) và danh sách tour gợi ý tương ứng.</p>
		</div>

		<!-- ===================== KHỐI CHỌN THÁNG ===================== -->
		<div class="etour-month-section"
		     data-etour-cd04
		     data-current-month="<?php echo esc_attr( $current_month ); ?>">

			<div class="etour-month-section__header">
				<h2 class="etour-month-section__title">1. Chọn tháng dự kiến khởi hành (Tháng 1 – Tháng 12)</h2>
				<p class="etour-month-section__tip">
					💡 <strong>Mẹo kiếm thử US1:</strong> Chọn Tháng 11, 12, 1, 2 để kiểm tra các mùa vắt qua năm!
				</p>
			</div>

			<nav class="etour-month-bar" data-month-bar aria-label="Chọn tháng">
				<?php for ( $m = 1; $m <= 12; $m++ ) : ?>
					<button
						class="etour-month-btn<?php echo $m === $current_month ? ' is-active' : ''; ?>"
						data-month="<?php echo esc_attr( $m ); ?>"
						aria-pressed="<?php echo $m === $current_month ? 'true' : 'false'; ?>"
						type="button"
					>Tháng <?php echo esc_html( $m ); ?></button>
				<?php endfor; ?>
			</nav>

			<p class="etour-cd04__meta" data-cd04-meta aria-live="polite">
				Đang xem Tháng <?php echo esc_html( $current_month ); ?>: đang tải dữ liệu…
			</p>

			<!-- 2 cột: mùa vụ | tour gợi ý -->
			<div class="etour-seasons-grid">

				<div class="etour-seasons-grid__seasons">
					<h3 class="etour-seasons-grid__heading">
						🍊 Mùa vụ Trái cây, Lễ hội &amp; Cảnh quan
					</h3>
					<div data-seasons-list aria-live="polite">
						<p class="etour-muted">Đang tải…</p>
					</div>
				</div>

				<div class="etour-seasons-grid__tours">
					<h3 class="etour-seasons-grid__heading">
						✈️ Tour Gợi ý phù hợp trong tháng<br>
						<small>(etour_tour_months)</small>
					</h3>
					<div data-tours-list aria-live="polite">
						<p class="etour-muted">Đang tải…</p>
					</div>
				</div>

			</div><!-- .etour-seasons-grid -->

		</div><!-- [data-etour-cd04] -->

		<!-- ===================== FORM TƯ VẤN ===================== -->
		<section class="etour-consult-section" id="etour-consult-form" aria-labelledby="etour-consult-title">

			<div class="etour-consult-inner">

				<h2 id="etour-consult-title" class="etour-consult-title">
					Nhận tư vấn Tour Miệt vườn theo tháng dự kiến
				</h2>
				<p class="etour-consult-desc">
					Shortcode <code>[etour_season_request_form]</code> – Áp dụng 5 lớp bảo vệ (Nonce CSRF, Consent, Sanitize, Validate, Prepared Statement).
				</p>

				<?php if ( 'ok' === $notice ) : ?>
					<p class="etour-alert etour-alert--ok" role="status">✅ Yêu cầu đã được ghi nhận. Chúng tôi sẽ liên hệ trong 24 giờ làm việc.</p>
				<?php elseif ( 'error' === $notice ) : ?>
					<p class="etour-alert etour-alert--err" role="alert">❌ Thông tin chưa hợp lệ. Vui lòng kiểm tra và gửi lại.</p>
				<?php endif; ?>

				<form class="etour-consult-form etour-form" method="post"
				      action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
				      novalidate>
					<input type="hidden" name="action" value="etour_cd04_consult">
					<?php wp_nonce_field( 'etour_cd04_consult', 'etour_cd04_nonce' ); ?>

					<!-- Họ và tên -->
					<div class="etour-field">
						<label for="ecd-name">Họ và tên <span aria-hidden="true">*</span></label>
						<input id="ecd-name" name="full_name" type="text" maxlength="120"
						       placeholder="Nguyễn Thảo" required autocomplete="name">
					</div>

					<!-- Email + SĐT -->
					<div class="etour-row">
						<div class="etour-field">
							<label for="ecd-email">Email <span aria-hidden="true">*</span></label>
							<input id="ecd-email" name="email" type="email" maxlength="190"
							       placeholder="thao.nguyen@example.com" required autocomplete="email">
						</div>
						<div class="etour-field">
							<label for="ecd-phone">Số điện thoại (10 chữ số, bắt đầu bằng 0) <span aria-hidden="true">*</span></label>
							<input id="ecd-phone" name="phone" type="tel" pattern="0[0-9]{9}"
							       placeholder="0912345678" required autocomplete="tel">
						</div>
					</div>

					<!-- Tháng + Số khách -->
					<div class="etour-row">
						<div class="etour-field">
							<label for="ecd-month">Tháng dự kiến đi (1–12) <span aria-hidden="true">*</span></label>
							<select id="ecd-month" name="expected_month" required>
								<option value="">— Chọn tháng —</option>
								<?php for ( $m = 1; $m <= 12; $m++ ) : ?>
									<option value="<?php echo esc_attr( $m ); ?>"
									        <?php selected( $m, $prefill_month ); ?>>
										Tháng <?php echo esc_html( $m ); ?>
									</option>
								<?php endfor; ?>
							</select>
						</div>
						<div class="etour-field">
							<label for="ecd-guests">Số khách (1–50) <span aria-hidden="true">*</span></label>
							<input id="ecd-guests" name="guests" type="number" min="1" max="50" value="4" required>
						</div>
					</div>

					<!-- Mùa quan tâm -->
					<div class="etour-field">
						<label for="ecd-season">Mùa trái cây / lễ hội bạn quan tâm</label>
						<input id="ecd-season" name="season_item" type="text" maxlength="150"
						       placeholder="VD: Mùa chôm chôm, Chợ nổi Cái Răng, Quýt hồng Lai Vung…"
						       value="<?php echo esc_attr( $prefill_item ); ?>">
					</div>

					<!-- Ghi chú -->
					<div class="etour-field">
						<label for="ecd-note">Ghi chú thêm (Phương tiện xuồng máy / xe khách, trẻ nhỏ…)</label>
						<textarea id="ecd-note" name="note" rows="3" maxlength="1000"
						          placeholder="VD: Gia đình có 2 trẻ em, muốn đi xuồng máy…"></textarea>
					</div>

					<!-- Consent -->
					<div class="etour-field etour-consent">
						<input id="ecd-consent" name="consent" type="checkbox" value="1" required>
						<label for="ecd-consent">
							Tôi đồng ý cho website xử lý dữ liệu cá nhân nêu trên để tư vấn tour miệt vườn,
							theo <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>">Chính sách quyền riêng tư</a>.
						</label>
					</div>

					<p class="etour-error" data-etour-error role="alert" tabindex="-1" hidden></p>

					<button type="submit" class="etour-btn etour-consult-submit">
						Gửi yêu cầu tư vấn
					</button>
				</form>

			</div><!-- .etour-consult-inner -->

		</section><!-- #etour-consult-form -->
		<?php
		return ob_get_clean();
	}
}
