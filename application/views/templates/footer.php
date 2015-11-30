</div>
<!--CLOSE GLOBAL WRAPPER-->
<div id="footer" style="<?php if (isset($opacity) && $opacity) echo "opacity:0.9;" ?>">        
    <div style="margin-left:auto;margin-right:auto;height:200px;position:relative;">
        <div style="width:27.5%;float:left;height:10px;"></div>
        <div style="width:15%;float:left;">
            <ol>
                <li>Feedback</li>
                <li><a href="#" onclick="openErrorReportDialog();
                        return false;">Report a bug</a></li>
                <li><a href="http://esctt.com/?nav=contact">Contact Us</a></li>
            </ol>
        </div>
        <div style="width:15%;float:left;">
            <ol>
                <li>Other Products</li>
                <li>CRM2plus&#8482;</li>
            </ol>
        </div>
        <div style="width:15%;float:left;">
            <ol>
                <li>Courses</li>
                <li><a href="https://escomputertraining.com/courses/details/industry/9/course/43"> Risk Management With RiskMP&#8482;</a></li>
                <li><a href="https://escomputertraining.com/courses/details/industry/9/course/29"> Microsoft Project</a></li>
            </ol>
        </div>
        <div style="width:27.5%;float:left;height:10px;"></div>
        <p style="font-size:12px;text-align:center;position:absolute;bottom:0;width:100%;">©2014 ESCTT Inc. All rights reserved.</p>
    </div>
</div>
<!--BEGIN ERROR REPORTING ELEMENTS-->
<div id="error_report_div" title="Error Reporting" style="display:none;width:auto;">
    <textarea id="error_text" placeholder="Describe the error(s) you are experiencing here." maxlength='250' rows="4" style="width:80%;" class="field"></textarea>
    <br/>
    <a href="#" id="submit_error" class="button button-flat-dblue button-large">Send</a>
</div>
<div id="error_result_div" style="display:none;width:auto;">
    <p id="error_result_message">Success</p>
</div>
<!--END ERROR REPORTING ELEMENTS-->
<script src="<?php echo base_url("assets/js/jquery-ui.js"); ?>" type="text/javascript"></script>
<script src="<?php echo base_url("assets/jtable/jquery.jtable.min.js"); ?>" type="text/javascript"></script>
<script src="<?php echo base_url("assets/buttons/js/buttons.js"); ?>" type="text/javascript"></script>
<script src="<?php echo base_url("assets/js/misc.js"); ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/js/table_helper.min.js'); ?>" type="text/javascript"></script>
</body>
</html>

<!--
 * Copyright (c) 2014 ESCTT Inc. All Right Reserved, http://esctt.com/
 * 
 * This source is subject to the ESCTT Inc. Permissive License.
 * All other rights reserved.
 * 
 * THIS CODE AND INFORMATION ARE PROVIDED "AS IS" WITHOUT WARRANTY OF ANY 
 * KIND, EITHER EXPRESSED OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND/OR FITNESS FOR A
 * PARTICULAR PURPOSE.
 * 
 -->