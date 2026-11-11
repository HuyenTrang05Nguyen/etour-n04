<?php
/**
 * Cấu hình cơ bản cho WordPress - Dự án E-Tourism Nhóm N04 (CD04)
 */

// ** Cài đặt MySQL - Thông tin kết nối CSDL ** //
define( 'DB_NAME', 'etour_n04' );
define( 'DB_USER', 'root' );
define( 'DB_PASSWORD', '' );
define( 'DB_HOST', 'localhost' );

/** Bảng mã CSDL - bắt buộc utf8mb4 (YC01)[cite: 14, 18] */
define( 'DB_CHARSET', 'utf8mb4' );
define( 'DB_COLLATE', '' );

/**#@+
 * Khóa xác thực và salt bảo mật.
 */
define( 'AUTH_KEY',         'etour-n04-auth-key-2026-phenikaa-cd04-9812739812' );
define( 'SECURE_AUTH_KEY',  'etour-n04-secure-auth-key-2026-phenikaa-cd04-192837' );
define( 'LOGGED_IN_KEY',    'etour-n04-logged-in-key-2026-phenikaa-cd04-761523' );
define( 'NONCE_KEY',        'etour-n04-nonce-key-2026-phenikaa-cd04-55443322' );
define( 'AUTH_SALT',        'etour-n04-auth-salt-2026-phenikaa-cd04-11223344' );
define( 'SECURE_AUTH_SALT', 'etour-n04-secure-auth-salt-2026-phenikaa-cd04-998877' );
define( 'LOGGED_IN_SALT',   'etour-n04-logged-in-salt-2026-phenikaa-cd04-665544' );
define( 'NONCE_SALT',       'etour-n04-nonce-salt-2026-phenikaa-cd04-33221100' );
/**#@-*/

/**
 * Tiền tố bảng CSDL của nhóm N04 (bắt buộc et04_).
 */
$table_prefix = 'et04_';

/**
 * Môi trường PHÁT TRIỂN (máy cục bộ) - Chỉ khai báo duy nhất 1 lần tại đây.
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );

/**
 * Gia cố an ninh bắt buộc (YC09)[cite: 14, 18].
 */
define( 'DISALLOW_FILE_EDIT', true );
define( 'WP_POST_REVISIONS', 10 );

/**
 * Đường dẫn website cục bộ hiện tại của bạn.
 */
define( 'WP_HOME', 'http://localhost/etour-n04' );
define( 'WP_SITEURL', 'http://localhost/etour-n04' );

/* That's all, stop editing! Happy publishing. */

/** Đường dẫn tuyệt đối đến thư mục WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Thiết lập biến WordPress và nạp các tệp. */
require_once ABSPATH . 'wp-settings.php';