<?php

class Products extends DataObject
{

    /**
     * @var array
     */
    private static $db = array(
        'ProductDescription' => 'Text',
        'ProductCode'        => 'Varchar(255)',
        'Barcode'            => 'Int',
        'DefaultSellPrice'   => 'Currency',
        'Guid'               => 'Varchar(255)',
        'ProductDetail' => 'HTMLText',
        'Active' => 'Boolean',
        'URLSegment' => 'Varchar(255)', // SEO Friendly shop?
    );

    /**
     * @var array
     */
    private static $has_one = array(
        'Category' => 'ProductCategory',
        'Image'         => 'Image',
        'ShopPage' => 'ShopPage',
    );

    /**
     * @var array
     */
    private static $summary_fields = array(
        'ProductDescription',
        'ProductCode',
        'Barcode',
        'DefaultSellPrice',
        'Guid',
        'ProductDetail',
        'Active'
    );

    /**
     * This assumes prices are excluding GST
     * This methods adds GST on top of prices
     *
     * @todo  Make this a configuration option
     * @return string formatted string
     */
    public function DefaultSellPriceIncGST()
    {
        $priceExcGST = $this->DefaultSellPrice;
        $priceIncGST = 0.00;

        // only add gst on prices > 0.00
        if ($priceExcGST != 0.00) {
            $priceIncGST = $priceExcGST * 1.15;
        }

        return number_format($priceIncGST,2);
    }


    /**
     * @return FieldList
     */
    public function getCMSFields()
    {

        $fields = parent::getcmsfields();

        $productDescription = new TextareaField('ProductDescription');
        $productDetail = new HTMLEditorField('ProductDetail');
        $productCode = new TextField('ProductCode');
        $barcode = new NumericField('Barcode');
        $price = new NumericField('DefaultSellPrice');
        $guid = new TextField('Guid');

        $fields->addFieldToTab('Root.Main', $productDescription);
        $fields->addFieldToTab('Root.Main', $productDetail);
        $fields->addFieldToTab('Root.Main', $productCode);
        $fields->addFieldToTab('Root.Main', $barcode);
        $fields->addFieldToTab('Root.Main', $price);
        $fields->addFieldToTab('Root.Main', $guid);

        return $fields;
    }

    /**
     * Based on the title of product it generates a URLSegment for it
     * so that SEO friendly Product pages can be generated.
     *
     * Credit: https://gist.github.com/Zauberfisch/9460395
     *
     * @return string URLSegment /product-title-x-y-z/
     */
    protected function onBeforeWrite()
    {
        parent::onBeforeWrite();
        if (!$this->ProductDescription) {
            $this->ProductDescription = $this->ProductDescription;
        }
        $filter = URLSegmentFilter::create();
        if (!$this->URLSegment) {
            $this->URLSegment = $this->ProductDescription;
        }
        $this->URLSegment = $filter->filter($this->URLSegment);
        if (!$this->URLSegment) {
            $this->URLSegment = uniqid();
        }
        $count = 2;
        while (static::get_by_url_segment($this->URLSegment, $this->ID)) {
            // add a -n to the URLSegment if it already existed
            $this->URLSegment = preg_replace('/-[0-9]+$/', null, $this->URLSegment) . '-' . $count;
            $count++;
        }
    }

    /**
     * @return string
     */
    public function Link()
    {
        $link = '';

        if ($this->isInDB()) {
            $controller = Controller::curr();

            $shop = ShopPage::get()->First();

            if ($shop && $shop->exists()) {
                $link = Controller::join_links($shop->Parent()->Link(), 'product', $this->URLSegment);
            }
        }
        return $link;
    }

    /**
     * @param $str
     * @return Product|Boolean
     */
    public static function get_by_url_segment($str, $excludeID = null)
    {
        if (!isset(static::$_cached_get_by_url[$str])) {
            $list = static::get()->filter('URLSegment', $str);
            if ($excludeID) {
                $list = $list->exclude('ID', $excludeID);
            }
            $obj = $list->First();
            static::$_cached_get_by_url[$str] = ($obj && $obj->exists()) ? $obj : false;
        }
        return static::$_cached_get_by_url[$str];
    }

    /**
     *  Product Title, this method was required in order to make
     *  "Link existing" return results with titles of products
     *  rather then matching ID numbers like #1 #2 etc
     *
     * @return string
     */
    public function getTitle(){
        return $this->ProductDescription;
    }
}
