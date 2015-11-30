<?
$count_recurring = 0;
foreach ($subscriptions as $row) {
    if ($row['profile_id'] != null) {
        $count_recurring++;
    }
}
$multiple_profiles = ($count_recurring > 1);
?>
<div id='page-content' class='sharp-page'>
    <div class="page-content-container">
        <div id='title'>
            <h1>Membership Details</h1>
        </div>
        <?php
        if (isset($notification)) {
            echo "<p style='color:red;'>" . $notification . "</p>";
        }
        ?>        
        <div id="tabs">
            <ul>
                <li><a href='#tabs-Subscriptions'>Current Subscriptions</a></li>
                <li><a href='#tabs-Receipts'>Payment Receipts</a></li>
            </ul>
            <div id='tabs-Subscriptions' class='tab'>
                <div class='toolbar'>
                    <div class='toolbar-title'>Your Current Subscriptions</div>
                </div>
                <div style="width:100%;" class="jTableContainer">
                    <div class="jtable-main-container">
                        <table class="jtable">
                            <thead>
                                <tr>
                                    <th class="jtable-column-header"><div class="jtable-column-header-container"><span class="jtable-column-header-text">Subscription Type</span></div></th>
                            <th class="jtable-column-header"><div class="jtable-column-header-container"><span class="jtable-column-header-text">Value</span></div></th>
                            <th class="jtable-column-header"><div class="jtable-column-header-container"><span class="jtable-column-header-text">Date of Redemption</span></div></th>
                            <th class="jtable-column-header"><div class="jtable-column-header-container"><span class="jtable-column-header-text">Expiry Date</span></div></th>
                            <th class="jtable-column-header"><div class="jtable-column-header-container"><span class="jtable-column-header-text">Renewal Date</span></div></th>
                            <th class="jtable-column-header"><div class="jtable-column-header-container"><span class="jtable-column-header-text">Options</span></div></th>
                            </tr>   
                            </thead>
                            <tbody>
                                <?
                                if (count($subscriptions) == 0) {
                                    echo "<tr class='jtable-no-data-row'><td colspan='6'>You do not have any active subscriptions.</td></tr>";
                                } else {
                                    $count = 0;
                                    foreach ($subscriptions as $row) {
                                        if ($count % 2 == 0) {
                                            echo "<tr class='jtable-data-row jtable-row-even'>";
                                        } else {
                                            "<tr class='jtable-data-row'>";
                                        }
                                        $type = $row['transaction_id'] == null ? "Promotion" : "Recurring";
                                        echo "<td>" . $type . "</td>";
                                        $value;
                                        if ($row['base_membership'] == 1) {
                                            $value = 'Base membership';
                                            if ($row['num_additional_users'] != 0) {
                                                $value.=" +" . $row['num_additional_users'] . " users";
                                            }
                                        } else {
                                            $value = $row['num_additional_users'] . " users";
                                        }
                                        echo "<td>" . $value . "</td>";
                                        echo "<td>" . $row['date_of_redemption'] . "</td>";
                                        echo "<td>" . $row['expiry_date'] . "</td>";
                                        echo "<td>" . $row['renewal_date'] . "</td>";
                                        echo "<td></td>";
                                        echo "</tr>";
                                        $count++;
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                        <div style='text-align:right;'>
                            <a href="<?php echo base_url('client/cancel_membership'); ?>" class="button button-flat-caution">Cancel Membership</a>
                            <a href="<?php echo base_url('client/update_billing'); ?>" class="button button-flat-dblue">Purchase/Edit Membership</a>
                            <input type="text" id="promotion_code">&nbsp;&nbsp;&nbsp;<a href="#" id="submit_promotion" class="button button-flat-dblue">Redeem Promotion</a>
                        </div>
                        <?php if ($multiple_profiles) { ?>
                            <p class='table-footer'>You currently have more than one recurring subscription. This is probably because you have changed your features mid-cycle.
                                Don't worry, on your next billing date these subscriptions will be combined into a single subscription and you will only
                                be charged once.</p>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div id='tabs-Receipts' class='tab'>
                <div class='toolbar'>
                    <div class='toolbar-title'>Invoices</div>
                </div>
                <div id="InvoicesTableContainer" style="width:100%;" class="jTableContainer"></div>
                <br/>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        $("#tabs").tabs();
    });</script>
<div id="submit_progress_div" title="Redeem Promotion" style="display:none;width:auto;">
    <p id='progress_text'></p>
</div>

<script type="text/javascript">
    
        loadInvoicesTable('InvoicesTableContainer');
    $('#submit_promotion').click(function(event) {
        event.preventDefault();
        $('#progress_text').html('Processing...');
        $("#submit_progress_div").dialog({modal: true});
        var url = config.base_url + "data_fetch/redeem_promotion";
        var posting = $.post(url, {
            promotion_code: $('#promotion_code').val()
        });
        posting.done(function(data) {
            var result = JSON.parse(data);
            if (result['success']) {
                $('#progress_text').html(result['message']);
                $("#submit_progress_div").dialog({modal: true, buttons: [{text: "Close", click: function() {
                                $(this).dialog("close");
                            }}]});
            } else {
                $('#progress_text').html(result['message']);
                $("#submit_progress_div").dialog({modal: true, buttons: [{text: "Close", click: function() {
                                $(this).dialog("close");
                            }}]});
            }
        });
        posting.fail(function()
        {
            $('#progress_text').html('An unknown error has occurred. Please try again later.');
        });
    });
</script>