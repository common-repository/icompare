<?php

function icompare_pull_product_from_ebay($product_name){
	// API request variables
	$options = get_option('icompare_plugin_options');

	$endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';  // URL to call
	$version = '1.0.0';  // API version supported by your application
	$appid = $options['app_id'];  // Replace with your own AppID
	$globalid = 'EBAY-US';  // Global ID of the eBay site you want to search (e.g., EBAY-DE)
	$query = $product_name;  // You may want to supply your own query
	$safequery = urlencode($query);  // Make the query URL-friendly
	$i = '0';  // Initialize the item filter index to 0

	// Create a PHP array of the item filters you want to use in your request
	$filterarray =
		array(
		    array(
		    'name' => 'FreeShippingOnly',
		    'value' => 'true',
		    'paramName' => '',
		    'paramValue' => ''),
		    array(
		    'name' => 'BestOfferOnly',
		    'value' => 'true',
		    'paramName' => '',
		    'paramValue' => ''),
		    array(
		    'name' => 'SortOrderType ',
		    'value' => 'PricePlusShippingLowest',
		    'paramName' => '',
		    'paramValue' => ''),
		);

	// Build the indexed item filter URL snippet
	icompare_buildURLArray($filterarray);

	// Construct the findItemsByKeywords HTTP GET call
	$apicall = "$endpoint?";
	$apicall .= "OPERATION-NAME=findItemsByKeywords";
	$apicall .= "&SERVICE-VERSION=$version";
	$apicall .= "&SECURITY-APPNAME=$appid";
	$apicall .= "&GLOBAL-ID=$globalid";
	$apicall .= "&keywords=$safequery";
	$apicall .= "&paginationInput.entriesPerPage=1";
	$apicall .= "$urlfilter";

	// Load the call and capture the document returned by eBay API
	$resp = @simplexml_load_file($apicall);

	// Check to see if the request was successful, else print an error
	if ($resp->ack == "Success") {
		$results = '';
	  	// If the response was loaded, parse it and build links
	  	foreach($resp->searchResult->item as $item) {
	    	$pic   = $item->galleryURL;
	    	$link  = $item->viewItemURL;
	    	$title = $item->title;
	    	$price = sprintf("%01.2f", $item->sellingStatus->convertedCurrentPrice);
        	$ship  = sprintf("%01.2f", $item->shippingInfo->shippingServiceCost);
        	$total = sprintf("%01.2f", ((float)$item->sellingStatus->convertedCurrentPrice
                      + (float)$item->shippingInfo->shippingServiceCost));

	    	// For each SearchResultItem node, build a link and append it to $results
	    	$results .= "<a href=\"".esc_url($link)."\" target=\"_blank\" class=\"hvr-grow\">
	    					<div class=\"product-container ebay\">
							<div class=\"product-info\">
								<h4>Ebay</h4>
								<div class=\"product-price\">
									<strong>$<span>".esc_html($total)."</span></strong>
								</div>
								<div class=\"product-img\">
									<img src=\"".esc_attr($pic)."\">
								</div>
								<div class=\"product-name\">
									<strong><span>".esc_html($title)."</span></strong>
								</div>
								<div class=\"product-link\">
									<span>View Product on Ebay</span>
								</div>
							</div>
							<div class=\"best-price disabled\"><img src=\"".plugins_url('icompare/img/ribbon_red_left_top.png')."\"/></div>
						</div>
						</a>";
	    	// $results .= "<img src=\"$pic\"></td><td><a href=\"$link\">$title -- USD $total</a>";
  		}
	}
	// If the response does not indicate 'Success,' print an error
	else {
		$results  = "<p><strong>No item is found in this category. Please select another category";
		$results .= "</strong></p>";
	}

	return $results;
}

function icompare_display_selected_ebay_product($product_id,$product_price,$affiliate_url){
	$ebay_id = $product_id;
	$totalprice = $product_price;
	$ebay_affiliate_url = $affiliate_url;
	$options = get_option('icompare_plugin_options');

	// API request variables
	$endpoint = 'http://open.api.ebay.com/shopping';  // URL to call
	$version = '515';  // API version supported by your application
	$appid =  $options['app_id'];  // Replace with your own AppID
	$globalid = 'EBAY-US';  // Global ID of the eBay site you want to search (e.g., EBAY-DE)
	//$query = 'iphone';  // You may want to supply your own query
	$item_id = $ebay_id;  // You may want to supply your own query
	// $safequery = urlencode($query);  // Make the query URL-friendly
	$i = '0';  // Initialize the item filter index to 0

	$apicall = "$endpoint?";
	$apicall .= "callname=GetSingleItem";
	$apicall .= "&responseencoding=XML";
	$apicall .= "&appid=$appid";
	$apicall .= "&version=$version";
	$apicall .= "&siteid=0";
	$apicall .= "&ItemID=$item_id";

	$resp = @simplexml_load_file($apicall);

	if ($resp->Ack == "Success") {
		$results = '';
	  	// If the response was loaded, parse it and build links
	  	foreach($resp->Item as $item) {
	    	$ebay_product_id = $item->ItemID;
	    	$pic   = $item->PictureURL;
	    	$link  = $ebay_affiliate_url;
	    	$title = $item->Title;
	    	// $price = sprintf("%01.2f", $item->sellingStatus->convertedCurrentPrice);
      //   	$ship  = sprintf("%01.2f", $item->shippingInfo->shippingServiceCost);
      //   	$total = sprintf("%01.2f", ((float)$item->sellingStatus->convertedCurrentPrice
      //                 + (float)$item->shippingInfo->shippingServiceCost));

	    	// For each SearchResultItem node, build a link and append it to $results
	    	$results .= "<a href=\"".esc_url($link)."\" target=\"_blank\" class=\"hvr-grow\">
	    					<div class=\"product-container ebay\">
							<div class=\"product-info\">
								<img src=\"".plugins_url('icompare-pro/img/ebay.png')."\"/>
								<div class=\"product-price\">
									<strong>$<span>".esc_html($totalprice)."</span></strong>
								</div>
								<div class=\"product-img\">
									<img src=\"".esc_attr($pic)."\">
								</div>
								<div class=\"product-name\">
									<strong><span>".esc_html($title)."</span></strong>
								</div>
								<div class=\"product-link\">
									<span>View Product on Ebay</span>
								</div>
							</div>
							<div class=\"best-price disabled\"><img src=\"".plugins_url('icompare/img/ribbon_red_left_top.png')."\"/></div>
						</div>
						</a>";
	    	// $results .= "<img src=\"$pic\"></td><td><a href=\"$link\">$title -- USD $total</a>";
  		}
	}
	// If the response does not indicate 'Success,' print an error
	else {
		$results  = "<p><strong>No item is found in this category. Please select another category";
		$results .= "</strong></p>";
	}

	return $results;
}

function icompare_display_selected_ebay_product_banner($product_id,$product_price,$affiliate_url){
	$ebay_id = $product_id;
	$totalprice = $product_price;
	$ebay_affiliate_url = $affiliate_url;
	$options = get_option('icompare_plugin_options');

	// API request variables
	$endpoint = 'http://open.api.ebay.com/shopping';  // URL to call
	$version = '515';  // API version supported by your application
	$appid =  $options['app_id'];  // Replace with your own AppID
	$globalid = 'EBAY-US';  // Global ID of the eBay site you want to search (e.g., EBAY-DE)
	//$query = 'iphone';  // You may want to supply your own query
	$item_id = $ebay_id;  // You may want to supply your own query
	// $safequery = urlencode($query);  // Make the query URL-friendly
	$i = '0';  // Initialize the item filter index to 0

	$apicall = "$endpoint?";
	$apicall .= "callname=GetSingleItem";
	$apicall .= "&responseencoding=XML";
	$apicall .= "&appid=$appid";
	$apicall .= "&version=$version";
	$apicall .= "&siteid=0";
	$apicall .= "&ItemID=$item_id";

	$resp = @simplexml_load_file($apicall);

	if ($resp->Ack == "Success") {
		$results = '';
	  	// If the response was loaded, parse it and build links
	  	foreach($resp->Item as $item) {
	    	$ebay_product_id = $item->ItemID;
	    	$pic   = $item->PictureURL;
	    	$link  = $ebay_affiliate_url;
	    	$title = $item->Title;
	    	// $price = sprintf("%01.2f", $item->sellingStatus->convertedCurrentPrice);
      //   	$ship  = sprintf("%01.2f", $item->shippingInfo->shippingServiceCost);
      //   	$total = sprintf("%01.2f", ((float)$item->sellingStatus->convertedCurrentPrice
      //                 + (float)$item->shippingInfo->shippingServiceCost));

	    	// For each SearchResultItem node, build a link and append it to $results
	    	$results .= "<a href=\"".esc_url($link)."\" target=\"_blank\">
	    					<div class=\"icompare-banner ebay\">
	    						<div class=\"product-info\">
	    							<h4>eBay</h4>
	    							<div class=\"product-img\">
										<img src=\"".esc_attr($pic)."\">
									</div>
									<div class=\"product-name\">
										<p><strong><span>".esc_html($title)."</span></strong></p>
									</div>
									<div class=\"product-price\">
										<strong>$<span>".esc_html($totalprice)."</span></strong>
									</div>
									<div class=\"product-link\">
										<span>View Product on Ebay</span>
									</div>
								</div>
								<div class=\"best-price disabled\"><img src=\"".plugins_url('icompare-pro/img/ribbon_red_left_top.png')."\"/></div>
							</div>
						</a>";
	    	// $results .= "<img src=\"$pic\"></td><td><a href=\"$link\">$title -- USD $total</a>";
  		}
	}
	// If the response does not indicate 'Success,' print an error
	else {
		$results  = "<p><strong>No item is found in this category. Please select another category";
		$results .= "</strong></p>";
	}

	return $results;
}

function icompare_search_product_from_ebay($product_name){
	// API request variables
	$options = get_option('icompare_plugin_options');

	$endpoint = 'http://svcs.ebay.com/services/search/FindingService/v1';  // URL to call
	$version = '1.0.0';  // API version supported by your application
	$appid = $options['app_id'];  // Replace with your own AppID
	$campid = $options['camp_id'];
	$tracking_id = $options['tracking_id'];
	$globalid = 'EBAY-US';  // Global ID of the eBay site you want to search (e.g., EBAY-DE)
	$query = $product_name;  // You may want to supply your own query
	$safequery = urlencode($query);  // Make the query URL-friendly
	$i = '0';  // Initialize the item filter index to 0

	// Create a PHP array of the item filters you want to use in your request
	$filterarray =
		array(
		    array(
		    'name' => 'FreeShippingOnly',
		    'value' => 'true',
		    'paramName' => '',
		    'paramValue' => ''),
		    array(
		    'name' => 'BestOfferOnly',
		    'value' => 'true',
		    'paramName' => '',
		    'paramValue' => ''),
		    array(
		    'name' => 'SortOrderType ',
		    'value' => 'PricePlusShippingLowest',
		    'paramName' => '',
		    'paramValue' => ''),
		);

	// Build the indexed item filter URL snippet
	icompare_buildURLArray($filterarray);

	// Construct the findItemsByKeywords HTTP GET call
	$apicall = "$endpoint?";
	$apicall .= "OPERATION-NAME=findItemsByKeywords";
	$apicall .= "&SERVICE-VERSION=$version";
	$apicall .= "&SECURITY-APPNAME=$appid";
	$apicall .= "&GLOBAL-ID=$globalid";
	$apicall .= "&keywords=$safequery";
	$apicall .= "&affiliate.trackingId=$campid";
	if($tracking_id != NULL){
		$apicall .= "&affiliate.customId=$tracking_id";
	}
	$apicall .= "&affiliate.networkId=9";
	$apicall .= "&paginationInput.entriesPerPage=5";
	$apicall .= "$urlfilter";

	// Load the call and capture the document returned by eBay API
	$resp = @simplexml_load_file($apicall, 'SimpleXMLElement', LIBXML_NOWARNING);

	// Check to see if the request was successful, else print an error
	if ($resp->ack == "Success") {
		$results = '';
	  	// If the response was loaded, parse it and build links
	  	foreach($resp->searchResult->item as $item) {
	    	$ebay_product_id = $item->itemId;
	    	$pic   = $item->galleryURL;
	    	$link  = $item->viewItemURL;
	    	$title = $item->title;
	    	$price = sprintf("%01.2f", $item->sellingStatus->convertedCurrentPrice);
        	$ship  = sprintf("%01.2f", $item->shippingInfo->shippingServiceCost);
        	$total = sprintf("%01.2f", ((float)$item->sellingStatus->convertedCurrentPrice
                      + (float)$item->shippingInfo->shippingServiceCost));

	    	// For each SearchResultItem node, build a link and append it to $results
	    	$results .= "<div class=\"product-container ebay\" data-product-id=\"".esc_attr($ebay_product_id)."\" data-product-totalprice=\"".esc_attr($total)."\" data-affiliate-url=\"".esc_attr($link)."\">
							<div class=\"product-info\">
								<div class=\"product-img\">
									<img src=\"".esc_attr($pic)."\">
								</div>
								<div class=\"product-name\">
									<strong><span>".esc_html($title)."</span></strong>
								</div>
								<div class=\"product-price\" >
									<strong>$<span>".esc_html($total)."</span></strong>
								</div>
							</div>
						</div>";
	    	// $results .= "<img src=\"$pic\"></td><td><a href=\"$link\">$title -- USD $total</a>";
  		}
	}
	// If the response does not indicate 'Success,' print an error
	else {
		$results  = "<p><strong>No item is found in this category. Please select another category";
		$results .= "</strong></p>";
	}

	return $results;

}

function icompare_pull_single_product_ebay($product_id,$product_price){
	$ebay_id = $product_id;
	$totalprice = $product_price;
	$options = get_option('icompare_plugin_options');

	// API request variables
	$endpoint = 'http://open.api.ebay.com/shopping';  // URL to call
	$version = '515';  // API version supported by your application
	$appid =  $options['app_id'];  // Replace with your own AppID
	$globalid = 'EBAY-US';  // Global ID of the eBay site you want to search (e.g., EBAY-DE)
	//$query = 'iphone';  // You may want to supply your own query
	$item_id = $ebay_id;  // You may want to supply your own query
	// $safequery = urlencode($query);  // Make the query URL-friendly
	$i = '0';  // Initialize the item filter index to 0

	$apicall = "$endpoint?";
	$apicall .= "callname=GetSingleItem";
	$apicall .= "&responseencoding=XML";
	$apicall .= "&appid=$appid";
	$apicall .= "&version=$version";
	$apicall .= "&siteid=0";
	$apicall .= "&ItemID=$item_id";

	$resp = @simplexml_load_file($apicall);

	if ($resp->Ack == "Success") {
		$results = '';
	  	// If the response was loaded, parse it and build links
	  	foreach($resp->Item as $item) {
	    	$ebay_product_id = $item->ItemID;
	    	$pic   = $item->PictureURL;
	    	$link  = $item->ViewItemURLForNaturalSearch;
	    	$title = $item->Title;
	    	// $price = sprintf("%01.2f", $item->sellingStatus->convertedCurrentPrice);
      //   	$ship  = sprintf("%01.2f", $item->shippingInfo->shippingServiceCost);
      //   	$total = sprintf("%01.2f", ((float)$item->sellingStatus->convertedCurrentPrice
      //                 + (float)$item->shippingInfo->shippingServiceCost));

	    	// For each SearchResultItem node, build a link and append it to $results
	    	$results .= "<div class=\"product-container ebay\" data-product-id=\"".esc_attr($ebay_product_id)."\">
							<div class=\"product-info\">
								<div class=\"product-img\">
									<img src=\"".esc_attr($pic)."\">
								</div>
								<div class=\"product-name\">
									<strong><span>".esc_html($title)."</span></strong>
								</div>
								<div class=\"product-price\">
									<strong>$<span>".esc_html($totalprice)."</span></strong>
								</div>
							</div>
						</div>";
	    	// $results .= "<img src=\"$pic\"></td><td><a href=\"$link\">$title -- USD $total</a>";
  		}
	}
	// If the response does not indicate 'Success,' print an error
	else {
		$results  = "<p><strong>No item is found in this category. Please select another category";
		$results .= "</strong></p>";
	}

	return $results;
}

// Generates an indexed URL snippet from the array of item filters
function icompare_buildURLArray ($filterarray) {
	global $urlfilter;
	global $i;
  	// Iterate through each filter in the array
  	foreach($filterarray as $itemfilter) {
    	// Iterate through each key in the filter
    	foreach ($itemfilter as $key =>$value) {
	      	if(is_array($value)) {
	        	foreach($value as $j => $content) { // Index the key for each value
	          		$urlfilter .= "&itemFilter($i).$key($j)=$content";
	        	}
	      	}else {
		        if($value != "") {
		          $urlfilter .= "&itemFilter($i).$key=$value";
		        }
	      	}
    	}
    	$i++;
  	}
 	return "$urlfilter";
} // End of icompare_buildURLArray function
?>