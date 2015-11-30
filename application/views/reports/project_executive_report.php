<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="report-title">
    <h1>Executive Summary Report</h1>
</div>
<div class="info">
    <div style="width:85%;float:left;">
        <p class="title">Project: </p>
        <p class="value"><?php echo $project_data['project_name']; ?></p>
    </div>

    <div class="spacer"></div>
    <div style="width:15%;float:left;">
        <p class="value"><?php echo date("Y-m-d"); ?></p>
    </div>
    <div class="bottom"></div>
    <div>
        <p class='title'>Total Expected Cost: </p><p class='value'>$<?php echo round($project_data['total_expected_cost']); ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
        <p class='title'>Total Mitigation Cost: </p><p class='value'>$<?php echo round($project_data['project_total_mitigation_cost']); ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
        <p class='title'>Severe Risks: </p><p class='value'><?php echo $project_data['urgent_risks']; ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
        <p class='title'>High Probability Risks: </p><p class='value'><?php echo $project_data['high_prob_risks']; ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
        <br/><br/>
    </div>
</div>

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
    <div id="calendar_chart_div"></div>                    
</div>

<div id="UpcomingRisksTableContainer" class="jTableContainer" style="width:100%;"></div>
<div id="TopRisksTableContainer" class="jTableContainer" style="width:100%;"></div>
<div id="SevereRisksTableContainer" class="jTableContainer" style="width:100%;"></div>
</body>
<script type="text/javascript">
    $(document).ready(function() {

        var project_id = <?php echo $project_data['project_id']; ?>;
        // alert('view: ' + project_id);
        loadViewProjectUpcomingRisksTable('UpcomingRisksTableContainer', project_id);
        loadViewProjectTopRisksTable('TopRisksTableContainer', project_id);
        loadViewProjectSevereRisksTable('SevereRisksTableContainer', project_id, false);
    });
</script>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type='text/javascript' src='<?php echo base_url('assets/js/pagelevel/view_project_report.js'); ?>'></script>
<script type="text/javascript" src="https://canvg.googlecode.com/svn/trunk/rgbcolor.js"></script>
<script type="text/javascript" src="https://canvg.googlecode.com/svn/trunk/canvg.js"></script>
