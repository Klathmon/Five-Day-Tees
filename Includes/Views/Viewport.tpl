<div
        id="InnerViewport"
        {if $primaryItem->getGender() == 'male'}
            data-maleid="{$primaryItem->getID()}"
        {else}
            data-femaleid="{$primaryItem->getID()}"
        {/if}
        {if $secondaryItem->getGender() == 'male'}
            data-maleid="{$secondaryItem->getID()}"
        {else}
            data-femaleid="{$secondaryItem->getID()}"
        {/if}

        data-url="{$primaryItem->getURL()}"
        >
    <h2 class="Name">{$primaryItem->getName()}</h2>

    <p class="Description">{$primaryItem->getDescription()}</p>

    <div class="FullImages">
        <img src="{$primaryItem->getFormattedProductImage(400,450,'png')}" alt="{$primaryItem->getName()} - {$primaryItem->getDescription()}"
             class="Primary"/>
        <img src="{$secondaryItem->getFormattedProductImage(400,450,'png')}" alt="{$secondaryItem->getName()} - {$secondaryItem->getDescription()}"
             class="Secondary"/>
    </div>

    <p class="Price"><span class="Number">${number_format($settings->getItemCurrentPrice($primaryItem), 2)}</span> + Shipping</p>

    <div class="Genders">
        <input
                type="radio"
                name="gender"
                value="male"
                id="male"
                {if $primaryItem->getGender() == 'male'}checked{/if}
                />
        <label for="male">Male</label>
        <input
                type="radio"
                name="gender"
                value="female"
                id="female"
                {if $primaryItem->getGender() == 'female'}checked{/if}
                />
        <label for="female">Female</label>
    </div>

    <div class="Sizes">
        {foreach $primaryItem->getSizesAvailable() as $size}
            <input type="radio" name="size" value="{$size}" id="{$size}"/>
            <label for="{$size}">{$size}</label>
        {/foreach}
    </div>

    <button id="AddToCart">Add To Cart</button>

    <div class="Social">
        <div class="GPlus" data-href="{$primaryItem->getPermalink()}" data-callback="plusone" data-annotation="inline" data-width="300"></div>
        <div class="Facebook" data-href="{$primaryItem->getPermalink()}" data-send="false" data-width="300" data-show-faces="true"></div>
    </div>
</div>