<div id='page-content' class='sharp-page'>
    <div class="page-content-container">
        <div id='title'>
            <h1>Account Information</h1>
        </div>
        <div id='wizard' style='width:500px;margin-left:auto;margin-right:auto;background-color:#f2f2f2;'>
            <?php
            echo validation_errors();
                echo form_open('user/edit_form/');
            ?>
            <div class='toolbar'>
                <div class='toolbar-title'>Account Information</div>
            </div>
            <div class='tab-content' style='padding:10px;'>
                <div class='tab-section'>
                    <div style='clear:both;padding-top:15px;max-width:100%;'>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>First Name:</p> <input type="text" name="txt_first_name" required value='<?php echo $user_data['first_name']; ?>'>
                        </div>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>Last Name:</p><input type="text" name="txt_last_name" required value='<?php echo $user_data['last_name']; ?>'>
                        </div></div>
                    <div style='clear:both;padding-top:15px;max-width:100%;'>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>Email:</p><input type="email" name="txt_email" required value='<?php echo $user_data['email']; ?>'>
                        </div>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>Username:</p>
                            <input type="text" name="txt_username" required value='<?php echo $user_data['username']; ?>'
                                   pattern="^[a-zA-Z][a-zA-Z0-9-_\.]{1,20}$" title="Please choose a username between 4 and 20 characters containing only numbers, uppercase/lowercase letters, and periods/underscores.">
                        </div>
                        <div style='clear:both;padding-top:15px;max-width:100%;'>
                            <div style='max-width:50%;float:left;'>
                                <p class='field-title'>Company Name:</p> <input type="text" name="txt_company_name" required value='<?php echo $user_data['company_name']; ?>'>
                            </div>
                            <div style='max-width:50%;float:left;'>
                                <p class='field-title'>Phone Number:</p><input type="tel" name="txt_phone" required value='<?php echo $user_data['phone']; ?>'>
                            </div>
                        </div>
                        <div style='width:100%;clear:both;height:20px;'>
                        </div>
                        <div style='text-align:center;width:100%;'>
                            <input type="submit" class="button button-flat-dblue button-large" value="Save"/>
                        </div>            
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>