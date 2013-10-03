<div class="Page" id="Store">

    <img src="/Static/Images/Layout/Topper.png" id="TopperTop"/>

    <div id="Viewport">
        {* Load the Current shirt here via ajax... *}
    </div>
    <img src="/Static/Images/Layout/Topper.png" id="TopperBottom"/>

    <h2>Store:</h2>

    <div class="ItemsContainer">
        {foreach $items as $item}
            <div class="Item {if $item@iteration == 1}Selected{/if}" data-id="{$item['ID']}">
                <img
                        class="Preview"
                        src="{$item['designImageURL']}"
                        alt="{$item['name']} - {$item['description']}"
                        height=150
                        width=150
                        />

                <h3 class="Name">{$item['name']}</h3>

                <p class="Price">${$item['price']}</p>
            </div>
        {/foreach}
    </div>
</div>