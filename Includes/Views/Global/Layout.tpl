{include 'Global/HTMLHeader.tpl'}
<!--suppress ALL -->
<header>
    <a id="logo" href="/"><img src="Images/Header/Logo.png"/></a>

    <nav id="SubNav">
        <ul>
            <li id="Cart"><img src="Images/Header/Cart.png"/>Cart</li>
            <li id="FAQ"><a href="/FAQ">FAQ</a></li>
            <li id="Contact"><a href="/Contact">FAQ</a></li>
        </ul>
    </nav>

    <nav id="MainNav">
        <ul>
            <li><a {if $title == 'Featured'}class="active"{/if} href="/Featured" title="Featured"><img src="Images/Header/Featured.png"/></a></li>
            <li><a {if $title == 'Store'}class="active"{/if} href="/Store" title="Featured"><img src="Images/Header/Store.png"/></a></li>
            <li><a {if $title == 'Vault'}class="active"{/if} href="/Vault" title="Featured"><img src="Images/Header/Vault.png"/></a></li>
        </ul>

        <h1 id="TagLine">A limited run design every 5 days</h1>
    </nav>
</header>
<article id="Main">
    {include $mainTemplate}
</article>
<footer>
    <p>Site created by Gregory Benner</p>
</footer>
{include 'Global/HTMLFooter.tpl'}