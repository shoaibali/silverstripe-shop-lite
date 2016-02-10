<?php

/**
 * This class represents a Customer
 * Please use a DataExtension to extend this class
 *
 */

class Customers extends DataObject
{

    /**
     *
     * @var array
     */
    private static $db = array(
        'Firstname' => 'Varchar(255)',
        'Lastname' => 'Varchar(255)',
        'Phone' => 'Varchar(255)',
        'Email' => 'Varchar(255)',
        'PostalAddress' => 'Varchar(255)',
        'Suburb' => 'Varchar(255)',
        'City' => 'Varchar(255)',
        'State' => 'Varchar(255)',
        'ZipCode' => 'Varchar(255)',
        'Country' => 'Varchar(255)',
    );

    /**
     * Customer have many orders
     *
     * @var array
     */
    private static $has_many = array(
        'CustomerOrders' => 'CustomerOrder'
    );

    /**
     * @var array
     */
    private static $summary_fields = array(
        'Firstname',
        'Lastname',
        'Email',
    );

    public function CountryName()
    {
        return (string) Zend_Locale::getTranslation($this->Country, "Country", i18n::get_locale());
    }

    public function emailCustomer($order)
    {
        $subject = 'Thank you for order on '. date('Y-m-d'.'!') . ' from SUNZ website';
        $from = SS_SEND_EMAIL_FROM;
        $to = $this->Email;

        $email = new Email();
        $email
            ->setFrom($from)
            ->setTo($to)
            ->setSubject($subject)
            ->setTemplate('CustomerEmail')
            ->populateTemplate(new ArrayData(array(
                'Customer' => $order->Customer(),
                'CartItems' => $order->OrderItems(),
                'ShippingCostTotal' => $order->shippingcost(),
                'CartTotal' => $order->TotalAmount
            )));

        // $email->populateTemplate($order);
        $email->send();
    }
}
