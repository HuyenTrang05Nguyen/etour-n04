<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Etour_Cd04_REST {

	const NS = 'etour/v1';

	public static function register_routes() {
		register_rest_route( self::NS, '/seasons', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( __CLASS__, 'get_seasons' ),
			'permission_callback' => '__return_true',
			'args'                => array(
				'month' => array(
					'type'              => 'integer',
					'default'           => 0,
					'validate_callback' => array( __CLASS__, 'validate_optional_month' ),
				),
			),
		) );

		register_rest_route( self::NS, '/suggest', array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => array( __CLASS__, 'get_suggest' ),
			'permission_callback' => '__return_true',
			'args'                => array(
				'month' => array(
					'required'          => true,
					'type'              => 'integer',
					'validate_callback' => array( __CLASS__, 'validate_strict_month' ),
				),
			),
		) );
	}

	public static function validate_optional_month( $value ) {
		if ( '' === $value || 0 === (int) $value ) {
			return true;
		}
		return self::validate_strict_month( $value );
	}

	public static function validate_strict_month( $value ) {
		if ( ! is_numeric( $value ) || (int) $value < 1 || (int) $value > 12 ) {
			return new WP_Error(
				'etour_invalid_month',
				'Tham số month phải là số nguyên từ 1 đến 12.',
				array( 'status' => 400 )
			);
		}
		return true;
	}

	public static function get_seasons( WP_REST_Request $req ) {
		$month = (int) $req->get_param( 'month' );
		$rows  = Etour_Cd04_DB::get_seasons_by_month( $month );
		$items = array();
		foreach ( $rows as $r ) {
			$items[] = array(
				'id'          => (int) $r->id,
				'province'    => $r->province,
				'item_name'   => $r->item_name,
				'item_type'   => $r->item_type,
				'month_from'  => (int) $r->month_from,
				'month_to'    => (int) $r->month_to,
				'description' => $r->description,
			);
		}
		return rest_ensure_response( array(
			'month' => $month,
			'count' => count( $items ),
			'items' => $items,
		) );
	}

	public static function get_suggest( WP_REST_Request $req ) {
		$month = (int) $req->get_param( 'month' );
		$items = Etour_Cd04_DB::get_suggested_tours( $month );
		return rest_ensure_response( array(
			'month' => $month,
			'count' => count( $items ),
			'items' => $items,
		) );
	}
}