<?php

function icompare_pull_product_from_amazon($product_name, $category_name){

	$options = get_option('icompare_plugin_options');

	$public     = $options['access_key'];
	$private    = $options['secrect_access_key'];
	$affiliate_id  = $options['affiliate_id'];
	$site = "com";

	$amazon = new AmazonProductAPI($public, $private, $site, $affiliate_id);

	$category = $category_name;
	$i = 0;
	$limit = 0;

	$params = array(
		"Operation"     => "ItemSearch",
		"SearchIndex"   => $category,
		"Keywords" => $product_name,
		"ResponseGroup" => "Medium"       
	);

	$result =  $amazon->queryAmazon($params);
	$json = json_encode($result);
	$result = json_decode($json, false);

	$similar_products = $result->Items->Item;
	foreach($similar_products as $si){

		if($limit < 1){
			$item_url = $si->DetailPageURL; //get its amazon url
			$product_title = $si->ItemAttributes->Title;
		  	if(isset($si->MediumImage->URL)){
		  		$img = $si->MediumImage->URL; //get the image url
		  	}else{
		  		$img = $si->ImageSets->ImageSet[$i]->MediumImage->URL;
		  		$i++;
		  	}

		  	if(isset($si->OfferSummary->LowestNewPrice->FormattedPrice)){
		  		$price = $si->OfferSummary->LowestNewPrice->FormattedPrice;
		  	}
		  	else if(isset($si->ItemAttributes->ListPrice->FormattedPrice)){
		  		$price = $si->ItemAttributes->ListPrice->FormattedPrice;
		  	}else{
		  		$price = "No Price Available";
		  	}
		  	$results_aws .= "<a href=\"".esc_url($item_url)."\" target=\"_blank\" class=\"hvr-grow\">
		  					<div class=\"product-container amazon\">
								<div class=\"product-info\">
									<h4>Amazon</h4>
									<div class=\"product-price\">
										<strong><span>".esc_html($price)."</span></strong>
									</div>
									<div class=\"product-img\">
										<img src=\"".esc_attr($img)."\"/>
									</div>
									<div class=\"product-name\">
										<strong><span>".esc_html($product_title)."</span></strong>
									</div>
									<div class=\"product-link\">
										<span>View Product on Amazon</span>
									</div>
								</div>
								<div class=\"best-price disabled\"><img src=\"".plugins_url('icompare/img/ribbon_red_left_top.png')."\"/></div>
							</div>
							</a>";
			$limit++;
		}
	}

	return $results_aws;
}

function icompare_search_product_from_amazon($product_name, $category_name){

	$options = get_option('icompare_plugin_options');

	$public     = $options['access_key'];
	$private    = $options['secrect_access_key'];
	$affiliate_id  = $options['affiliate_id'];
	$site = "com";

	$amazon = new AmazonProductAPI($public, $private, $site, $affiliate_id);

	$category = $category_name;
	$i = 0;
	$limit = 0;

	$params = array(
		"Operation"     => "ItemSearch",
		"SearchIndex"   => $category,
		"Keywords" => $product_name,
		"ResponseGroup" => "Medium"   
	);

	$result =  $amazon->queryAmazon($params);
	$json = json_encode($result);
	$result = json_decode($json, false);

	$similar_products = (isset($result->Items->Item) ? $result->Items->Item : NULL);
	if(isset($result->Items->Request->Errors->Error->Message) != NULL){
	  $results_aws .= "<p><strong>No item is found in this category. Please select another category</strong></p>";
	}else if($similar_products == NULL){
	  $results_aws .= "<p><strong>No item is found in this category. Please select another category</strong></p>";
	}else{
		foreach($similar_products as $si){

			if($limit < 5){
				$item_url = $si->DetailPageURL; //get its amazon url
				$product_title = $si->ItemAttributes->Title;
				$amazon_id = $si->ASIN;
			  	if(isset($si->MediumImage->URL)){
			  		$img = $si->MediumImage->URL; //get the image url
			  	}else{
			  		$img = $si->ImageSets->ImageSet[$i]->MediumImage->URL;
			  		$i++;
			  	}

			  	if(isset($si->OfferSummary->LowestNewPrice->FormattedPrice)){
			  		$price = $si->OfferSummary->LowestNewPrice->FormattedPrice;
			  	}
			  	else if(isset($si->ItemAttributes->ListPrice->FormattedPrice)){
			  		$price = $si->ItemAttributes->ListPrice->FormattedPrice;
			  	}else{
			  		$price = "No Price Available";
			  	}
			  	$results_aws .= "<div class=\"product-container amazon\" data-product-id=\"".esc_attr($amazon_id)."\">
									<div class=\"product-info\">
										<div class=\"product-img\">
											<img src=\"".esc_attr($img)."\"/>
										</div>
										<div class=\"product-name\">
											<strong><span>".esc_html($product_title)."</span></strong>
										</div>
										<div class=\"product-price\">
											<strong><span>".esc_html($price)."</span></strong>
										</div>
									</div>
								</div>";
				$limit++;
			}
		}
	}

	return $results_aws;
}

function icompare_display_selected_amazon_product($product_id){

	$ASIN = $product_id;
	$options = get_option('icompare_plugin_options');

	$public     = $options['access_key'];
	$private    = $options['secrect_access_key'];
	$affiliate_id  = $options['affiliate_id'];
	$site = "com";

	$amazon = new AmazonProductAPI($public, $private, $site, $affiliate_id);

	$params = array(
		"Operation"     => "ItemLookup",
		"IdType"	=> "ASIN",
		"ItemId"   => $ASIN,
		"ResponseGroup" => "Medium"       
	);

	$i = 0;
	$limit = 0;

	$result =  $amazon->queryAmazon($params);
	$json = json_encode($result);
	$result = json_decode($json, true);


	$item_url = $result['Items']['Item']['DetailPageURL']; //get its amazon url
	$product_title = $result['Items']['Item']['ItemAttributes']['Title'];
	$amazon_id = $result['Items']['Item']['ASIN'];
		  	if(isset($result['Items']['Item']['MediumImage']['URL'])){
		  		$img = $result['Items']['Item']['MediumImage']['URL']; //get the image url
		  	}else{
		  		$img = $result['Items']['Item']['ImageSets']['ImageSet']['MediumImage']['URL'];
		  		$i++;
		  	}

		  	if(isset($result['Items']['Item']['OfferSummary']['LowestNewPrice']['FormattedPrice'])){
		  		$price = $result['Items']['Item']['OfferSummary']['LowestNewPrice']['FormattedPrice'];
		  	}
		  	else if(isset($result['Items']['Item']['ItemAttributes']['ListPrice']['FormattedPrice'])){
		  		$price = $result['Items']['Item']['ItemAttributes']['ListPrice']['FormattedPrice'];
		  	}else{
		  		$price = "No Price Available";
		  	}
		  	$results_aws = "<a href=\"".esc_url($item_url)."\" target=\"_blank\" class=\"hvr-grow\">
		  					<div class=\"product-container amazon\">
								<div class=\"product-info\">
									<img src=\"".plugins_url('icompare-pro/img/amazon.png')."\"/>
									<div class=\"product-price\">
										<strong><span>".esc_html($price)."</span></strong>
									</div>
									<div class=\"product-img\">
										<img src=\"".esc_attr($img)."\"/>
									</div>
									<div class=\"product-name\">
										<strong><span>".esc_html($product_title)."</span></strong>
									</div>
									<div class=\"product-link\">
										<span>View Product on Amazon</span>
									</div>
								</div>
								<div class=\"best-price disabled\"><img src=\"".plugins_url('icompare/img/ribbon_red_left_top.png')."\"/></div>
							</div>
							</a>";

	return $results_aws;
}

function icompare_display_selected_amazon_product_banner($product_id){

	$ASIN = $product_id;
	$options = get_option('icompare_plugin_options');

	$public     = $options['access_key'];
	$private    = $options['secrect_access_key'];
	$affiliate_id  = $options['affiliate_id'];
	$site = "com";

	$amazon = new AmazonProductAPI($public, $private, $site, $affiliate_id);

	$params = array(
		"Operation"     => "ItemLookup",
		"IdType"	=> "ASIN",
		"ItemId"   => $ASIN,
		"ResponseGroup" => "Medium"       
	);

	$i = 0;
	$limit = 0;

	$result =  $amazon->queryAmazon($params);
	$json = json_encode($result);
	$result = json_decode($json, true);


	$item_url = $result['Items']['Item']['DetailPageURL']; //get its amazon url
	$product_title = $result['Items']['Item']['ItemAttributes']['Title'];
	$amazon_id = $result['Items']['Item']['ASIN'];
		  	if(isset($result['Items']['Item']['MediumImage']['URL'])){
		  		$img = $result['Items']['Item']['MediumImage']['URL']; //get the image url
		  	}else{
		  		$img = $result['Items']['Item']['ImageSets']['ImageSet']['MediumImage']['URL'];
		  		$i++;
		  	}

		  	if(isset($result['Items']['Item']['OfferSummary']['LowestNewPrice']['FormattedPrice'])){
		  		$price = $result['Items']['Item']['OfferSummary']['LowestNewPrice']['FormattedPrice'];
		  	}
		  	else if(isset($result['Items']['Item']['ItemAttributes']['ListPrice']['FormattedPrice'])){
		  		$price = $result['Items']['Item']['ItemAttributes']['ListPrice']['FormattedPrice'];
		  	}else{
		  		$price = "No Price Available";
		  	}
		  	  $results_aws = "<a href=\"".esc_url($item_url)."\" target=\"_blank\">
		  				     	<div class=\"icompare-banner amazon\">
		  				     		<div class=\"product-info\">
				  				     	<h4>Amazon</h4>
				  				     	<div class=\"product-img\">
											<img src=\"".esc_attr($img)."\"/>
										</div>
										<div class=\"product-name\">
											<p><strong><span>".esc_html($product_title)."</span></strong></p>
										</div>
										<div class=\"product-price\">
											<strong><span>".esc_html($price)."</span></strong>
										</div>
										<div class=\"product-link\">
											<span>View Product on Amazon</span>
										</div>
									</div>
									<div class=\"best-price disabled\"><img src=\"".plugins_url('icompare-pro/img/ribbon_red_left_top.png')."\"/></div>
								</div>
		  				    </a>";

	return $results_aws;
}


function icompare_pull_single_product_amazon($product_id){

	$ASIN = $product_id;
	$options = get_option('icompare_plugin_options');

	$public     = $options['access_key'];
	$private    = $options['secrect_access_key'];
	$affiliate_id  = $options['affiliate_id'];
	$site = "com";

	$amazon = new AmazonProductAPI($public, $private, $site, $affiliate_id);

	$params = array(
		"Operation"     => "ItemLookup",
		"IdType"	=> "ASIN",
		"ItemId"   => $ASIN,
		"ResponseGroup" => "Medium"       
	);

	$i = 0;
	$limit = 0;

	$result =  $amazon->queryAmazon($params);
	$json = json_encode($result);
	$result = json_decode($json, true);


	$item_url = $result['Items']['Item']['DetailPageURL']; //get its amazon url
	$product_title = $result['Items']['Item']['ItemAttributes']['Title'];
	$amazon_id = $result['Items']['Item']['ASIN'];
		  	if(isset($result['Items']['Item']['MediumImage']['URL'])){
		  		$img = $result['Items']['Item']['MediumImage']['URL']; //get the image url
		  	}else{
		  		$img = $result['Items']['Item']['ImageSets']['ImageSet']['MediumImage']['URL'];
		  		$i++;
		  	}

		  	if(isset($result['Items']['Item']['OfferSummary']['LowestNewPrice']['FormattedPrice'])){
		  		$price = $result['Items']['Item']['OfferSummary']['LowestNewPrice']['FormattedPrice'];
		  	}
		  	else if(isset($result['Items']['Item']['ItemAttributes']['ListPrice']['FormattedPrice'])){
		  		$price = $result['Items']['Item']['ItemAttributes']['ListPrice']['FormattedPrice'];
		  	}else{
		  		$price = "No Price Available";
		  	}
		  	$results_aws = "<div class=\"product-container amazon\" data-product-id=\"".esc_attr($amazon_id)."\">
								<div class=\"product-info\">
									<div class=\"product-img\">
										<img src=\"".esc_attr($img)."\"/>
									</div>
									<div class=\"product-name\">
										<strong><span>".esc_html($product_title)."</span></strong>
									</div>
									<div class=\"product-price\">
										<strong><span>".esc_html($price)."</span></strong>
									</div>
								</div>
							</div>";

	return $results_aws;

}

?>