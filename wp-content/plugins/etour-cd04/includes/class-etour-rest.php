<?php
/**
 * REST API của module – namespace etour/v1.
 *   GET /wp-json/etour/v1/places?province=…   (công khai, dữ liệu không nhạy cảm)
 *   GET /wp-json/etour/v1/requests?status=…   (yêu cầu quyền manage_options)
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Etour_REST {

	const NS = 'etour/v1';

	public static function register_routes() {
		register_rest_route( self::NS, '/places', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( __CLASS__, 'get_places' ),
			'permission_callback' => '__return_true', // Công khai có chủ đích: chỉ dữ liệu danh mục.
			'args'                => array(
				'province' => array(
					'type'              => 'string',
					'default'           => '',
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		) );

		register_rest_route( self::NS, '/requests', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( __CLASS__, 'get_requests' ),
			'permission_callback' => function () {
				return current_user_can( 'manage_options' ); // Chứa dữ liệu cá nhân → bắt buộc kiểm tra quyền.
			},
			'args'                => array(
				'status' => array(
					'type'    => 'string',
					'default' => '',
					'enum'    => array_merge( array( '' ), array_keys( Etour_DB::statuses() ) ),
				),
			),
		) );
	}

	public static function get_places( WP_REST_Request $req ) {
		$rows = Etour_DB::get_places( $req->get_param( 'province' ) );
		$data = array_map( function ( $r ) {
			return array(
				'id'       => (int) $r->id,
				'name'     => $r->name,
				'province' => $r->province,
				'category' => $r->category,
				'price'    => (int) $r->price,
				'summary'  => $r->summary,
			);
		}, $rows );
		return rest_ensure_response( array( 'count' => count( $data ), 'items' => $data ) );
	}

	public static function get_requests( WP_REST_Request $req ) {
		$rows = Etour_DB::get_requests( $req->get_param( 'status' ), 100 );
		return rest_ensure_response( array( 'count' => count( $rows ), 'items' => $rows ) );
	}
}
