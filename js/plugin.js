jQuery( document ).ready(function() {
    var price_ebay = jQuery(".sl-product-row .product-container.ebay > .product-info > .product-price > strong > span").text();
    var price_aws = jQuery(".sl-product-row .product-container.amazon > .product-info > .product-price > strong > span").text();

    // console.log(price_ebay);
    // console.log(price_aws);
    if(price_ebay == undefined || price_ebay == ""){
        price_aws = price_aws.replace('$','');
        price_aws = price_aws.replace(',','');
        price_aws = parseFloat(price_aws);
        jQuery(".sl-product-row .amazon .best-price").removeClass("disabled");
    }else if(price_aws == undefined || price_aws == ""){
        price_ebay = price_ebay.replace(',','');
        price_ebay = parseFloat(price_ebay);
        jQuery(".sl-product-row .ebay .best-price").removeClass("disabled");
    }else{
        price_aws = price_aws.replace('$','');
        price_aws = price_aws.replace(',','');
        price_ebay = price_ebay.replace(',','');
        price_ebay = parseFloat(price_ebay);
        price_aws = parseFloat(price_aws);

        if(price_ebay < price_aws){
            // console.log("Amazon is higher that Ebay");
            jQuery(".sl-product-row .ebay .best-price").removeClass("disabled");
        }else if(price_ebay > price_aws){
            // console.log("Ebay is higher that Amazon");
            jQuery(".sl-product-row .amazon .best-price").removeClass("disabled");
        }
    }

    

    var distance = jQuery('.sl-product-row').offset().top - 100;

    jQuery(window).scroll(function() {
        if ( jQuery(window).scrollTop() >= distance ) {
            // Your div has reached the top
            jQuery(".icompare-banner-row").fadeOut();;
        }else{
            jQuery(".icompare-banner-row").show();
        }
    });

});