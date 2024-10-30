jQuery(document).ready(function($) {
    
    /*
     * The data array if a form is not used. In this case only a button is used
     * requiring the data to be serialized manually.
     */
	jQuery("#search-product").click(function(a){
		a.preventDefault();
		if(jQuery("#item-name-search").val() == ""){
			window.alert("Enter Product name");
		}
		else if(jQuery("#category-name").val() == ""){
			window.alert("Select Category Name");
		}
		else {
            jQuery("#zsSetOptionLoader").show();
            var data = {
    			action: 'ebay_product',
    			product_name: jQuery("#item-name-search").val(),
    			category_name: jQuery("#category-name").val()
    		};

            //POST the data and append the results to the results div
            jQuery.post(ajaxurl, data, function(response) {  
                jQuery("#zsSetOptionLoader").fadeOut();    
                jQuery("#update-post").css("display", "inline-block");
                jQuery("#search-results").html(response);          
            });
        }   
	});

    $("#reselect-products").click(function(b){
        b.preventDefault();
        $("#saved-products").css("display", "none");
        $("#search-inputs").css("display", "block");
        $("#search-product").css("display", "inline-block");
        
        $("#reselect-products").css("display", "none");
        $("#product-id-amazon").val("");
        $("#product-id-ebay").val("");
        $("#product-totalPrice-ebay").val("");
        $("#product-affiliateURL-ebay").val("");
        $("#product-affiliateURL-ebay").val("");
    });


	$(document).on('click', ".product-container", function() {
    	if($(this).hasClass("amazon")){
    		// window.alert("Product Select is from Amazon");
    		$(".product-container.amazon").removeClass("product-selected");
    		$(this).addClass("product-selected");
            var amazon_id = $(this).data("productId");
            $("#product-id-amazon").val(amazon_id);
    	}
    	if($(this).hasClass("ebay")){
    		// window.alert("Product Select is from eBay");
    		$(".product-container.ebay").removeClass("product-selected");
    		$(this).addClass("product-selected");
            var ebay_id = $(this).data("productId");
            var ebay_totalprice = $(this).data("productTotalprice");
            var ebay_affiliate_url = $(this).data("affiliateUrl");
            $("#product-id-ebay").val(ebay_id);
            $("#product-totalPrice-ebay").val(ebay_totalprice);
            $("#product-affiliateURL-ebay").val(ebay_affiliate_url);
    	}
    });

});