<div id="Cart">
    <div id="CartHeader">
        <h4>Your Cart</h4>

        <div id="CloseCart">X</div>
    </div>
    <table id="ItemsTable">
        
        <thead>
            <tr>
                <th class="Name">Name</th>
                <th class="Gender">Gender</th>
                <th class="Size">Size</th>
                <th class="Price">Price</th>
                <th class="Quantity">Quantity</th>
                <th class="Buttons">&nbsp;</th>
            </tr>
        </thead>
        
        <tbody>
            {foreach $cartItems as $cartItem}
                <tr data-id="{$cartItem->getID()}">
                    <td class="Name">{$cartItem->getArticle()->getName()}</td>
                    <td class="Gender">{$cartItem->getProduct()->getType()}</td>
                    <td class="Size">{$cartItem->getSize()}</td>
                    <td class="Price">{currency amount=$cartItem->getCurrentPrice()}</td>
                    <td class="Quantity"><input class="Quantity" type="number" min="0" step="1"
                                                value="{$cartItem->getQuantity()}"></td>
                    <td class="Buttons">
                        <button class="Update" title="Click to update the quantity of this item">Update</button>
                        <button class="Remove" title="Click to remove this item from your cart">Remove</button>
                    </td>
                </tr>
                {foreachelse}
                <tr>
                    <td colspan="100">You don't have anything in your cart</td>
                </tr>
            {/foreach}
        </tbody>
        
        {if !is_null($coupon)}
            <tfoot>
                <tr id="CouponLine">
                    <td colspan=3>Coupon Code: {$coupon->getCode()}</td>
                    <td colspan=2>
                        {currency amount=$coupon->getAmount()}
                    </td>
                    <td>
                        <button id="RemoveCoupon">Remove Coupon</button>
                    </td>
                </tr>
            </tfoot>
        {/if}
    </table>
    
    {if !empty($callOutBoxText) && $callOutBoxText != ''}
        <div id="CallOutBox">
            <p>{$callOutBoxText}</p>
        </div>
    {/if}
    
    <div id="Shipping">
        <h5>Pick your Shipping Method:</h5>
        {foreach $shippingMethods as $shippingMethod}
            <label class="ShippingMethod">
                <input
                        type="radio"
                        name="Shipping"
                        data-id="{$shippingMethod->getID()}"
                        {if $chosenShippingMethod == $shippingMethod}checked{/if}/>
                {$shippingMethod->getName()} - ({currency amount=$shippingMethod->calculateShippingPrice($preShippingTotal)})
            </label>
        {/foreach}
    </div>
    
    {if is_null($coupon)}
        <div id="CouponsContainer">
            <input type="text" id="CouponCode" placeholder="Coupon Code"/>
            <button id="SubmitCoupon" title="Click to add a coupon">Enter Coupon</button>
        </div>
    {/if}
    
    <p id="Total">Total - <span>{currency amount=$total}</span></p>
    <button id="EmptyCart" title="Click to empty your cart of all items">Empty Cart</button>
    {if is_null($chosenShippingMethod)}
        <a id="PayPal" title="Please choose a shipping method" class="Disabled">
            <img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif"/>
        </a>
    {else}
        <!--suppress HtmlUnknownTarget -->
        <a id="PayPal" href="/Checkout" title="Checkout with PayPal">
            <img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif"/>
        </a>
    {/if}
</div>