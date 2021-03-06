<?php

namespace Yahoo;

/**
 * List of definiations for search parameters
 * As defined throughout http://developer.yahoo.co.jp/webapi/auctions/ 
 * These should be used directly as arguments for function calls
 */
define('YAHOO_ORDER_ASC',													'A');
define('YAHOO_ORDER_DESC',												'D');
define('YAHOO_SORT_END_TIME',										'End');
define('YAHOO_SORT_HAS_IMG',										'Img');
define('YAHOO_SORT_BID_COUNT',								 'Bids');
define('YAHOO_SORT_PRICE',										'Cbids');
define('YAHOO_SORT_BID_BUY',							 'Bidorbuy');
define('YAHOO_AUCTION_TYPE_ALL',										0);
define('YAHOO_AUCTION_TYPE_AUCTION',						  	1);
define('YAHOO_AUCTION_TYPE_FIXED',									2);
define('YAHOO_ITEM_CONDITION_ALL',									0);
define('YAHOO_ITEM_CONDITION_NEW',									1);
define('YAHOO_ITEM_CONDITION_USED',									2);
define('YAHOO_SEARCH_TYPE_ALL',									'All');
define('YAHOO_SEARCH_TYPE_ANY',									'Any');
define('YAHOO_QUERY_TYPE_ALL',								  	0x2);
define('YAHOO_QUERY_TYPE_TITLE_ONLY',					  	0x4);
define('YAHOO_QUERY_TYPE_TITLE_TEXT',							0x8);

class Api {

	protected $app_id;
	protected $app_secret;
	protected $api_endpoint;
	protected $encoding;
	protected $has_secret;

	/**
	 * Creates a new instance of [Api]
	 * @param {string} $app_id - Your Yahoo Auctions API App ID
	 * @param {string} $app_secret - Your Yahoo Auctions API App Secret
	 * @return {bool} Always true
	 */
	public function __construct( string $app_id, string $app_secret = null ) {
		$this->app_id = $app_id;
		$this->app_secret = $app_secret;
		$this->api_endpoint = "https://auctions.yahooapis.jp/AuctionWebService/V";
		$this->encoding = "php";
		if ( $app_secret ) {
			$this->has_secret = true;
		} else {
			$this->has_secret = false;
		}
	}

	/**
	 * Builds a get query url, utilising key value stores passed in from $params
	 * @param {string} $action - The action to request
	 * @param {array} $params - The parameters to build into the query
	 * @return {string} - The URL of the built query
	 */
	private function build_get_query_url( string $action, array $params, int $api_version = 2 ): string {

		$api_base = sprintf("%s%s/", $this->api_endpoint, $api_version);
		$params['output'] = $this->encoding;
		$params['appid'] = $this->app_id;
		return sprintf("%s%s?%s", $api_base, $action, http_build_query($params));

	}

	/**
	 * Performs a HTTP GET request to a specified url
	 * @return {array} - The array of returned data
	 */
	private function get_request( string $url ): array {

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_HTTPGET, true);
		$buffer = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);

		return $this->standard_response($buffer,$error); 

	}

	/**
	 * Takes raw CURL data and issues a standard response
	 * @param {string} $buffer - The raw string buffer from CURL
	 * @param {string} $error - The raw error buffer from CURL
	 * @return {array} - The standardized array
	 */
	private function standard_response(string $buffer, string $error = null): array {

		try {
			$data = unserialize($buffer);
		} catch(Exception $e) {
			$data = array();
			$error = "Non serialized response received";
		}

		if ( empty($data['ResultSet']) ) {
			$data['ResultSet'] = array(); //No results found.
		}

		if (! empty($data['Error']) ) {
			$error = $data['Error'];
		}

		$response = array(
			'status' => ( $error ? 'error' : 'success' ),
			'error' => $error,
			'data' => $data['ResultSet']
		);

		return $response;

	}

	/**
	 * Fetches and returns the category tree or subcategory tree
	 * @param {int} $category - The category to get children of. Defaults to 0.
	 * @return {array} - An array of the category tree
	 */
	public function get_category_tree(int $category = 0): array {

		$query_url = $this->build_get_query_url('categoryTree', array(
			'category' => $category
		));

		return $this->get_request( $query_url );

	}

	/**
	 * Fetches and returns a list of products in a category
	 * @param {int} $category - The category ID
	 * @param {int} $page - The page number
	 * @param {string} $sort - The sort column for ordering [YAHOO_SORT_END_TIME|YAHOO_SORT_PRICE|...]
	 * @param {string} $order - The order by column [YAHOO_ORDER_ASC|YAHOO_ORDER_DESC]
	 * @param {string} $auction_type - The auction type [YAHOO_AUCTION_TYPE_ALL|YAHOO_AUCTION_TYPE_AUCTION|YAHOO_AUCTION_TYPE_FIXED]
	 * @param {float} $mix_auction_price - The minimum auction price for auctions (Default: null)
	 * @param {float} $max_auction_price - The maximum auction price for auctions (Default: null)
	 * @param {float} $min_buyout_price - The minimum buyout price (Default: null)
	 * @param {float} $max_buyout_price - The maximum buyout price (Default: null)
	 * @param {bool} $easy_payment - Whether to filter results that support easy payment
	 * @param {bool} $new_items - Whether to only show new items
	 * @param {bool} $free_shipping - Whether to only display free shipping items
	 * @param {bool} $has_buyout_price - Whether to only display items with a buyout price
	 * @param {int} $item_condition - Filter by the item condition [YAHOO_ITEM_CONDITION_ALL|YAHOO_ITEM_CONDITION_NEW|YAHOO_ITEM_CONDITION_USED]
	 * @return {array} - An array of the product data
	 */
	public function get_auction_list(
		int $category = 0, 
		int $page = 0,
		string $sort = YAHOO_SORT_END_TIME,
		string $order = YAHOO_ORDER_ASC,
		string $auction_type = YAHOO_AUCTION_TYPE_ALL,
		float $min_auction_price = null,
		float $max_auction_price = null,
		float $min_buyout_price = null,
		float $max_buyout_price = null,
		bool $easy_payment = null,
		bool $new_items = null,
		bool $free_shipping = null,
		bool $has_buyout_price = null,
		int $item_condition = YAHOO_ITEM_CONDITION_ALL 
	): array {

	//Build the query.
	$query_url = $this->build_get_query_url('categoryLeaf', array(
		'category' => $category,
		'page' => $page,
		'sort' => $sort,
		'order' => $order,
		'store' => $auction_type,
		'aucminprice' => $min_auction_price,
		'aucmaxprice' => $max_auction_price,
		'aucmin_bidorbuy_price' => $min_buyout_price,
		'aucmin_bidorbuy_price' => $max_buyout_price,
		'easypayment' => $easy_payment,
		'new' => $new_items,
		'freeshipping' => $free_shipping,
		'buynow' => $has_buyout_price,
		'item_status' => $item_condition
	));

	return $this->get_request( $query_url );

	}

	/**
	 * Fetches and returns a list of products being sold by a seller
	 * @param {int} $seller_id - The seller's ID
	 * @param {int} $page - The page number
	 * @param {string} $sort - The sort column for ordering [YAHOO_SORT_END_TIME|YAHOO_SORT_PRICE|...]
	 * @param {string} $order - The order by column [YAHOO_ORDER_ASC|YAHOO_ORDER_DESC]
	 * @param {string} $auction_type - The auction type [YAHOO_AUCTION_TYPE_ALL|YAHOO_AUCTION_TYPE_AUCTION|YAHOO_AUCTION_TYPE_FIXED]
	 * @param {float} $mix_auction_price - The minimum auction price for auctions (Default: null)
	 * @param {float} $max_auction_price - The maximum auction price for auctions (Default: null)
	 * @param {float} $min_buyout_price - The minimum buyout price (Default: null)
	 * @param {float} $max_buyout_price - The maximum buyout price (Default: null)
	 * @param {bool} $easy_payment - Whether to filter results that support easy payment
	 * @param {bool} $new_items - Whether to only show new items
	 * @param {bool} $free_shipping - Whether to only display free shipping items
	 * @param {bool} $has_buyout_price - Whether to only display items with a buyout price
	 * @param {int} $item_condition - Filter by the item condition [YAHOO_ITEM_CONDITION_ALL|YAHOO_ITEM_CONDITION_NEW|YAHOO_ITEM_CONDITION_USED]
	 * @return {array} - An array of the product data
	 */
	public function get_seller_auction_list(
		string $seller_id = null,
		int $page = 0,
		string $sort = YAHOO_SORT_END_TIME,
		string $order = YAHOO_ORDER_ASC,
		string $auction_type = YAHOO_AUCTION_TYPE_ALL,
		float $min_auction_price = null,
		float $max_auction_price = null,
		float $min_buyout_price = null,
		float $max_buyout_price = null,
		bool $easy_payment = null,
		bool $new_items = null,
		bool $free_shipping = null,
		bool $has_buyout_price = null,
		int $item_condition = YAHOO_ITEM_CONDITION_ALL 
	): array {

	if ( ! $seller_id ?? null )	{
		return array();
	}

	$query_url = $this->build_get_query_url('sellingList', array(
		'sellerID' => $seller_id,
		'page' => $page,
		'sort' => $sort,
		'order' => $order,
		'store' => $auction_type,
		'aucminprice' => $min_auction_price,
		'aucmaxprice' => $max_auction_price,
		'aucmin_bidorbuy_price' => $min_buyout_price,
		'aucmin_bidorbuy_price' => $max_buyout_price,
		'easypayment' => $easy_payment,
		'new' => $new_items,
		'freeshipping' => $free_shipping,
		'buynow' => $has_buyout_price,
		'item_status' => $item_condition
	));

	return $this->get_request( $query_url );
	}

	/**
	 * Searches for a list of products as defined by user input
	 * @param {string} $query - The search query term 
	 * @param {string} $type - The type of search [YAHOO_SEARCH_TYPE_ALL|YAHOO_SEARCH_TYPE_ANY]
	 * @param {string} $query_type - The type of query, i.e. search title and text [YAHOO_QUERY_TYPE_ALL|YAHOO_QUERY_TYPE_TITLE_ONLY|YAHOO_QUERY_TYPE_TITLE_TEXT]
	 * @param {int} $page - The page number
	 * @param {string} $sort - The sort column for ordering [YAHOO_SORT_END_TIME|YAHOO_SORT_PRICE|...]
	 * @param {string} $order - The order by column [YAHOO_ORDER_ASC|YAHOO_ORDER_DESC]
	 * @param {int} $category - The category ID to search in, Default: 0
	 * @param {string} $auction_type - The auction type [YAHOO_AUCTION_TYPE_ALL|YAHOO_AUCTION_TYPE_AUCTION|YAHOO_AUCTION_TYPE_FIXED]
	 * @param {float} $mix_auction_price - The minimum auction price for auctions (Default: null)
	 * @param {float} $max_auction_price - The maximum auction price for auctions (Default: null)
	 * @param {float} $min_buyout_price - The minimum buyout price (Default: null)
	 * @param {float} $max_buyout_price - The maximum buyout price (Default: null)
	 * @param {int} $location - The area code of an item
	 * @param {bool} $easy_payment - Whether to filter results that support easy payment
	 * @param {bool} $new_items - Whether to only show new items
	 * @param {bool} $free_shipping - Whether to only display free shipping items
	 * @param {bool} $has_buyout_price - Whether to only display items with a buyout price
	 * @param {int} $item_condition - Filter by the item condition [YAHOO_ITEM_CONDITION_ALL|YAHOO_ITEM_CONDITION_NEW|YAHOO_ITEM_CONDITION_USED]
	 * @return {array} - An array of the product data
	 */
	public function search (
		string $query = null,
		string $type = YAHOO_SEARCH_TYPE_ALL,
		string $query_type = YAHOO_QUERY_TYPE_ALL,
		int $page = 0,
		string $sort = YAHOO_SORT_END_TIME,
		string $order = YAHOO_ORDER_ASC,
		int $category = 0,
		string $auction_type = YAHOO_AUCTION_TYPE_ALL,
		float $min_auction_price = null,
		float $max_auction_price = null,
		float $min_buyout_price = null,
		float $max_buyout_price = null,
		int $location = 0,
		bool $easy_payment = null,
		bool $new_items = null,
		bool $free_shipping = null,
		bool $has_buyout_price = null,
		int $item_condition = YAHOO_ITEM_CONDITION_ALL 
	): array {

	if ( ! $query ?? null )	{
		return array();
	}

	$query_url = $this->build_get_query_url('search', array(
		'query' => $query,
		'type' => $type,
		'f' => $query_type,
		'page' => $page,
		'sort' => $sort,
		'order' => $order,
		'category' => $category,
		'store' => $auction_type,
		'aucminprice' => $min_auction_price,
		'aucmaxprice' => $max_auction_price,
		'aucmin_bidorbuy_price' => $min_buyout_price,
		'aucmin_bidorbuy_price' => $max_buyout_price,
		'easypayment' => $easy_payment,
		'new' => $new_items,
		'freeshipping' => $free_shipping,
		'buynow' => $has_buyout_price,
		'item_status' => $item_condition
	));

	return $this->get_request( $query_url );
	}

	/**
	 * Fetches and returns an array of product data.
	 * @param {string} $auction_id - The ID of the auction to fetch.
	 * @return {array} - The array of the auction data
	 */
	public function get_auction( string $auction_id = null ): array {

		if (! $auction_id ?? null ) {
			return array();
		}

		$query_url = $this->build_get_query_url( 'auctionItem', array(
			'AuctionID' => $auction_id
		) );

		return $this->get_request( $query_url );

	}

	/**
	 * Fetches and returns an array of auction bid history
	 * @param {string} $auction_id - The ID of the auction to fetch bid history for
	 * @param {int} $page - The page number of the request, defaults to 1
	 * @return {array} - The array of the bid history
	 */
	public function get_auction_bid_history( string $auction_id = null, int $page = 1 ): array {

		if (! $auction_id ?? null ) {
			return array();
		}

		//Note: Bid history uses V1 of Yahoo API.
		//It is not available on V2.
		$query_url = $this->build_get_query_url( 'BidHistory', array(
			'AuctionID' => $auction_id,
			'page' => $page
		), 1 );

		return $this->get_request( $query_url );

	}

	/**
	 * Fetches and returns an array of _all_ auction bid history
	 * @param {string} $auction_id - The ID of the auction to fetch bid history for
	 * @param {int} $page - The page number of the request, defaults to 1
	 * @return {array} - The array of the bid history
	 */
	public function get_all_auction_bid_history( string $auction_id = null, int $page = 1 ): array {

		if (! $auction_id ?? null ) {
			return array();
		}

		//Note: Bid history uses V1 of Yahoo API.
		//It is not available on V2.
		$query_url = $this->build_get_query_url( 'BidHistoryDetail', array(
			'AuctionID' => $auction_id,
			'page' => $page
		), 1 );

		return $this->get_request( $query_url );

	}

	/**
	 * Fetches a list of questions and answers for an auction
	 * @param {string} $auction_id - The ID of the auction to fetch Q&A for
	 * @return {array} - The resulting Q&A data
	 */
	public function get_auction_qa( string $auction_id = null ): array {

		if (! $auction_id ?? null ) {
			return array();
		}

		//Note: ShowQandA uses V1 of Yahoo API.
		$query_url = $this->build_get_query_url( 'ShowQandA', array(
			'AuctionID' => $auction_id
		), 1 );

		return $this->get_request( $query_url );

	}

}
