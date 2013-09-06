<div class="Page" id="Admin">
    <h2>Admin Section</h2>

    <h3>Global Settings:</h3>
    <table id="Global">
        <tr>
            <th>Starting Display Date</th>
            <th>Default Retail Amount</th>
            <th>Default Sales Limit</th>
            <th>Number of days between shirts</th>
            <th>Featured Added Price</th>
            <th>Non-Featured Added Price</th>
            <th>Store Added Price</th>
            <th>Save</th>
        </tr>
        <tr>
            <td><input type="date" id="StartingDisplayDate" value="{$startingDisplayDate|date_format:"%Y-%m-%d"}"/></td>
            <td><input type="number" id="Retail" value="{$retail}" step="1" min="10" max="99"/></td>
            <td><input type="number" id="SalesLimit" value="{$salesLimit}" step="1" min="1" max="1000"/></td>
            <td><input type="number" id="DaysApart" value="{$daysApart}" step="1" min="1" max="30"/></td>
            <td><input type="number" id="Level1" value="{$level1}" step="1" min="0" max="10"/></td>
            <td><input type="number" id="Level2" value="{$level2}" step="1" min="0" max="10"/></td>
            <td><input type="number" id="Level3" value="{$level3}" step="1" min="0" max="10"/></td>
            <td>
                <button id="SaveGlobalSettings">Save</button>
            </td>
        </tr>
        <tr>
            <td colspan=7><textarea id="CartCallout">{$cartCallout}</textarea></td>
        </tr>
    </table>

    {if $config->getMode() == 'DEV'}
        <button onclick="$.post('/Admin', { 'Command': 'ReloadAllItems' }, function(data) { location.reload(); } )">Clear Out Shirts</button>
    {/if}

    <h3>Coupons:</h3>
    <table id="Coupons">
        <tr>
            <th class="Code">Code</th>
            <th class="IsPercent">Is Percent?</th>
            <th class="Amount">Amount Off</th>
            <th class="UsesRemaining">Uses Remaining</th>
            <th class="Buttons"></th>
        </tr>
        {foreach $coupons as $coupon}
            <tr data-code="{$coupon->getCode()}">
                <th class="Code">{$coupon->getCode()}</th>
                <th class="IsPercent"><input type="checkbox" {if $coupon->isPercent()}checked{/if}/></th>
                <th class="Amount"><input type="number" step=1 value="{$coupon->getAmount()}"/></th>
                <th class="UsesRemaining"><input type="number" step=1 value="{$coupon->getUsesRemaining()}"/></th>
                <th class="Buttons">
                    <button class="Save">Save</button>
                    <button class="Delete">Delete</button>
                </th>
            </tr>
        {/foreach}
        <tr>
            <th class="Code"><input type="text"/></th>
            <th class="IsPercent"><input type="checkbox" checked/></th>
            <th class="Amount"><input type="number" step=1/></th>
            <th class="UsesRemaining"><input type="number" step=1/></th>
            <th class="Buttons">
                <button class="Add">Add New Coupon</button>
            </th>
        </tr>
    </table>

    <h3>All Items:</h3>
    <table class="ItemsTable" id="Items">
        <tr>
            <th>Name</th>
            <th>Gender</th>
            <th>Description</th>
            <th>Cost</th>
            <th>Base Retail</th>
            <th>Display Date</th>
            <th>Votes</th>
            <th>Number sold</th>
            <th>Sales Limit</th>
            <th>Sizes Available</th>
            <th></th>
        </tr>
        {foreach $items as $item}
            <tr data-id="{$item->getID()}">
                <td><input type="text" id="Name" value="{$item->getName()}"/></td>
                <td><input type="text" value="{$item->getGender()}" disabled/></td>
                <td><textarea id="Description">{$item->getDescription()}</textarea></td>
                <td><input type="number" value="{$item->getCost()}" disabled/></td>
                <td><input type="number" id="Retail" value="{$item->getRetail()}"/></td>
                <td><input type="date" id="DisplayDate" value="{$item->getDisplayDate()|date_format:"%Y-%m-%d"}"/></td>
                <td><input type="number" id="Votes" value="{$item->getVotes()}"/></td>
                <td><input type="number" id="Sold" value="{$item->getNumberSold()}"/></td>
                <td><input type="number" id="SalesLimit" value="{$item->getSalesLimit()}"/></td>
                <td><input type="text" value="{foreach $item->getSizesAvailable() as $size}{$size},{/foreach}" disabled/></td>
                <td>
                    <button class="SaveItem">Save</button>
                    <button class="DeleteItem">Delete</button>
                </td>
            </tr>
        {/foreach}
    </table>
</div>