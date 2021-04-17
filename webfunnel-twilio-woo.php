<?php
/*
Plugin Name: Web Funnel Twilio Woo
Description: Web Funnel Twilio for WooCommerce
Plugin URI:  https://webfunnel.io
Author:      Hans Gcn
Version:     1.1
License:     GPLv2 or later
License URI: https://webfunnel.io
*/

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {

	exit; 
}



add_action( 'woocommerce_thankyou', 'action_woocommerce_thankyou' );


function action_woocommerce_thankyou( $order_get_id ) {
    $order = wc_get_order( $order_get_id );
    send_twilio( $order );
    
}


function send_twilio ($order) {

require_once plugin_dir_path(__FILE__) . 'vendor/autoload.php';
require_once plugin_dir_path(__FILE__) . 'src/settings.php';
// require('wp-content/plugins/woocommerce/includes/class-wc-order.php');

// Get Customer details
$firstname = $order->get_billing_first_name();
$lastname = $order->get_billing_last_name();
$orderId = $order->get_id();
$total = $order->get_total();


// Get Item Name and Details Array

$product_details = array();
$order_items = $order->get_items();
foreach( $order_items as $product ) {
                $product_details[] = "Product Name: " . $product['name'] . " " . "x " . $product['qty'];

            }

            $product_list = implode( ', ', $product_details );
            
//get woocommerce billing phone
// $order = wc_get_order($order_id);
$billing_phone = $order->get_billing_phone();
$trim_phone_number = ltrim($billing_phone, 0);
$areaCode = '+63';
$finalPhoneNumber = $areaCode.$trim_phone_number;


$sid = $settings['sid'];
$token = $settings['token'];

//Your twillio number
$fromNumber = '+1234567';

$adminNumber = '+12345678';

// Number of your customer
$toNumber = $finalPhoneNumber;



$message = "Hey, " . $firstname . " " . "Thanks for your order" . " " . "Here are the details of your order: " . "\n" . "Order ID:" . $orderId . ". " . "Total: " . $total.".";

$message2 = "Hey, " . "ADMIN" . " " . "There's an order." . " " . "Here are the details of the order: " . "\n" . $product_list . ". " . "Total: " . $total.".";

$client = new Twilio\Rest\Client($sid, $token);

//For customer message
$client->messages->create(
    $toNumber, 
    array(
        'from' => $fromNumber,
        'body' => $message
    )
);

// For admin message

$client->messages->create(
    $adminNumber, 
    array(
        'from' => $fromNumber,
        'body' => $message2
    )
);

}

