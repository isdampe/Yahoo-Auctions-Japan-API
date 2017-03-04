<?php 
require '../src/Yahoo.php';
require '../config/apikey.php';

$api = new \Yahoo\Api($api_key);

print_r( $api->search( 'sw20', YAHOO_SEARCH_TYPE_ALL, YAHOO_QUERY_TYPE_ALL, 0, YAHOO_SORT_END_TIME, YAHOO_ORDER_ASC,2084230793  ) );


