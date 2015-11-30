        <div id='page-content' class='sharp-page'>
            <div class="page-content-container">
<div id='title'>
    <h1><?php echo $title; ?></h1>
</div>
<div class='breadcrumb-container'>
    <p id='breadcrumb'>
        <a href='<?php echo base_url('dashboard'); ?>'>Dashboard</a>
        <a href='<?php echo base_url('project/view/' . $response_data['project_id']); ?>'><?php echo $response_data['project_name']; ?></a>
        <a href='<?php echo base_url('task/view/' . $response_data['task_id']); ?>'><?php echo $response_data['task_name']; ?></a>
        <a href='<?php echo base_url('risk/view/' . $response_data['risk_id']); ?>'><?php echo "Risk Analysis"; ?></a>
        <?php echo "<a href='".base_url('response/view/'.$response_data['response_id'])."'>Response Planning</a>" ?>
    </p>
</div>
<div id='wizard' style='width:90%;margin-left:auto;margin-right:auto;background-color:#f2f2f2;'>
    <?php
    echo validation_errors();
    echo form_open('response/update_form/' . $response_data['response_id']);
    ?>
    <div class='toolbar'>
        <div class='toolbar-title'>Plan</div>
    </div>
    <div class='tab-content' style='padding:10px;position:relative;'>
        <div style='width:60%;float:left;'>
            <div style='width:100%;'>
                <div style='max-width:50%;float:left;'>
                    <p class='field-title'>Owner:</p>
                    <input type='text' id='owner' name='owner' maxlength='20' required class='field' 
                           placeholder='Response owner' value='<?php echo $response_data['owner']; ?>'>
                </div>
                <div style='max-width:50%;float:right;'>
                    <p class='field-title'>Cost:</p>
                    $<input id='cost' name='cost' type='number' min='0' max='999999999'
                            value='<?php echo round($response_data['cost']); ?>' step='1' class='field'>
                </div>
            </div>
            <div style='clear:both;padding-top:15px;width:100%;'>
                <div style='max-width:50%;float:left;'>
                    <p class='field-title'>Release Progress:</p>
                    <select id='release_progress' name='release_progress' required
                            class='field' value='<?php echo $response_data['release_progress']; ?>'>
                        <option value="Planning">Planning</option>
                        <option value="Ongoing">Ongoing</option>
                        <option value="Complete">Complete</option>
                        <option value="Complete">Cancelled</option>
                    </select>
                </div>
                <div style='max-width:50%;float:right;'>
                    <p class='field-title'>Planned Closure:</p>
                    <input type='date' id ='planned_closure' name='planned_closure' type='text' 
                           maxlength='20' class='field' required
                           value='<?php echo $response_data['planned_closure']; ?>'>
                </div>
            </div>
            <div style='width:100%;clear:both;padding-top:10px;float:left;' >
                    <p class='field-title'>Post Response $:</p>
                    $<input id='post_response' name='post_response' type='number' min='0' max='999999999'
                            value='<?php echo round($response_data['post_response']); ?>' step='1' class='field'>
            </div>
            <div style='width:100%;clear:both;padding-top:10px;'>
                <p class='field-title'>Current Status:</p>
                <textarea id='current_status' name='current_status' required class='field'
                          style='width:100%;resize:none;margin:0;' rows='5' maxlength='250'
                          placeholder='In 250 characters or less, record the current status of the response here.'
                          ><?php echo $response_data['current_status']; ?></textarea>
            </div>
        </div>
        <div style='clear:both;text-align:center;width:100%;'><input type="submit" class="button button-flat-dblue button-large" value="Save"/></div>
    </div>
</form>
</div>
                </div>
</div>