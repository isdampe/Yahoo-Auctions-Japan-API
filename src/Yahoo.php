<?php

namespace Yahoo;

define('YAHOO_ORDER_ASC',            'A');
define('YAHOO_ORDER_DESC',           'D');
define('YAHOO_SORT_END_TIME',      'End');
define('YAHOO_SORT_HAS_IMG',       'Img');
define('YAHOO_SORT_BID_COUNT',    'Bids');
define('YAHOO_SORT_PRICE',       'Cbids');
define('YAHOO_SORT_BID_BUY',  'Bidorbuy');
define('YAHOO_AUCTION_TYPE_ALL',       0);
define('YAHOO_AUCTION_TYPE_AUCTION',   1);
define('YAHOO_AUCTION_TYPE_FIXED',     2);
define('YAHOO_ITEM_CONDITION_ALL',		 0);
define('YAHOO_ITEM_CONDITION_NEW',		 1);
define('YAHOO_ITEM_CONDITION_USED',		 2);

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
		$this->api_endpoint = "https://auctions.yahooapis.jp/AuctionWebService/V2/";
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
	private function build_get_query_url( string $action, array $params ): string {

		$params['output'] = $this->encoding;
		$params['appid'] = $this->app_id;
		return sprintf("%s%s?%s", $this->api_endpoint, $action, http_build_query($params));

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
	public function get_product_list(
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

		return array();

	}

}
