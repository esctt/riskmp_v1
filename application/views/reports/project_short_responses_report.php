<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="report-title">
    <h1>Response Planning Report</h1>
</div>
<div class="info">
    <div style="width:85%;float:left;">
        <p class="title">Project: </p>
        <p class="value"><?php echo $project_data['project_name']; ?></p>
    </div>
    <div class="spacer"></div>
    <div style="width:15%;float:left;">
<!--        <p class="title">Generated On: </p>-->
        <p class="value"><?php echo date("Y-m-d"); ?></p>
    </div>
    <div class="bottom"></div>
</div>
<div id="ResponseTableContainer" class="jTableContainer" style="width:100%;"></div>
</body>
<script type="text/javascript">
    $(document).ready(function() {
        var project_id = <?php echo $project_data['project_id']; ?>;
        loadProjectShortResponsesReportTable('ResponseTableContainer', project_id);
    });
</script>