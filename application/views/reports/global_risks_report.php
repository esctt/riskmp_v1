<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="report-title">
    <h1>Global Risk Report</h1>
</div>
<div class="info">
    <div style="width:32%;float:left;">

    </div>
    <div class="spacer"></div>
    <div style="width:32%;float:left;">
        <p class="value"><?php echo date("Y-m-d"); ?></p>
    </div>
    <div class="bottom"></div>
</div>
<div id="RiskTableContainer" class="jTableContainer" style="width:100%;"></div>
</body>
<script type="text/javascript">
    $(document).ready(function() {
        loadGlobalRisksReportTable('RiskTableContainer');
    });

</script>