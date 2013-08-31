<div id="Cart">
    <h4>Your Cart</h4>
    <table id="ItemsTable">
        <tr>
            <th>Name</th>
            <th>Gender</th>
            <th>Size</th>
            <th>Price</th>
            <th>Quantity</th>
            <th></th>
        </tr>
        {foreach $cartItems as $cartItem}
            <tr data-id="{$cartItem->item->getID()}" data-size="{$cartItem->getSize()}">
                <td>{$cartItem->item->getName()}</td>
                <td>{$cartItem->item->getGender()}</td>
                <td>{$cartItem->getSize()}</td>
                <td>${number_format($cartItem->getCurrentPrice(), 2)}</td>
                <td><input class="Quantity" type="number" min="0" step="1" value="{$cartItem->getQuantity()}"></td>
                <td>
                    <button class="Update" title="Click to update the quantity of this item">Update</button>
                    <button class="Remove" title="Click to remove this item from your cart">Remove</button>
                </td>
            </tr>
            {foreachelse}
            <tr>
                <td colspan="100">You don't have anything in your cart</td>
            </tr>
        {/foreach}
    </table>
    {if !empty($callOutBoxText) && $callOutBoxText != ''}
        <div id="CallOutBox">
            <p>{$callOutBoxText}</p>
        </div>
    {/if}
    <div id="CouponsContainer" {if $disableCoupon == true}class="Disabled"{/if}>
        <input type="text" id="CuponCode" placeholder="Coupon Code" {if $disableCoupon == true}disabled{/if}/>
        <button id="SubmitCoupon" title="Click to add a coupon" {if $disableCoupon == true}disabled{/if}>Enter Coupon</button>
    </div>
    <p id="SubTotal">Subtotal - {$subTotal|default:'$0.00'}</p>
    <button id="EmptyCart" title="Click to empty your cart of all items">Empty Cart</button>
    <button id="BuyWithPayPal">Buy With PayPal!?</button>
</div>