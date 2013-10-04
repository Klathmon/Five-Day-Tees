<div id="InnerViewport" data-encodedname="{$primary->getEncodedName()}">

    <h2 class="Name">{$primary->getArticle()->getName()}</h2>

    <p class="Description">{$primary->getProduct()->getDescription()}</p>

    <div class="FullImages">
        {foreach ['Primary' => $primary, 'Secondary' => $secondary] as $class => $item}
            <img
                    src="{$item->getProduct()->getFormattedImage(450,450,'png')}"
                    alt="{$item->getArticle()->getName()} - {$item->getProduct()->getDescription()}"
                    class="{$class}"
                    height=450
                    width=450
                    />
        {/foreach}
    </div>

    <p class="Price"><span class="Number">{currency amount=$primary->getCurrentPrice()}</span> + Shipping</p>

    <div class="Genders">
        {foreach [$primary, $secondary] as $item}
            <input
                    type="radio"
                    name="gender"
                    value="{$item->getProduct()->getType()}"
                    id="{$item->getProduct()->getType()}"
                    data-id="{$item->getID()}"
                    {if $primary == $item}checked{/if}
                    />
            <label for="{$item->getProduct()->getType()}">{$item->getProduct()->getType()|capitalize}</label>
        {/foreach}
    </div>

    <div class="Sizes">
        {html_radios 
            name='size'
            values=$primary->getProduct()->getSizes()
            output=$primary->getProduct()->getSizes()
            labels=true
            label_ids=true
        }
    </div>

    <button id="AddToCart">Add To Cart</button>

    <div class="Social">
        <div class="GPlus" data-href="{$primary->getPermalink()}" data-callback="plusone" data-annotation="inline"
             data-width="300"></div>
        <div class="Facebook" data-href="{$primary->getPermalink()}" data-send="false" data-width="300" data-show-faces="true"></div>
    </div>
</div>