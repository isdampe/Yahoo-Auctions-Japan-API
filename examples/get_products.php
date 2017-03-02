<?php 
require '../src/Yahoo.php';
require '../config/apikey.php';

$api = new \Yahoo\Api($api_key);

/*
print_r( $api->get_product_list(
	23336, //Category ID
	0,		 //Page
	YAHOO_SORT_END_TIME,  //Sort default
	YAHOO_ORDER_ASC,  //Order default
	YAHOO_AUCTION_TYPE_ALL,  //Auction type all default
	1000.00, //min auction price
	50000.00 //max auction price
) );
 */

//Get seller product list
print_r( $api->get_seller_product_list(
	'xo607co', //Seller ID
	0,		 //Page
	YAHOO_SORT_END_TIME,  //Sort default
	YAHOO_ORDER_ASC,  //Order default
	YAHOO_AUCTION_TYPE_ALL,  //Auction type all default
	1000.00, //min auction price
	50000.00 //max auction price
) );
