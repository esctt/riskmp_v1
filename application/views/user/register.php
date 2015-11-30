        <div id='wizard' style='width:540px;margin-left:auto;margin-right:auto;background-color:#f2f2f2;min-height:400px;margin-top:50px;opacity:0.9;'>
            <?php
            echo form_open('user/register_form/');
            ?>
            <div class='toolbar'>
                <div class='toolbar-title'>Register</div>
            </div>
            <div class='tab-content' style='padding: 10px 25px;'>
                <p class='top-instruction'>Enter your information to get started with RiskMP</p>
                <p><?php echo validation_errors(); ?></p>
                <div class='tab-section'>
                    <div style='clear:both;padding-top:15px;max-width:100%;'>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>First Name:</p> <input type="text" name="txt_first_name" required>
                        </div>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>Last Name:</p><input type="text" name="txt_last_name" required>
                        </div>
                    </div>
                    <div style='clear:both;padding-top:15px;max-width:100%;'>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>Email:</p><input type="email" name="txt_email" required>
                        </div>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>Username:</p><input type="text" name="txt_username" required
                                                                       pattern="^[a-zA-Z][a-zA-Z0-9-_\.]{1,20}$" title="Please choose a username between 4 and 20 characters containing only numbers, uppercase/lowercase letters, and periods/underscores.">
                        </div>
                    </div>
                    <div style='clear:both;padding-top:15px;max-width:100%;'>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>Password:</p><input type="password" name="txt_password" required>
                        </div>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>Re-enter Password:</p><input type="password" name="txt_password_conf" required>
                        </div>
                    </div>
                    <div style='clear:both;padding-top:15px;max-width:100%;'>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>Phone:</p><input type="tel" name="txt_phone" required>
                        </div>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>Company Name:</p><input type="text" name="txt_company_name">
                        </div>
                    </div>
                    <div style='clear:both;padding-top:15px;max-width:100%;'>
                        <div style='max-width:100%;float:left;'>
                            <p class='field-title'>License & Agreement:</p><input type="checkbox" name="agreement_acceptance" value='accepted' required> <a href="http://v1.riskmp.com/policy.html" target='_blank'> I have read the <u>agreement</u> and I accept it.</a><br>
                        </div>
                    </div>
                </div>
                <div style='text-align:center;padding-top:15px;padding-top:15px;clear:both;'>
                    <input type="submit" class="button button-flat-dblue button-large" value="Get Started"/>
                </div>            
                </form>
            </div>
        </div>
