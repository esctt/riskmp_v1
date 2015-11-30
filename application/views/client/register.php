        <div id='wizard' style='width:550px;margin-left:auto;margin-right:auto;background-color:#f2f2f2;min-height:400px;margin-top:50px;opacity:0.9;'>
            <?php
            echo validation_errors();
            echo form_open('client/register_form/');
            ?>
            <div class='toolbar'>
                <div class='toolbar-title'>Register for RiskMP</div>
            </div>
            <div class='tab-content' style='padding:10px;margin-left:auto;margin-right:auto;width:500px;'>
                <div class='tab-section'>
                    <div style='clear:both;max-width:100%;'>
                        <div style='width:50%;float:left;'>
                            <p class='field-title'>Company Name:</p> <input type="text" name="txt_company_name" required>
                        </div>
                        <div style='width:50%;float:left;'>
                            <p class='field-title'>Company Phone:</p><input type="tel" name="txt_phone" required>
                        </div>
                    </div>
                    <div style='clear:both;padding-top:15px;max-width:100%;'>
                        <div style='width:50%;float:left;'>
                            <p class='field-title'>First Name:</p> <input type="text" name="txt_first_name" required>
                        </div>
                        <div style='width:50%;float:left;'>
                            <p class='field-title'>Last Name:</p><input type="text" name="txt_last_name" required>
                        </div>
                    </div>
                    <div style='clear:both;padding-top:15px;max-width:100%;'>
                        <div style='width:50%;float:left;'>
                            <p class='field-title'>Email:</p><input type="email" name="txt_email" required>
                        </div>
                        <div style='width:50%;float:left;'>
                            <p class='field-title'>Username:</p><input type="text" name="txt_username" required>
                        </div>
                    </div>
                    <div style='clear:both;padding-top:15px;max-width:100%;'>
                        <div style='width:50%;float:left;'>
                            <p class='field-title'>Password:</p><input type="password" name="txt_password" required>
                        </div>
                        <div style='width:50%;float:left;'>
                            <p class='field-title'>Re-enter Password:</p><input type="password" name="txt_password_conf" required>
                        </div>
                    </div>
                    <div style='clear:both;padding-top:15px;max-width:100%;'>
                        <div style='max-width:100%;'>
                            <p class='field-title'>Access Code:</p><input type="text" name="txt_coupon_code" required>
                        </div>
                    </div>
                </div>
                <br/>
                <div style='text-align:center;'>
                    <input type="submit" class="button button-flat-dblue button-large" value="Save"/>
                </div>            
                </form>
            </div>
        </div>