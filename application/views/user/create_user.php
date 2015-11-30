<div id='page-content' class='sharp-page'>
    <div class="page-content-container">
        <div id='title'>
            <h1>New User</h1>
        </div>
        <div id='wizard' style='width:50%;margin-left:auto;margin-right:auto;background-color:#f2f2f2;min-height:400px;'>
            <?php
            echo validation_errors();
            echo form_open('user/create_form/');
            ?>
            <div class='toolbar'>
                <div class='toolbar-title'>New User</div>
            </div>
            <div class='tab-content' style='padding:10px;'>
                <p class='top-instruction'>Use this tool to add a new user to your account.</p>
                <div class='tab-section'>
                    <div style='clear:both;padding-top:15px;max-width:100%;'>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>First Name:</p> <input type="text" name="txt_first_name" required>
                        </div>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>Last Name:</p><input type="text" name="txt_last_name" required>
                        </div></div>
                    <div style='clear:both;padding-top:15px;max-width:100%;'>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>Email:</p><input type="email" name="txt_email" required>
                        </div>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>Username:</p><input type="text" name="txt_username" required
                                                                       pattern="^[a-zA-Z][a-zA-Z0-9-_\.]{1,20}$" title="Please choose a username between 4 and 20 characters containing only numbers, uppercase/lowercase letters, and periods/underscores.">
                        </div></div>
                    <div style='clear:both;padding-top:15px;max-width:100%;'>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>Password:</p><input type="password" name="txt_password" required>
                        </div>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>Re-enter Password:</p><input type="password" name="txt_password_conf" required>
                        </div></div>
                    <div style='max-width:100%;clear:both;padding-top:10px;'>
                        <p class='field-title'>User Type:</p>
                        <select id='user_type' name='user_type' required class='field'>
                            <option value="User">User</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div style='text-align:center;'>
                    <input type="submit" class="button button-flat-dblue button-large" value="Save"/>
                </div>            
                </form>
            </div>
        </div>

    </div>
</div>