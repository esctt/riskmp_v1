<div id='page-content' class='sharp-page'>
    <div class="page-content-container">
        <div id='title'>
            <h1><?php echo $title ?></h1>
        </div>

        <div class='breadcrumb-container'>
            <p id='breadcrumb'>
                <a href='<?php echo base_url('dashboard'); ?>'>Dashboard</a>
                <a href='<?php echo base_url('project/view/' . $task_data['project_id']); ?>'><?php echo $task_data['project_name']; ?></a>
                <a href='<?php echo base_url('task/view/' . $task_data['task_id']); ?>'><?php echo $task_data['task_name']; ?></a>
            </p>
        </div>

        <div class='window'>
            <div class='toolbar'>
                <div class='toolbar-title'>Task Info</div>
            </div>
            <div id='task-info' class='info' style='padding:10px'>
                <p class='title'>WBS: </p><p class='value'><?php echo $task_data['WBS']; ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
                <p class='title'>Task Name: </p><p class='value'><?php echo $task_data['task_name']; ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
                <p class='title'>Duration: </p><p class='value'><?php echo $task_data['duration']; ?> days</p>&nbsp;&nbsp;&nbsp;&nbsp;
                <p class='title'>Start Date: </p><p id='task_start_date' class='value'><?php echo $task_data['start_date']; ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
                <p class='title'>Finish Date: </p><p class='value'><?php echo $task_data['finish_date']; ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
                <p class='title'>Cost: <p class='value'>$<?php echo $task_data['cost']; ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
                <p class='title'>Resource Names: </p><p class='value'><?php echo $task_data['resource_names']; ?></p>&nbsp;&nbsp;&nbsp;&nbsp;
                <br/><br/>
            </div>
            <div id="RiskTableContainer" class="jTableContainer" style="width:100%;"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        var task_id = <?php echo $task_data['task_id']; ?>;
        var modify = <?php echo $modify ? 'true' : 'false'; ?>;
        // alert(document.getElementById('task_start_date').innerHTML);
        loadViewTaskRisksTable(document.getElementById('task_start_date').innerHTML, 'RiskTableContainer', task_id, modify, 'create_risk');

    });
</script>