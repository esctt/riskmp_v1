<div id='page-content' class='sharp-page'>
    <div class="page-content-container">
        <div id='title'>
            <h1>Update Billing or Membership Information</h1>
        </div>
        <div id='wizard' style='width:50%;margin-left:auto;margin-right:auto;background-color:#f2f2f2;min-height:400px;'>
            <?php
            //echo validation_errors();
            //echo form_open('client/billing_form/');
            ?>
            <div class='toolbar'>
                <div class='toolbar-title'>Your Current Plan:</div>
            </div>
            <div class='tab-content' style='padding:0px 10px;'>
                <p class='field-title'>Paid Subscription:</p>
                <?php
                if ($existing_profile != null) {
                    if ($existing_profile['base_membership'] == 1) {
                        echo "<p>Base membership</p>";
                    }
                    if ($existing_profile['num_additional_users'] != 0) {
                        echo "<p>" . $existing_profile['num_additional_users'] . " additional users</p>";
                    }
                    echo "<p class='price-value-bold'>$" . $existing_profile['amount'] . " per " . strtolower($existing_profile['billingperiod']);
                } else {
                    echo "<p>You do not currently have a subscription.</p>";
                }
                ?>
                <p class="field-title">Included Promotions: </p>
                <?php
                if ($promo_base_membership || $promo_num_users) {
                    if ($promo_base_membership) {
                        echo "<p>Base Membership</p>";
                    }
                    if ($promo_num_users) {
                        echo "<p>" . $promo_num_users . " additional users</p>";
                    }
                } else {
                    echo "<p>You have no promotions.</p>";
                }
                ?>
            </div>
            <div class='toolbar'>
                <div class='toolbar-title'>Subscription Details: </div>
            </div>
            <div class='tab-content' style='padding:0px 10px;'>
                <p class='field-title'>How would you like to be billed?</p>
                <input type="radio" name="billingperiod" value="Month" onchange='update_total();'
                       <?php if ($existing_profile != null && $existing_profile['billingperiod'] == 'Month') echo "checked='checked'"; ?> >Monthly Billing<?php
                       if (!$promo_base_membership)
                           echo " ($" . MONTHLY_BASE_PRICE . "/month)";
                       ?>
                <input type="radio" name="billingperiod" value="Year" onchange='update_total();' 
                       <?php if ($existing_profile == null || $existing_profile['billingperiod'] == 'Year') echo "checked='checked'"; ?> >Annual Billing<?php
                       if (!$promo_base_membership)
                           echo " ($" . ANNUAL_BASE_PRICE . "/year)";
                       ?>
                <p class='field-title'>How many additional users need access?</p>
                <?php if ($existing_profile != null && $existing_profile['num_additional_users'] != 0) { ?>
                    <input type="radio" name="additional_users"  value=0 onchange='update_total();'>Just me
                    <input type="radio" name="additional_users" checked='checked' value=1 onchange='update_total();'>I need more users
                <?php } else {
                    ?>
                    <input type="radio" name="additional_users"  value=0 checked='checked' onchange='update_total();'>Just me
                    <input type="radio" name="additional_users" value=1 onchange='update_total();'>I need more users
                    <?php
                }
                ?>
                <p class='field'>$<?php echo MONTHLY_USER_PRICE; ?>/month per user OR $<?php echo ANNUAL_USER_PRICE; ?>/year per user</p> 
                <p class='field-title' id='user_prompt'>How many additional users? </p>
                <input type='number' name='num_additional_users' id='num_users' min='0' value=<?php
                if ($existing_profile != null && $existing_profile['num_additional_users'] != 0) {
                    echo $existing_profile['num_additional_users'];
                } else {
                    echo '0';
                }
                ?> onchange='update_total();'/>
                <p class='price-title-bold'>Total: </p><p id='total_charge' class='price-value-bold'></p>
                <p>Note: You will be charged the full amount immediately, unless you have an existing billing profile. If you do,
                    you will be charged a prorated amount for any features you are adding until the next billing date. On that next billing date,
                    you will be charged the new fee. Your prorated amount will be calculated in the next step and you'll be able
                    to review it before confirming your subscription.</p>
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
                <input type='text' name="acct" value="4872257184345422"/>
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
                <!--<input type='number' name="expyear" value="2019" style='max-width:90px;' min='<?php echo date("Y"); ?>' max=ec/>-->
                <p class='field-title'>First Name </p>
                <input type='text' name="first_name" value="John"/>
                <p class='field-title'>Last Name </p>
                <input type='text' name="last_name" value="Smithy"/>
                <br/>
                <div style='width:100%;text-align:center;'><a class="button button-flat-dblue button-large" id="confirm_subscription">Proceed</a></div>
            </div>
            <!--        </form>-->
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
    <p id='confirm_base_membership' style='display:none;'>Base membership</p>
    <p id='confirm_num_users'>0 additional users</p>
    <p class='price-title-bold'>Total: </p><p id='confirm_total_charge' class='price-value-bold'></p>
    <p class='price-title-bold'>Amount due now: </p><p id='confirm_init_charge' class='price-value-bold'></p>
    <p class='field-title'>Next payment: </p><p class='field-value' id='confirm_start_date'></p>
</div>

<script type='text/javascript'>
var form_submitted = false;
update_total();
function update_total() {
    var base_charge;
    var user_charge;
    var cycle;
    if ($('input[name="billingperiod"]:checked').val() == 'Year') {
        cycle = "per year";
        base_charge = <?php echo ANNUAL_BASE_PRICE; ?>;
        user_charge = <?php echo ANNUAL_USER_PRICE; ?>;
    } else {
        cycle = "per month";
        base_charge = <?php echo MONTHLY_BASE_PRICE; ?>;
        user_charge = <?php echo MONTHLY_USER_PRICE; ?>;
    }
    var promo_base_membership = <?php echo $promo_base_membership ? 'true' : 'false'; ?>;
    var total = 0;
    if (!promo_base_membership) {
        total = base_charge;
    }
    if ($('#num_users').val() < 0) {
        $('#num_users').val(0);
    }
    if ($('input[name="additional_users"]:checked').val() == 1) {
        $('#user_prompt').css('display', 'block');
        $('#num_users').css('display', 'block');
        var num_users = $('#num_users').val();
        total = total + user_charge * num_users;
    } else {
        $('#user_prompt').css('display', 'none');
        $('#num_users').css('display', 'none');
    }
    total = Number(total).toFixed(2);
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
    var num_users = $('input[name="additional_users"]:checked').val() == 1 ? $('#num_users').val() : 0;
    var posting = $.post(url, {
        billingperiod: $('[name="billingperiod"]:checked').val(),
        num_additional_users: num_users
    });
    posting.done(function(data) {
        var result = JSON.parse(data);
        if (result['success']) {
            $("#submit_progress_div").dialog('close');
            if (result['profilestartdate'] == null) {
                $('#progress_text').html(result['message']);
                $('#processing-img').css('display', 'none');
                $("#submit_progress_div").dialog({modal: true, buttons: [{text: "Close", click: function() {
                                window.location.href = config.base_url + "client/view_subscriptions";
                            }}]});
                form_submitted = false;
                return;
            }
            if (result['base_membership'] == 0) {
                $('#confirm_base_membership').css('display', 'none');
            } else {
                $('#confirm_base_membership').css('display', 'block');
            }
            if (result['num_additional_users'] == 0) {
                $('#confirm_num_users').css('display', 'none');
            } else {
                $('#confirm_num_users').css('display', 'block');
                $('#confirm_num_users').html(result['num_additional_users'] + " additional users");
            }
            $('#confirm_total_charge').html('$' + Number(result['amt']).toFixed(2));
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
    $("#submit_progress_div").dialog({modal: true, closeOnEscape: false});
    var url = config.base_url + "data_fetch/create_billing_profile";
    var posting = $.post(url, {
        billingperiod: $('[name="billingperiod"]:checked').val(),
        num_additional_users: $('[name="num_additional_users"]').val(),
        creditcardtype: $('[name="creditcardtype"]').val(),
        acct: $('[name="acct"]').val(),
        expdate: $('[name="expmonth"]').val() + $('[name="expyear"]').val(),
        firstname: $('[name="firstname"]').val(),
        lastname: $('[name="lastname"]').val()
    });
    posting.done(function(data) {
        var result = JSON.parse(data);
        if (result['success']) {
            $('#progress_text').html(result['message']);
            $('#processing-img').css('display', 'none');
            $("#submit_progress_div").dialog({modal: true, buttons: [{text: "Close", click: function() {
                            window.location.href = config.base_url + "client/view_subscriptions";
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
