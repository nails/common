<?php

if (!function_exists('getBasket')) {

    /**
     * Returns the contents of the basket
     * @return stdClass
     */
    function getBasket()
    {
        get_instance()->load->model('shop/shop_model');
        get_instance()->load->model('shop/shop_basket_model');

        return get_instance()->shop_basket_model->get();
    }
}

// --------------------------------------------------------------------------

if (!function_exists('getBasketCount')) {

    /**
     * Returns the number of items in the baset
     * @param  boolean $respectQuantity Whether to respect the item quantities
     * @return int
     */
    function getBasketCount($respectQuantity = true)
    {
        get_instance()->load->model('shop/shop_model');
        get_instance()->load->model('shop/shop_basket_model');

        return get_instance()->shop_basket_model->getCount($respectQuantity);
    }
}

// --------------------------------------------------------------------------

if (!function_exists('getBasketTotal')) {

    /**
     * Returns the basket quantity
     * @param  boolean $includeSymbol    Whether to include the currency symbol
     * @param  boolean $includeThousands Whether to mark the thousands
     * @return string
     */
    function getBasketTotal($includeSymbol = false, $includeThousands = false)
    {
        get_instance()->load->model('shop/shop_model');
        get_instance()->load->model('shop/shop_basket_model');

        return get_instance()->shop_basket_model->getTotal($includeSymbol, $includeThousands);
    }
}
