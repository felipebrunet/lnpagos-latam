<?php

require_once(dirname(__DIR__) . '/includes/init.php');

use LNPagosPlugin\LNPagosAPI;


// add_action( 'http_api_debug', 'http_call_debug', 10, 5 );
 
function http_call_debug( $response, $type, $class, $args, $url ) {
    // You can change this from error_log() to var_dump() but it can break AJAX requests
    error_log( 'Request URL: ' . var_export( $url, true ) );
    error_log( 'Request Args: ' . var_export( $args, true ) );
    error_log( 'Request Response : ' . var_export( $response, true ) );
}


function create_api() {
	$user_name = 'felipe';
	return new LNPagosAPI($user_name);
}


class LNBitsAPITest extends WP_UnitTestCase {


	public function test_create_invoice_and_check() {
		$api = create_api();
		$r = $api->createInvoice(10, "Testing invoice");
		$this->assertEquals(200, $r['status']);
		$this->assertArrayHasKey('encoded_payment_request', $r['response']);
		$this->assertArrayHasKey('id', $r['response']);
		$this->assertEquals("awaiting_payment", $r['response']['state']);
	}
}