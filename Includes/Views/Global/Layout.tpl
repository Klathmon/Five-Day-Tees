{include 'Global/HTMLHeader.tpl'}

<!--suppress ALL -->
<header>
    <a id="logo" href="/"><img src="/Static/Images/Header/Logo.png"/></a>

    <nav id="SubNav">
        <ul>
            <li id="CartButton"><img src="/Static/Images/Header/Cart.png"/>Cart</li>
            <li><a href="/FAQ">FAQ</a></li>
            <li><a href="/Contact">Contact</a></li>
        </ul>
    </nav>

    <nav id="MainNav">
        <ul>
            <li {if $title == 'Featured'}class="Active"{/if}>
                <a href="/Featured" title="Featured"><img src="/Static/Images/Header/Featured.png"/></a>
            </li>
            <li {if $title == 'Store'}class="Active"{/if}>
                <a href="/Store" title="Store"><img src="/Static/Images/Header/Store.png"/></a>
            </li>
            <li {if $title == 'Vault'}class="Active"{/if}>
                <a href="/Vault" title="Vault"><img src="/Static/Images/Header/Vault.png"/></a>
            </li>
        </ul>
        <h1 id="TagLine">A new limited run design every 5 days</h1>
    </nav>
</header>
<article id="Main">
    {include $mainTemplate}
</article>
<footer>
    <p>Site created by Gregory Benner</p>
</footer>

{include 'Global/HTMLFooter.tpl'}