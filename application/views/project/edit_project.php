<div id='page-content' class='sharp-page'>
    <div class="page-content-container">
        <div style='text-align:center;'>
            <div class='toolbar'>
                <div class='toolbar-title'>Edit</div>
            </div>
            <div id='wizard' style='display:inline-block;background-color:#f2f2f2;'>
                <?php
                echo validation_errors();
                echo form_open('project/edit_form/' . $project_data['project_id']);
                ?>
                <div class='tab-content' style='padding:10px;text-align:left;'>
                    <div style='width:100%'>
                        <p class='field-title'>Project Name:</p>
                        <input type='text' id='project_name' name='project_name' maxlength='20'
                               required class='field' value='<?php echo $project_data['project_name'] ?>'>
                        <br/>
                        <p class='field-title'>Status:</p>
                        <select id='status' name='status' required class='field'
                                value='<?php echo $project_data['project_name'] ?>'
                                onchange='status_change();'>
                            <option value='Active'>Active</option>
                            <option value='Closed'>Closed</option>
                        </select>
                        <p id='date_completed_title' class='field-title' style='display:none;'>Date Completed:</p>
                        <input type='date' id='date_completed' name='date_completed' style='display:none;'
                               class='field' value='<?php echo $project_data['date_completed'] ?>'>
                        <br/>
                    </div>
                    <div style='width:100%;text-align:center;'><input type="submit" value="Save" class='button button-flat-dblue button-large'></div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

                            function status_change() {
                                var status = document.getElementById('status').value;
                                var title = document.getElementById('date_completed_title');
                                var date_completed = document.getElementById('date_completed');
                                if (status == "Active") {
                                    date_completed.style.display = 'none';
                                    title.style.display = 'none';
                                } else {
                                    date_completed.style.display = 'block';
                                    title.style.display = 'block';
                                }
                            }

</script>