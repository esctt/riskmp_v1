<div id='page-content' class='sharp-page'>
    <div class="page-content-container">
        <div id='title'>
            <h1><?php echo $userdata['first_name'] . " " . $userdata['last_name'] ?></h1>
            <h2><?php // echo $userdata['company_name'];  ?></h2>
        </div>

        <div id="tabs">
            <ul>
                <li><a href="#tabs-Projects">Projects</a></li>
                <li><a href='#tabs-Risks'>Risks</a></li>
                <li><a href='#tabs-Account'>My Account</a></li>
            </ul>
            <div id='tabs-Projects' class='tab'>
                <div id="ProjectTableContainer" class="jTableContainer" style="width:100%;"></div>
            </div>
            <div id='tabs-Risks' class='tab'>
                <div id="RiskTableContainer" class="jTableContainer" style="width:100%;"></div>
                <div class="filter-toolbar">
                    <form>
                        Column:
                        <select id="FieldSelect">
                            <option value="event">Risk Event</option>
                            <option value="impact">Impact</option>
                            <option value="impact_discussion">Impact Discussion</option>
                        </select>
                        Search For:
                        <input type="text" id="txtFilter"/>
                        <input type="submit" id="FilterButton" value="filter" class="button button-flat-dblue button-small"/>
                    </form>
                </div>
            </div>
            <div id='tabs-Account' class='tab'>
                <div class='toolbar'>
                    <div class='toolbar-title'>Account Information</div>
                </div>
                <div class='info' id='account_info' style='padding:20px;'>
                    <p class="title">First Name: </p>
                    <p class="value"><?php echo $userdata['first_name']; ?> </p>
                    <br/>
                    <p class="title">Last Name: </p>
                    <p class="value"><?php echo $userdata['last_name']; ?> </p>
                    <br/>
                    <p class="title">Company Name: </p>
                    <p class="value"><?php echo $userdata['company_name']; ?> </p>
                    <br/>
                    <p class="title">Email: </p>
                    <p class="value"><?php echo $userdata['email']; ?> </p>
                    <br/>
                    <p class="title">Phone: </p>
                    <p class="value"><?php echo $userdata['phone']; ?> </p>
                    <br/>
                    <p class="title">Username: </p>
                    <p class="value"><?php echo $userdata['username']; ?> </p>
                    <br/>
                    <a class="button button-flat-dblue" href="<?php echo base_url('user/edit'); ?>">Update Information</a>
                    <br/><br/>
                    <p class="title">Password: </p>
                    <p class="value">********</p>
                    <br/>
                    <a class="button button-flat-dblue" href="<?php echo base_url('user/change_password'); ?>">Change Password</a>
                    <br/>
                    <br/>
                    <p class="title">Subscription Information: </p>
                    <p class="value">You are currently subscribed. </p>
                    <br/>
                    <a class="button button-flat-dblue" href="<?php echo base_url('user/subscription_details'); ?>">Manage Subscription</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {
        $("#tabs").tabs();
    });
</script>

<script type="text/javascript">
    $(document).ready(function() {
        loadDashboardProjectTable('ProjectTableContainer');
        loadDashboardRiskTable('RiskTableContainer', 'FilterButton', 'FieldSelect', 'txtFilter');
    });
</script>