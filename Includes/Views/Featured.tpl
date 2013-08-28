<div class="Page" id="Featured">
    <div id="viewport">
        {* Load the Current shirt here via ajax... *}
    </div>
    <h2>Featured Shirts:</h2>

    <div class="ItemsContainer">
        {foreach $items as $item}
            <div class="Item">
                <img class="Preview" src="{$item->getFormattedDesignImage(150, 150, 'jpg')}" alt="{$item->getName()} - {$item->getDescription()}"/>

                <h3 class="Name">{$item->getName()}</h3>

                <p class="Price">{$item->getNicePrice()}</p>
            </div>
        {/foreach}
    </div>
</div>