<div class="window" id="login" style="margin-top:150px;opacity:0.9;">
    <?php
    echo form_open('user/do_login/');
    ?>
    <div class="toolbar">
        <div class="toolbar-title">Login</div>
    </div>
    <div style="padding:0px 20px 5px;">
        <?php if (isset($_SESSION['login_message'])) {
            echo "<p style='margin-top:10px;margin-bottom:0;color:#FF0000;'>".$_SESSION['login_message']."</p>";
            unset($_SESSION['login_message']);
        } ?>
        <p class="dialog-field-title">Username or email:</p>
        <input type='text' name='username' id='username' required value='' placeholder='Username'><br/>
        <p class="dialog-field-title">Password:</p>
        <input type='password' name='password' id='password' required value='' placeholder='Password'><br/>
        <p class="submit"><input type="submit" class="button button-flat-dblue button-large" value="Login"></p>
    </div>
</form>
<div style="width:100%;text-align:right;"><a href="<?php echo base_url('user/forgot_password'); ?>" class="black-link" style="margin-right:5px;">Forgot your password?</a></div>
</div>