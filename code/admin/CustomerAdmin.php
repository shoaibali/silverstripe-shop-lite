<?php

class CustomerAdmin extends ModelAdmin
{

    /**
     * This model only manages Customers model admin
     *
     * @var array
     */
    private static $managed_models = array(
        'Customers'
    );

    /**
     * This will be the path to edit products in CMS
     *
     * @var string
     */
    private static $url_segment = 'customers';

    /**
     * Title displayed in side bar in CMS
     *
     * @var string
     */
    private static $menu_title = 'Customers';
}
