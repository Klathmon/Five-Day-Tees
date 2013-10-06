<div class="Page" id="FAQ">
    <div class="Preview">
        <img class="left" src="/Static/Images/Ceph.png"/>

        <p>
            As an angry cephalopod, the most frequently asked questions I receive are, “Why are you in my house?”
            and “Won’t you please spare the children?”
        </p>

        <p>
            But you probably want to know about Five Day Tees. You know… what we do…how it works - stuff like that.
        </p>

        <p>
            When a mommy cephalopod and a daddy cephalopod love each other very much…wait, wrong story.
        </p>

        <p>
            Five Day Tees features a new, limited edition, t-shirt for each day of the week Monday – Friday. That’s
            five days a week (get it?). The site itself is loaded with curiosities as well, so click around. Play. You
            might find some random katamari style minutia, or some really cool coupons or deals.
        </p>
    </div>

    <h1>Frequently Asked Questions and other information.</h1>

    <div class="Block">
        <h3>How Does It Work?</h3>

        <p>
            <span>How do you explain the featured shirt of day?</span><br/>
            SCIENCE! Or... We post 5 new shirts a week and feature them Monday through Friday. On the day a shirt is
            featured it’s cheap. Other days it’s regular price. All the shirts are limited editions. You’d be pretty
            special if you had one.
        </p>

        <p>
            <span>What’s the deal with the store?</span><br/>
            Did you miss the shirt design’s featured day (when it was cheap?) What were you thinking? Well, you can
            check out the store – those shirt designs that haven’t sold out are placed in the store until the limited run
            is exhausted. A meter is assigned to each shirt giving you an idea of how many are left. So you have
            another shot at buying that unique, limited edition.
        </p>

        <p>
            <span>Dafaq is a vault?</span><br/>
            That’s where old designs go to die when we run out of stock. They are fed bread, water and tortured with
            fire. If you go there and vote for the shirts we release them back into the store! But beware- the shirts
            have
            gone mad from being in captivity and come back all new and different... like they’ve been bitten by a
            werewolf or something.
        </p>
    </div>

    <div class="Block">
        <h3>Why Not 7 Day Tees?</h3>

        <p>
            What, we don’t work hard enough for you people? Weekends are for copious amounts of drinking and other
            recreational pursuits, late nights and big breakfasts at 3pm the afternoon. Without the weekends off we
            wouldn’t have the inspiration to design the t-shirts you get during the week.
        </p>
    </div>

    <div class="Block">
        <h3>Why a limited amount?</h3>

        <p>
            Because things are better when they’re a rarity, of course. And we want you to look cool. What’s cooler
            than having a shirt nobody you know has? You’d be the only one. The king. The leader. President of awesome.
            Who would want diamonds if they were as common as copper?
        </p>
    </div>

    <div class="Block">
        <h3>Shipping</h3>

        <p>
            All orders are shipped out within 1 - 2 days of receipt of payment.<br/>
            We currently offer {count($shippingMethods)} shipping methods:
        </p>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Estimated Time</th>
                    <th>
                        $0 - 
                        {currency amount=$shippingMethods[0]->getTier1PriceLimit()}
                    </th>
                    <th>
                        {currency amount=$shippingMethods[0]->getTier1PriceLimit() add=Currency::createFromCents(1)} -
                        {currency amount=$shippingMethods[0]->getTier2PriceLimit()}
                    </th>
                    <th>
                        {currency amount=$shippingMethods[0]->getTier2PriceLimit() add=Currency::createFromCents(1)} -
                        {currency amount=$shippingMethods[0]->getTier3PriceLimit()}
                    </th>
                </tr>
            </thead>
            <tbody>
                {foreach $shippingMethods as $shippingMethod}
                    <tr>
                        <td>{$shippingMethod->getName()}</td>
                        <td>
                            {$shippingMethod->getEstimatedDaysLow()} -
                            {$shippingMethod->getEstimatedDaysHigh()}
                            Business Days
                        </td>
                        <td>
                            {currency amount=$shippingMethod->getTier1Cost()}
                        </td>
                        <td>
                            {currency amount=$shippingMethod->getTier2Cost()}
                        </td>
                        <td>
                            {currency amount=$shippingMethod->getTier3Cost()}
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
        <p>
            For orders over {currency amount=$shippingMethods[0]->getTier3PriceLimit()} please contact us before placing your order. To get the details read our terms of
            agreement.<br/>
            <span>*Please Note, we cannot ship to PO Boxes or APO Addresses</span>
        </p>
    </div>

    <div class="Block">
        <h3>What if the shirt is defective?</h3>

        <p>
            If your merchandise does arrive defective, we ask that you contact us within 2 weeks to coordinate an
            acceptable solution. Please include your order number and a detailed explanation of the problem – a picture
            is
            always a good idea. Include size exchange or color exchange info. We can’t resell anything but we can offer
            you a
            store credit.
        </p>
    </div>

    <div class="Block">
        <h3>What if I make a mistake when I place my order?</h3>

        <p>
            Because we make each shirt to order - all orders are final, however, we want to be as ‘helpful’ as we
            can. If you make a mistake with your order, contact us and we will see what we can reasonably do to help you
            get
            the shirt you want. We can’t resell anything but we can offer you a store credit. (Did I just repeat
            myself?)
        </p>
    </div>

    <div class="Block">
        <h3>Should I return items in their original shipping containers and/or packaging?(Is this thing on?)</h3>

        <p>
            No. All sales are final so there is no need to return the shirt. If you really, really want to return a
            shirt you would have to pay for the return postage. But why would you go to such extremes – you have to ask
            yourself this simple question. Do I have a postal fetish?
        </p>
    </div>
</div> 