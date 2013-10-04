{include 'Global/HTMLHeader.tpl'}

<!--suppress ALL -->
<header>
    <a id="logo" href="/">{html_image file="/Static/Images/Header/Logo.png" alt="logo"}</a>

    <nav id="SubNav">
        <ul>
            <li id="CartButton">{html_image file="/Static/Images/Header/Cart.png" alt="cart"}Cart</li>
            <li><a href="/FAQ">FAQ</a></li>
            <li><a href="/Contact">Contact</a></li>
        </ul>
    </nav>

    <nav id="MainNav">
        <ul>
            {foreach ['Featured', 'Store', 'Vault'] as $link}
                {$path='/Static/Images/Header/' + $link + '.png'}
                <li {if $title == $link}class="Active"{/if}>
                    <a href="/{$link}" title="{$link}">{html_image file="/Static/Images/Header/{$link}.png" alt=$link}</a>
                </li>
            {/foreach}
        </ul>
        <h1 id="TagLine">A new limited run design every 5 days</h1>
    </nav>
</header>
<article id="Main">
    {include $mainTemplate}
</article>

{include 'Global/HTMLFooter.tpl'}