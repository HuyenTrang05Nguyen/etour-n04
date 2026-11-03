<?php
/**
 * Tầng dữ liệu (Data tier) của module: định nghĩa bảng, cài đặt, truy vấn.
 * Mọi truy vấn có tham số đều dùng $wpdb->prepare() hoặc $wpdb->insert()/update().
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Etour_DB {

	/** Tên bảng danh mục (ví dụ: điểm đến). */
	public static function places_table() {
		global $wpdb;
		return $wpdb->prefix . 'etour_demo_places';
	}

	/** Tên bảng yêu cầu do người dùng gửi qua form. */
	public static function requests_table() {
		global $wpdb;
		return $wpdb->prefix . 'etour_demo_requests';
	}

	/**
	 * Tạo bảng bằng dbDelta(). Quy tắc của dbDelta: mỗi cột một dòng,
	 * hai dấu cách sau PRIMARY KEY, khai báo KEY thay vì INDEX.
	 */
	public static function install() {
		global $wpdb;
		$charset = $wpdb->get_charset_collate(); // utf8mb4 + collation của CSDL.
		$places   = self::places_table();
		$requests = self::requests_table();

		$sql = "CREATE TABLE {$places} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  name varchar(191) NOT NULL,
  province varchar(100) NOT NULL,
  category varchar(50) NOT NULL DEFAULT '',
  price int(10) unsigned NOT NULL DEFAULT 0,
  summary text NULL,
  is_active tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY  (id),
  KEY province (province),
  KEY category (category)
) {$charset};
CREATE TABLE {$requests} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  place_id bigint(20) unsigned NOT NULL,
  full_name varchar(120) NOT NULL,
  email varchar(190) NOT NULL,
  phone varchar(20) NOT NULL,
  travel_date date NOT NULL,
  guests smallint(5) unsigned NOT NULL DEFAULT 1,
  note text NULL,
  consent tinyint(1) NOT NULL DEFAULT 0,
  status varchar(20) NOT NULL DEFAULT 'new',
  user_id bigint(20) unsigned NOT NULL DEFAULT 0,
  created_at datetime NOT NULL,
  PRIMARY KEY  (id),
  KEY place_id (place_id),
  KEY status (status)
) {$charset};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
		update_option( 'etour_db_version', ETOUR_DB_VERSION );
		self::seed_demo();
	}

	/** Nạp dữ liệu minh họa khi bảng còn trống (giá là giá trị mô phỏng; nhóm thay bằng dữ liệu từ dataset của chủ đề). */
	public static function seed_demo() {
		global $wpdb;
		$table = self::places_table();
		if ( (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ) > 0 ) {
			return;
		}
		$rows = array(
			array( 'Vịnh Hạ Long', 'Quảng Ninh', 'Biển đảo', 290000, 'Du thuyền ngắm vịnh, chèo kayak hang Luồn.' ),
			array( 'Phố cổ Hội An', 'Quảng Nam', 'Di sản', 120000, 'Đô thị cổ, đèn lồng, ẩm thực đường phố.' ),
			array( 'Tràng An', 'Ninh Bình', 'Danh thắng', 250000, 'Đi thuyền qua hệ thống hang động đá vôi.' ),
			array( 'Đại Nội Huế', 'Huế', 'Di sản', 200000, 'Quần thể cung điện triều Nguyễn.' ),
			array( 'Chợ nổi Cái Răng', 'Cần Thơ', 'Văn hóa', 0, 'Giao thương trên sông lúc bình minh.' ),
			array( 'Sa Pa – Tả Van', 'Lào Cai', 'Cộng đồng', 0, 'Bản làng, ruộng bậc thang, trekking.' ),
			array( 'Phong Nha – Kẻ Bàng', 'Quảng Bình', 'Hang động', 250000, 'Hệ thống hang động và rừng nguyên sinh.' ),
			array( 'Đà Lạt – hồ Tuyền Lâm', 'Lâm Đồng', 'Sinh thái', 0, 'Rừng thông, hồ nước, khí hậu mát mẻ.' ),
		);
		foreach ( $rows as $r ) {
			$wpdb->insert( $table, array( 'name' => $r[0], 'province' => $r[1], 'category' => $r[2], 'price' => $r[3], 'summary' => $r[4], 'is_active' => 1 ), array( '%s', '%s', '%s', '%d', '%s', '%d' ) );
		}
	}

	/** Tự cập nhật cấu trúc khi tăng ETOUR_DB_VERSION. */
	public static function maybe_upgrade() {
		if ( get_option( 'etour_db_version' ) !== ETOUR_DB_VERSION ) {
			self::install();
		}
	}

	/** Danh sách điểm đến đang hoạt động, lọc theo tỉnh (tùy chọn). */
	public static function get_places( $province = '' ) {
		global $wpdb;
		$table = self::places_table();
		if ( '' !== $province ) {
			return $wpdb->get_results( $wpdb->prepare(
				"SELECT id, name, province, category, price, summary FROM {$table} WHERE is_active = 1 AND province = %s ORDER BY name",
				$province
			) );
		}
		return $wpdb->get_results( "SELECT id, name, province, category, price, summary FROM {$table} WHERE is_active = 1 ORDER BY province, name" );
	}

	public static function place_exists( $id ) {
		global $wpdb;
		$table = self::places_table();
		return (bool) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE id = %d AND is_active = 1", $id ) );
	}

	/** Ghi một yêu cầu; trả về ID hoặc false. */
	public static function insert_request( array $data ) {
		global $wpdb;
		$ok = $wpdb->insert(
			self::requests_table(),
			array(
				'place_id'    => $data['place_id'],
				'full_name'   => $data['full_name'],
				'email'       => $data['email'],
				'phone'       => $data['phone'],
				'travel_date' => $data['travel_date'],
				'guests'      => $data['guests'],
				'note'        => $data['note'],
				'consent'     => 1,
				'status'      => 'new',
				'user_id'     => get_current_user_id(),
				'created_at'  => current_time( 'mysql' ),
			),
			array( '%d', '%s', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%d', '%s' )
		);
		return $ok ? (int) $wpdb->insert_id : false;
	}

	/** Danh sách yêu cầu cho trang quản trị/REST (có lọc trạng thái). */
	public static function get_requests( $status = '', $limit = 50 ) {
		global $wpdb;
		$r = self::requests_table();
		$p = self::places_table();
		$sql = "SELECT r.*, p.name AS place_name FROM {$r} r LEFT JOIN {$p} p ON p.id = r.place_id";
		if ( '' !== $status ) {
			return $wpdb->get_results( $wpdb->prepare( $sql . ' WHERE r.status = %s ORDER BY r.created_at DESC LIMIT %d', $status, $limit ) );
		}
		return $wpdb->get_results( $wpdb->prepare( $sql . ' ORDER BY r.created_at DESC LIMIT %d', $limit ) );
	}

	public static function update_status( $id, $status ) {
		global $wpdb;
		return false !== $wpdb->update( self::requests_table(), array( 'status' => $status ), array( 'id' => $id ), array( '%s' ), array( '%d' ) );
	}

	/** Các trạng thái hợp lệ – dùng chung cho kiểm tra hợp lệ (whitelist). */
	public static function statuses() {
		return array(
			'new'       => 'Mới',
			'contacted' => 'Đã liên hệ',
			'confirmed' => 'Đã xác nhận',
			'cancelled' => 'Đã hủy',
		);
	}
}
