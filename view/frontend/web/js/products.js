define(['jquery', 'mage/url','mage/cookies' ,'jquery/ui', 'slick'], function($,urlBuilder) {
    'use strict';

    function renderTileProduct(product_ids) {
        var product_ids = product_ids.join(',');
        $.ajax({
            url: urlBuilder.build('csearch/index/products'),
            type: 'POST',
            data: {product_ids: product_ids ,form_key: $.mage.cookies.get('form_key')},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#product_list').html(response.products_info);

                    $('.recommend-lists').slick({
                        dots: true,
                        infinite: false,
                        speed: 300,
                        slidesToShow: 5,
                        slidesToScroll: 1,
                        responsive: [{
                                breakpoint: 1024,
                                settings: {
                                    slidesToShow: 3,
                                    slidesToScroll: 1,
                                    infinite: true,
                                    dots: true
                                }
                            },
                            {
                                breakpoint: 600,
                                settings: {
                                    slidesToShow: 2,
                                    slidesToScroll: 1
                                }
                            },
                            {
                                breakpoint: 480,
                                settings: {
                                    slidesToShow: 1,
                                    slidesToScroll: 1
                                }
                            }
                            // You can unslick at a given breakpoint now by adding:
                            // settings: "unslick"
                            // instead of a settings object
                        ]
                    });
                }
            }
        });
        return true;
    }

 
    return {
        renderTileProduct: renderTileProduct
    };
});

 