<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div class="report-title">
    <h1>Risk Analysis Report</h1>
</div>
<div class="info">
    <div style="width:85%;float:left;">
        <p class="title">Project: </p>
        <p class="value"><?php echo $risk_data['project_name']; ?></p>
    </div>
    <div class="spacer"></div>
    <div style="width:15%;float:left;">
        <p class="value"><?php echo date("Y-m-d"); ?></p>
        <br/><br/>
    </div>
    <div style='width:100%;clear:both;border-bottom: 1px #336699 solid;'>
        <p class='title'>Risk Statement: </p>
        <p class='value'><?php echo "If ".$risk_data['event']." by ".$risk_data['date_of_concern']." then ".$risk_data['impact']; ?></p>
    </div>
    <br/>
    <div style="width:20%;float:left;">
        <p class="title">WBS </p>
        <p class="value"><?php echo $risk_data['WBS']; ?></p>
    </div>
    <div class="spacer"></div>
    <div style="width:20%;float:left;">
        <p class="title">Date Identified: </p>
        <p class="value"><?php echo $risk_data['date_identified']; ?></p>
    </div>
    <div class="spacer"></div>
    <div style="width:20%;float:left;">
        <p class="title">Date Closed: </p>
        <p class="value"><?php echo $risk_data['date_closed']; ?></p>
    </div>
        <br/><br/>
    <div style="width:20%;float:left;clear:both;">
        <p class="title">Probability: </p>
        <p class="value"><?php echo $risk_data['probability']."%"; ?></p>
    </div>
    <div class="spacer"></div>
    <div style="width:20%;float:left;">
        <p class="title">Cost Impact: </p>
        <p class="value"><?php echo "$".round($risk_data['cost_impact']); ?></p>
    </div>
    <div class="spacer"></div>
    <div style="width:20%;float:left;">
        <p class="title">Expected Cost: </p>
        <p class="value"><?php echo "$".round($risk_data['expected_cost']); ?></p>
    </div>
    <div class="spacer"></div>
    <div style="width:20%;float:left;">
        <p class="title">Priority: </p>
        <p class="value"><?php echo $risk_data['priority_monetary']; ?></p>
    </div>
        <br/><br/>
    <div class="spacer" style="width:22%;clear:both;"></div>
    <div style="width:20%;float:left;">
        <p class="title">Impact Effect: </p>
        <p class="value"><?php echo $risk_data['impact_effect']; ?></p>
    </div>
    <div class="spacer"></div>
    <div style="width:20%;float:left;">
        <p class="title">Impact Effect: </p>
        <p class="value"><?php echo $risk_data['overall_impact']; ?></p>
    </div>
    <div class="spacer"></div>
    <div style="width:20%;float:left;">
        <p class="title">Priority: </p>
        <p class="value"><?php echo $risk_data['priority_effect']; ?></p>
    </div>
    <div style='width:100%;height:10px;clear:both;'></div> <!--Bottom of Risk Info Section-->
</div>
<div id="ResponseTableContainer" class="jTableContainer" style="width:100%;"></div>
</body>
<script type="text/javascript">
    $(document).ready(function() {
        var risk_id = <?php echo $risk_data['risk_id']; ?>;
        loadRiskAnalysisReportTable('ResponseTableContainer', risk_id);
    });
</script>