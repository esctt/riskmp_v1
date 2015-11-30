<div id='page-content' class='sharp-page'>
    <div class="page-content-container">
        <div id='title'>
            <h1>New Subscription</h1>
        </div>
        <div id='wizard' style='width:50%;margin-left:auto;margin-right:auto;background-color:#f2f2f2;min-height:400px;'>
            <div class='toolbar'>
                <div class='toolbar-title'>Your Current Plan:</div>
            </div>
            <div class='tab-content' style='padding:0px 10px;'>
                <?php if ($promotion_expiry == null) {
                    echo "<p>You do not currently have a membership";
                } else {
                    echo "<p>You currently have a promotional subscription that will expire on $promotion_expiry.</p>";
                }
                ?>
            </div>
            <div class='toolbar'>
                <div class='toolbar-title'>Subscription Details: </div>
            </div>
            <div class='tab-content' style='padding:0px 10px;'>
                <p class='field-title'>How would you like to be billed?</p>
                <input type="radio" name="billingperiod" value="Month" onchange='update_total();' checked='checked'>Monthly Billing<?php
                           echo " ($" . MONTHLY_PRICE . "/month)";
                       ?>
                <input type="radio" name="billingperiod" value="Year" onchange='update_total();'>Annual Billing<?php
                           echo " ($" . ANNUAL_PRICE . "/year)";
                       ?>
                <br/><br/>
                <p class='price-title-bold'>Subtotal: </p><p id='total_charge' class='price-value-bold'></p>
                <?php 
                    if ($promotion_expiry == null) {
                        echo '<p>You will be charged the full amount immediately</p>';
                    } else {
                        echo "<p>You will not be charged until your current subscription expires on $promotion_expiry.</p>";
                    }
                ?>
            </div>
            <div class='toolbar'>
                <div class='toolbar-title'>Billing Details: </div>
            </div>
            <div class='tab-content' style='padding:0px 10px;'>
                <p class='field-title'>Credit Card Type: </p>
                <select name="card_type">
                    <option value="0">Visa</option>
                    <option value="1">MasterCard</option>
                    <option value="2">American Express</option>
                </select>
                <p class='field-title'>Credit Card Number </p>
                <input type='text' name="acct"/>
                <p class='field-title'>Expiration Date </p>
                <select name='expmonth'>
                    <?php 
                    $months = unserialize(CALENDAR_MONTHS);
                    foreach ($months as $num => $name) {
                        echo "<option value='$num'>$name ($num)</option>";
                    }
                        ?>
                </select>
                <select name='expyear'>
                    <?php 
                    $curyear = intval(date("Y"));
                    for ($y = $curyear; $y < $curyear + 20; $y++) {
                        echo "<option value='$y'>$y</option>";
                    }
                        ?>
                </select>
                <p class='field-title'>First Name </p>
                <input type='text' name="first_name"/>
                <p class='field-title'>Last Name </p>
                <input type='text' name="last_name"/>
                <br/>
                <p class='field-title'>Location</p>
                <label><input type='radio' name='location' value='canada' checked/> I live in Canada</label>
                <label><input type='radio' name='location' value='other'/> I live outside of Canada</label>
                <div id='province_select'>
                <p class='field-title'>Province </p>
                <select name='province'>
                    <?php 
                    $provinces = unserialize(PROVINCES);
                    foreach ($provinces as $sym => $name) {
                        echo "<option value='$sym'>$name ($sym)</option>";
                    }
                        ?>
                </select>
                </div>
                <div style='width:100%;text-align:center;'><a class="button button-flat-dblue button-large" id="confirm_subscription">Proceed</a></div>
            </div>
        </div>
    </div>
</div>

<div id="submit_progress_div" title="Submit Payment" style="display:none;width:auto;">
    <div style="width:10px;height:100%;float:left;vertical-align:middle;">
    <img id='processing-img' src="<?php echo base_url('assets/images/processing.gif'); ?>" style="height:40px;width:40px;margin-top:30px;"/>
    </div>
    <div style="float:right;width:220px;">
    <p id='progress_text'></p>
    </div>
</div>

<div id="subscription_confirmation_div" title="Confirmation" style="display:none;width:450px;">
    <p class="top-instruction">Please confirm the details below before clicking submit.</p>
    <p class="field-title">What you're getting:</p>
    <p id='confirm_membership'>Recurring Membership to RiskMP</p>
    <p class='price-title-bold'>Subtotal: </p><p id='confirm_subtotal' class='price-value-bold'></p><br/>
    <p class='price-title-bold'>Tax: </p><p id='confirm_tax' class='price-value-bold'></p><br/>
    <p class='price-title-bold'>Total: </p><p id='confirm_total_charge' class='price-value-bold'></p><br/>
    <p class='price-title-bold'>Amount due now: </p><p id='confirm_init_charge' class='price-value-bold'></p><br/>
    <p class='field-title' style='display:inline;'>Next payment: </p><p class='field-value' id='confirm_start_date' style='display:inline;'></p>
</div>

<script type='text/javascript'>
var form_submitted = false;
update_total();
$('input[name="location"]').change(function() {
    if ($('input[name="location"]:checked').val() == 'canada') {
        $('#province_select').css('display', 'block');
    } else {
        $('#province_select').css('display', 'none');
    }
});
function update_total() {
    var bill_amt;
    var cycle;
    if ($('input[name="billingperiod"]:checked').val() == 'Year') {
        cycle = "per year";
        bill_amt = <?php echo ANNUAL_PRICE; ?>;
    } else {
        cycle = "per month";
        bill_amt = <?php echo MONTHLY_PRICE; ?>;
    }
    var total = Number(bill_amt).toFixed(2);
    $('#total_charge').html('$' + total + ' ' + cycle);
}
$('#confirm_subscription').click(function(event) {
    event.preventDefault();
    if (form_submitted) {
        return;
    }
    form_submitted = true;
    $('#progress_text').html('Processing...');
    $('#processing-img').css('display', 'block');
    $("#submit_progress_div").dialog({modal: true, closeOnEscape: false});
    var url = config.base_url + "data_fetch/confirm_subscription_amounts";
    var posting = $.post(url, {
        billingperiod: $('[name="billingperiod"]:checked').val(),
        province: $('input[name="location"]:checked').val() == 'canada' ? $('select[name="province"]').val() : 'other'
    });
    posting.done(function(data) {
        var result = JSON.parse(data);
        if (result['success']) {
            $("#submit_progress_div").dialog('close');
            if (result['profilestartdate'] == null) {
                $('#progress_text').html(result['message']);
                $('#processing-img').css('display', 'none');
                $("#submit_progress_div").dialog({modal: true, buttons: [{text: "Close", click: function() {
                                window.location.href = config.base_url + "user/subscription_details";
                            }}]});
                form_submitted = false;
                return;
            }
            var subtotal = Number(result['amt']).toFixed(2);
            var taxamt = Number(result['taxamt']).toFixed(2);
            var total = Number(subtotal) + Number(taxamt);
            $('#confirm_subtotal').html('$' + subtotal);
            $('#confirm_tax').html('$' + taxamt);
            $('#confirm_total_charge').html('$' + total);
            $('#confirm_init_charge').html('$' + Number(result['initamt']).toFixed(2));
            $('#confirm_start_date').html(result['profilestartdate']);
            $("#subscription_confirmation_div").dialog({modal: true,
                buttons: [
                    {text: "Submit", click: function() {
                            submit_subscription();
                    }},
                    {text: "Cancel", click: function() {
                            $(this).dialog("close");
                        }
                    }]});
                form_submitted = false;
        } else {
            var output = result['message'];
            if (result['errors'] != undefined || result['errors'] != null) {
                result['errors'].forEach(function(entry) {
                    output = output + "\n" + entry.L_LONGMESSAGE;
                });
            }
            $('#progress_text').html(output);
            $('#processing-img').css('display', 'none');
            $("#submit_progress_div").dialog({modal: true, buttons: [{text: "Close", click: function() {
                            $(this).dialog("close");
                        }}]});
        }
    });
    posting.fail(function()
    {
        $('#progress_text').html('An unknown error has occurred. Please try again later.');
        $('#processing-img').css('display', 'none');
    });
    form_submitted = false;
});
function submit_subscription() {
    if (form_submitted) {
        return;
    }
    form_submitted = true;
    $('#progress_text').html('Processing...');
    $('#processing-img').css('display', 'block');
    $("#submit_progress_div").dialog({modal: true, closeOnEscape: false, open: function(event, ui) { $(".ui-dialog-titlebar-close", ui.dialog || ui).hide(); }});
    var url = config.base_url + "data_fetch/create_billing_profile";
    var posting = $.post(url, {
        billingperiod: $('[name="billingperiod"]:checked').val(),
        creditcardtype: $('[name="creditcardtype"]').val(),
        acct: $('[name="acct"]').val(),
        expdate: $('[name="expmonth"]').val() + $('[name="expyear"]').val(),
        firstname: $('[name="firstname"]').val(),
        lastname: $('[name="lastname"]').val(),
        province: $('input[name="location"]:checked').val() == 'canada' ? $('select[name="province"]').val() : 'other'
    });
    posting.done(function(data) {
        var result = JSON.parse(data);
        if (result['success']) {
            $('#progress_text').html(result['message']);
            $('#processing-img').css('display', 'none');
            $("#submit_progress_div").dialog({modal: true, buttons: [{text: "Close", click: function() {
                            window.location.href = config.base_url + "user/subscription_details";
                        }}]});
        } else {
            var output = result['message'];
            if (result['errors'] != undefined || result['errors'] != null) {
                result['errors'].forEach(function(entry) {
                    output = output + "\n" + entry.L_LONGMESSAGE;
                });
            }
            $('#progress_text').html(output);
            $('#processing-img').css('display', 'none');
            $("#submit_progress_div").dialog({modal: true, buttons: [{text: "Close", click: function() {
                            $(this).dialog("close");
                        }}]});
        }
    });
    posting.fail(function()
    {
        $('#progress_text').html('An unknown error has occurred. Please try again later.');
    });
    form_submitted = false;
}
</script>