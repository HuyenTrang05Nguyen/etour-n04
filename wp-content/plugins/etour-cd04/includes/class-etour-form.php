<?php
/**
 * Tầng nghiệp vụ phía người dùng: shortcode hiển thị form và xử lý gửi form.
 * Năm lớp bảo vệ: (1) nonce, (2) kiểm tra quyền/điều kiện, (3) sanitize,
 * (4) validate, (5) truy vấn tham số hóa; dữ liệu xuất ra luôn được escape.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Etour_Form {

	public static function register() {
		add_shortcode( 'etour_request_form', array( __CLASS__, 'render_form' ) );
		add_shortcode( 'etour_places', array( __CLASS__, 'render_places' ) );
		// admin-post.php: xử lý cho cả khách chưa đăng nhập (nopriv) và đã đăng nhập.
		add_action( 'admin_post_nopriv_etour_submit_request', array( __CLASS__, 'handle_submit' ) );
		add_action( 'admin_post_etour_submit_request', array( __CLASS__, 'handle_submit' ) );
	}

	/** [etour_places] – vùng chứa; dữ liệu được JS nạp qua REST (Fetch). */
	public static function render_places() {
		wp_enqueue_style( 'etour-core' );
		wp_enqueue_script( 'etour-core' );
		ob_start(); ?>
		<section class="etour-places" aria-labelledby="etour-places-title">
			<h2 id="etour-places-title">Điểm đến gợi ý</h2>
			<label for="etour-filter-province">Lọc theo tỉnh/thành</label>
			<select id="etour-filter-province" data-etour-filter>
				<option value="">Tất cả</option>
			</select>
			<div class="etour-grid" data-etour-places aria-live="polite">
				<p class="etour-muted">Đang tải dữ liệu…</p>
			</div>
		</section>
		<?php
		return ob_get_clean();
	}

	/** [etour_request_form] – form gửi yêu cầu tư vấn. */
	public static function render_form() {
		wp_enqueue_style( 'etour-core' );
		wp_enqueue_script( 'etour-core' );
		$places = Etour_DB::get_places();
		$notice = isset( $_GET['etour'] ) ? sanitize_key( wp_unslash( $_GET['etour'] ) ) : '';
		ob_start();
		if ( 'ok' === $notice ) {
			echo '<p class="etour-alert etour-alert--ok" role="status">Đã ghi nhận yêu cầu. Bộ phận tư vấn sẽ liên hệ trong 24 giờ làm việc.</p>';
		} elseif ( 'error' === $notice ) {
			echo '<p class="etour-alert etour-alert--err" role="alert">Thông tin chưa hợp lệ. Vui lòng kiểm tra và gửi lại.</p>';
		}
		?>
		<form class="etour-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate data-etour-form>
			<input type="hidden" name="action" value="etour_submit_request">
			<?php wp_nonce_field( 'etour_submit_request', 'etour_nonce' ); ?>
			<div class="etour-field">
				<label for="etour-place">Điểm đến <span aria-hidden="true">*</span></label>
				<select id="etour-place" name="place_id" required>
					<option value="">— Chọn điểm đến —</option>
					<?php foreach ( $places as $p ) : ?>
						<option value="<?php echo esc_attr( $p->id ); ?>"><?php echo esc_html( $p->name . ' (' . $p->province . ')' ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="etour-field">
				<label for="etour-name">Họ và tên <span aria-hidden="true">*</span></label>
				<input id="etour-name" name="full_name" type="text" maxlength="120" required autocomplete="name">
			</div>
			<div class="etour-row">
				<div class="etour-field">
					<label for="etour-email">Email <span aria-hidden="true">*</span></label>
					<input id="etour-email" name="email" type="email" maxlength="190" required autocomplete="email">
				</div>
				<div class="etour-field">
					<label for="etour-phone">Số điện thoại <span aria-hidden="true">*</span></label>
					<input id="etour-phone" name="phone" type="tel" pattern="0[0-9]{9}" required autocomplete="tel">
				</div>
			</div>
			<div class="etour-row">
				<div class="etour-field">
					<label for="etour-date">Ngày khởi hành <span aria-hidden="true">*</span></label>
					<input id="etour-date" name="travel_date" type="date" required>
				</div>
				<div class="etour-field">
					<label for="etour-guests">Số khách <span aria-hidden="true">*</span></label>
					<input id="etour-guests" name="guests" type="number" min="1" max="50" value="2" required>
				</div>
			</div>
			<div class="etour-field">
				<label for="etour-note">Ghi chú</label>
				<textarea id="etour-note" name="note" rows="3" maxlength="1000"></textarea>
			</div>
			<div class="etour-field etour-consent">
				<input id="etour-consent" name="consent" type="checkbox" value="1" required>
				<label for="etour-consent">Tôi đồng ý cho website xử lý dữ liệu cá nhân nêu trên để tư vấn tour, theo <a href="<?php echo esc_url( get_privacy_policy_url() ); ?>">Chính sách quyền riêng tư</a>.</label>
			</div>
			<p class="etour-error" data-etour-error role="alert" tabindex="-1" hidden></p>
			<button type="submit" class="etour-btn">Gửi yêu cầu</button>
		</form>
		<?php
		return ob_get_clean();
	}

	/** Xử lý POST từ admin-post.php. */
	public static function handle_submit() {
		$back = wp_get_referer() ? wp_get_referer() : home_url( '/' );

		// (1) Nonce – chống giả mạo yêu cầu (CSRF).
		if ( ! isset( $_POST['etour_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['etour_nonce'] ) ), 'etour_submit_request' ) ) {
			wp_die( esc_html__( 'Phiên làm việc không hợp lệ. Vui lòng tải lại trang.', 'etour-core' ), 403 );
		}

		// (2) Điều kiện nghiệp vụ: phải có đồng ý xử lý dữ liệu cá nhân.
		$consent = isset( $_POST['consent'] ) && '1' === $_POST['consent'];

		// (3) Sanitize – làm sạch theo kiểu dữ liệu.
		$data = array(
			'place_id'    => isset( $_POST['place_id'] ) ? absint( $_POST['place_id'] ) : 0,
			'full_name'   => isset( $_POST['full_name'] ) ? sanitize_text_field( wp_unslash( $_POST['full_name'] ) ) : '',
			'email'       => isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '',
			'phone'       => isset( $_POST['phone'] ) ? preg_replace( '/\D/', '', wp_unslash( $_POST['phone'] ) ) : '',
			'travel_date' => isset( $_POST['travel_date'] ) ? sanitize_text_field( wp_unslash( $_POST['travel_date'] ) ) : '',
			'guests'      => isset( $_POST['guests'] ) ? absint( $_POST['guests'] ) : 0,
			'note'        => isset( $_POST['note'] ) ? sanitize_textarea_field( wp_unslash( $_POST['note'] ) ) : '',
		);

		// (4) Validate – kiểm tra hợp lệ phía máy chủ (không tin dữ liệu phía client).
		$errors = self::validate( $data, $consent );
		if ( $errors ) {
			wp_safe_redirect( add_query_arg( 'etour', 'error', $back ) );
			exit;
		}

		// (5) Ghi dữ liệu bằng truy vấn tham số hóa.
		$id = Etour_DB::insert_request( $data );
		wp_safe_redirect( add_query_arg( 'etour', $id ? 'ok' : 'error', $back ) );
		exit;
	}

	/** Trả về mảng lỗi (rỗng nếu hợp lệ). Tách riêng để dễ kiểm thử. */
	public static function validate( array $d, $consent ) {
		$e = array();
		if ( ! $consent ) {
			$e[] = 'consent';
		}
		if ( ! $d['place_id'] || ! Etour_DB::place_exists( $d['place_id'] ) ) {
			$e[] = 'place_id';
		}
		$len = mb_strlen( $d['full_name'] );
		if ( $len < 2 || $len > 120 ) {
			$e[] = 'full_name';
		}
		if ( ! is_email( $d['email'] ) ) {
			$e[] = 'email';
		}
		if ( ! preg_match( '/^0\d{9}$/', $d['phone'] ) ) {
			$e[] = 'phone';
		}
		$date = DateTime::createFromFormat( 'Y-m-d', $d['travel_date'], wp_timezone() );
		$today = new DateTime( 'today', wp_timezone() );
		if ( ! $date || $date->format( 'Y-m-d' ) !== $d['travel_date'] || $date < $today ) {
			$e[] = 'travel_date';
		}
		if ( $d['guests'] < 1 || $d['guests'] > 50 ) {
			$e[] = 'guests';
		}
		return $e;
	}
}
