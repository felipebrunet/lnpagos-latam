<?php
namespace LNBudaPlugin;

// use OpenNode\OpenNode;
// use OpenNode\Merchant;
// use OpenNode\OrderIsNotValid;
// use OpenNode\OrderNotFound;


/**
 * For calling LNBuda API
 */

class LNBudaAPI {

    // protected $buda_url_get = 'https://www.buda.com/api/v2/pay/';
    // protected $buda_url_check = 'https://realtime.buda.com/sub?channel=lightninginvoices%40';
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