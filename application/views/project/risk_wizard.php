<div id='page-content' class='sharp-page'>
    <div class="page-content-container">
        <?php
        /*
         * Risk Wizard view contains a form for identifying and editing risks.
         * Throughout the form, checks are made to variable $editmode to check whether
         * the user is editing a risk. If so, the value is loaded. Note that $editmode
         * is boolean, and obtains its value at the beginning of the program by evaluating
         * whether the $risk_data associative array has been sent to the view.
         * 
         * An associative array $task_data must be sent to the view. This must contain the 
         * properties of the task for which this risk is identified. They are loaded into a table
         * below.
         */
        ?>
        <div id='title'>
            <h1>Risk Identification</h1>
        </div>
        <div class='breadcrumb-container'>
            <p id='breadcrumb'>
                <a href='<?php echo base_url('dashboard'); ?>'>Dashboard</a>
                <a href='<?php echo base_url('project/view/' . $task_data['project_id']); ?>'><?php echo $task_data['project_name']; ?></a>
                <a href='<?php echo base_url('task/view/' . $task_data['task_id']); ?>'><?php echo $task_data['task_name']; ?></a>
                <?php if ($edit_mode) echo "<a href='" . base_url('risk/view/' . $risk_data['risk_id']) . "'>Risk Analysis</a>" ?>
            </p>
        </div>
        <div id='wizard'>
            <?php
            echo validation_errors();
            $vals_loaded = isset($risk_data);
            //load appropriate form from the controller
            if ($edit_mode)
                echo form_open('risk/edit_wizard_form/' . $risk_data['risk_id']);
            else
                echo form_open('risk/wizard_form/' . $task_data['task_id']);
            ?>
            <div id="tabs" style='width:90%;margin-left:auto;margin-right:auto;'>
                <style scoped>
                    .tab-content {
                        padding:10px;
                    }
                    .tab-section {
                        padding-left:10px;
                        padding-right:10px;
                    }
                </style>
                <ul>
                    <li><a href='#tabs-start'>Start</a></li>
                    <li><a href='#tabs-step1'>Step 1</a></li>
                    <li><a href='#tabs-step2'>Step 2</a></li>
                    <li><a href='#tabs-step3'>Step 3</a></li>
                    <li><a href='#tabs-step4'>Step 4</a></li>
                    <li><a href='#tabs-step5'>Step 5</a></li>
                    <li><a href='#tabs-step6'>Step 6</a></li>
                </ul>
                <div id='tabs-start' class='tab'>
                    <div class='toolbar'>
                        <div class='toolbar-title'>Start</div>
                    </div>
                    <div class='tab-content'>
                        <p class="top-instruction">Welcome to the Risk Identification Wizard!<br><br>
                            This tool will guide you through the process of identifying a risk.</p>
                        <div id='type' class='tab-section' style='margin-right:35%;'>
                            <p class='field-title'>Risk Type:</p>
                            <select id='risk_type' name='risk_type' required class='field'
                                    <?php if ($vals_loaded) echo "value='" . $risk_data['type'] . "'" ?>>
                                <option value="Threat">Threat</option>
                                <option value="Opportunity">Opportunity</option>
                            </select>
                            <p class='field-description' style="display:block;">Not all risks are bad. In fact, sometimes risks can
                                be positive. When a risk is positive, we refer to it as an opportunity. If a risk
                                is negative, then it is a threat. Please choose the type of risk you are identifying above.</p>
                        </div>
                        <div style='text-align:center;'><a href="#" class="button btnNext  button-flat-dblue button-large">Next</a></div>
                    </div>
                </div>
                <div id='tabs-step1' class='tab' style='min-height:850px;'>
                    <div class='toolbar'>
                        <div class='toolbar-title'>Step 1: Risk Statement</div>
                    </div>
                    <div class='tab-content'>
                        <div style="width:100%;background-color:#FFFFFF;" class="jTableContainer">
                            <div class="jtable-main-container">
                                <table class="jtable">
                                    <thead>
                                        <tr>
                                            <th class="jtable-column-header">
                                    <div class="jtable-column-header-container"><span class="jtable-column-header-text">WBS</span></div>
                                    </th>
                                    <th class="jtable-column-header"><div class="jtable-column-header-container"><span class="jtable-column-header-text">Task Name</span></div></th>
                                    <th class="jtable-column-header"><div class="jtable-column-header-container"><span class="jtable-column-header-text">Duration</span></div></th>
                                    <th class="jtable-column-header"><div class="jtable-column-header-container"><span class="jtable-column-header-text">Work</span></div></th>
                                    <th class="jtable-column-header"><div class="jtable-column-header-container"><span class="jtable-column-header-text">Start</span></div></th>
                                    <th class="jtable-column-header"><div class="jtable-column-header-container"><span class="jtable-column-header-text">Finish</span></div></th>
                                    <th class="jtable-column-header"><div class="jtable-column-header-container"><span class="jtable-column-header-text">Fixed Cost</span></div></th>
                                    <th class="jtable-column-header"><div class="jtable-column-header-container"><span class="jtable-column-header-text">Cost</span></div></th>
                                    <th class="jtable-column-header"><div class="jtable-column-header-container"><span class="jtable-column-header-text">Price</span></div></th>
                                    <th class="jtable-column-header"><div class="jtable-column-header-container"><span class="jtable-column-header-text">Resources</span></div></th>
                                    <th class="jtable-column-header"><div class="jtable-column-header-container"><span class="jtable-column-header-text">Supplier</span></div></th>
                                    </tr>   
                                    </thead>
                                    <tbody>
                                        <tr class="jtable-data-row jtable-row-even">
                                            <td><?php echo $task_data['WBS']; ?></td>
                                            <td><?php echo $task_data['task_name']; ?></td>
                                            <td><?php echo $task_data['duration']; ?></td>
                                            <td><?php echo $task_data['work']; ?></td>
                                            <td><?php echo $task_data['start_date']; ?></td>
                                            <td><?php echo $task_data['finish_date']; ?></td>
                                            <td><?php echo $task_data['fixed_cost']; ?></td>
                                            <td><?php echo $task_data['cost']; ?></td>
                                            <td><?php echo $task_data['price']; ?></td>
                                            <td><?php echo $task_data['resource_names']; ?></td>
                                            <td><?php echo $task_data['vendor']; ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div>
                            <div class='right-graphic'>
                                <div class='graphic-title'>sources of risk</div>
                                <ul class='graphic-list'>
                                    <li class='element'>scope</li>
                                    <ul>
                                        <li>Task</li>
                                    </ul>

                                    <li class='element'>time</li>
                                    <ul>
                                        <li>Duration</li>
                                        <li>Work</li>
                                        <li>Start Date</li>
                                        <li>Finish Date</li>
                                    </ul>
                                    <li class='element'>resources</li>
                                    <ul>
                                        <li>Equipment</li>
                                        <li>Materials</li>
                                        <li>Crews</li>
                                        <li>Subcontractors</li>
                                        <li>Staff</li>
                                        <li>Vendors</li>
                                    </ul>
                                    <li class='element'>costs</li>
                                    <ul>
                                        <li>Fixed Costs</li>
                                        <li>Actual Costs</li>
                                        <li>Reserves</li>
                                    </ul>
                                </ul>
                            </div>
                            <p class='top-instruction'>Next, we identify the key elements of the risk</p>
                            <div class='tab-section' style='margin-right:45%;'>
                                <p class='field-title'>Event:</p>
                                <textarea id='event' name='event' maxlength='250' class="field" 
                                          style='width:100%;resize:none;' rows='4' 
                                          wrap='soft' onchange='update_risk_statement();'
                                          placeholder="Describe the risk event here."><?php if ($vals_loaded) echo $risk_data['event'] ?></textarea>
                                <p class='field-description'>The risk event is the occurrence about which we are concerned.
                                    Examples of events may be "Contractor strike".  In 250 characters or less, describe
                                    the risk event in the box above.</p>
                            </div>
                            <div class='tab-section' style='margin-right:45%;'>
                                <p class='field-title'>Impact:</p>
                                <textarea id='impact' name='impact' maxlength='250' class="field" 
                                          style='width:100%;resize:none;' rows='4' 
                                          wrap='soft' onchange='update_risk_statement();'
                                          placeholder="Describe the risk impact here."><?php if ($vals_loaded) echo $risk_data['impact'] ?></textarea>
                                <p class='field-description'>The risk impact is the consequence of the risk event.
                                    For instance, consequences for our previous Risk Event examples may be "Delay in start".
                                    In 250 characters or less, describe the Risk Impact in the
                                    box above.</p>
                            </div>
                            <div class='tab-section' style='margin-right:45%;'>
                                <p class='field-title'>Date of Concern:</p>
                                <input id='date_of_concern' name='date_of_concern' onchange="update_risk_statement();" 
                                       class="field" type="date" style='width:100%;'
                                       <?php if ($vals_loaded) {echo "value='" . $risk_data['date_of_concern'] . "'";} else {echo "value='" . $task_data['start_date'] . "'";} ?>>
                                <p class='field-description'>Lastly, use the calendar tool above to 
                                    identify the date on which the risk is most likely to occur. The default value is the task's start date.</p>
                            </div>
                            <br/>
                            <br/>
                            <div class='tab-section' style='margin-right:45%;'>
                                <p class='field-title'>Risk Statement:</p>
                                <textarea id='risk_statement' maxlength='250' class="field"
                                          style='width:100%;resize:none;background-color:#FFFFFF;' 
                                          rows='4' wrap='soft' disabled="true" 
                                          placeholder='The resultant risk statement is generated for you.'></textarea>
                                <p class='field-description'>The resultant risk statement is generated for you by combining the
                                    key elements of the risk into a statement: If (Event) by (Date of Concern) then (Impact).</p>
                            </div>
                        </div>
                        <div style='text-align:center;'><a href="#" class="button btnNext  button-flat-dblue button-large">Next</a></div>
                    </div>
                </div>
                <div id='tabs-step2' class='tab'>
                    <div class='toolbar'>
                        <div class='toolbar-title'>Step 2: Preliminary Assessment</div>
                    </div>
                    <div class='tab-content' style='position:relative;'>
                        <p class='top-instruction'>Let's do a preliminary assessment of our risk</p>
                        <div id='graphic' style='width:35%;position:absolute;right:0;margin-right:1%;padding:10px;'>
                            Total Impact: <label id="lbl_total_impact"></label>
                            <table id='action-table' cellspacing="10">
                                <tr>
                                    <td id="low_high">Low Impact - High Probability<br/><strong>Action: Monitor</strong></td>
                                    <td id="high_high">High Impact - High Probability<br/><strong>Action: Proceed</strong></td>
                                </tr>
                                <tr>
                                    <td id="low_low">Low Impact - Low Probability<br/><strong>Action: Pass</strong></td>
                                    <td id="high_low">High Impact - Low Probability<br /><strong>Action: Monitor</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class='tab-section'>
                            <p class='field-title'>Impact Effect:</p>
                            <div class='range-slider-container' style='width:60%'>
                                <input type='range' id='rng_impact_effect' name='rng_impact_effect'
                                       min='0' max='100' value='<?php
                                       if ($vals_loaded)
                                           echo $risk_data['impact_effect'];
                                       else
                                           echo '0';
                                       ?>' style='width:99%' onchange='update_rng_impact_effect();'>
                                <output for='rng_impact_effect' class='range-output' onforminput='value = rng_impact_effect.valueAsNumber;'</output>
                            </div>
                            <label id='lbl_impact_effect'></label>
                            <p class='field-description' style='display:block;'>On a scale of 0 to 100, rank the effect that the risk will have.</p>
                        </div>
                        <div class='tab-section'>

                            <p class='field-title'>Cost Impact:</p>
                            <div class='range-slider-container' style='width:60%'>
                                <input type='range' id='rng_cost_impact' name='rng_cost_impact'
                                       min='0' max='100' value='0' style='width:99%' onchange='update_rng_cost_impact();'>
                            </div>
                            <label id='lbl_cost_impact'></label>
                            <p class='field-description' style='display:block;'>Rank the cost impact as low, medium or high.</p>
                        </div>
                        <div class='tab-section'>

                            <p class='field-title'>Impact - Days Delay:</p>
                            <div class='range-slider-container' style='width:60%'>
                                <input type='range' id='rng_days_delay' name='rng_days_delay'
                                       min='0' max='100' value='0' style='width:99%' onchange='update_rng_days_delay();'>
                            </div>
                            <label id='lbl_days_delay'></label>
                            <p class='field-description' style='display:block;'>Days delay to critical path.</p>
                        </div>
                        <div class='tab-section'>
                            <p class='field-title'>Probability:</p>
                            <div class='range-slider-container' style='width:60%'>
                                <input type='range' id='rng_probability' name='rng_probability'
                                       min='0' max='100' value='<?php
                                       if ($vals_loaded)
                                           echo $risk_data['probability'];
                                       else
                                           echo '0';
                                       ?>' style='width:99%' onchange='update_rng_probability();'>
                                <output for='rng_probability' class='range-output' onforminput='value = rng_probability.valueAsNumber;'</output>
                            </div>
                            <label id='lbl_probability'></label>
                            <p class='field-description' style='display:block;'>On a scale of 0% to 100%, rank probability that the risk will occur.</p>
                        </div>
                        <div style='text-align:center;'><a href="#" class="button btnNext  button-flat-dblue button-large">Next</a></div>
                    </div>
                </div>
                <div id='tabs-step3' class='tab'>
                    <div class='toolbar'>
                        <div class='toolbar-title'>Step 3: Quantitative Impact Assessment</div>
                    </div>

                    <div class='tab-content'>
                        <p class='top-instruction'>Now let's get some numbers</p>
                        <div class='tab-section'>
                            <p class='field-title'>Impact Effect:</p>
                            &nbsp;&nbsp;<input id='txt_impact_effect' name='txt_impact_effect' type='number' min='0' max='100'
                                               value='<?php
                                               if ($vals_loaded)
                                                   echo $risk_data['impact_effect'];
                                               else
                                                   echo '0';
                                               ?>' onchange='update_txt_impact_effect();' class='field'
                                               style='width:15%;'>
                            <p class='field-description'>We've carried over the impact effect number you gave us in the last step.
                                Feel free to adjust this number to determine a more accurate value based on the guidelines below:
                                <br><br>
                                An assignment of 0 indicates no impact on company reputation, 
                                the client and/or future business endeavours.  An impact effect of 100 represents a severe
                                impact. Experienced risk managers will assign a number based on past projects.</p>
                        </div>
                        <div class='tab-section'>
                            <p class='field-title'>Impact - Days Delay:</p>
                            <input id='txt_days_delay' name='txt_days_delay' type='number' min='-999999999' max='999999999'
                                    value='<?php
                                    if ($vals_loaded)
                                        echo $risk_data['days_delay'];
                                    else
                                        echo '0';
                                    ?>' step='1' class='field' onchange='update_txt_days_delay();'
                                    style='width:15%;'>
                            <p class='field-description'> Estimate the delay in days to the critical path or to the successor of this task. 
                            This will allow you to record and compare the impact to the project schedule.</p>
                        </div>
                        <div class='tab-section'>
                            <p class='field-title'>Cost Impact:</p>
                            $<input id='txt_cost_impact' name='txt_cost_impact' type='number' min='-999999999' max='999999999'
                                    value='<?php
                                    if ($vals_loaded)
                                        echo $risk_data['cost_impact'];
                                    else
                                        echo '0';
                                    ?>' step='1' class='field' onchange='update_txt_cost_impact();'
                                    style='width:15%;'>
                            <p class='field-description'> We also need an estimated dollar value for the cost impact of the risk. 
                                The Cost Impact of a risk is often difficult to predict.
                                Use project costs (labour and materials) from the Task definition as a guideline. 
                                It also helps to refer to contract documents, reserves, and company policy to assist you in estimating a number</p>
                        </div>
                        <div style='text-align:center;'><a href="#" class="button btnNext  button-flat-dblue button-large">Next</a></div>
                    </div>
                </div>
                <div id='tabs-step4' class='tab'>
                    <div class='toolbar'>
                        <div class='toolbar-title'>Step 4: Quantitative Probability Assessment</div>
                    </div>
                    <div class='tab-content' style='min-height:620px;'>
                        <div class='right-graphic'>
                            <div class='graphic-title'>probability guidelines</div>
                            <ul class='graphic-list'>
                                <li class='element'>High</li>
                                <ul>
                                    <li>Risk event very likely</li>
                                    <li>High Probability - 70-100%</li>
                                    <li>Example: Increasing moisture in the basement 
                                        is a good indication of a leak.</li>
                                </ul>

                                <li class='element'>Medium</li>
                                <ul>
                                    <li>Risk event likely</li>
                                    <li>40-70% Probability</li>
                                    <li>Example: In weather forecasts: P.O.P. (Probability of Precipitation) - 55%</li>
                                </ul>
                                <li class='element'>Low</li>
                                <ul>
                                    <li>Risk event unlikely</li>
                                    <li>Low Probability - 0-40%</li>
                                    <li>Example: The Leafs making the playoffs.</li>
                                </ul>
                            </ul>
                        </div>
                        <p class='top-instruction'>Let's figure out a more accurate probability</p>
                        <div class='tab-section' style ='margin-right:40%;'>
                            <p class='field-title'>Probability:</p>
                            <input id='txt_probability' name='txt_probability' type='number' min='0' max='100'
                                   value='<?php
                                   if ($vals_loaded)
                                       echo $risk_data['probability'];
                                   else
                                       echo '0';
                                   ?>' onchange='update_txt_probability();' class='field'
                                   style='width:15%;'>%
                            <p class='field-description' style='display:block;'>We've carried over the number you gave us for probability in
                                step 2. Refer to the graphic on the right side to assist you in coming up with a more accurate number.</p>
                        </div>
                        <div style='text-align:center;'><a href="#" class="button btnNext  button-flat-dblue button-large">Next</a></div>
                    </div>
                </div>
                <div id='tabs-step5' class='tab'>
                    <div class='toolbar'>
                        <div class='toolbar-title'>Step 5: Calculated values</div>
                    </div>
                    <div class='tab-content'>
                        <p class='top-instruction'>Let's do some calculations</p>
                        <label class='word-calculation'>Impact Effect x Probability = Overall Impact</label>
                        <br/>
                        <br/>
                        <label class='calculation' id='lbl_calc_impact_effect'>0</label>
                        <label class='calculation'> x </label>
                        <label class='calculation' id ='lbl_calc_probability_1'>0</label>
                        <label class='calculation'>%</label>
                        <label class='calculation'> = </label>
                        <label class='calculation' id='lbl_overall_impact'>0</label><br/>
                        <br/><br/>
                        <br/>
                        <label class='word-calculation'>'Impact - Days Delay' x Probability = Expected Delay</label>
                        <br/>
                        <br/>
                        <label class='calculation' id='lbl_calc_days_delay'>0</label>
                        <label class='calculation'> x </label>
                        <label class='calculation' id ='lbl_calc_probability_3'>0</label>
                        <label class='calculation'>%</label>
                        <label class='calculation'> = </label>
                        <label class='calculation' id='lbl_expected_delay'>0</label><br/>
                        <br/><br/>
                        <br/>
                        <label class='word-calculation'>Cost Impact x Probability = Expected Cost</label>
                        <br/>
                        <br/>
                        <label class='calculation'>$</label>
                        <label class='calculation' id='lbl_calc_cost_impact'>0.00</label>
                        <label class='calculation'> x </label>
                        <label class='calculation' id ='lbl_calc_probability_2'>0</label>
                        <label class='calculation'>%</label>
                        <label class='calculation'> = </label>
                        <label class='calculation'>$</label>
                        <label class='calculation' id='lbl_expected_cost'>0.00</label><br/>
                        <div style='text-align:center;'><a href="#" class="button btnNext  button-flat-dblue button-large">Next</a></div>
                    </div>
                </div>
                <div id='tabs-step6' class='tab'>
                    <div class='toolbar'>
                        <div class='toolbar-title'>Step 6: Impact Discussion</div>
                    </div>
                    <div class='tab-content'>
                        <p class='top-instruction'>We'll finish by recording some additional notes</p>
                        <div class='tab-section'>
                            <p class='field-title'>Impact Discussion:</p>
                            <textarea id='impact_discussion' name='impact_discussion' maxlength='250' class="field" 
                                      style='width:100%;resize:none;' rows='6' 
                                      wrap='soft' onkeyup='update_risk_statement();'
                                      placeholder="Record your impact discussion notes here."><?php if ($vals_loaded) echo $risk_data['impact_discussion'] ?></textarea>
                            <p class='field-description'>Record any additional notes about the risk in the impact discussion box above.</p>
                        </div>
                        <div class='tab-section'>
                            <p class='field-title'>Save</p>
                            <p class='field-description' style='display:block;width:60%'>When you're ready,
                                click the save button to save this new risk.<br/><br/>After you click save, we'll compare this risk to every
                                other risk in the project and assign "Priority Effect" and "Priority Monetary"
                                values to it so you know which risks you should focus on first.</p><br/>
                        </div>
                        <div style='text-align:center;'>
                            <input type="submit" class="button button-flat-dblue button-large" value="Save"/>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>
<script>
                                              $(function() {
                                                  $("#tabs").tabs();
                                                  $(".btnNext").click(function() {
                                                      $("#tabs").tabs("option", "active", $("#tabs").tabs('option', 'active') + 1);
                                                  });
                                                  $(".btnPrev").click(function() {
                                                      $("#tabs").tabs("option", "active", $("#tabs").tabs('option', 'active') - 1);
                                                  });
                                              });
</script>

<script type ="text/javascript">
    <?php
        if ($edit_mode) {?>
            $(document).ready(function() {
                update_txt_impact_effect();
                update_txt_cost_impact();
                update_txt_days_delay();
            });
        <?php
        }
    ?>
    function update_txt_impact_effect() {
        var value = document.getElementById('txt_impact_effect').value;
        var rng = document.getElementById('rng_impact_effect');
        rng.value = value;
        document.getElementById('lbl_calc_impact_effect').innerHTML = value;
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent("change", false, true);
        rng.dispatchEvent(evt);
        var lbl = document.getElementById('lbl_impact_effect');
        if (value <= 33) {
            lbl.innerHTML = 'Low';
        } else if (value <= 66) {
            lbl.innerHTML = 'Medium';
        } else {
            lbl.innerHTML = 'High';
        }
        calculate_overall_impact();
        update_total_impact();
    }

    function update_rng_impact_effect() {
        var value = document.getElementById('rng_impact_effect').value;
        document.getElementById('txt_impact_effect').value = value;
        document.getElementById('lbl_calc_impact_effect').innerHTML = value;
        var lbl = document.getElementById('lbl_impact_effect');
        if (value <= 33) {
            lbl.innerHTML = 'Low';
        } else if (value <= 66) {
            lbl.innerHTML = 'Medium';
        } else {
            lbl.innerHTML = 'High';
        }
        calculate_overall_impact();
        update_total_impact();
    }

    function update_rng_cost_impact() {
        var value = document.getElementById('rng_cost_impact').value;
        var lbl = document.getElementById('lbl_cost_impact');
        if (value <= 33) {
            lbl.innerHTML = 'Low';
        } else if (value <= 66) {
            lbl.innerHTML = 'Medium';
        } else {
            lbl.innerHTML = 'High';
        }
        update_total_impact();
    }

    function update_txt_cost_impact() {
        var value = document.getElementById('txt_cost_impact').value;
        document.getElementById('lbl_calc_cost_impact').innerHTML = value;
        calculate_expected_cost();
    }

    function update_total_impact() {
        var values = new Array();
        values[0] = document.getElementById('rng_cost_impact').value;
        values[1] = document.getElementById('rng_impact_effect').value;
        var avg = (values[0] + values[1]) / 2;
        if (avg < 50) {
            document.getElementById('lbl_total_impact').innerHTML = "Low";
        } else {
            document.getElementById('lbl_total_impact').innerHTML = "High";
        }
        update_action();
    }

    function update_action() {
        //reset all colors
        document.getElementById("low_low").style.backgroundColor = "#336699";
        document.getElementById("low_high").style.backgroundColor = "#336699";
        document.getElementById("high_low").style.backgroundColor = "#336699";
        document.getElementById("high_high").style.backgroundColor = "#336699";
        var impact = document.getElementById('lbl_total_impact').innerHTML;
        var probability = document.getElementById('rng_probability').value;
        if (impact == 'Low') {
            if (probability < 50) {
                document.getElementById("low_low").style.backgroundColor = "#109E00";
            } else {
                document.getElementById("low_high").style.backgroundColor = "#FFBC02";
            }
        } else {
            if (probability < 50) {
                document.getElementById("high_low").style.backgroundColor = "#FFBC02";
            } else {
                document.getElementById("high_high").style.backgroundColor = "#FF0505";
            }
        }
    }

    function update_rng_days_delay() {
        var value = document.getElementById('rng_days_delay').value;
        var lbl = document.getElementById('lbl_days_delay');
        if (value <= 33) {
            lbl.innerHTML = 'Low';
        } else if (value <= 66) {
            lbl.innerHTML = 'Medium';
        } else {
            lbl.innerHTML = 'High';
        }
        // update_total_impact();
    }

    function update_txt_days_delay() {
        var value = document.getElementById('txt_days_delay').value;
        document.getElementById('lbl_calc_days_delay').innerHTML = value;
        calculate_expected_delay();
    }

    function update_rng_probability() {
        var value = document.getElementById('rng_probability').value;
        var lbl = document.getElementById('lbl_probability');
        document.getElementById('txt_probability').value = value;
        document.getElementById('lbl_calc_probability_1').innerHTML = value;
        document.getElementById('lbl_calc_probability_2').innerHTML = value;
        document.getElementById('lbl_calc_probability_3').innerHTML = value;
        if (value <= 33) {
            lbl.innerHTML = 'Low';
        } else if (value <= 66) {
            lbl.innerHTML = 'Medium';
        } else {
            lbl.innerHTML = 'High';
        }
        update_action();
        calculate_overall_impact();
        calculate_expected_cost();
        calculate_expected_delay();
    }

    function update_txt_probability() {
        var value = document.getElementById('txt_probability').value;
        var rng = document.getElementById('rng_probability');
        document.getElementById('lbl_calc_probability_1').innerHTML = value;
        document.getElementById('lbl_calc_probability_2').innerHTML = value;
        document.getElementById('lbl_calc_probability_3').innerHTML = value;
        rng.value = value;
        var evt = document.createEvent("HTMLEvents");
        evt.initEvent("change", false, true);
        rng.dispatchEvent(evt);
        var lbl = document.getElementById('lbl_probability');
        if (value <= 33) {
            lbl.innerHTML = 'Low';
        } else if (value <= 66) {
            lbl.innerHTML = 'Medium';
        } else {
            lbl.innerHTML = 'High';
        }
        update_action();
        calculate_overall_impact();
        calculate_expected_cost();
        calculate_expected_delay();
    }

    function calculate_overall_impact() {
        var probability = document.getElementById('txt_probability').value;
        var effect = document.getElementById('txt_impact_effect').value;
        document.getElementById('lbl_overall_impact').innerHTML = (probability * effect) / 100;
    }

    function calculate_expected_delay() {
        var probability = document.getElementById('txt_probability').value;
        var days_delay = document.getElementById('txt_days_delay').value;
        document.getElementById('lbl_expected_delay').innerHTML = (probability * days_delay) / 100;
    }

    function calculate_expected_cost() {
        var probability = document.getElementById('txt_probability').value;
        var cost = document.getElementById('txt_cost_impact').value;
        document.getElementById('lbl_expected_cost').innerHTML = (probability * cost) / 100;
    }

    function update_risk_statement() {
        var event = document.getElementById('event').value;
        var date_of_concern = document.getElementById('date_of_concern').value;
        var impact = document.getElementById('impact').value;
        if (event === '' || date_of_concern === '' || impact === '') {
            return;
        }
        var txt_risk_statement = document.getElementById('risk_statement');

        txt_risk_statement.value = "If " +
                event + " by " +
                date_of_concern + " then " +
                impact;
    }
</script>