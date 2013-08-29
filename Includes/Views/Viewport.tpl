<h2 class="Name">{$item->getName()}</h2>
<p class="Description">{$item->getDescription()}</p>

<div class="FullImages">
    <img src="{$item->getFormattedProductImage(400,450,'png')}" alt="{$item->getName()} - {$item->getDescription()}"/>
</div>

<p class="Price">${number_format($settings->getItemCurrentPrice($item), 2)}</p>

<div class="Genders">
    <input type="radio" name="gender" value="male" {if $item->getGender() == 'male'}checked{/if}>
    <input type="radio" name="gender" value="female" {if $item->getGender() == 'female'}checked{/if}>
</div>

<div class="Sizes">
    {foreach $item->getSizesAvailable() as $size}
        <label><input type="radio" name="size" value="{$size}"/>{$size}</label>
    {/foreach}
</div>

<button id="AddToCart">Add To Cart</button>

<div class="Social">
    <div class="GPlus" data-href="{$item->getPermalink()}" data-callback="plusone" data-annotation="inline" data-width="300"></div>
    <div class="Facebook" data-href="{$item->getPermalink()}" data-send="false" data-width="300" data-show-faces="true"></div>
</div>

