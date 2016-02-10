<?php

class CustomerOrder extends DataObject
{

    /**
     * @var array
     */
    private static $db = array(
        'PaymentType' => 'Enum("Pre Order,Credit Card,Direct Deposit,Cash")',
        'Status' => 'Enum("Pending,Paid,Cancel,Declined,Expired,Failed")',
        'TotalAmount' => 'Currency'
    );

    /**
     * Every customer has an order
     *
     * @var array
     */
    private static $has_one = array(
        'Customer' => 'Customers',
        'CreditCardInfo' => 'CreditCardInfo'
    );

    /**
     * This order can belong to one customer
     * @var array
     */
    private static $belongs_to = array(
        'Customer' => 'Customers'
    );

    /**
     * This order can contain many items (products)
     *
     * @see  Products
     * @var array
     */
    private static $has_many = array(
        'OrderItems' => 'OrderItem'
    );


    /**
     * Fields / columns to be displayed in gridfield CMS
     *
     * @var array
     */
    private static $summary_fields = array(
        'Customer.Firstname',
        'PaymentType',
        'Status',
        'TotalAmount'
    );

    /**
     *  Administrators are only allowed to view Customer's order
     *
     * @param Member $member
     * @return bool
     */
    public function canView($member = null)
    {
        return Permission::check('ADMIN', 'any', $member);
    }

    /**
     *  Administrators are only allowed to edit Customer's order
     *
     * @param Member $member
     * @return bool
     */
    public function canEdit($member = null)
    {
        return Permission::check('ADMIN', 'any', $member);
    }

    /**
     *  No one is allowed to delete an order
     *
     * @param Member $member
     * @return bool
     */
    public function canDelete($member = null)
    {
        return false;
    }

    /**
     *  No one is allowed to create an order
     *
     * @param Member $member
     * @return bool
     */
    public function canCreate($member = null)
    {
        return false;
    }

    /**
     * Shipping cost for this order
     * @todo  This needs to be configureable
     * @return String Formats a number as a currency string
     */
    public function getShippingCost()
    {

        $total = $this->TotalAmount;
        $cost = 0.00;
        return money_format('%i', $cost);
    }
}
