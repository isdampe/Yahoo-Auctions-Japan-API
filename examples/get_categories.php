<?php 
require '../src/Yahoo.php';
require '../config/apikey.php';

$api = new \Yahoo\Api($api_key);

print_r( $api->get_category_tree() );


//Get child category.
//print_r( $api->get_category_tree(23336) );
