<div class="row">
    <div class="col-sm-8">
        <h3>Order flowchart</h3>
        <ol>
            <li>The customer selects desired items and clicks on 'ORDER'</li>
            <li>The customer receives an email with confirmation link, this link will later serve as track and trace(click <span class="glyphicon glyphicon-eye-open"></span> to view). After the customers confirms the order, the status 'Confirmed' will automatically update.</li>
            <li>All admins subscribed to the 'order placed committee' maillist receive a notification.</li>
            <li>Step 4, 5 and 6 have to happen within 2 days of eachother due to fluctuating prices! <br>One admin adds the items to the shopping cart at the selected store by clicking the shopping cart icon ( <span class="glyphicon glyphicon-shopping-cart"></span> ) and <b>updates the price</b>. When entering the price, add 5 cents margin to account for price fluctuations.</li>
            <li>When the customer comes to pay, first check if the price has changed significantly due to temporary discounts and update accordingly. The customer pays for the order (either at the ETV or via deb). <b>Update 'Paid' status</b>.</li>
            <li>One of the admins orders the items at the selected store by clicking the shopping cart icon ( <span class="glyphicon glyphicon-shopping-cart"></span> ). <b>Update 'Ordered' status</b>.</li>
            <li>When the items arrive <b>Update 'Arrived' status</b> for ONLY the store of which the parcel arrived. The customer won't see the status change to arrived until the parcels from all stores within the same order number have their status changed to arrived</li>
            <li>The customer comes to retrieve their order (<b>Update 'Retrieved' status</b>) and the order is thereby finalised</li>
        </ol>
    </div>
    <div class="col-sm-4">
        <h3>Updating information</h3>
        The flowchart describes entering a price and multiple status updates. All these updates can be performed by expanding the current order (click <span class="glyphicon glyphicon-expand"></span>). The status booleans can be checked or unchecked, the price input requires a numeric value. The price can be changed until the order has been paid.<br>
        Confirm the updates by clicking 'Submit'.<br>
        If an order placed by the customer consists of orders at multiple different stores, the customer won't see an update in the track n trace until that specific action has been completed for all stores. e.g.: The customer order consists of items from 2 stores: farnell and RS. If the items have been ordered by the committee at RS but not yet at Farnell, the customer will not see the 'Ordered at supplier' status checked.
    </div>
</div>