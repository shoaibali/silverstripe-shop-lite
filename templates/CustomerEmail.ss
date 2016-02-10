
            <section class="planeBlock supportBlock" id ="print">
                        <div class="container">
                            <strong>Name</strong> : $Customer.Firstname $Customer.Lastname <br/>
                            <strong>Shipping address</strong> : <br/> $Customer.PostalAddress <br/>
                            $Customer.Suburb <br/>
                            $Customer.City $Customer.ZipCode<br/>
                            $Customer.State <br/>
                            $Customer.CountryName <br/>
                            <strong>Phone</strong> : $Customer.Phone <br/>
                         </div>
                    <div class="container textCenter delivery-form">

                      <table class="shoppingcart table table-striped table-hover">
                        <thead>
                          <tr>
                            <th colspan="1">Item name</th>
                            <th>Price</th>
                            <th colspan="1">Quantity</th>
                          </tr>
                        </thead>
                        <tbody>

                        <% loop $CartItems %>

                          <tr>

                            <td class="item-description">$ProductDescription</td>
                            <td>${$DefaultSellPrice}</td>
                            <td>$Quantity</td>
                          </tr>
                          <% end_loop %>

                            <tr>
                                <th class="shipping-cost">Shipping cost</th>
                                <th class="shipping-total">${$ShippingCostTotal}</th>
                                <th>&nbsp;</th>
                            </tr>

                            <tr>
                                <th class="total">Total</th>
                                <th class="cart-total">${$CartTotal}</th>
                                <th>&nbsp;</th>
                            </tr>

                        </tbody>
                      </table>

                    </div>
                </section>
