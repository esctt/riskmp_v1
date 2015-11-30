        <div id='page-content' class='sharp-page'>
            <div class="page-content-container">
<div id='title'>
    <h1><?php echo $title;?></h1>
</div>
<div id='wizard' style='width:90%;margin-left:auto;margin-right:auto;background-color:#f2f2f2;min-height:1000px;'>
    <?php
    echo validation_errors();
    echo form_open('risk/update_form/' . $risk_data['risk_id']);
    ?>
    <div class='toolbar'>
        <div class='toolbar-title'>New Update</div>
    </div>
    <div class='tab-content' style='padding:10px;'>
        <p class='top-instruction'>This tool allows you to make updates to a risk while keeping track of what you had before.
        <br/>Updates to your risk allow you to keep track of its progress.</p>
        <div style='width:60%;'>
            <p class='grey-text' style='width:100%;'>Risk Statement:
                <?php echo "If " . $risk_data['event'] . " by " . $risk_data['date_of_concern'] . " then " . $risk_data['impact']; ?>
            </p>
            <div class="tab_section">
            <p class='field-title'>Impact:</p>
                        <textarea id='impact' name='impact' maxlength='250' class="field" 
                                  style='width:100%;resize:none;' rows='4' 
                                  wrap='soft' onchange='update_risk_statement();'
                                  placeholder="Describe the risk impact here."><?php echo $risk_data['impact']; ?></textarea>
            </div>
            <div class='tab-section'>
                    <p class='field-title'>Impact Effect:</p>
                    <div class='range-slider-container' style='width:60%'>
                        <input type='range' id='rng_impact_effect' name='rng_impact_effect'
                               min='0' max='100' value='<?php echo $risk_data['impact_effect']; ?>' style='width:99%'>
                        <output for='rng_impact_effect' class='range-output' onforminput='value = rng_impact_effect.valueAsNumber;'</output>
                    </div>
                    <p class='field-description' style='display:block;'>On a scale of 0 to 100, rank the effect that the risk will have.</p>
                </div>
                <div class='tab-section'>
                    <p class='field-title'>Impact -  Days Delay:</p>
                    <input id='txt_days_delay' name='txt_days_delay' type='number' min='-999999' max='999999'
                            value='<?php echo $risk_data['days_delay']; ?>' step='1' class='field'
                            style='width:15%;'>
                    <p class='field-description' style='display:block;'>Enter the days delay.</p>
                </div>
                <div class='tab-section'>
                    <p class='field-title'>Cost Impact:</p>
                    $<input id='txt_cost_impact' name='txt_cost_impact' type='number' min='0' max='999999999'
                            value='<?php echo $risk_data['cost_impact']; ?>' step='1' class='field'
                            style='width:15%;'>
                    <p class='field-description' style='display:block;'>Enter the expected cost impact.</p>
                </div>
                <div class='tab-section'>
                    <p class='field-title'>Probability:</p>
                    <div class='range-slider-container' style='width:60%'>
                        <input type='range' id='rng_probability' name='rng_probability'
                               min='0' max='100' value='<?php echo $risk_data['probability']; ?>' style='width:99%'>
                        <output for='rng_probability' class='range-output' onforminput='value = rng_probability.valueAsNumber;'</output>
                    </div>
                    <label id='lbl_probability'></label>
                    <p class='field-description' style='display:block;'>On a scale of 0% to 100%, rank probability that the risk will occur.</p>
                </div>
            <div class='tab-section'>
                    <p class='field-title'>Impact Discussion:</p>
                    <textarea id='impact_discussion' name='impact_discussion' maxlength='250' class="field" 
                              style='width:100%;resize:none;' rows='6' 
                              wrap='soft'
                              placeholder="Record your impact discussion notes here."><?php echo $risk_data['impact_discussion']; ?></textarea>
                </div>
            <div style='text-align:center;'>
                    <input type="submit" class="button button-flat-dblue button-large" value='Save'/>
                </div>            
    </div>
</form>
</div>
</div></div>
</div>