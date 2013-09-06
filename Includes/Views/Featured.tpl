<div class="Page" id="Featured">

    <img src="/Static/Images/Layout/Topper.png" id="TopperTop"/>

    <div id="Viewport">
        {* Load the Current shirt here via ajax... *}
    </div>
    <img src="/Static/Images/Layout/Topper.png" id="TopperBottom"/>

    <h2>Featured Shirts:</h2>

    <div class="ItemsContainer">
        {foreach $items as $item}
            <div class="Item {if $item@iteration == 3}Selected{/if}" data-linkname="{$item->getURL()}">
                <img
                        class="Preview"
                        src="{$item->getFormattedDesignImage(150, 150, 'jpg')}"
                        alt="{$item->getName()} - {$item->getDescription()}"
                        height=150
                        width=150
                        />

                <h3 class="Name">{$item->getName()}</h3>

                <p class="Price">${number_format($settings->getItemCurrentPrice($item), 2)}</p>
            </div>
        {/foreach}
    </div>
</div>