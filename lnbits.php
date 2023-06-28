<?php

/*
Plugin Name: LNPay Latam
Plugin URI: https://github.com/felipebrunet/lnpay-latam
Description: Cobra en Bitcoin Lightning directo a tu cuenta, sin comisiones.
Version: 1.0.0
Author: Felipe Brunet
Author URI: https://felipebrunet.github.io/ 
*/


add_action('plugins_loaded', 'lnbuda_init');

define('LNBUDA_PAYMENT_PAGE_SLUG', 'lnbuda_payment');


require_once(__DIR__ . '/includes/init.php');

use LNBudaPlugin\Utils;
use LNBudaPlugin\LNBudaAPI;



function woocommerce_lnbuda_activate() {
    if (!current_user_can('activate_plugins')) return;

    global $wpdb;

    if ( null === $wpdb->get_row( "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name = '".LNBUDA_PAYMENT_PAGE_SLUG."'", 'ARRAY_A' ) ) {
        $page = array(
          'post_title'  => __( 'Pago con Bitcoin Lightning' ),
          'post_name' => LNBUDA_PAYMENT_PAGE_SLUG,
          'post_status' => 'publish',
          'post_author' => wp_get_current_user()->ID,
          'post_type'   => 'page',
          'post_content' => render_template('payment_page.php', array())
        );

        // insert the post into the database
        wp_insert_post( $page );
    }
}

register_activation_hook(__FILE__, 'woocommerce_lnbuda_activate');


// Helper to render templates under ./templates.
function render_template($tpl_name, $params) {
    return wc_get_template_html($tpl_name, $params, '', plugin_dir_path(__FILE__).'templates/');
}


// Generate lnbuda_payment page, using ./templates/lnbuda_payment.php
function lnbuda_payment_shortcode() {
    $check_payment_url = trailingslashit(get_bloginfo('wpurl')) . '?wc-api=wc_gateway_lnBuda';

    if (isset($_REQUEST['order_id'])) {
        $order_id = absint($_REQUEST['order_id']);
        $order = wc_get_order($order_id);
        $invoice = $order->get_meta("buda_invoice");
        $success_url = $order->get_checkout_order_received_url();
    } else {
        // Likely when editting page with this shortcode, use dummy order.
        $order_id = 1;
        $invoice = "lnbc0000";
        $success_url = "/dummy-success";
    }

    $template_params = array(
        "invoice" => $invoice,
        "check_payment_url" => $check_payment_url,
        'order_id' => $order_id,
        'success_url' => $success_url
    );
    
    return render_template('payment_shortcode.php', $template_params);
}



// This is the entry point of the plugin, where everything is registered/hooked up into WordPress.
function lnbuda_init() {
    if (!class_exists('WC_Payment_Gateway')) {
        return;
    };

    // Register shortcode for rendering Lightning invoice (QR code)
    add_shortcode('lnbuda_payment_shortcode', 'lnbuda_payment_shortcode');

    // Register the gateway, essentially a controller that handles all requests.
    function add_lnbuda_gateway($methods) {
        $methods[] = 'WC_Gateway_LNBuda';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'add_lnbuda_gateway');


    // Defined here, because it needs to be defined after WC_Payment_Gateway is already loaded.
    class WC_Gateway_LNBuda extends WC_Payment_Gateway {
        public function __construct() {
            global $woocommerce;

            $this->id = 'lnbuda';
            $this->icon = plugin_dir_url(__FILE__).'assets/lightning.png';
            $this->has_fields = false;
            $this->method_title = 'BudaLink';
            $this->method_description = 'Reciba pagos en Bitcoin Lightning sin comisiones extra, usando su BudaLink.';

            $this->init_form_fields();
            $this->init_settings();

            $this->title = $this->get_option('title');
            $this->description = $this->get_option('description');


            $user_name = $this->get_option('buda_user_name');
            $this->api = new LNBudaAPI($user_name);

            add_action('woocommerce_update_options_payment_gateways_'.$this->id, array($this, 'process_admin_options'));
            add_action('woocommerce_thankyou_'.$this->id, array($this, 'thankyou'));
            add_action('woocommerce_api_wc_gateway_'.$this->id, array($this, 'check_payment'));
        }

        /**
         * Render admin options/settings.
         */
        public function admin_options() {
            ?>
            <h3><?php _e('BudaLink', 'woothemes'); ?></h3>
            <p><?php _e('Aceptar Bitcoin al instante mediante BudaLink/Lightning.', 'woothemes'); ?></p>
            <table class="form-table">
                <?php $this->generate_settings_html(); ?>
            </table>
            <?php

        }

        /**
         * Generate config form fields, shown in admin->WooCommerce->Settings.
         */
        public function init_form_fields() {
            // echo("init_form_fields");
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __('Habilitar pagos con Lightning', 'woocommerce'),
                    'label' => __('Habilitar pagos con BTC Lightning via BudaLink', 'woocommerce'),
                    'type' => 'checkbox',
                    'description' => '',
                    'default' => 'no',
                ),
                'title' => array(
                    'title' => __('Título', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('The payment method title which a customer sees at the checkout of your store.', 'woocommerce'),
                    'default' => __('Paga con Bitcoin Lightning', 'woocommerce'),
                ),
                'description' => array(
                    'title' => __('Descripción', 'woocommerce'),
                    'type' => 'textarea',
                    'description' => __('The payment method description which a customer sees at the checkout of your store.', 'woocommerce'),
                    'default' => __('Powered by BudaLink. Puedes usar cualquier billetera lightning para pagar.'),
                ),
                'buda_user_name' => array(
                    'title' => __('Nombre de usuario Buda Link', 'woocommerce'),
                    'type' => 'text',
                    'description' => __('Tu nombre de usuario en Buda para BudaLink. Obten el tuyo en <a href="https://www.buda.com/link" target="_blank">aquí</a>.', 'woocommerce'),
                    'default' => '',
              ),
            );
        }


        /**
         * ? Output for thank you page.
         */
        public function thankyou() {
            if ($description = $this->get_description()) {
                echo esc_html(wpautop(wptexturize($description)));
            }
        }


        /**
         * Called from checkout page, when "Place order" hit, through AJAX.
         * 
         * Call LNBuda API to create an invoice, and store the invoice in the order metadata.
         */
        public function process_payment($order_id) {
            $order = wc_get_order($order_id);

            // This will be stored in the Lightning invoice (ie. can be used to match orders in LNBuda)
            $memo = "WordPress Order= ".$order->get_id()." Total= ".$order->get_total().get_woocommerce_currency();

            // TODO convert any currency to the currency of the company
            $amount = $order->get_total();
            
            // Call LNBuda server to create invoice
            // Expected 201
            // {
            //  "checking_id": "e0dfcde5ff9708d71e5947c86b9d46cc230fd0f16e0a5f03b00ac97f0b23e684", 
            //  "lnurl_response": null, 
            //  "payment_id": "e0dfcde5ff9708d71e5947c86b9d46cc230fd0f16e0a5f03b00ac97f0b23e684", 
            //  "payment_request": "..."
            // }
            $r = $this->api->createInvoice($amount, $memo);

            if ($r['status'] == 200 || $r['status'] == 201) {
                $resp = $r['response'];
                $order->add_meta_data('buda_invoice', $resp['encoded_payment_request'], true);
                $order->add_meta_data('buda_payment_id', $resp['id'], true);
                $order->save();

                // TODO: configurable payment page slug
                $redirect_url = add_query_arg(array("order_id" => $order->get_id()), get_permalink( get_page_by_path( LNBUDA_PAYMENT_PAGE_SLUG ) ));

                return array(
                    "result" => "success",
                    "redirect" => $redirect_url
                );
            } else {
                error_log("LNBuda API failure. Status=".$r['status']);
                error_log(print_r($r['response'], true));
                return array(
                    "result" => "failure",
                    "messages" => array("Failed to create Buda invoice.")
                );
            }
        }


        /**
         * Called by lnbuda_payment page (with QR code), through ajax.
         * 
         * Checks whether given invoice was paid, using LNBuda API,
         * and updates order metadata in the database.
         */
        public function check_payment() {
            $order = wc_get_order($_REQUEST['order_id']);
            $payment_id = $order->get_meta('buda_payment_id');
            $r = $this->api->checkInvoicePaid($payment_id);

            if ($r['status'] == 200 || $r['status'] == 201) {
                $order->add_order_note('Payment is settled and has been credited to your Buda account. Purchased goods/services can be securely delivered to the customer.');
                $order->payment_complete();
                $order->save();
                error_log("PAID");
                echo(json_encode(array(
                    'result' => 'success',
                    'redirect' => $order->get_checkout_order_received_url(),
                    'paid' => true
                )));
            } else {
                echo(json_encode(array(
                    'result' => 'failure',
                    'paid' => false,
                    'messages' => array('Request to Buda failed.')
                )));

            }
            die();
        }
    }
}
