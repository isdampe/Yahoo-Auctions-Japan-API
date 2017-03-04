# Yahoo Auctions Japan API Library for PHP

This is a small class library that can be used for accessing Yahoo Auctions Japan's API, in a standardized,
reliable format.

It has been built around the official documentation, found here: http://developer.yahoo.co.jp/webapi/auctions/.

## Requirements

This library has been written to support PHP 7.0 or greater.

#### List of requirements

* php-7.0 or greater
* curl
* php-7.0-curl

## Getting started

See the [examples](examples/) directory to see more detailed examples.

#### Fetch a list of auctions

```php
require 'src/Yahoo.php';

$api_key = "insert_your_api_key";
$api = new \Yahoo\Api( $api_key );

$auction_list = $api->get_auction_list();

```

#### Fetch auction information

```php
require 'src/Yahoo.php';

$api_key = "insert_your_api_key";
$api = new \Yahoo\Api( $api_key );

$auction_id = "insert_your_auction_id";
$auction_info = $api->get_auction( $auction_id );

```

## Available methods

##### \Yahoo\Api\get_category_tree()
_Fetches and returns the category tree or subcategory tree_

```php
/**
 * @param {int} $category - The category to get children of. Defaults to 0.
 * @return {array} - An array of the category tree
 */
```

##### \Yahoo\Api\get_auction_list()
Fetches and returns a list of products in a category

```php
/**
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

```

##### \Yahoo\Api\get_seller_auction_list()
Fetches and returns a list of products being sold by a seller

```php
/**
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
```

##### \Yahoo\Api\search()
Searches for a list of products as defined by user input

```php
/**
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
```

##### \Yahoo\Api\get_auction()
Fetches and returns an array of product data.

```php
/**
 * @param {string} $auction_id - The ID of the auction to fetch.
 * @return {array} - The array of the auction data
 */
```

##### \Yahoo\Api\get_auction_bid_history()
Fetches and returns an array of auction bid history

```php
/**
 * @param {string} $auction_id - The ID of the auction to fetch bid history for
 * @param {int} $page - The page number of the request, defaults to 1
 * @return {array} - The array of the bid history
 */
```

##### \Yahoo\Api\get_all_auction_bid_history()
Fetches and returns an array of _all_ auction bid history

```php
/**
 * @param {string} $auction_id - The ID of the auction to fetch bid history for
 * @param {int} $page - The page number of the request, defaults to 1
 * @return {array} - The array of the bid history
 */
```

##### \Yahoo\Api\get_auction_qa()
Fetches a list of questions and answers for an auction

```php
/**
 * @param {string} $auction_id - The ID of the auction to fetch Q&A for
 * @return {array} - The resulting Q&A data
 */
```

