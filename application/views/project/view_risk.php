<?php $this->load->helper('form'); echo form_open_multipart('upload/do_upload/' . $risk_data['risk_id']); ?>
<div id='page-content' class='sharp-page'>
    <div class="page-content-container">
        <div id='title'>
            <h1><?php echo $title ?></h1>
        </div>
        <div class='breadcrumb-container'>
            <p id='breadcrumb'>
                <a href='<?php echo base_url('dashboard'); ?>'>Dashboard</a>
                <a href='<?php echo base_url('project/view/' . $risk_data['project_id'] . "/3"); ?>'><?php echo $risk_data['project_name']; ?></a>
                <a href='<?php echo base_url('task/view/' . $risk_data['task_id']); ?>'><?php echo $risk_data['task_name']; ?></a>
                <a href='<?php echo base_url('risk/view/' . $risk_data['risk_id']); ?>'><?php echo "Risk Analysis"; ?></a>
            </p>
        </div>
        <div class='window'>
            <div class='toolbar'>
                <div class='toolbar-title'>Risk Info</div>
                <div class='toolbar-options'>
                    <a href='<?php echo base_url('/risk/report/' . $risk_data['risk_id']); ?>' class='button button-flat' target="_blank" style=''>Risk Report</a>
                    <?php if ($modify) { ?>
                        <a href='<?php echo base_url('/risk/edit/' . $risk_data['risk_id']); ?>' class='button button-flat' style=''>Edit Risk</a>
                        <a href='<?php echo base_url('/risk/update/' . $risk_data['risk_id']); ?>' class='button button-flat' style=''>New Update</a>
                    <?php } ?>
                </div>
            </div>
            <div id='risk-info' class='info' style='padding: 10px;'>
                <div style='width:100%;'>
                    <p class='title'>Risk Statement: </p>
                    <p id='risk_statement' class='value'></p>
                </div>
                <br/>
                <div style='width:17%;float:left;'>
                    <p class='title'>WBS: </p><p class='value'><?php echo $risk_data['WBS']; ?></p>
                    <br/>
                    <br/>
                    <p class='title'>Date of Concern: </p><p class='value'><?php echo $risk_data['date_of_concern']; ?></p>
                    <br/>
                    <br/>
                    <p class='title'>Date Identified: </p><p class='value'><?php echo $risk_data['date_identified']; ?></p>
                    <br/>
                    <br/>
                    <p class='title'>Date Closed: </p><p class='value'><?php echo $risk_data['date_closed']; ?></p>
                </div>
                <div class='spacer' style='width:2%;float:left;height:1px;'></div>
                <div style='width:25%;float:left;'>
                    <p class='title'>Risk Event: <br/> </p><p class='value'><?php echo $risk_data['event']; ?></p>
                    <br/>
                    <br/>
                    <br/>
                    <p class='title'>Impact: <br/> </p><p id='impact' class='value'></p>
                    <br/>
                    <br/>
                    <p class='title'>Days Delay: </p><p id='days_delay' class='value'></p>
                </div>
                <div class='spacer' style='width:2%;float:left;height:1px;'></div>
                <div style='width:10%;float:left;'>
                    <p class='title'>Probability:<br/> </p><p id='probability' class='value'></p>
                    <br/>
                    <br/>
                    <br/>
                    <p class='title'>Mitigation Cost: <br/> </p><p id='mitigation_cost' class='value'>$<?php echo $risk_data['total_mitigation_cost']; ?></p>
                    <br/>
                    <br/>
                    <p class='title'>Adjusted Cost: <br/> </p><p id='adjusted_cost' class='value'>$<?php echo $risk_data['adjusted_cost']; ?></p>
                </div>
                <div class='spacer' style='width:2%;float:left;height:1px;'></div>
                <div style='width:13%;float:left;'>
                    <p class='title'>Cost Impact: <br/> </p><p id='cost_impact' class='value'></p>
                    <br/>
                    <br/>
                    <br/>
                    <p class='title'>Impact Effect: <br/> </p><p id='impact_effect' class='value'></p>
                    <br/>
                    <br/>
                    <p class='title'>Priority Days: </p><p id='priority_days' class='value'></p>
                </div>
                <div class='spacer' style='width:2%;float:left;height:1px;'></div>
                <div style='width:15%;float:left;'>
                    <p class='title'>Expected Cost: <br/> </p><p id='expected_cost' class='value'></p>
                    <br/>
                    <br/>
                    <br/>
                    <p class='title'>Overall Impact: <br/> </p><p id='overall_impact' class='value'></p>
                    <br/>
                    <br/>
                    <p class='title'>Expected Delay: </p><p id='expected_delay' class='value'></p>
                </div>
                <div class='spacer' style='width:2%;float:left;height:1px;'></div>
                <div style='width:7%;float:left;'>
                    <p class='title'>Priority ($): <br/> </p><p id='priority_monetary' class='value'></p>
                    <br/>
                    <br/>
                    <br/>
                    <p class='title'>Priority: <br/> </p><p id='priority_effect' class='value'></p>
                </div>
                <div style='width:100%;clear:both;'></div>
                <br/>
                <div style='width:50%;float:left;'>
                    <p class='title'>Impact Discussion & Root Cause: <br/> </p><p id='impact_discussion' class='value'></p>
                </div>
                <div style='max-width:50%;float:right;padding-right:20px;'>
                    <p class='title'>Date of Update:</p><select id='select_update' onload='update_fields();' onchange='update_fields();'>
                        <?php
                        foreach ($risk_data['updates'] as $update) {
                            echo "<option>" . $update['date_of_update'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div style='width:100%;float:left;'>
                    <br/>
                    <p class='title'>Media: </p><p id='img_url' class='value'></p>
                </div>
                <div style='width:100%;clear:both;height:10px;'></div>
                <br/>
                <div style="width:50%;float:left;text-align:left;">
                    <div style="width:25%;float:left;text-align:left;" id='addthiseventdiv'>
                        <!-- AddThisEvent generated using jQuery to support addition to risk media and description-->
                        <!-- <a href="" title="Add to Calendar" class="addthisevent">
                            Add to Calendar
                            <span class="_start"><?php echo $risk_data['date_of_concern']; ?></span>
                            <span class="_end"><?php echo $risk_data['date_of_concern']; ?></span>
                            <span class="_zonecode">15</span>
                            <span class="_summary"><?php echo $risk_data['event']; ?></span>
                            <span class="_description"><?php if ( $risk_data_cal['impact_discussion'] ) { echo $risk_data_cal['impact_discussion'];} ?></span>
                            <span class="_organizer"><?php echo $_SESSION['first_name']." ".$_SESSION['last_name']; ?></span>
                            <span class="_organizer_email"><?php echo $_SESSION['email']; ?></span>
                            <span class="_all_day_event">true</span>
                            <span class="_date_format">DD/MM/YYYY</span>
                        </a> -->
                    </div>
                    <div style="width:25%;float:left;text-align:left;margin-top:5px;">
                        <a href="#" class='button button-flat' style="border: 1px solid #d9d9d9;" onclick="show_Selector()" id="AddMediaButton">Add Media</a>
                        <script type="text/javascript">function show_Selector () { document.getElementById('FileSelector').style.display = 'inline';}</script>
                        <!-- <p><?php echo form_open_multipart('upload/do_upload');?></p> -->
                        <input type="file" id="FileSelector" name="userfile" onchange="this.form.submit();document.getElementById('AddMediaButton').innerHTML = 'Uploading...';" value="upload" style="margin-top: 7px;display: none;" />                                                         
                    </div>
                </div>
                <div style="width:50%;float:right;text-align:right;">
                    <p class="title">Severe?</p>&nbsp<p class="value"><?php if ($risk_data['urgent'])
                            echo "Yes";
                        else
                            echo "No";
                        ?></p>                                                                
                </div>
                <div style='width:100%;height:20px;clear:both;'></div> <!--Bottom of Risk Info Section-->
            </div>
            <div id="ResponseTableContainer" class="jTableContainer" style="width:100%;"></div>
        </div>
    </div>
</div>

<script type='text/javascript'>

                        var updates = <?php echo json_encode($risk_data['updates']); ?>;

                        $(document).ready(function() {
                            update_fields();

                        });
                        function update_fields() {

                            var update_index = document.getElementById('select_update').selectedIndex;
                            document.getElementById('impact').innerHTML = updates[update_index]['impact'];
                            document.getElementById('days_delay').innerHTML = updates[update_index]['days_delay'];
                            document.getElementById('probability').innerHTML = updates[update_index]['probability'] + '%';
                            document.getElementById('impact_effect').innerHTML = updates[update_index]['impact_effect'];
                            document.getElementById('cost_impact').innerHTML = '$' + updates[update_index]['cost_impact'];
                            document.getElementById('overall_impact').innerHTML = updates[update_index]['overall_impact'];
                            document.getElementById('expected_cost').innerHTML = '$' + updates[update_index]['expected_cost'];
                            document.getElementById('expected_delay').innerHTML = updates[update_index]['expected_delay'];
                            document.getElementById('impact_discussion').innerHTML = updates[update_index]['impact_discussion'];
                            document.getElementById('priority_effect').innerHTML = updates[update_index]['priority_effect'];
                            document.getElementById('priority_monetary').innerHTML = updates[update_index]['priority_monetary'];
                            document.getElementById('priority_days').innerHTML = updates[update_index]['priority_days'];
                            document.getElementById('risk_statement').innerHTML = "If " + "<?php echo $risk_data['event']; ?>" + " by " +
                                    "<?php echo $risk_data['date_of_concern']; ?>" + " then " + updates[update_index]['impact'];
                            var names = "<?php echo $risk_data['img_url']; ?>";
                            // Incase of glitch at time of data submission in edit_risk data fetch
                            while(names.charAt(0)===',')
                                names = names.substr(1); 
                            var filenames = names.split(',');
                            // document.getElementById('img_url').innerHTML = filenames;
                            var linkURL = config.base_url + "assets/images/uploads";
                            var media_description = "";
                            if ( typeof filenames[1] === "undefined") {
                                document.getElementById('img_url').innerHTML = '<a title="Click to view media." href="' + linkURL + '/' + filenames[0] + '" target="_blank">' + filenames[0] + '</a>';
                                media_description = linkURL + '/' + filenames[0];
                            }
                            else if ( typeof filenames[2] === "undefined") {
                                document.getElementById('img_url').innerHTML = '<a title="Click to view media." href="' + linkURL + '/' + filenames[0] + '" target="_blank">' + filenames[0] + '</a>, <a title="Click to view media." href="' + linkURL + '/' + filenames[1] + '" target="_blank">' + filenames[1] + '</a>';
                                media_description = linkURL + '/' + filenames[0] + '\n' + linkURL + '/' + filenames[1];
                            }
                            else {
                                document.getElementById('img_url').innerHTML = '<a title="Click to view media." href="' + linkURL + '/' + filenames[0] + '" target="_blank">' + filenames[0] + '</a>, <a title="Click to view media." href="' + linkURL + '/' + filenames[1] + '" target="_blank">' + filenames[1] + '</a>, <a title="Click to view media." href="' + linkURL + '/' + filenames[2] + '" target="_blank">' + filenames[2] + '</a>';
                                media_description = linkURL + '/' + filenames[0] + '\n' + linkURL + '/' + filenames[1] + '\n' + linkURL + '/' + filenames[2];
                            }

                            if (!names) {
                                document.getElementById('img_url').innerHTML = "No uploaded media items.";
                                media_description = "No uploaded media items.";
                            }
                            var impact_discussion = "";
                            if (updates[update_index]["impact_discussion"]) {
                                impact_discussion = updates[update_index]["impact_discussion"];
                            }
                            else {
                                impact_discussion = 'No Impact Discussion.'
                            }

                            var $containerDiv = $('#addthiseventdiv');
                            var $anchor = $('<a href="" title="Add to Calendar" class="addthisevent">Add to Calendar</a>').appendTo($containerDiv);
                            var $span_start = $('<span class="_start"><?php echo $risk_data["date_of_concern"]; ?></span>').appendTo($anchor);
                            var $span_end = $('<span class="_end"><?php echo $risk_data["date_of_concern"]; ?></span>').appendTo($anchor);
                            var $span_zonecode = $('<span class="_zonecode">15</span>').appendTo($anchor);
                            var $span_summary = $('<span class="_summary"><?php echo $risk_data["event"]; ?></span>').appendTo($anchor);
                            var $span_description = $("<span class='_description'> Impact Discussion: \n" + impact_discussion + "\n Media: \n" + media_description + "</span>").appendTo($anchor);
                            var $span_organizer = $('<span class="_organizer"><?php echo $_SESSION["first_name"] . " " . $_SESSION["last_name"]; ?></span>').appendTo($anchor);
                            var $span_organizer_email = $('<span class="_organizer_email"><?php echo $_SESSION["email"]; ?></span>').appendTo($anchor);
                            var $span_all_day_event = $('<span class="_all_day_event">true</span>').appendTo($anchor);
                            var $span_date_format = $('<span class="_date_format">DD/MM/YYYY</span>').appendTo($anchor);

                        }
</script>

<script type="text/javascript">
    $(document).ready(function() {
        var risk_id = <?php echo $risk_data['risk_id']; ?>;
        var modify = <?php echo $modify ? 'true' : 'false'; ?>;
        loadViewRiskResponsesTable('ResponseTableContainer', risk_id, modify);
    });
</script>

<!-- AddThisEvent -->
<script type="text/javascript" src="<?php echo base_url('assets/js/pagelevel/addtocal.js/'); ?>"></script>