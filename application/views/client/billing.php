<div id='page-content' class='sharp-page'>
    <div class="page-content-container">
        <div id='title'>
            <h1>Billing</h1>
        </div>
        <?php if (isset($error_message)) echo "<p style='font-size:16px;font-weight:bold;color:red;'>".$error_message."</p>"; ?>
    </div>
    <div id="billing-info" class="info">
        <p class="title">Member since: </p><p class="value"><?php echo $client_data['client_date_registered'];?></p>
        <p class='title'>Current Plan: </p><p class="value"><?php echo $client_data['current_plan']; ?></p>
        <p class="title">Membership Expiry: </p><p class="value"><?php echo $client_data['membership_expiry'];?></p>
        <p class="title">Outstanding Balance: </p><p class="value"><?php echo $client_data['outstanding_balance'];?></p>
        <a href="#" class="button button-flat-dblue button-large">Edit Membership</a>
        <a href="#" class="button button-flat-dblue button-large">Edit Billing Information</a>
        <a href="#" class="button button-flat-dblue button-large">Cancel Membership</a>
    </div>
</div>
