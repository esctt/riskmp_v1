        <div id='page-content' class='sharp-page'>
            <div class="page-content-container">
<?php
echo form_open('project/do_import/' . $project_data['project_id']);
echo validation_errors();
?>
<div class='toolbar'>
    <div class='toolbar-title'>Import Tasks</div>
</div>
<div id='wizard' style='padding:10px;'>
    <p class="grey-text">This tool allows you to import tasks from a comma-delimited (.CSV) file.</p>
    <p class="grey-text">If you haven't already installed the RiskMP add-in to Microsoft Project, follow the instructions <a href='<?php echo base_url('pages/installing');?>'>here</a> before proceeding.</p>
    <p class='grey-text'>If you have set up the Risk MP add-in, then follow these steps to import your tasks to RiskMP:
        <br/>
        1. With your project open, select the RiskMP tab at the top of the Project Window<br/>
        2. Click the 'Export to RiskMP' button<br/>
        3. Right-click on the box below and click paste (or press Ctrl+V)<br/>
        4. Click Submit, and we'll take care of the rest. Wait for the page to reload with your task list.<br/>
    <p><textarea name="pasteddata" id="pasteddata" autofocus="true" cols="100" rows="20" required></textarea></p>
    <p class="field"><input type="submit" id="submit" value="Submit" name="submit" /></p>
</div>
</form>
</div>
</div>