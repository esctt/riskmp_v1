<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="report-title">
    <h1>Response Tracking Report</h1>
</div>
<div class="info">
    <div style="width:85%;float:left;">
        <p class="title">Project: </p>
        <p class="value"><?php echo $response_data['project_name']; ?></p>
    </div>
    <div class="spacer"></div>
    <div style="width:15%;float:left;">
        <p class="value"><?php echo date("Y-m-d"); ?></p>
    </div>
    <div class="bottom"></div>
</div>
<div id='response-info' class='info'>
    <div style='width:100%;'>
        <p class='title'>Risk Statement: </p>
        <p id='risk_statement' class='value'><?php echo $response_data['risk_statement']; ?></p>
        <br/><br/>
    </div>
    <div style='width:24%;float:left;'>
        <p class='title'>WBS: </p><p class='value'><?php echo $response_data['WBS']; ?></p>
        <br/><br/>
        <p class='title'>Date of Plan: </p><p class='value' style="margin-left: 1.688em"><?php echo $response_data['date_of_plan']; ?></p>
        <br/><br/>
        <p class='title'>Last Updated: </p><p class='value' style="margin-left: 1.188em"><?php echo $response_data['date_of_update']; ?></p>
        <br/><br/>
        <p class='title'>Planned Closure: </p><p class='value'><?php echo $response_data['planned_closure']; ?></p>
    </div>
    <div class='spacer' style='width:1%;float:left;height:1px;'></div>
    <div style='width:24%;float:left;'>
        <p class='title'>Owner: </p><p class='value'><?php echo $response_data['owner']; ?></p>
        <br/><br/>
        <p class='title'>Cost: </p><p class='value'>$<?php echo round($response_data['cost']); ?></p>
        <br/><br/>
        <p class='title'>Release Progress: </p><p class='value'><?php echo $response_data['release_progress']; ?></p>
        <br/><br/>
        <p class='title'>Action: </p><p class='value'><?php echo $response_data['action']; ?></p>
    </div>
    <div class='spacer' style='width:1%;float:left;height:1px;'></div>
    <div style='width:50%;float:left;'>
        <p class='title'>Current Status: <br/> </p><p class='value'><?php echo $response_data['current_status']; ?></p>
        <br/>
        <br/>
        <p class='title'>Action Plan: <br/> </p><p class='value'><?php echo $response_data['action_plan']; ?></p>
    </div>
    <div id='bottom' style='clear:both;width:100%'></div>
</div>
<div id="ResponseUpdateTableContainer" class="jTableContainer" style="width:100%;"></div>
</body>
<script type="text/javascript">
    $(document).ready(function() {
        var response_id = <?php echo $response_data['response_id']; ?>;
        loadResponseTrackingReportTable('ResponseUpdateTableContainer', response_id);
    });
</script>