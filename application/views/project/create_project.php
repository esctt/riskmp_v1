        <div id='page-content' class='sharp-page'>
            <div class="page-content-container">
<div id='title'>
    <h1>New Project</h1>
</div>
<div style='text-align:center;'>
    <div id='wizard' style='display:inline-block;background-color:#f2f2f2;'>
        <?php
        echo validation_errors();
        echo form_open('project/create_form');
        ?>
        <div class='toolbar'>
            <div class='toolbar-title'>Create Project</div>
        </div>
        <div class='tab-content' style='padding:10px;text-align:left;'>
            <div style='width:100%'>
                <p class='field-title'>Project Name:</p>
                <input type='text' id='project_name' name='project_name' maxlength='20'
                       required class='field' 
                       placeholder='Project Name'>
            </div>
            <div style='width:100%;text-align:center;'><input type="submit" value="Save" class="button button-flat-dblue button-large"></div>
        </div>
        </form>
    </div>
</div></div>
</div>