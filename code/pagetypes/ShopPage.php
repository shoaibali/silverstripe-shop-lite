<?php

class ShopPage extends Page
{

    public static $defaults = array (
        'ShowInMenus' => false,
        'ShowInSearch' => false
    );
}

class ShopPage_Controller extends Page_Controller
{

    /**
     * @var array
     */
    private static $allowed_actions = array (
        'add',
        'remove',
        'delete',
        'total',
        'clear',
        'product',
        'shippingcosttotal',
        'jsonTotal',
    );

    /**
     * Renders product page given URLSegment
     *
     * @return SS_HTTPResponse
     */
    public function product()
    {
        $URLSegment = Convert::raw2sql($this->request->allParams()); // just paranoid hence the convert
        $product = Products::get()
            ->filter(array('URLSegment' => $URLSegment['ID']));
        $total = $this->total();
        if ($product) {
            return $this->customise(array(
                        'Product' => $product->First(),
                        'CartTotal' => $total["total"],
                        'ProductShippingCost' => $total["shippingtotal"]
                    ))->renderWith(array("ProductPage", "Page"));
        }

        // TODO Send the user through site search instead?
        return $this->redirectBack();
    }

    /**
     * Index lists all the products
     *
     * @param  SS_HTTPRequest $request
     * @return PaginatedList
     */
    public function index(SS_HTTPRequest $request)
    {

        $transaction = Session::get("transaction");

        // clear old session data
        if (!empty($transaction)) {
            Session::clear("transaction");
            Session::clear("order");
        }

        $filter = array('Active' => true);

        $searchQuery = $request->getVar('search');

        if ($searchQuery) {
            $filter['ProductDescription:PartialMatch'] = $searchQuery;
        }

        $produts = Products::get()->filter($filter);

        $paginatedProducts = PaginatedList::create(
            $produts,
            $request
        );
        $paginatedProducts->setPageLength(10);

        $total = $this->total();
        return array(
            'Products' => $paginatedProducts,
            'searchQuery' => $searchQuery,
            'CartItems' => $this->CartItems(),
            'CartItemsCount' => $this->CartItemsCount(),
            'CartTotal' => $total["total"],
            'ShippingTotal' => $total["shippingtotal"]
        );
    }

    /**
     * Finds all the items in shoppingcart session
     *
     * @return  ArrayList
     */
    public function CartItems()
    {
        $cartItems = Session::get("shoppingcart");
        $items = array();
        $products = new ArrayList();

        if ($cartItems) {
            $items = array_unique(explode(",", $cartItems));

            if (!empty($items)) {
                foreach($items as $item) {
                    $products->add(Products::get_by_id('Products', (int)$item));
                }
            }
        }

        return $products;
    }

    /**
     *  Number of items in the cart
     *
     * @return Int
     */
    public function CartItemsCount()
    {
        return (!empty(Session::get("shoppingcart")))? count(explode(",", Session::get("shoppingcart"))) : 0;
    }

    /**
     * Adds the item to cart
     *
     * @return SS_HTTPResponse
     */
    public function add()
    {

        $productID = (int) $this->request->param("ID");

        // is it a valid product it?
        $product = Products::get_by_id("Products", $productID);

        if ($product) {
            if (empty(Session::get("shoppingcart"))) {
                $currentItems = explode(",", $productID);
            } else {
                $currentItems = explode(",", Session::get("shoppingcart") . "," . $productID);
            }

            Session::set("shoppingcart", implode(",", $currentItems));
        }

        return $this->redirectBack();
    }

    /**
     * Complete deletes the item from cart regardless of quantity
     *
     * @return SS_HTTPResponse
     */
    public function delete()
    {
        $productID = $this->request->param("ID");
        $cartItems = explode(",", Session::get("shoppingcart"));

        foreach($cartItems as $k => $v) {
            if ($productID == $v) {
                unset($cartItems[$k]);
            }
        }


        if (!empty($cartItems)) {
            Session::set("shoppingcart", implode(",", $cartItems));
        } else {
            // empty the shopping cart
            Session::clear("shoppingcart");
        }

        return $this->redirectBack();
    }

    /**
     * This only removes one item at a time
     *
     * @return SS_HTTPResponse
     */
    public function remove()
    {
        $productID = $this->request->param("ID");
        $cartItems = explode(",", Session::get("shoppingcart"));

        if (($key = array_search($productID, $cartItems)) !== false) {
            unset($cartItems[$key]);
        }

        // if the cart is not empty clear it
        if (!empty($cartItems)) {
            Session::set("shoppingcart", implode(",", $cartItems));
        } else {
            // empty the shopping cart
            Session::clear("shoppingcart");
        }

        return $this->redirectBack();
    }

    /**
     * For a given ProductID it tells us how much of that product
     * we have in the cart
     *
     * @param  Int $productID
     * @return Int Total products of a given productID in cart
     */
    public function quantity($productID)
    {
        $quantity = 0;
        if ($productID) {
            $items = explode(",", Session::get("shoppingcart"));

            foreach($items as $item) {
                if ($item == $productID) {
                    $quantity++;
                }
            }
        }

        return $quantity;
    }

    /**
     * Returns total of cart and shipping total
     *
     * @return  Array
     */
    public function total()
    {
        $items = explode(",", Session::get("shoppingcart"));
        $total = 0.00;
        $shippingtotal = 0.00;

        if (!empty($items)) {
            foreach($items as $item) {
                $product = Products::get_by_id('Products', (int) $item);
                if ($product) {
                    $total += $product->DefaultSellPriceIncGST();
                }
            }
        }
        // total is inclusive of shipping costs
        if ($total != 0.00) {
            $shippingtotal = $this->shippingcost($total);
            $total += $shippingtotal;
        }

        return array(
            "total" => money_format('%i', $total),
            "shippingtotal" => money_format('%i', $shippingtotal)
            );
    }

    /**
     * Used for jquery requests to get total
     *
     * @return Array
     */
    public function jsonTotal()
    {
        return json_encode($this->total());
    }

    /**
     * Calcualtes the shipping cost based ontotal
     *
     * @param  Float $total
     * @return Strig formatted total
     * @todo  Make shipping cost configurable
     */
    public function shippingcost($total)
    {
        $cost = 0.00;
        return money_format('%i', $cost);
    }

    /**
     * Total with shipping cost only
     * @return String
     */
    public function shippingcosttotal()
    {
        $shippingcosttotal = $this->total();
        return $this->shippingcost($shippingcosttotal["shippingtotal"]);
    }


    /**
     * Deletes everything from shopping cart
     *
     * @return SS_HTTPResponse
     */
    public function clear()
    {
        Session::clear("shoppingcart");
        return $this->redirect($this->Link());
    }

}
