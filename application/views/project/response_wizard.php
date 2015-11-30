<div id='page-content' class='sharp-page'>
    <div class="page-content-container">
        <?php
        /*
         * Response Wizard view contains a form for creating and editing responses.
         * Throughout the form, checks are made to variable $editmode to check whether
         * the user is editing a response. If so, the value is loaded. Note that $editmode
         * is boolean, and obtains its value at the beginning of the program by evaluating
         * whether the $response_data associative array has been sent to the view.
         * 
         * An associative array $risk_data must be sent to the view. This must contain the 
         * properties of the task for which this risk is identified. This is used to display the
         * risk statement, and to send the correct parameter to the form if creating a new response.
         */
        ?>
        <div id='title'>
            <h1><?php echo $title; ?></h1>
        </div>
        <div class='breadcrumb-container'>
            <p id='breadcrumb'>
                <a href='<?php echo base_url('dashboard'); ?>'>Dashboard</a>
                <?
                $bcrumbsource;
                if ($edit_mode) {
                    $bcrumbsource = $response_data;
                } else {
                    $bcrumbsource = $risk_data;
                }
                ?>
                <a href='<?php echo base_url('project/view/' . $bcrumbsource['project_id']); ?>'><?php echo $bcrumbsource['project_name']; ?></a>
                <a href='<?php echo base_url('task/view/' . $bcrumbsource['task_id']); ?>'><?php echo $bcrumbsource['task_name']; ?></a>
                <a href='<?php echo base_url('risk/view/' . $bcrumbsource['risk_id']); ?>'><?php echo "Risk Analysis"; ?></a>
<?php if ($edit_mode) echo "<a href='" . base_url('response/view/' . $bcrumbsource['response_id']) . "'>Response Planning</a>" ?>
            </p>
        </div>
        <div id='wizard' style='width:90%;margin-left:auto;margin-right:auto;background-color:#f2f2f2;min-height:600px;'>
            <?php
            echo validation_errors();
            $vals_loaded = isset($response_data);
            //load appropriate form from the controller
            if ($edit_mode) {
                echo form_open('response/edit_form/' . $response_data['response_id']);
            } else {
                echo form_open('response/create_form/' . $risk_data['risk_id']);
            }
            ?>
            <div class='toolbar'>
                <div class='toolbar-title'>Plan</div>
            </div>
            <div class='tab-content' style='padding:10px;position:relative;'>
                <div style='width:60%;float:left;'>
                    <p class='grey-text' style='width:100%;'>Risk Statement:
                      <?php if ($edit_mode) echo $response_data['risk_statement'];
                      else echo $risk_data['risk_statement']; ?>
                    </p>
                    <p class='grey-text' style='width:100%;'>Risk Cost Impact:
                      <?php echo $risk_data['cost_impact']; ?>
                    </p>
                    <p class='field-title'>Action:</p>
                    <div id='action-slider-container' style='width:100%;'>
                        <input type='range' id='rng_action' name='rng_action'
                               min='0' max='4' value='<?php
                               if ($vals_loaded) {
                                   switch ($response_data['action']) {
                                       case "Pursue":
                                           echo 0;
                                           break;
                                       case "Accept":
                                           echo 1;
                                           break;
                                       case "Mitigate":
                                           echo 2;
                                           break;
                                       case "Transfer":
                                           echo 3;
                                           break;
                                       case "Avoid":
                                           echo 4;
                                           break;
                                   }
                               } else {
                                   echo 0;
                               }
                               ?>' style='width:99%' onchange='update_rng_action();'>
                    </div>
                    <div id='lbl_action0' class="field-title" style="color:#336699;width:15%;float:left;text-align:left;">Pursue</div>
                    <div id='lbl_action1' class="field-title" style="color:#336699;width:20%;float:left;text-align:center;">Accept</div>
                    <div id='lbl_action2' class="field-title" style="color:#336699;width:30%;float:left;text-align:center;">Mitigate</div>
                    <div id='lbl_action3' class="field-title" style="color:#336699;width:20%;float:left;text-align:center;">Transfer</div>
                    <div id='lbl_action4' class="field-title" style="color:#336699;width:15%;float:left;text-align:right;">Avoid</div>
                    <div style="width:100%;height:5px;clear:both;"></div>
                    <p class='field-title'>Action Plan:</p>
                    <textarea id='action_plan' name='action_plan' required class='field'
                              style='width:100%;resize:none;margin:0;' rows='5' maxlength='250'
                              placeholder='In 250 characters or less, record your action plan here.'
                              ><?php if ($vals_loaded) echo $response_data['action_plan']; ?></textarea>
                    <div style='width:100%;'>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>Owner:</p>
                            <input type='text' id='owner' name='owner' maxlength='20' required class='field' 
                                   placeholder='Response owner' 
<?php if ($vals_loaded) echo " value='" . $response_data['owner'] . "'"; ?>>
                        </div>
                        <div style='max-width:50%;float:right;'>
                            <p class='field-title'>Date of Plan:</p>
                            <input type='date' id ='date_of_plan' name='date_of_plan'
                                   maxlength='20' required class='field'
                                   onchange='update_days_open();'
<?php if ($vals_loaded) echo " value='" . $response_data['date_of_plan'] . "'";
else echo " value='" . date('Y-m-d') . "'"; ?>>
                        </div>
                    </div>
                    <div style='clear:both;padding-top:15px;width:100%;'>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>Release Progress:</p>
                            <select id='release_progress' name='release_progress' required
                                    class='field' <?php if ($vals_loaded) echo " value='" . $response_data['release_progress'] . "'"; ?>>
                                <option value="Planning">Planning</option>
                                <option value="Ongoing">Ongoing</option>
                                <option value="Complete">Complete</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div style='max-width:50%;float:right;'>
                            <p class='field-title'>Planned Closure:</p>
                            <input type='date' id ='planned_closure' name='planned_closure' type='text' 
                                   maxlength='20' required class='field'
<?php if ($vals_loaded) echo " value='" . $response_data['planned_closure'] . "'"; ?>>
                        </div>
                    </div>
                    <div style='clear:both;padding-top:15px;width:100%;'>
                        <div style='max-width:50%;float:left;'>
                            <p class='field-title'>Cost:</p>
                            $<input id='cost' name='cost' type='number' min='0' max='999999999'
                                    value='<?php
                                    if ($vals_loaded)
                                        echo $response_data['cost'];
                                    else
                                        echo '0';
                                    ?>' step='1' class='field'>
                        </div>
                        <div style='max-width:50%;float:right;'>
                            <p class='field-title'>Post Response $:</p>
                            $<input id='post_response' name='post_response' type='number' min='0' max='999999999'
                                    value='<?php
                                    if ($vals_loaded)
                                        echo $response_data['post_response'];
                                    else
                                        echo '0';
                                    ?>' step='1' class='field'>
                        </div>
                    </div>
                    <div style='width:100%;clear:both;padding-top:10px;'>
                        <p class='field-title'>Current Status:</p>
                        <textarea id='current_status' name='current_status' required class='field'
                                  style='width:100%;resize:none;margin:0;' rows='5' maxlength='250'
                                  placeholder='In 250 characters or less, record the current status of the response here.'
                                  ><?php if ($vals_loaded) echo $response_data['current_status']; ?></textarea>
                    </div>
                </div>
                <div id='plan-image' style='float:left;width:40%;text-align:center;height:750px;
                     background:url("<?php echo base_url('assets/images/plan.png'); ?>") no-repeat center center;'>
                    <p id='days_open' class='field-title' style='margin-right:0;'>Days open: 0</p>
                </div><div style='clear:both;text-align:center;width:100%;'><input type="submit" class="button button-flat-dblue button-large" value="Save"/></div>
            </div>
            </form>
        </div>
    </div>
</div>
<script type='text/javascript'>
    function update_days_open() {
        var date_of_plan = new Date(document.getElementById('date_of_plan').value);
        var date_now = new Date().getTime();
        var days_open = Math.round((date_now - date_of_plan) / 86400000);
        document.getElementById('days_open').innerHTML = "Days open: " + days_open;
    }
</script>