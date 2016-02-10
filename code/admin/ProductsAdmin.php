<?php

class ProductsAdmin extends ModelAdmin
{

    /**
     * This model only manages Products model admin
     *
     * @var array
     */
    private static $managed_models = array(
        'Products',
        'ProductCategory'
    );

    /**
     * This will be the path to edit products in CMS
     *
     * @var string
     */
    private static $url_segment = 'products';

    /**
     * Title displayed in side bar in CMS
     *
     * @var string
     */
    private static $menu_title = 'Products';
}
