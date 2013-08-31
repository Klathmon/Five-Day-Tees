{include 'Global/HTMLHeader.tpl'}
<!--suppress ALL -->
<header>
    <a id="logo" href="/"><img src="{$config->getStaticURL()}Images/Header/Logo.png"/></a>

    <nav id="SubNav">
        <ul>
            <li id="Cart"><img src="{$config->getStaticURL()}Images/Header/Cart.png"/>Cart</li>
            <li id="FAQ"><a href="/FAQ">FAQ</a></li>
            <li id="Contact"><a href="/Contact">Contact</a></li>
        </ul>
    </nav>

    <nav id="MainNav">
        <ul>
            <li {if $title == 'Featured'}class="Active"{/if}>
                <a href="/Featured" title="Featured"><img src="{$config->getStaticURL()}Images/Header/Featured.png"/></a>
            </li>
            <li {if $title == 'Store'}class="Active"{/if}>
                <a href="/Store" title="Store"><img src="{$config->getStaticURL()}Images/Header/Store.png"/></a>
            </li>
            <li {if $title == 'Vault'}class="Active"{/if}>
                <a href="/Vault" title="Vault"><img src="{$config->getStaticURL()}Images/Header/Vault.png"/></a>
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