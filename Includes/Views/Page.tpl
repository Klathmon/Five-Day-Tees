<div class="Page" id="{$ID}">

    {html_image file='/Static/Images/Layout/Topper.png' id='TopperTop'}

    <div id="Viewport">
        {* Load the Current shirt here via ajax... *}
    </div>

    {html_image file='/Static/Images/Layout/Topper.png' id='TopperBottom'}

    <h2>{$subHeader}</h2>

    <div class="ItemsContainer">
        {foreach $items as $item}
            <div
                    {* TODO: Fix "Featured" item iteration back to 3 once it's filled up again *}
                    class="Item {if $ID == 'Featured' && $item@iteration == 1}Selected{elseif $ID != 'Featured' && $item@iteration == 1}Selected{/if}"
                    data-encodedname="{$item->getEncodedName()}"
                    >
                <img
                        class="Preview"
                        src="{$item->getArticle()->getFormattedImage(150, 150, 'jpg')}"
                        alt="{$item->getArticle()->getName()} - {$item->getProduct()->getDescription()}"
                        height=150
                        width=150
                        />

                <h3 class="Name">{$item->getArticle()->getName()}</h3>

                <p class="Price">{currency amount=$item->getCurrentPrice()}</p>
            </div>
        {/foreach}
    </div>
</div>