<?php

class OrderItem extends DataObject
{

    /**
     * @var array
     */
    private static $db = array(
        'ProductDescription' => 'Varchar(255)',
        'ProductCode' => 'HTMLText',
        'Guid' => 'Varchar(255)',
        'ProductDetail' => 'Varchar(255)',
        'Quantity' => 'Int',
        'DefaultSellPrice' => 'Currency',
    );

    /**
     * [$summary_fields description]
     * @var array
     */
    private static $summary_fields = array(
        'ProductCode',
        'Quantity',
        'DefaultSellPrice'
    );

    private static $has_one = array(
        'Order' => 'CustomerOrder'
    );

    private static $belongs_to = array(
        'CustomerOrder' => 'CustomerOrder'
    );

    public function canView($member = null) {
        return Permission::check('ADMIN', 'any', $member);
    }

    public function canEdit($member = null) {
        return Permission::check('ADMIN', 'any', $member);
    }

    public function canDelete($member = null) {
        return false;
    }

    public function canCreate($member = null) {
        return false;
    }

}
