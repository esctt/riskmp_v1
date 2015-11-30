<div id='page-content' class='sharp-page'>
    <div class="page-content-container">
        <div style='margin-left:auto;margin-right:auto;text-align:center;width:400px;' class='window'>
            <div class='toolbar'>
                <div class='toolbar-title'>Forgot Password</div>
            </div>
            <div id='wizard' style='display:inline-block;background-color:#f2f2f2;'>
                <div class='tab-content' style='padding:10px;text-align:left;'>
                    <div style='width:100%'>
                        <p class="field-description" style="display:block;">Don't worry, it happens to the best of us. Just enter your username and we'll email you a link to reset your password.</p>
                        <p class='field-title'>Username or email:</p>
                        <input type="text" name="txt_username" id="txt_username" required>
                    </div>
                    <br/>
                    <div style='width:100%;text-align:center;'><input id="submit_reset" type="submit" value="Submit" class='button button-flat-dblue button-large'></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="submit_progress_div" title="Forgot Password" style="display:none;width:auto;">
    <p id='progress_text'></p>
</div>
<script type="text/javascript">
    $('#submit_reset').click(function(event) {
        event.preventDefault();
        $('#progress_text').html('Processing...');
        $("#submit_progress_div").dialog({modal: true});
        var url = config.base_url + "data_fetch/forgot_password";
        var posting = $.post(url, {
            username: $('#txt_username').val()
        });
        posting.done(function(data) {
            var result = JSON.parse(data);
            if (result['success']) {
                $('#progress_text').html(result['message']);
                $("#submit_progress_div").dialog({modal: true, buttons: [{text: "Close", click: function() {
                                window.location.href = config.base_url + "login";
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