<div
        id="InnerViewport"
        >
    
    <h2 class="Name">{$name}</h2>

    <p class="Description">{$description}</p>

    <div class="FullImages">
        <img src="{$articleImageURL}" alt="{$name} - {$description}" class="Primary" height=450 width=400/>
        <img src="{$secondaryArticleImageURL}" alt="{$name} - {$secondaryDescription}" class="Secondary" height=450 width=400/>
    </div>

    <p class="Price"><span class="Number">${$price}</span> + Shipping</p>

    <div class="Genders">
        {foreach $types as $typeName => $typeID}
            <input 
                type="radio"
                name="gender"
                value="{$typeName}"
                id="{$typeName}"
                data-id="{$typeID}"
                {if $typeName == $type}checked{/if}
            />
            <label for="{$typeName}">{$typeName|capitalize}</label>
        {/foreach}
    </div>

    <div class="Sizes">
        {foreach $sizes as $sizeName => $sizeID}
            <input type="radio" name="size" value="{$sizeName}" id="{$sizeName}" data-id="{$sizeID}"/>
            <label for="{$sizeName}">{$sizeName|upper}</label>
        {/foreach}
    </div>

    <button id="AddToCart">Add To Cart</button>

    <div class="Social">
        <div class="GPlus" data-href="{$permalink}" data-callback="plusone" data-annotation="inline" data-width="300"></div>
        <div class="Facebook" data-href="{$permalink}" data-send="false" data-width="300" data-show-faces="true"></div>
    </div>
</div>