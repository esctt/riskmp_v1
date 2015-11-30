<div class="window" id="login" style="margin-top:150px;opacity:0.9;">
    <?php
    echo form_open('user/accepted_agreement/');
    ?>
    <div class="toolbar">
        <div class="toolbar-title">User Agreement</div>
    </div>
    <div style="padding:0px 20px 5px;">
        <p class='field-title'>License & Agreement:</p><input type="checkbox" name="agreement_acceptance" value="accepted" required> <a href="http://v1.riskmp.com/policy.html" target='_blank'> I have read the <u>agreement</u> and I accept it.</a><br>
        <p class="submit"><input type="submit" class="button button-flat-dblue button-large" value="Continue"></p>
    </div>
</form>
</div>
