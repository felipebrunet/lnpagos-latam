<?php
namespace LNPagosPlugin;

class CurlWrapper {

    
    // Buda new functions
    private function simpleBudaRequest($method, $url, $params) {
        $url = add_query_arg($params, $url);
        $r = wp_remote_request($url, array(
            'method' => $method
        ));

        if (is_wp_error($r)) {
            error_log('WP_Error: '.$r->get_error_message());
            return array(
                'status' => 500,
                'response' => $r->get_error_message()
            );
        }

        return array(
            'status' => $r['response']['code'],
            'response' => json_decode($r['body'], true)
        );

    }

    public function getBuda($url, $params) {
        // error_log("Request es: ".$url);
        return $this->simpleBudaRequest('GET', $url, $params);
    }

}
