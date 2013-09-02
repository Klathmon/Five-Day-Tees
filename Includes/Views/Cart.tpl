<div id="Cart">
    <div id="CartHeader">
        <h4>Your Cart</h4>

        <div id="CloseCart">X</div>
    </div>
    <table id="ItemsTable">
        <tr>
            <th class="Name">Name</th>
            <th class="Gender">Gender</th>
            <th class="Size">Size</th>
            <th class="Price">Price</th>
            <th class="Quantity">Quantity</th>
            <th class="Buttons"></th>
        </tr>
        {foreach $cartItems as $cartItem}
            <tr data-id="{$cartItem->item->getID()}" data-size="{$cartItem->getSize()}">
                <td class="Name">{$cartItem->item->getName()}</td>
                <td class="Gender">{$cartItem->item->getGender()}</td>
                <td class="Size">{$cartItem->getSize()}</td>
                <td class="Price">${number_format($cartItem->getCurrentPrice(), 2)}</td>
                <td class="Quantity"><input class="Quantity" type="number" min="0" step="1" value="{$cartItem->getQuantity()}"></td>
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
        {if $disableCoupon == true}
            <tr id="CouponLine">
                <td colspan=3>Coupon Code: {$couponCode}</td>
                <td colspan=2>
                    {if $isPercent == true}
                        {if $Amount > 0}+{/if}{number_format($Amount, 0)}%
                    {else}
                        {if $Amount > 0}+{/if}${number_format($Amount, 2)}
                    {/if}
                </td>
                <td>
                    <button id="RemoveCoupon">Remove Coupon</button>
                </td>
            </tr>
        {/if}
    </table>
    {if !empty($callOutBoxText) && $callOutBoxText != ''}
        <div id="CallOutBox">
            <p>{$callOutBoxText}</p>
        </div>
    {/if}
    {if $disableCoupon == false}
        <div id="CouponsContainer">
            <input type="text" id="CouponCode" placeholder="Coupon Code"/>
            <button id="SubmitCoupon" title="Click to add a coupon">Enter Coupon</button>
        </div>
    {/if}
    <p id="SubTotal">Subtotal - <span>{$subTotal|default:'$0.00'}</span></p>
    <button id="EmptyCart" title="Click to empty your cart of all items">Empty Cart</button>
    <a id="PayPal" href="/Checkout" title="Checkout with PayPal"><img src="https://www.paypal.com/en_US/i/btn/btn_xpressCheckout.gif"/></a>
</div>