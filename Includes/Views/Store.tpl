<div class="Page" id="Store">
    <div id="viewport">
        {* Load the Current shirt here via ajax... *}
    </div>
    <h2>Store:</h2>

    <div class="ItemsContainer">
        {foreach $items as $item}
            <div class="Item {if $item@iteration == 1}Selected{/if}">
                <img class="Preview" src="{$item->getFormattedDesignImage(150, 150, 'jpg')}" alt="{$item->getName()} - {$item->getDescription()}"/>

                <h3 class="Name">{$item->getName()}</h3>

                <p class="Price">${number_format($settings->getItemCurrentPrice($item), 2)}</p>
            </div>
        {/foreach}
    </div>
</div>