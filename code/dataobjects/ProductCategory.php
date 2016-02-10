<?php

class ProductCategory extends DataObject
{

    /**
     * @var array
     */
    private static $db = array(
        'Name' => 'Varchar(255)',
        'SortOrder' => 'Int'
    );

    /**
     * This is used by gridfield and respected as
     * default sort order when being rendered
     *
     * @var string
     */
    private static $default_sort = "SortOrder";

    /**
     * This category also has an Image
     *
     * @var array
     */
    private static $has_one = array(
        'Image' => 'Image',
    );

    /**
     * We can have many products in this category
     *
     * @var array
     */
    private static $has_many = array(
        'Products' => 'Products'
    );
}
