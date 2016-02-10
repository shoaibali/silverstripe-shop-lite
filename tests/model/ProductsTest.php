<?php

class ProductsTest extends SapphireTest
{
    public function testURLSegment()
    {
        $product = new Products(
            array(
                'ProductDescription' => 'My Product',
                'ProductCode'        => 'MYPRODUCT',
                'Barcode'            => '1234',
                'DefaultSellPrice'   => 100.00,
                'Guid'               => 'XXXXXXXX',
                'ProductDetail' => '<p>Product detail</p>',
                'Active' => true,
            )
        );

        $product->write();

        $this->assertEquals("my-product", $product->URLSegment);
    }

}
