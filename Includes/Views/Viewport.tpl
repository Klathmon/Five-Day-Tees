<div
        id="InnerViewport"
        data-malearticleid="{$maleArticleID}"
        data-femalearticleid="{$femaleArticleID}"
        data-url="{$URLName}"
        >
    
    <h2 class="Name">{$name}</h2>

    <p class="Description">{$description}</p>

    <div class="FullImages">
        <img src="{$articleImageURL}" alt="{$name} - {$description}" class="Primary" height=450 width=400/>
        <img src="{$secondaryArticleImageURL}" alt="{$name} - {$secondaryDescription}" class="Secondary" height=450 width=400/>
    </div>

    <p class="Price"><span class="Number">${$price}</span> + Shipping</p>

    <div class="Genders">
        <input
                type="radio"
                name="gender"
                value="male"
                id="male"
                {if $type == 'male'}checked{/if}
                />
        <label for="male">Male</label>
        <input
                type="radio"
                name="gender"
                value="female"
                id="female"
                {if $type == 'female'}checked{/if}
                />
        <label for="female">Female</label>
    </div>

    <div class="Sizes">
        {foreach $sizesAvailable as $size}
            <input type="radio" name="size" value="{$size}" id="{$size}"/>
            <label for="{$size}">{$size}</label>
        {/foreach}
    </div>

    <button id="AddToCart">Add To Cart</button>

    <div class="Social">
        <div class="GPlus" data-href="{$permalink}" data-callback="plusone" data-annotation="inline" data-width="300"></div>
        <div class="Facebook" data-href="{$permalink}" data-send="false" data-width="300" data-show-faces="true"></div>
    </div>
</div>