<div id='page-content' class='sharp-page'>
    <div class="window" style="margin-top:150px;margin-bottom:150px;width:400px;margin-left:auto;margin-right:auto;">
        <div class="toolbar">
            <div class="toolbar-title">Cancel Membership</div>
        </div>
        <p>After your current membership expires, your subscription will not be renewed and you will
            no longer have access. Are you sure?</p>
        <div style='width:100%;text-align:center;'>
            <a href="<?php echo base_url('dashboard'); ?>" class="button button-flat-dblue button-large">Nevermind</a>
            &nbsp;&nbsp;
            <a id='do_cancel' href="#" class="button button-flat-caution button-large ">Yes, I'm sure</a>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('#do_cancel').click(function(event) {
        event.preventDefault();
        var url = config.base_url + "data_fetch/cancel_membership";
        var posting = $.post(url, {});
        posting.done(function(data) {
            var result = JSON.parse(data);
            if (result['success']) {
                window.location.href = config.base_url + "dashboard";
            } else {
                alert(result['message']);
            }
        });
        posting.fail(function()
        {
            alert('An unknown error has occurred. Please try again later.');
        });
    });
</script>