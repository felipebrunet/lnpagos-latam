<?php
namespace LNPagosPlugin;

/**
 * For calling LNPagos API
 */

class LNPagosAPI {

    protected $user_name;
    protected $api_key;
    protected $api_secret;

    public function __construct($user_name, $api_key, $api_secret) {
        $this->buda_url_get = 'https://www.buda.com';
        $this ->buda_url_check = 'https://realtime.buda.com';
        $this->user_name = $user_name;
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
    }    

    public function createInvoice($amount, $memo) {
        $mt = explode(' ', microtime(false));
        $nonce = strval($mt[1]) . strval( $mt[0]* 1000000 );
        $route = '/api/v2/pay/' . $this->user_name . '/invoice?amount=' . $amount;
        $msg_firmar = strval('GET' . ' ' . $route . ' ' . $nonce);
        $signature = hash_hmac('sha384', $msg_firmar, $this->api_secret);
        $c = new CurlWrapper();
        $headers = array (
            'X-SBTC-APIKEY' => $this->api_key,
            'X-SBTC-NONCE' => $nonce,
            'X-SBTC-SIGNATURE' => $signature,
        );
        // error_log(print_r($headers, true));
        // error_log(print_r($signature, true));
        return $c->getBuda( $this->buda_url_get . $route, array(), $headers );
    }

    public function checkInvoicePaid($payment_id) {
        $mt = explode(' ', microtime(false));
        $nonce = strval($mt[1]) . strval( $mt[0]* 1000000 );
        $route = '/sub?channel=lightninginvoices%40' . $payment_id;
        $msg_firmar = strval('GET' . ' ' . $route . ' ' . $nonce);
        $signature = hash_hmac('sha384', $msg_firmar, $this->api_secret);
        $c = new CurlWrapper();
        $headers = array (
            'X-SBTC-APIKEY' => $this->api_key,
            'X-SBTC-NONCE' => $nonce,
            'X-SBTC-SIGNATURE' => $signature,
        );
        return $c->getBuda( $this->buda_url_check . $route, array(), $headers );
    }
}