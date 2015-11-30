<div id='page-content' class='sharp-page'>
    <div class="page-content-container">
        <div style='margin-left:auto;margin-right:auto;text-align:center;width:600px;' class='window'>
            <div class='toolbar'>
                <div class='toolbar-title'><?php echo $title; ?></div>
            </div>
            <div id='wizard' style='display:inline-block;background-color:#f2f2f2;'>
                <?php
                echo validation_errors();
                echo form_open('user/change_pwd_form/');
                ?>
                <div class='tab-content' style='padding:10px;text-align:left;'>
                    <div style='width:100%'>
                        <p class='field-title'>Old Password:</p>
                        <input type='password' id='old_pwd' name='old_pwd' class='field' autocomplete="off" required/>
                        <p class='field-title'>New Password:</p>
                        <input type='password' id='set_pwd' name='set_pwd' autofill='false' maxlength='20'
                               required class='field' autocomplete="off" pattern='(?=^.{6,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$'
                               title='Please choose a password that is at least 6 characters and contains both lowercase and uppercase letters and at least one number/symbol.'/>
                        <p class='field-description' style="display:block;">Please choose a password that is at least 6 characters and contains both lowercase and uppercase letters and at least one number/symbol.</p>
                        <p class='field-title'>Confirm Password:</p>
                        <input type='password' name='set_pwd_conf' required id='set_pwd_conf'
                               class='field' autocomplete="off" onblur='pass_match();'/>
                    </div>
                    <div style='width:100%;text-align:center;'><input type="submit" value="Save" class='button button-flat-dblue button-large'></div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function pass_match() {
        var p1 = document.getElementById('set_pwd');
        var p2 = document.getElementById('set_pwd_conf');
        if (p1.value !== p2.value)
            p2.setCustomValidity('Passwords must match.');
        else
            p2.setCustomValidity('');
    }
</script>