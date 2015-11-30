<div id='page-content' class='sharp-page'>
    <div class="page-content-container">
        <div id='title'>
            <h1><?php echo $title ?></h1>
        </div>
        <div class='breadcrumb-container'>
            <p id='breadcrumb'>
                <a href='<?php echo base_url('dashboard'); ?>'>Dashboard</a>
                <a href='<?php echo base_url('project/view/' . $project_data['project_id']); ?>'><?php echo $project_data['project_name']; ?></a>
            </p>
        </div>

        <div id="tabs">
            <ul>
                <li><a href='#tabs-ExecutiveSummary'>Executive Summary</a></li>
                <li><a href='#tabs-Tasks'>Tasks</a></li>
                <li><a id='ThirdTab' href='#tabs-TasksWithRisks'>Tasks With Risks</a></li>
                <li><a id='FourthTab' href='#tabs-Risks'>Risks</a></li>
                <li><a href='#tabs-Responses'>Responses</a></li>
                <li><a href='#tabs-Details'>Project Details</a></li>
                <li><a id='SeventhTab' href='#tabs-Lessons-Learned'>Lessons Learned</a></li>
                <li><a href='#tabs-AllReports'>All Reports</a></li>
                <?php
                if ($admin) { ?>
                    <li><a href='#tabs-Users'>Users</a></li>
                <?php } ?>
            </ul>
            <div id='tabs-ExecutiveSummary' class='tab'>
                <div class='toolbar'>
                    <div class='toolbar-title'>Executive Summary</div>
                    <div class='toolbar-options'>
                        <a title="Background of image is transparent." class='toolbar-button button button-flat' id="linker" onclick="saveAsImg(document.getElementById('calendar_chart_div'));">Chart As Image</a>
                    </div>
                    <div class='toolbar-options'>
                        <a href='<?php echo base_url('project/executive_report/' . $project_data['project_id']); ?>' class='toolbar-button button button-flat' target='_blank'>View Report</a>
                    </div>
                </div>
                <div id='exec-info' class='info' style='padding-left:10px;padding-right:10px; font-size: 15px;'>
                    <br/>
                    <p class='title'>Expected Cost: </p><p class='value'>$<?php echo round($project_data['total_expected_cost']); ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
                    <p class='title'>Mitigation Cost: </p><p class='value'>$<?php echo round($project_data['project_total_mitigation_cost']); ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
                    <p class='title'>Maximum Exposure: </p><p class='value'>$<?php echo round($project_delay_data['total_maximum_exposure']); ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
                    <p class='title'>Adjusted Cost: </p><p class='value'>$<?php echo round($project_delay_data['total_adjusted_cost']); ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
                    <p class='title'>Expected Delay: </p><p class='value'><?php echo round($project_delay_data['total_expected_delay']); ?> days</p>&nbsp;&nbsp;&nbsp;&nbsp; 
                    <p class='title'>Severe Risks: </p><p class='value'><?php echo $project_data['urgent_risks']; ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
                    <p class='title'>High Probability Risks: </p><p class='value'><?php echo $project_data['high_prob_risks']; ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
                    <!-- <div style="width: 30%;float:right;">
                        <a title="Background of image is transparent." id="linker" onclick="saveAsImg(document.getElementById('calendar_chart_div'));"><button>Save Calendar Chart As Image</button></a>
                    </div> -->
                    <br/><br/>
                </div>
                <!--<div style="width:100%;clear:both;height:22px;"></div>-->
                <div style="margin-left:auto;margin-right:auto;width:1000px;">
                    <div style="width: 100%;">
                        <div style="width: 40%;float:left;">
                            <h2 style="margin-bottom:0;margin-top:0;">Calendar - Expected Cost</h2>
                        </div>
                        <!-- <div style="width: 30%;float:right;">
                            <a title="Background of image is transparent." id="linker" onclick="saveAsImg(document.getElementById('calendar_chart_div'));"><button>Save Chart As Image</button></a>
                        </div> -->
                    </div>
                    
                    <div style="width:100%;clear:both;height:20px;"></div>
                    
                    <!--<div id='exec-info' class='info' style='padding-left:10px;padding-right:10px; font-size: 14px;'>
                        <br/>
                        <p class='title'>Total Expected Cost: </p><p class='value'>$<?php echo round($project_data['total_expected_cost']); ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
                        <p class='title'>Total Mitigation Cost: </p><p class='value'>$<?php echo round($project_data['project_total_mitigation_cost']); ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
                        <p class='title'>Severe Risks: </p><p class='value'><?php echo $project_data['urgent_risks']; ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
                        <p class='title'>High Probability Risks: </p><p class='value'><?php echo $project_data['high_prob_risks']; ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
                        <br/><br/>
                    </div>-->
                    
                    <!--<div style="width:100%;clear:both;height:15px;"></div>-->
                    <div id="calendar_chart_div"></div>
                    
                </div>
                <div style="width:100%;">
                    <div id="UpcomingRisksTableContainer" class="jTableContainer" style="width:48%;float:left;"></div>
                    <div id="TopRisksTableContainer" class="jTableContainer" style="width:48%;float:right;"></div>
                    <div style="width:100%;clear:both;height:50px;"></div>
                </div>
                <div style="width:100%;">
                    <div id="SevereRisksTableContainer" class="jTableContainer" style="width:48%;float:left;"></div>
                    <div id="AllRisksTableContainer" class="jTableContainer" style="width:48%;float:right;"></div>
                    <div style="width:100%;clear:both;height:1px;"></div>
                </div>
                <div style="width:100%;padding:17px;">
                    <h2>Expected Cost vs. Date of Concern</h2>
                    <div id="chart_div" style="height:400px;width:95%;"></div>
                </div>
            </div>
            <div id='tabs-Tasks' class='tab'>
                <div id="TaskTableContainer" class="jTableContainer" style="width:100%;"></div>
            </div>
            <div id='tabs-TasksWithRisks' class='tab'>
                <div id="TasksWithRisksTableContainer" class="jTableContainer" style="width:100%;"></div>
            </div>
            <div id='tabs-Risks' class='tab'>
                <div id="RiskTableContainer" class="jTableContainer" style="width:100%;"></div>
            </div>
            <div id='tabs-Responses' class='tab'>
                <div id="ResponseTableContainer" class="jTableContainer" style="width:100%;"></div>
            </div>
            <div id='tabs-Details' class='tab'>
                <div class='toolbar'>
                    <div class='toolbar-title'>Project Details</div>
                </div>
                <div class="info" style='padding:20px;'>
                    <p class="title">Project Name: </p>
                    <p class="value"><?php echo $title ?> </p>
                    <br/>
                    <p class="title">Tasks: </p>
                    <p class="value"><?php echo $project_data['num_tasks']; ?></p>
                    <br/>
                    <p class="title">Active Risks: </p>
                    <p class="value"><?php echo $project_data['active_risks']; ?></p>
                    <br/>
                    <p class="title">Closed Risks: </p>
                    <p class="value"><?php echo $project_data['closed_risks']; ?></p>
                    <br/>
                    <p class="title">Expected Cost: </p>
                    <p class="value">$<?php echo round($project_data['total_expected_cost']); ?></p>
                    <br/>
                    <p class="title">Expected Delay: </p>
                    <p class="value"><?php echo round($project_delay_data['total_expected_delay']); ?> days</p>
                    <br/>
                    <p class="title">Mitigation Cost: </p>
                    <p class="value">$<?php echo round($project_data['project_total_mitigation_cost']); ?></p>
                    <br/>
                    <p class="title">Maximum Exposure: </p>
                    <p class="value">$<?php echo round($project_delay_data['total_maximum_exposure']); ?></p>
                    <br/>
                    <p class="title">Adjusted Cost: </p>
                    <p class="value">$<?php echo round($project_delay_data['total_adjusted_cost']); ?></p>
                    <br/>
                    <p class="title">Severe Risks: </p>
                    <p class="value"><?php echo $project_data['urgent_risks']; ?></p>
                    <br/>
                    <p class="title">High Probability Risks: </p>
                    <p class="value"><?php echo $project_data['high_prob_risks']; ?></p>
                    <br/>
                    <p class="title">Date Created: </p>
                    <p class="value"><?php echo $project_data['date_created']; ?></p>
                    <br/>
                    <p class="title">Last Modified: </p>
                    <p class="value"><?php
                        echo $project_data['date_modified'] . " by " .
                        $project_data['last_modifier'];
                        ?></p>
                    <br/>
                    <p class="title">Date Completed: </p>
                    <p class="value"><?php echo $project_data['date_completed']; ?></p>
                </div>
            </div>
            <div id='tabs-Lessons-Learned' class='tab'>
                <div id="LessonsLearnedTableContainer" class="jTableContainer" style="width:100%;"></div>
            </div>
            <div id='tabs-AllReports' class='tab'>
                </br>
                <div class='toolbar'>
                    <div class='toolbar-title'>Executive Summary</div>
                    <div class='toolbar-options'>
                        <a href='<?php echo base_url('project/executive_report/' . $project_data['project_id']); ?>' class='toolbar-button button button-flat' target="_blank">View Report</a>&nbsp;&nbsp;&nbsp;
                    </div>
                </div>
                </br>
                <div class='toolbar'>
                    <div class='toolbar-title'>Global Risk Report</div>
                    <div class='toolbar-options'>
                        <a href='<?php echo base_url('user/short_global_risk_pdf_report/' . $project_data['project_id']); ?>' class='toolbar-button button button-flat' target="_blank">View PDF Report</a>&nbsp;&nbsp;&nbsp;
                        <a href='<?php echo base_url('user/short_global_risk_report/' . $project_data['project_id']); ?>' class='toolbar-button button button-flat' target="_blank">View Report</a>&nbsp;&nbsp;&nbsp;
                        <a href='<?php echo base_url('user/global_risk_report/' . $project_data['project_id']); ?>' class='toolbar-button button button-flat' target="_blank">View Full Report</a>&nbsp;&nbsp;&nbsp;
                    </div>
                </div>
                </br>
                <div class='toolbar'>
                    <div class='toolbar-title'>Tasks WIth Risks</div>
                    <div class='toolbar-options'>
                        <a href='<?php echo base_url('project/short_task_pdf_report/' . $project_data['project_id']); ?>' class='toolbar-button button button-flat' target="_blank">View PDF Report</a>&nbsp;&nbsp;&nbsp;
                        <a href='<?php echo base_url('project/short_task_report/' . $project_data['project_id']); ?>' class='toolbar-button button button-flat' target="_blank">View Report</a>&nbsp;&nbsp;&nbsp;
                        <a href='<?php echo base_url('project/task_report/' . $project_data['project_id']); ?>' class='toolbar-button button button-flat' target="_blank">View Full Report</a>&nbsp;&nbsp;&nbsp;
                    </div>
                </div>
                </br>
                <div class='toolbar'>
                    <div class='toolbar-title'>Risk Identification Report</div>
                    <div class='toolbar-options'>
                        <a href='<?php echo base_url('project/short_risk_pdf_report/' . $project_data['project_id']); ?>' class='toolbar-button button button-flat' target="_blank">View PDF Report</a>&nbsp;&nbsp;&nbsp;
                        <a href='<?php echo base_url('project/short_risk_report/' . $project_data['project_id']); ?>' class='toolbar-button button button-flat' target="_blank">View Report</a>&nbsp;&nbsp;&nbsp;
                        <a href='<?php echo base_url('project/risk_report/' . $project_data['project_id']); ?>' class='toolbar-button button button-flat' target="_blank">View Full Report</a>&nbsp;&nbsp;&nbsp;
                    </div>
                </div>
                </br>
                <div class='toolbar'>
                    <div class='toolbar-title'>Response Planning Report</div>
                    <div class='toolbar-options'>
                        <a href='<?php echo base_url('project/short_response_pdf_report/' . $project_data['project_id']); ?>' class='toolbar-button button button-flat' target="_blank">View PDF Report</a>&nbsp;&nbsp;&nbsp;
                        <a href='<?php echo base_url('project/short_response_report/' . $project_data['project_id']); ?>' class='toolbar-button button button-flat' target="_blank">View Report</a>&nbsp;&nbsp;&nbsp;
                        <a href='<?php echo base_url('project/response_report/' . $project_data['project_id']); ?>' class='toolbar-button button button-flat' target="_blank">View Full Report</a>&nbsp;&nbsp;&nbsp;
                    </div>
                </div>
                </br>
                <div class='toolbar'>
                    <div class='toolbar-title'>All Risks</div>
                    <div class='toolbar-options'>
                        <a href='<?php echo base_url('project/risks_with_responses_report/' . $project_data['project_id']); ?>' class='toolbar-button button button-flat' target="_blank">View Report</a>&nbsp;&nbsp;&nbsp;
                    </div>
                </div>
                </br>
                <div class='toolbar'>
                    <div class='toolbar-title'>Lessons Learned</div>
                    <div class='toolbar-options'>
                        <a href='<?php echo base_url('project/lessons_learned_pdf_risk_report/' . $project_data['project_id']); ?>' class='toolbar-button button button-flat' target="_blank">View PDF Report</a>&nbsp;&nbsp;&nbsp;
                        <a href='<?php echo base_url('project/short_lessons_learned_report/' . $project_data['project_id']); ?>' class='toolbar-button button button-flat' target="_blank">View Report</a>&nbsp;&nbsp;&nbsp;
                        <a href='<?php echo base_url('project/lessons_learned_report/' . $project_data['project_id']); ?>' class='toolbar-button button button-flat' target="_blank">View Full Report</a>&nbsp;&nbsp;&nbsp;
                    </div>
                </div>
            </div>
            <?php if ($admin) { ?>
                <div id='tabs-Users' class='tab'>
                    <div id='UserTableContainer' class='jTableContainer' style='width:100%'></div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $("#tabs").tabs();
    });
    var open_tab = <?php echo $tab; ?>; //the tab to be opened once page is loaded
    var project_id = <?php echo $project_data['project_id']; ?>;
    var modify = <?php echo $modify ? 'true' : 'false'; ?>;
    $(document).ready(function() {
        loadViewProjectTasksTable('TaskTableContainer', project_id, modify);
        loadViewProjectRisksTable('RiskTableContainer', project_id, modify);
        loadViewProjectResponsesTable('ResponseTableContainer', project_id, modify);
        loadViewProjectTasksWithRisksTable('TasksWithRisksTableContainer', project_id, modify);
        loadViewProjectUpcomingRisksTable('UpcomingRisksTableContainer', project_id);
        loadViewProjectTopRisksTable('TopRisksTableContainer', project_id);
        loadViewProjectSevereRisksTable('SevereRisksTableContainer', project_id, true);
        loadViewProjectAllRisksTable('AllRisksTableContainer', project_id, true);
        loadViewLessonsLearnedTable('LessonsLearnedTableContainer', project_id, modify);
<?php if ($admin) echo "loadViewProjectUsersTable('UserTableContainer', project_id);"; ?>
    });
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type='text/javascript' src='<?php echo base_url('assets/js/pagelevel/view_project_1.1.1.js'); ?>'></script>
<script type="text/javascript" src="https://canvg.googlecode.com/svn/trunk/rgbcolor.js"></script>
<script type="text/javascript" src="https://canvg.googlecode.com/svn/trunk/canvg.js"></script>
