<?php 
require '../src/Yahoo.php';
require '../config/apikey.php';

$api = new \Yahoo\Api($api_key);

//print_r( $api->get_auction( 'c590168729' ) );

//print_r( $api->get_all_auction_bid_history( 'r181830533' ) );

print_r( $api->get_auction_qa( 'r181830533' ) );


