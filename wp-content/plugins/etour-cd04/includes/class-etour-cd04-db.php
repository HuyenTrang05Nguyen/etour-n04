<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Etour_Cd04_DB {

	public static function seasons_table() {
		global $wpdb;
		return $wpdb->prefix . 'etour_seasons';
	}

	public static function tour_months_table() {
		global $wpdb;
		return $wpdb->prefix . 'etour_tour_months';
	}

	public static function requests_table() {
		global $wpdb;
		return $wpdb->prefix . 'etour_season_requests';
	}

	public static function install() {
		global $wpdb;
		$charset  = $wpdb->get_charset_collate();
		$seasons  = self::seasons_table();
		$tmonths  = self::tour_months_table();
		$requests = self::requests_table();

		$sql = "CREATE TABLE {$seasons} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  province varchar(80) NOT NULL,
  item_name varchar(150) NOT NULL,
  item_type varchar(30) NOT NULL,
  month_from tinyint(3) unsigned NOT NULL,
  month_to tinyint(3) unsigned NOT NULL,
  description text NULL,
  is_active tinyint(1) unsigned NOT NULL DEFAULT 1,
  PRIMARY KEY  (id),
  KEY idx_months (month_from, month_to),
  KEY idx_province (province)
) {$charset};
CREATE TABLE {$tmonths} (
  product_sku varchar(40) NOT NULL,
  month tinyint(3) unsigned NOT NULL,
  PRIMARY KEY  (product_sku, month),
  KEY idx_month (month)
) {$charset};
CREATE TABLE {$requests} (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  user_id bigint(20) unsigned NOT NULL DEFAULT 0,
  full_name varchar(120) NOT NULL,
  email varchar(120) NOT NULL,
  phone varchar(20) NOT NULL,
  expected_month tinyint(3) unsigned NOT NULL,
  season_item varchar(150) NULL,
  guests smallint(5) unsigned NOT NULL DEFAULT 2,
  note text NULL,
  consent tinyint(1) unsigned NOT NULL DEFAULT 1,
  status varchar(20) NOT NULL DEFAULT 'new',
  created_at datetime NOT NULL,
  PRIMARY KEY  (id),
  KEY idx_status (status),
  KEY idx_user (user_id)
) {$charset};";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
		update_option( 'etour_cd04_db_version', ETOUR_CD04_DB_VERSION );
		self::seed();
	}

	public static function maybe_upgrade() {
		if ( get_option( 'etour_cd04_db_version' ) !== ETOUR_CD04_DB_VERSION ) {
			self::install();
		}
	}

	public static function seed() {
		self::import_csv(
			'seasons.csv',
			self::seasons_table(),
			array( 'id', 'province', 'item_name', 'item_type', 'month_from', 'month_to', 'description' ),
			array( '%d', '%s', '%s', '%s', '%d', '%d', '%s' )
		);
		self::import_csv(
			'tour_months.csv',
			self::tour_months_table(),
			array( 'product_sku', 'month' ),
			array( '%s', '%d' )
		);
	}

	public static function import_csv( $file, $table, $cols, $formats ) {
		global $wpdb;
		if ( (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" ) > 0 ) {
			return 0;
		}
		$path = ETOUR_CD04_DIR . 'data/' . $file;
		if ( ! is_readable( $path ) ) {
			return 0;
		}
		$fh = fopen( $path, 'r' );
		if ( ! $fh ) {
			return 0;
		}
		$head = fgetcsv( $fh, 0, ',', '"', '' );
		if ( ! is_array( $head ) ) {
			fclose( $fh );
			return 0;
		}
		$head[0] = preg_replace( '/^\xEF\xBB\xBF/', '', $head[0] );
		$head    = array_map( 'trim', $head );
		if ( $head !== $cols ) {
			fclose( $fh );
			return new WP_Error( 'etour_csv', 'Sai tiêu đề cột: ' . $file );
		}

		$total = 0;
		while ( false !== ( $row = fgetcsv( $fh, 0, ',', '"', '' ) ) ) {
			if ( count( $row ) !== count( $cols ) ) {
				continue;
			}
			$data = array_combine( $cols, array_map( 'trim', $row ) );
			$res  = $wpdb->insert( $table, $data, $formats );
			if ( $res ) {
				$total++;
			}
		}
		fclose( $fh );
		return $total;
	}

	public static function get_seasons_by_month( $month = 0 ) {
		global $wpdb;
		$table = self::seasons_table();
		$month = (int) $month;

		if ( $month >= 1 && $month <= 12 ) {
			$sql = $wpdb->prepare(
				"SELECT id, province, item_name, item_type, month_from, month_to, description
				 FROM {$table}
				 WHERE is_active = 1
				   AND (
				     (month_from <= month_to AND %d BETWEEN month_from AND month_to)
				     OR
				     (month_from > month_to AND (%d >= month_from OR %d <= month_to))
				   )
				 ORDER BY province ASC, id ASC",
				$month,
				$month,
				$month
			);
			return $wpdb->get_results( $sql );
		}

		return $wpdb->get_results(
			"SELECT id, province, item_name, item_type, month_from, month_to, description
			 FROM {$table}
			 WHERE is_active = 1
			 ORDER BY province ASC, id ASC"
		);
	}

	public static function get_suggested_tours( $month ) {
		global $wpdb;
		$table = self::tour_months_table();
		$skus  = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT product_sku FROM {$table} WHERE month = %d ORDER BY product_sku ASC",
				(int) $month
			)
		);

		if ( empty( $skus ) || ! function_exists( 'wc_get_product_id_by_sku' ) ) {
			return array();
		}

		$items = array();
		foreach ( $skus as $sku ) {
			$pid = wc_get_product_id_by_sku( $sku );
			if ( ! $pid ) {
				continue;
			}
			$product = wc_get_product( $pid );
			if ( ! $product || 'publish' !== $product->get_status() ) {
				continue;
			}
			$img_id  = $product->get_image_id();
			$img_url = $img_id ? wp_get_attachment_image_url( $img_id, 'woocommerce_thumbnail' ) : '';

			$items[] = array(
				'id'         => (int) $product->get_id(),
				'sku'        => (string) $product->get_sku(),
				'name'       => (string) $product->get_name(),
				'price'      => (int) $product->get_price(),
				'price_html' => wp_strip_all_tags( $product->get_price_html() ),
				'permalink'  => esc_url_raw( $product->get_permalink() ),
				'image'      => $img_url ? esc_url_raw( $img_url ) : '',
			);
		}
		return $items;
	}

	public static function insert_request( array $d ) {
		global $wpdb;
		$table = self::requests_table();
		$ok    = $wpdb->insert(
			$table,
			array(
				'user_id'        => get_current_user_id(),
				'full_name'      => $d['full_name'],
				'email'          => $d['email'],
				'phone'          => $d['phone'],
				'expected_month' => $d['expected_month'],
				'season_item'    => $d['season_item'],
				'guests'         => $d['guests'],
				'note'           => $d['note'],
				'consent'        => 1,
				'status'         => 'new',
				'created_at'     => current_time( 'mysql' ),
			),
			array( '%d', '%s', '%s', '%s', '%d', '%s', '%d', '%s', '%d', '%s', '%s' )
		);
		return $ok ? (int) $wpdb->insert_id : 0;
	}
}