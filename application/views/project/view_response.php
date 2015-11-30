<div id='page-content' class='sharp-page'>
    <div class="page-content-container">
        <div id='title'>
            <h1><?php echo $title ?></h1>
        </div>

        <div class='breadcrumb-container'>
            <p id='breadcrumb'>
                <a href='<?php echo base_url('dashboard'); ?>'>Dashboard</a>
                <a href='<?php echo base_url('project/view/' . $response_data['project_id'] . "/4"); ?>'><?php echo $response_data['project_name']; ?></a>
                <a href='<?php echo base_url('task/view/' . $response_data['task_id']); ?>'><?php echo $response_data['task_name']; ?></a>
                <a href='<?php echo base_url('risk/view/' . $response_data['risk_id']); ?>'><?php echo "Risk Analysis"; ?></a>
                <a href='<?php echo base_url('response/view/' . $response_data['response_id']); ?>'><?php echo "Response Planning"; ?></a>
            </p>
        </div>

        <div class='window'>
            <div class='toolbar'>
                <div class='toolbar-title'>Response Planning</div>
                <div class='toolbar-options'>
                    <a href='<?php echo base_url('/response/report/' . $response_data['response_id']); ?>' class='button button-flat' target="_blank" style=''>Response Report</a>
                    <?php
                    if ($modify) {
                        echo "<a href='" . base_url('/response/edit/' . $response_data['response_id']) . "' class='button button-flat' style=''>Edit Response</a>";
                    }
                    ?>
                </div>
            </div>
            <div id='response-info' class='info' style='padding:10px'>
                <div style='width:100%;'>
                    <p class='title'>Risk Statement: </p>
                    <p id='risk_statement' class='value'><?php echo $response_data['risk_statement']; ?></p>
                </div>
                <div style='width:24%;float:left;'>
                    <p class='title'>WBS: </p><p class='value'><?php echo $response_data['WBS']; ?></p>
                    <br/><br/>
                    <p class='title'>Date of Plan: </p><p class='value'><?php echo $response_data['date_of_plan']; ?></p>
                    <br/><br/>
                    <p class='title'>Last Updated: </p><p class='value'><?php echo $response_data['date_of_update']; ?></p>
                    <br/><br/>
                    <p class='title'>Planned Closure: </p><p class='value'><?php echo $response_data['planned_closure']; ?></p>
                </div>
                <div class='spacer' style='width:2%;float:left;height:1px;'></div>
                <div style='width:15%;float:left;'>
                    <p class='title'>Action: <br/> </p><p class='value'><?php echo $response_data['action']; ?></p>
                    <br/><br/>
                    <p class='title'>Release Progress: <br/> </p><p class='value'><?php echo $response_data['release_progress']; ?></p>
                    <br/><br/>
                    <p class='title'>Post Response $: </p><p class='value'>$<?php echo $response_data['post_response']; ?></p>
                </div>
                <div class='spacer' style='width:2%;float:left;height:1px;'></div>
                <div style='width:15%;float:left;'>
                    <p class='title'>Owner: <br/> </p><p class='value'><?php echo $response_data['owner']; ?></p>
                    <br/><br/>
                    <p class='title'>Cost: <br/> </p><p class='value'>$<?php echo $response_data['cost']; ?></p>
                </div>
                <div class='spacer' style='width:2%;float:left;height:1px;'></div>
                <div style='width:40%;float:left;'>
                    <p class='title'>Current Status: <br/> </p><p class='value'><?php echo $response_data['current_status']; ?></p>
                    <br/>
                    <br/>
                    <p class='title'>Action Plan: <br/> </p><p class='value'><?php echo $response_data['action_plan']; ?></p>
                </div>
                <div id='bottom' style='clear:both;width:100%'></div>
            </div>
            <!--<div class='toolbar'>
                <div class='toolbar-title'>Response Tracking</div>
                <div class='toolbar-options'>
                    <a href='<?php// echo base_url('/response/update/' . $response_data['response_id']); ?>' class='button button-flat' style=''>New Update</a>
                    <a id='create_response_update' href='#' class='button button-flat' style=''>Quick</a>
                </div>
            </div>-->
            <div id="ResponseUpdateTableContainer" class="jTableContainer" style="width:100%;"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var response_id = <?php echo $response_data['response_id']; ?>;
        var modify = <?php echo $modify ? 'true' : 'false'; ?>;
        loadViewResponseResponseUpdatesTable('ResponseUpdateTableContainer', response_id, modify);
    });
</script>