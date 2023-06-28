<?php
namespace LNPagosPlugin;

// use OpenNode\OpenNode;
// use OpenNode\Merchant;
// use OpenNode\OrderIsNotValid;
// use OpenNode\OrderNotFound;


/**
 * For calling LNPagos API
 */

class LNPagosAPI {

    protected $user_name;

    public function __construct($user_name) {
        $this->buda_url_get = 'https://www.buda.com/api/v2/pay/';
        $this ->buda_url_check = 'https://realtime.buda.com/sub?channel=lightninginvoices%40';
        $this->user_name = $user_name;
    }

    public function createInvoice($amount, $memo) {
        $c = new CurlWrapper();
        return $c->getBuda( $this->buda_url_get . $this->user_name.'/invoice?amount=' . $amount . '&description=' . $memo, array() );
    }

    public function checkInvoicePaid($payment_id) {
        $c = new CurlWrapper();
        return $c->getBuda( $this->buda_url_check . $payment_id, array() );
    }
}