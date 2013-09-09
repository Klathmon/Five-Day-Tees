<div class="Page" id="Featured">

    <img src="/Static/Images/Layout/Topper.png" id="TopperTop"/>

    <div id="Viewport">
        {* Load the Current shirt here via ajax... *}
    </div>
    <img src="/Static/Images/Layout/Topper.png" id="TopperBottom"/>

    <h2>Featured Shirts:</h2>

    <div class="ItemsContainer">
        {foreach $items as $item}
            <div class="Item {if $item@iteration == 3}Selected{/if}" data-linkname="{$item['URLName']}">
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