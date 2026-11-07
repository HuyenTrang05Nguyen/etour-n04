<?php
/**
 * Trang quản trị của module: liệt kê – lọc – đổi trạng thái – xuất CSV.
 * Chỉ người có quyền manage_options truy cập; mọi thao tác ghi đều kiểm tra nonce.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Etour_Admin {

	const CAP = 'manage_options';

	public static function register_menu() {
		add_menu_page( 'Yêu cầu tư vấn', 'E-Tourism', self::CAP, 'etour-requests', array( __CLASS__, 'render_page' ), 'dashicons-palmtree', 26 );
	}

	// Đăng ký xử lý admin-post actions; được gọi từ hook 'admin_init' trong etour-cd04.php.
	public static function register_actions() {
		add_action( 'admin_post_etour_update_status', array( __CLASS__, 'handle_status' ) );
		add_action( 'admin_post_etour_export_csv', array( __CLASS__, 'handle_export' ) );
	}

	public static function render_page() {
		if ( ! current_user_can( self::CAP ) ) {
			wp_die( esc_html__( 'Bạn không có quyền truy cập trang này.', 'etour-core' ), 403 );
		}
		$statuses = Etour_DB::statuses();
		$filter   = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : '';
		if ( ! isset( $statuses[ $filter ] ) ) {
			$filter = '';
		}
		$rows = Etour_DB::get_requests( $filter, 100 );
		?>
		<div class="wrap">
			<h1 class="wp-heading-inline">Yêu cầu tư vấn</h1>
			<a class="page-title-action" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=etour_export_csv' ), 'etour_export_csv' ) ); ?>">Xuất CSV</a>
			<hr class="wp-header-end">
			<ul class="subsubsub">
				<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=etour-requests' ) ); ?>" class="<?php echo '' === $filter ? 'current' : ''; ?>">Tất cả</a> |</li>
				<?php foreach ( $statuses as $key => $label ) : ?>
					<li><a href="<?php echo esc_url( add_query_arg( array( 'page' => 'etour-requests', 'status' => $key ), admin_url( 'admin.php' ) ) ); ?>" class="<?php echo $filter === $key ? 'current' : ''; ?>"><?php echo esc_html( $label ); ?></a> |</li>
				<?php endforeach; ?>
			</ul>
			<table class="widefat striped">
				<thead><tr><th>ID</th><th>Thời điểm gửi</th><th>Khách hàng</th><th>Liên hệ</th><th>Điểm đến</th><th>Ngày đi</th><th>Số khách</th><th>Trạng thái</th></tr></thead>
				<tbody>
				<?php if ( ! $rows ) : ?>
					<tr><td colspan="8">Chưa có yêu cầu.</td></tr>
				<?php endif; ?>
				<?php foreach ( $rows as $r ) : ?>
					<tr>
						<td><?php echo esc_html( $r->id ); ?></td>
						<td><?php echo esc_html( $r->created_at ); ?></td>
						<td><?php echo esc_html( $r->full_name ); ?></td>
						<td><?php echo esc_html( $r->email ); ?><br><?php echo esc_html( $r->phone ); ?></td>
						<td><?php echo esc_html( $r->place_name ); ?></td>
						<td><?php echo esc_html( $r->travel_date ); ?></td>
						<td><?php echo esc_html( $r->guests ); ?></td>
						<td>
							<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
								<input type="hidden" name="action" value="etour_update_status">
								<input type="hidden" name="id" value="<?php echo esc_attr( $r->id ); ?>">
								<?php wp_nonce_field( 'etour_update_status_' . $r->id ); ?>
								<label class="screen-reader-text" for="st-<?php echo esc_attr( $r->id ); ?>">Trạng thái</label>
								<select id="st-<?php echo esc_attr( $r->id ); ?>" name="status">
									<?php foreach ( $statuses as $key => $label ) : ?>
										<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $r->status, $key ); ?>><?php echo esc_html( $label ); ?></option>
									<?php endforeach; ?>
								</select>
								<button class="button button-small">Lưu</button>
							</form>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
	}

	public static function handle_status() {
		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
		if ( ! current_user_can( self::CAP ) ) {
			wp_die( 'Không đủ quyền.', 403 );
		}
		check_admin_referer( 'etour_update_status_' . $id );
		$status = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : '';
		if ( $id && array_key_exists( $status, Etour_DB::statuses() ) ) { // whitelist
			Etour_DB::update_status( $id, $status );
		}
		wp_safe_redirect( admin_url( 'admin.php?page=etour-requests' ) );
		exit;
	}

	public static function handle_export() {
		if ( ! current_user_can( self::CAP ) ) {
			wp_die( 'Không đủ quyền.', 403 );
		}
		check_admin_referer( 'etour_export_csv' );
		$rows = Etour_DB::get_requests( '', 5000 );
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=etour-requests-' . gmdate( 'Ymd' ) . '.csv' );
		$out = fopen( 'php://output', 'w' );
		fwrite( $out, "\xEF\xBB\xBF" ); // BOM để Excel hiển thị đúng tiếng Việt.
		fputcsv( $out, array( 'id', 'created_at', 'full_name', 'email', 'phone', 'place', 'travel_date', 'guests', 'status' ) );
		foreach ( $rows as $r ) {
			// Chống CSV/Formula injection: thêm dấu nháy nếu ô bắt đầu bằng = + - @.
			$safe = array_map( function ( $v ) {
				$v = (string) $v;
				return preg_match( '/^[=+\-@]/', $v ) ? "'" . $v : $v;
			}, array( $r->id, $r->created_at, $r->full_name, $r->email, $r->phone, $r->place_name, $r->travel_date, $r->guests, $r->status ) );
			fputcsv( $out, $safe );
		}
		fclose( $out );
		exit;
	}
}
