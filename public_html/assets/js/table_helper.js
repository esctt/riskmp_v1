/*
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
 */

/*
 * Contains functions for generating jTable instances throughout the web application.
 */
defaultResponseMessages = {
    loadingMessage: "Loading responses...",
    noDataAvailable: "There are no responses.",
    deleteConfirmation: "You are about to permanently delete this response. Are you sure?",
    pageSizeChangeLabel: "Responses per page",
    addNewRecord: "New Response",
    editRecord: "Edit Response"
};
defaultResponseFields = {
    response_id: {
        key: true,
        list: false
    },
    WBS: {
        title: "WBS",
        create: false,
        maxlength: 9,
        width: "0%",
        columnResizable: false,
        input: function(data) {
            if (data.record) {
                return '<input type="text" disabled="true" value="' + data.record.WBS + '"/>';
            }
        }
    },
    risk_statement: {
        title: "Risk Statement",
        create: false,
        edit: false,
        sorting: false,
        width: "40%"
    },
    date_of_plan: {
        title: "Date of Plan",
        type: "date",
        defaultValue: "" + new Date().getFullYear() + "-" + (new Date().getMonth()+1) + "-" + new Date().getDate(),
        width: "0%",
        columnResizable: false
    },
    action_plan: {
        title: "Action Plan",
        type: "link",
        maxlength: 250,
        linkURL: config.base_url + "response/view",
        addKeyToURL: true,
        width: "30%"
    },
    owner: {
        title: "Owner",
        maxlength: 20,
        width: "0%",
        columnResizable: false
    },
    release_progress: {
        title: "Release Progress",
        options: {
            Planning: "Planning",
            Ongoing: "Ongoing",
            Complete: "Complete",
            Cancelled: "Cancelled"
        },
        width: "0%"
    },
    action: {
        title: "Action",
        options: {
            Pursue: "Pursue",
            Accept: "Accept",
            Mitigate: "Mitigate",
            Transfer: "Transfer",
            Avoid: "Avoid"
        },
        width: "0%"
    },
    cost: {
        title: "Cost",
        type: "currency",
        width: "0%",
        columnResizable: false
    },
    post_response: {
        title: "Post Response $",
        type: "currency",
        width: "0%",
        columnResizable: false
    },
    date_of_update: {
        title: "Date of Update",
        sorting: true,
        edit: false,
        create: false,
        type: "date",
        width: "0%",
        columnResizable: false
    },
    planned_closure: {
        title: "Planned Closure",
        type: "date",
        width: "0%"
    },
    current_status: {
        title: "Current Status",
        type: "textarea",
        maxlength: 250,
        width: "30%"
    }
};

defaultShortResponseFields = {
    response_id: {
        key: true,
        list: false
    },
    WBS: {
        title: "WBS",
        create: false,
        maxlength: 9,
        width: "0%",
        columnResizable: false,
        visibility: "hidden",
        input: function(data) {
            if (data.record) {
                return '<input type="text" disabled="true" value="' + data.record.WBS + '"/>';
            }
        }
    },
    risk_statement: {
        title: "Risk Statement",
        create: false,
        edit: false,
        sorting: false,
        width: "40%"
    },
    date_of_plan: {
        title: "Date of Plan",
        type: "date",
        defaultValue: "2014-01-27",
        width: "0%",
        visibility: "hidden",
        columnResizable: false
    },
    action_plan: {
        title: "Action Plan",
        type: "link",
        maxlength: 250,
        linkURL: config.base_url + "response/view",
        addKeyToURL: true,
        width: "30%"
    },
    owner: {
        title: "Owner",
        maxlength: 20,
        width: "0%"
    },
    release_progress: {
        title: "Release Progress",
        options: {
            Planning: "Planning",
            Ongoing: "Ongoing",
            Complete: "Complete",
            Cancelled: "Cancelled"
        },
        width: "0%",
        visibility: "hidden"
    },
    action: {
        title: "Action",
        options: {
            Pursue: "Pursue",
            Accept: "Accept",
            Mitigate: "Mitigate",
            Transfer: "Transfer",
            Avoid: "Avoid"
        },
        width: "0%",
        visibility: "hidden"
    },
    cost: {
        title: "Cost",
        type: "currency",
        visibility: "hidden",
        width: "0%"
    },
    post_response: {
        title: "Post Response $",
        type: "currency",
        width: "0%",
        columnResizable: false
    },
    date_of_update: {
        title: "Date of Update",
        sorting: true,
        edit: false,
        create: false,
        type: "date",
        width: "0%",
        visibility: "hidden"
    },
    planned_closure: {
        title: "Planned Closure",
        type: "date",
        width: "0%"
    },
    current_status: {
        title: "Current Status",
        type: "textarea",
        maxlength: 250,
        width: "30%"
    }
};


defaultTaskMessages = {
    loadingMessage: "Loading tasks...",
    noDataAvailable: "There are no tasks for this project.",
    deleteConfirmation: "You are about to permanently delete this task including all associated risks and responses. Are you sure?",
    pageSizeChangeLabel: "Tasks per page",
    addNewRecord: "New Task",
    editRecord: "Edit Task"
};
defaultTaskFields = {
    task_id: {
        key: true,
        list: false
    },
    WBS: {
        title: "WBS",
        visibility: "fixed",
        columnResizable: false,
        width: "0%"
    },
    task_name: {
        title: "Task Name",
        type: "link",
        linkURL: config.base_url + "task/view",
        addKeyToURL: true,
        width: "10%"
    },
    duration: {
        title: "Duration",
        displayPostfix: " days",
        inputPostfix: " days",
        type: "number",
        max: 9999.99,
        min: 0,
        step: 0.01,
        width: "0%"
    },
    work: {
        title: "Work",
        displayPostfix: " hours",
        inputPostfix: " hours",
        type: "number",
        max: 99999,
        min: 0,
        width: "0%"
    },
    start_date: {
        title: "Start Date",
        type: "date",
        columnResizable: false,
        width: "0%"
    },
    finish_date: {
        title: "Finish Date",
        type: "date",
        columnResizable: false,
        width: "0%"
    },
    fixed_cost: {
        title: "Fixed Cost",
        type: "currency",
        width: "0%"
    },
    cost: {
        title: "Cost",
        type: "currency",
        width: "0%"
    },
    price: {
        title: "Price",
        type: "currency",
        width: "0%"
    },
    resource_names: {
        title: "Resource Names",
        maxlength: 250,
        width: "30%"
    },
    vendor: {
        title: "Supplier",
        maxlength: 30,
        width: "0%"
    },
    active_risks: {
        list: false,
        edit: false,
        create: false,
        width: "30%"
    },
    closed_risks: {
        list: false,
        edit: false,
        create: false,
        width: "30%"
    }
};

defaultRiskMessages = {
    loadingMessage: "Loading risks...",
    noDataAvailable: "There are no risks identified.",
    deleteConfirmation: "You are about to permanently delete this risk and all associated responses. Are you sure?",
    pageSizeChangeLabel: "Risks per page",
    addNewRecord: "New Risk",
    editRecord: "Edit Risk"
};
defaultRiskFields = {
    risk_id: {
        key: true,
        list: false
    },
    WBS: {
        title: "WBS",
        visibility: "fixed",
        create: false,
        input: function(data) {
            if (data.record)
                return '<input type="text" disabled="true" value="' + data.record.WBS + '"/>';
        },
        width: "0%",
        maxlength: 14,
        columnResizable: false
    },
    task_name: {
        title: "Task Name",
        type: "link",
        create: false,
        width: "0%"
    },
    event: {
        title: "Risk Event",
        type: "link",
        maxlength: 255,
        linkURL: config.base_url + "risk/view",
        addKeyToURL: true,
        width: "15%"
    },
    date_of_concern: {
        title: "Date of Concern",
        type: "date",
        width: "0%",
        maxlength: 12,
        columnResizable: false
    },
    impact: {
        title: "Impact",
        type: "textarea",
        maxlength: 250,
        width: "30%"
    },
    date_identified: {
        title: "Date Identified",
        type: "date",
        width: "0%",
        maxlength: 12,
        columnResizable: false,
        defaultValue: "" + new Date().getFullYear() + "-" + (new Date().getMonth()+1) + "-" + new Date().getDate()
    },
    days_open: {
        title: "Days Open",
        edit: false,
        create: false,
        width: "0%"
    },
    date_closed: {
        title: "Date Closed",
        type: "date",
        visibility: "hidden",
        width: "0%",
        maxlength: 12
    },
    type: {
        title: "Type",
        options: {
            Threat: "Threat",
            Opportunity: "Opportunity"
        },
        visibility: "hidden",
        width: "0%"
    },
    total_mitigation_cost: {
        title: "Mitigation Cost",
        type: "currency",
        edit: false,
        create: false,
        visibility: "hidden",
        width: "0%"
    },
    probability: {
        title: "Probability",
        displayPostfix: "%",
        inputPostfix: "%",
        type: "number",
        max: 100,
        min: 0,
        width: "0%",
        columnResizable: false
    },
    impact_effect: {
        title: "Impact Effect",
        inputPostfix: " (0-100)",
        type: "number",
        max: 100,
        min: 0,
        width: "0%"
    },
    days_delay: {
        title: "Days Delay",
        displayPostfix: " days",
        inputPostfix: " days",
        type: "number",
        max: 999999,
        min: -999999,
        step: 1,
        width: "0%"
    },
    cost_impact: {
        title: "Cost Impact",
        type: "currency",
        width: "0%"
    },
    overall_impact: {
        title: "Overall Impact",
        edit: false,
        create: false,
        columnResizable: false,
        maxlength: 7,
        width: "0%"
    },
    expected_cost: {
        title: "Expected Cost",
        edit: false,
        create: false,
        type: "currency",
        width: "0%",
        maxlength: 14
    },
    expected_delay: {
        title: "Expected Delay",
        edit: false,
        create: false,
        type: "number",
        width: "0%",
        maxlength: 14,
        displayPostfix: " days"
    },
    impact_discussion: {
        title: "Impact Discussion",
        type: "textarea",
        maxlength: 250,
        width: "55%"
    },
    adjusted_cost: {
        title: "Adjusted Cost",
        edit: false,
        create: false,
        type: "currency",
        width: "0%",
        maxlength: 14
    },
    priority_effect: {
        title: "Priority Effect",
        edit: false,
        create: false,
        width: "0%",
        columnResizable: false
    },
    priority_monetary: {
        title: "Priority ($)",
        edit: false,
        create: false,
        width: "0%",
        columnResizable: false
    },
    priority_days: {
        title: "Priority Days",
        edit: false,
        create: false,
        width: "0%",
        columnResizable: false
    },
    urgent: {
        title: "Severe?",
        options: {
            0: "No",
            1: "Yes"
        },
        width: '0%'
    },
    date_of_update: {
        title: "Date of Update",
        sorting: true,
        edit: false,
        create: false,
        type: "date",
        width: "0%",
        columnResizable: false,
        maxlength: 14
    }
};

default_ShortRiskFields = {
    risk_id: {
        key: true,
        list: false
    },
    WBS: {
        title: "WBS",
        visibility: "fixed",
        create: false,
        input: function(data) {
            if (data.record)
                return '<input type="text" disabled="true" value="' + data.record.WBS + '"/>';
        },
        width: "0%",
        maxlength: 14,
        columnResizable: false
    },
    task_name: {
        title: "Task Name",
        type: "link",
        create: false,
        visibility: "hidden",
        width: "0%"
    },
    event: {
        title: "Risk Event",
        type: "link",
        maxlength: 255,
        linkURL: config.base_url + "risk/view",
        addKeyToURL: true,
        width: "15%"
    },
    date_of_concern: {
        title: "Date of Concern",
        type: "date",
        width: "0%",
        maxlength: 12,
        columnResizable: false
    },
    impact: {
        title: "Impact",
        type: "textarea",
        maxlength: 250,
        width: "30%"
    },
    date_identified: {
        title: "Date Identified",
        type: "date",
        width: "0%",
        visibility: "hidden",
        maxlength: 12,
        defaultValue: "" + new Date().getFullYear() + "-" + (new Date().getMonth()+1) + "-" + new Date().getDate()
    },
    days_open: {
        title: "Days Open",
        edit: false,
        create: false,
        width: "0%",
        visibility: "hidden"
    },
    date_closed: {
        title: "Date Closed",
        type: "date",
        visibility: "hidden",
        width: "0%",
        maxlength: 12
    },
    type: {
        title: "Type",
        options: {
            Threat: "Threat",
            Opportunity: "Opportunity"
        },
        visibility: "hidden",
        width: "0%"
    },
    total_mitigation_cost: {
        title: "Mitigation Cost",
        type: "currency",
        edit: false,
        create: false,
        visibility: "hidden",
        width: "0%"
    },
    probability: {
        title: "Probability",
        displayPostfix: "%",
        inputPostfix: "%",
        type: "number",
        max: 100,
        min: 0,
        width: "0%",
        columnResizable: false
    },
    impact_effect: {
        title: "Impact Effect",
        inputPostfix: " (0-100)",
        type: "number",
        max: 100,
        min: 0,
        width: "0%",
        visibility: "hidden"
    },
    days_delay: {
        title: "Days Delay",
        displayPostfix: " days",
        inputPostfix: " days",
        type: "number",
        max: 999999,
        min: -999999,
        step: 1,
        width: "0%",
        visibility: "hidden"
    },
    cost_impact: {
        title: "Cost Impact",
        type: "currency",
        width: "0%",
        visibility: "hidden"
    },
    overall_impact: {
        title: "Overall Impact",
        edit: false,
        create: false,
        columnResizable: false,
        maxlength: 7,
        width: "0%"
    },
    expected_cost: {
        title: "Expected Cost",
        edit: false,
        create: false,
        type: "currency",
        width: "0%",
        maxlength: 14
    },
    expected_delay: {
        title: "Expected Delay",
        edit: false,
        create: false,
        type: "number",
        width: "0%",
        maxlength: 14,
        displayPostfix: " days",
        visibility: "hidden"
    },
    impact_discussion: {
        title: "Impact Discussion",
        type: "textarea",
        visiblity: "hidden",
        maxlength: 250,
        width: "55%",
        visibility: "hidden"
    },
    adjusted_cost: {
        title: "Adjusted Cost",
        edit: false,
        create: false,
        type: "currency",
        width: "0%",
        maxlength: 14
    },
    priority_effect: {
        title: "Priority Effect",
        edit: false,
        create: false,
        width: "0%",
        columnResizable: false
    },
    priority_monetary: {
        title: "Priority ($)",
        edit: false,
        create: false,
        width: "0%",
        columnResizable: false
    },
    priority_days: {
        title: "Priority Days",
        edit: false,
        create: false,
        width: "0%",
        columnResizable: false,
        visibility: "hidden"
    },
    urgent: {
        title: "Severe?",
        options: {
            0: "No",
            1: "Yes"
        },
        width: '0%',
        visibility: "hidden"
    }
};

defaultResponseUpdateMessages = {
    loadingMessage: "Loading response updates...",
    noDataAvailable: "There have been no updates to this response",
    deleteConfirmation: "You are about to permanently delete this response update. Are you sure?",
    pageSizeChangeLabel: "Items per page",
    editRecord: "Edit Update",
    addNewRecord: "New Update"
};
defaultResponseUpdateFields = {
    response_update_id: {
        key: true,
        list: false
    },
    date_of_update: {
        title: "Date of Update",
        sorting: true,
        edit: false,
        create: false,
        type: "date",
        width: "0%",
        columnResizable: false,
        maxlength: 14
    },
    owner: {
        title: "Owner",
        maxlength: 20,
        width: "10%",
        columnResizable: false
    },
    release_progress: {
        title: "Release Progress",
        options: {
            Planning: "Planning",
            Ongoing: "Ongoing",
            Complete: "Complete",
            Cancelled: "Cancelled"
        },
        width: "10%",
        columnResizable: false,
        maxlength: 11
    },
    planned_closure: {
        title: "Planned Closure",
        type: "date",
        width: "0%",
        columnResizable: false,
        maxlength: 14
    },
    current_status: {
        title: "Current Status",
        maxlength: 250,
        width: "10%"
    },
    cost: {
        title: "Cost",
        type: "currency",
        width: "0%",
        columnResizable: false,
        maxlength: 10
    },
    post_response: {
        title: "Post Response $",
        type: "currency",
        width: "0%",
        columnResizable: false
    }
};

defaultProjectMessages = {
    deleteConfirmation: "You are about to delete this project and all associated data. This cannot be undone. Are you sure?",
    loadingMessage: "Loading projects...",
    noDataAvailable: "There are no projects to display!",
    pageSizeChangeLabel: "Projects per page",
    editRecord: "Edit Project",
    addNewRecord: "New Project"
};
defaultProjectFields = {
    project_id: {
        key: true,
        list: false
    },
    project_name: {
        title: 'Project Name',
        type: "link",
        maxlength: 255,
        linkURL: config.base_url + "project/view",
        addKeyToURL: true,
        width: "10%"
    },
    num_tasks: {
        title: 'Tasks',
        edit: false,
        create: false,
        width: "10%"
    },
    active_risks: {
        title: 'Active Risks',
        edit: false,
        create: false,
        width: "10%"
    },
    closed_risks: {
        title: 'Closed Risks',
        edit: false,
        create: false,
        width: "10%"
    },
    total_expected_cost: {
        title: 'Total Expected Cost',
        edit: false,
        create: false,
        type: 'currency',
        width: "0%"
    },
    project_total_mitigation_cost: {
        title: 'Total Mitigation Cost',
        edit: false,
        create: false,
        type: 'currency',
        width: "0%"
    },
    date_modified: {
        title: 'Last Updated',
        type: 'date',
        edit: false,
        create: false,
        width: "0%"
    },
    last_modifier: {
        title: 'Updated By',
        edit: false,
        create: false,
        width: "5%"
    },
    status: {
        title: 'Status',
        edit: false,
        create: false,
        width: "10%"
    }

};
LessonsLearnedFields = {
    risk_id: {
        key: true,
        list: false
    },
    occurred: {
        title: "Occurred",
        width: "0%",
        options: {
            yes: 'Yes',
            no: 'No'
        },
        edit: false,
        create: false
    },
    date_closed: {
        title: "Date Closed",
        type: "date",
        width: "3%",
        maxlength: 12
    },
    probability: {
        title: "Probability",
        displayPostfix: "%",
        inputPostfix: "%",
        type: "number",
        max: 100,
        min: 0,
        width: "1%"
    },
    event: {
        title: "Risk Event",
        type: "link",
        maxlength: 255,
        linkURL: config.base_url + "risk/view",
        addKeyToURL: true,
        width: "26%"
    },
    cause: {
        title: "Cause",
        type: "multiselectddl",
        width: '70%',
        options: {
            owner: '    Owner',
            architect: '    Architect',
            engineer: '    Engineer',
            sub_contractor: '    Sub Contractor',
            nature: '    Nature',
            supplier: '    Supplier',
            GC: '    GC',
            personnel: '    PERSONNEL',
            personnel_time: ' Time',
            personnel_know_how: ' Know How',
            personnel_communications: ' Communications',
            personnel_authority: ' Authority',
            personnel_desire: ' Desire',
            process: '    PROCESS',
            process_time: ' Time',
            process_resources: ' Resources',
            process_technology: ' Technology',
            process_customer: ' Customer',
            process_management: ' Management',
            //Display set to none
            not_identified: '    Not identified'
         }
    }
    ,
    date_of_update: {
        title: "Date of Update",
        sorting: true,
        edit: false,
        create: false,
        type: "date",
        width: "0%"
    },
    days_open: {
        title: "Days Open",
        edit: false,
        create: false,
        width: "0%"
    }
};
LessonsLearnedResponseFields = {
    response_id: {
        key: true,
        list: false
    },
    successful: {
        title: "Successful",
        width: "0%",
        options: {
            yes: 'Yes',
            no: 'No'
        },
        edit: false,
        create: false
    },
    action_plan: {
        title: "Action Plan",
        type: "link",
        maxlength: 250,
        linkURL: config.base_url + "response/view",
        addKeyToURL: true,
        width: "30%"
    },
    date_of_update: {
        title: "Date of Update",
        sorting: true,
        edit: false,
        create: false,
        type: "date",
        width: "0%"
    },
    release_progress: {
        title: "Release Progress",
        options: {
            Planning: "Planning",
            Ongoing: "Ongoing",
            Complete: "Complete",
            Cancelled: "Cancelled"
        },
        width: "0%"
    }
    ,
    cause: {
        title: "Cause",
        type: "multiselectddl",
        width: '70%',
        options: {
            owner: '    Owner',
            architect: '    Architect',
            engineer: '    Engineer',
            sub_contractor: '    Sub Contractor',
            nature: '    Nature',
            supplier: '    Supplier',
            GC: '    GC',
            personnel: '    PERSONNEL',
            personnel_time: ' Time',
            personnel_know_how: ' Know How',
            personnel_communications: ' Communications',
            personnel_authority: ' Authority',
            personnel_desire: ' Desire',
            process: '    PROCESS',
            process_time: ' Time',
            process_resources: ' Resources',
            process_technology: ' Technology',
            process_customer: ' Customer',
            process_management: ' Management',
            //Display set to none
            not_identified: '    Not identified'
         }
    }
};
defaultUserMessages = {
    deleteConfirmation: 'This user will no longer have access. This cannot be undone. Are you sure?',
    loadingMessage: "Loading users...",
    noDataAvailable: "There are no users to display!",
    pageSizeChangeLabel: "Users per page",
    editRecord: "Edit User",
    addNewRecord: "Create User"
};
function getRiskFieldsSmall() {
    var fields = $.extend(true, {}, defaultRiskFields); //copy, don't reference
    fields.impact.visibility = 'hidden';
    fields.date_identified.visibility = 'hidden';
    fields.days_open.visibility = 'hidden';
    fields.probability.visibility = 'hidden';
    fields.impact_effect.visibility = 'hidden';
    fields.cost_impact.visibility = 'hidden';
    fields.overall_impact.visibility = 'hidden';
    fields.urgent.visibility = 'hidden';
    fields.priority_effect.visibility = 'hidden';
    fields.impact_discussion.visibility = 'hidden';
    return fields;
}
function getResponseFieldsSmall() {
    var fields = $.extend(true, {}, defaultResponseFields); //copy, don't reference
    fields.WBS.visibility = 'hidden';
    fields.date_of_plan.visibility = 'hidden';
    fields.action.visibility = 'hidden';
    fields.risk_statement.visibility = 'hidden';
    fields.date_of_update.visibility = 'hidden';
    fields.planned_closure.visibility = 'hidden';
    fields.current_status.visibility = 'hidden';
    return fields;
}
function getResponseChildTable(ContainerID) {
    var table = {
        title: '',
        width: '5%',
        sorting: false,
        edit: false,
        create: false,
        display: function(data) {
            //create an image to be used to open child table
            var $img = $('<img src="' + config.base_url + 'assets/images/expand_row-small.png" title="View Responses" style="height:30px;width:30px;cursor:pointer;" height="30" width="30"/>');
            $img.click(function() {
                $('#' + ContainerID).jtable('openChildTable',
                        $img.closest('tr'),
                        {
                            title: data.record.event,// + ' - Response Plans'
                            actions: {
                                listAction: config.base_url + "data_fetch/responses/" + data.record.risk_id,
                                updateAction: config.base_url + 'data_fetch/edit_response/'
                            },
                            messages: defaultResponseMessages,
                            fields: getResponseFieldsSmall()
                        }, function(data) {//opened handler
                    data.childTable.jtable('load');
                });
            });
            //return image to show on row
            return $img;
        }
    };
    return table;
}
function getLessonsLearnedResponseChildTable(ContainerID, project_id) {
    var fields = {
        successful: {
            title: "Successful?",
            display: function(data) {
                if ( data.record.successful == 'yes' ) {
                    return "<div style='width:100%;text-align:center;'><input type='checkbox' checked='true' id='checkbox" + data.record.response_id + "' onclick='successful_checkbox_clicked(" + data.record.response_id + ", " + data.record.risk_id + ");'></div>";
                }
                else if ( data.record.successful == 'no' || data.record.successful == '' || data.record.successful == 'not_identified') {
                    return "<div style='width:100%;text-align:center;'><input type='checkbox' id='checkbox" + data.record.response_id + "' onclick='successful_checkbox_clicked(" + data.record.response_id + ", " + data.record.risk_id + ");'></div>";
                }
            },
            width: "0%",
            columnResizable: false,
            sorting: false,
            edit: false,
            create: false
        }
    };
    
    fields = $.extend(true, fields, LessonsLearnedResponseFields); //copy, don't reference
    fields.successful.width = '0%';
    fields.action_plan.width = '35%';
    fields.date_of_update.width = '0%';
    fields.release_progress.width = '0%';
    fields.cause.width = '50%';

    var table = {
        title: '',
        width: '5%',
        sorting: false,
        create: false,
        display: function(data) {
            //create an image to be used to open child table
            var $img = $('<img src="' + config.base_url + 'assets/images/expand_row-small.png" title="View Responses" class="row-opener" style="height:30px;width:30px;cursor:pointer;" height="30" width="30" id="response_img_' + data.record.risk_id + '"/>');
            $img.click(function() {
                $('#' + ContainerID).jtable('openChildTable',
                        $img.closest('tr'),
                        {
                            title: data.record.event,// + ' - Response Plans'
                            actions: {
                                listAction: config.base_url + "data_fetch/responses/" + data.record.risk_id,
                                deleteAction: config.base_url + 'data_fetch/delete_response/',
                                updateAction: config.base_url + 'data_fetch/update_lessons_learned_response/'
                            },
                            messages: defaultResponseMessages,
                            fields: fields
                            ,
                            formSubmitting: function(event,data){
                                $('select[name=cause]', data.form).attr('name','cause[]');
                                // location.assign(config.base_url + 'project/view/' + project_id + '/6');
                                return data;
                            }
                        }, function(data) {//opened handler
                    data.childTable.jtable('load');
                });
            });
            //return image to show on row
            return $img;
        }
    };
    return table;
}
function getReportLessonsLearnedResponseChildTable(ContainerID, project_id) {
    var fields = $.extend(true, defaultResponseFields, LessonsLearnedResponseFields); //copy, don't reference
    
    fields.WBS.visibility = 'hidden';
    fields.risk_statement.visibility = 'hidden';
    fields.date_of_plan.visibility = 'hidden';
    fields.owner.visibility = 'hidden';
    fields.action.visibility = 'hidden';
    fields.cost.visibility = 'hidden';
    fields.planned_closure.visibility = 'hidden';
    fields.current_status.visibility = 'hidden';
    
    fields.action_plan.width = '45%';
    fields.release_progress.width = '5%';
    fields.date_of_update.width = '2%';
    fields.successful.width = '0%';
    fields.cause.width = '48%';

    fields.action_plan.columnResizable = true;
    fields.release_progress.columnResizable = true;
    fields.date_of_update.columnResizable = true;
    fields.successful.columnResizable = true;
    fields.cause.columnResizable = true;

    var table = {
        title: '',
        width: '5%',
        sorting: false,
        create: false,
        edit: false,
        display: function(data) {
            //create an image to be used to open child table
            var $img = $('<img src="' + config.base_url + 'assets/images/expand_row-small.png" title="View Responses" class="row-opener" style="height:30px;width:30px;cursor:pointer;" height="30" width="30" id="response_img_' + data.record.risk_id + '"/>');
            $img.click(function() {
                $('#' + ContainerID).jtable('openChildTable',
                        $img.closest('tr'),
                        {
                            title: data.record.event,// + ' - Response Plans'
                            actions: {
                                listAction: config.base_url + "data_fetch/responses/" + data.record.risk_id
                            },
                            messages: defaultResponseMessages,
                            fields: fields
                        }, function(data) {//opened handler
                    data.childTable.jtable('load');
                });
            });
            //return image to show on row
            return $img;
        }
    };
    return table;
}
function getReportLessonsLearnedFullResponseChildTable(ContainerID, project_id) {
    fields = $.extend(true, defaultResponseFields, LessonsLearnedResponseFields); //copy, don't reference

    var table = {
        title: '',
        width: '5%',
        sorting: false,
        create: false,
        edit: false,
        display: function(data) {
            //create an image to be used to open child table
            var $img = $('<img src="' + config.base_url + 'assets/images/expand_row-small.png" title="View Responses" class="row-opener" style="height:30px;width:30px;cursor:pointer;" height="30" width="30" id="response_img_' + data.record.risk_id + '"/>');
            $img.click(function() {
                $('#' + ContainerID).jtable('openChildTable',
                        $img.closest('tr'),
                        {
                            title: data.record.event,// + ' - Response Plans'
                            actions: {
                                listAction: config.base_url + "data_fetch/responses/" + data.record.risk_id
                            },
                            messages: defaultResponseMessages,
                            fields: fields
                        }, function(data) {//opened handler
                    data.childTable.jtable('load');
                });
            });
            //return image to show on row
            return $img;
        }
    };
    return table;
}
function loadDashboardProjectTable(ContainerID) {
    var actions = {
        listAction: config.base_url + 'data_fetch/projects'
    };
    actions.deleteAction = config.base_url + 'data_fetch/delete_project';
    actions.createAction = config.base_url + 'data_fetch/create_project';
    actions.updateAction = config.base_url + 'data_fetch/edit_project';
    $('#' + ContainerID).jtable({
        title: 'Projects',
        paging: true,
        pageSize: 25,
        sorting: true,
        defaultSorting: 'project_name ASC',
        actions: actions,
        messages: defaultProjectMessages,
        fields: defaultProjectFields
    });
    $('#' + ContainerID).jtable('load');
}
function loadDashboardRiskTable(ContainerID, FilterButtonID, FilterFieldID, FilterTextID) {
    var fields = $.extend(true, {project_name: defaultProjectFields.project_name, occurred: LessonsLearnedFields.occurred, cause: LessonsLearnedFields.cause}, defaultRiskFields); //copy, don't reference
    
            fields.project_name.width = '10%';
            fields.cause.width = '22%';
    fields.occurred.width = '0%';
    fields.WBS.width = '0%';
            fields.event.width = '20%';
    fields.date_of_concern.width = '0%';
            fields.impact.width = '23%';
    fields.probability.width = '0%';
    fields.overall_impact.width = '0%';
    fields.expected_cost.width = '0%';
    fields.priority_effect.width = '0%';
    fields.priority_monetary.width = '0%';
    
    fields.task_name.visibility = 'hidden';
    fields.date_identified.visibility = 'hidden';
    fields.days_open.visibility = 'hidden';
    fields.date_closed.visibility = 'hidden';
    fields.type.visibility = 'hidden';
    fields.total_mitigation_cost.visibility = 'hidden';
    fields.impact_effect.visibility = 'hidden';
    fields.cost_impact.visibility = 'hidden';
    fields.impact_discussion.visibility = 'hidden';
    fields.urgent.visibility = 'hidden'; 

    delete fields.project_name.linkURL;
    delete fields.project_name.addKeyToURL;
    
    $('#' + ContainerID).jtable({
        title: 'Risks',
        paging: true,
        pagesize: 25,
        sorting: true,
        defaultSorting: 'WBS ASC',
        actions: {
            listAction: config.base_url + 'data_fetch/risks_by_user'
        },
        messages: defaultRiskMessages,
        fields: fields,
        toolbar: {
            items: [{
                    text: 'Full Global Risk Report',
                    click: function() {
                        window.open(config.base_url + 'global_risk_report', '_blank');
                    },
                    cssClass: 'toolbar-button button button-flat'
                },
            {
                    text: 'Global Risk Report',
                    click: function() {
                        window.open(config.base_url + 'short_global_risk_report', '_blank');
                    },
                    cssClass: 'toolbar-button button button-flat'
                }]
        }
    });
    //reload records when user clicks filter button
    $('#' + FilterButtonID).click(function(e) {
        e.preventDefault();
        $('#' + ContainerID).jtable('load', {
            filter_field: $('#' + FilterFieldID).val(),
            filter_value: $('#' + FilterTextID).val()
        });
    });
    $('#' + FilterButtonID).click();
}
function loadViewProjectUpcomingRisksTable(ContainerID, project_id) {
    var UpcomingRisksTableFields = getRiskFieldsSmall();
    UpcomingRisksTableFields.Responses = getResponseChildTable(ContainerID);
    $('#' + ContainerID).jtable({
        title: 'Upcoming Risks',
        paging: false,
        sorting: false,
        actions: {
            listAction: config.base_url + 'data_fetch/upcoming_risks/' + project_id
        },
        messages: defaultRiskMessages,
        fields: UpcomingRisksTableFields
    });
    $('#' + ContainerID).jtable('load');
}
function loadViewProjectTopRisksTable(ContainerID, project_id) {
    var TopRisksTableFields = getRiskFieldsSmall();
    TopRisksTableFields.Responses = getResponseChildTable(ContainerID);
    $('#TopRisksTableContainer').jtable({
        title: 'Top Risks By Cost',
        paging: false,
        sorting: false,
        actions: {
            listAction: config.base_url + 'data_fetch/top_risks/' + project_id
        },
        messages: defaultRiskMessages,
        fields: TopRisksTableFields
    });
    $('#' + ContainerID).jtable('load');
}
function loadViewProjectSevereRisksTable(ContainerID, project_id, paging) {
    var SevereRisksTableFields = getRiskFieldsSmall();
    SevereRisksTableFields.Responses = getResponseChildTable(ContainerID);

    $('#' + ContainerID).jtable({
        title: 'Severe Risks',
        paging: paging,
        pageSize: 5,
        sorting: true,
        actions: {
            listAction: config.base_url + 'data_fetch/severe_risks/' + project_id
        },
        messages: defaultRiskMessages,
        fields: SevereRisksTableFields
    });
    $('#' + ContainerID).jtable('load');
}

function loadViewProjectAllRisksTable(ContainerID, project_id, paging, no_toolbar) {
    var AllRisksTableFields = getRiskFieldsSmall();
    AllRisksTableFields.Responses = getResponseChildTable(ContainerID);
    var toolbar;
    if (no_toolbar === true) {
        toolbar = {}
    } else {
        toolbar = {
            items: [{
                        text: 'View Full Report',
                        click: function() {
                            window.open(config.base_url + 'project/risks_with_responses_report/' + project_id, '_blank');
                        },
                        cssClass: 'toolbar-button button button-flat'
                    }]
        };
    }
    $('#' + ContainerID).jtable({
        title: 'All Risks',
        paging: paging,
        pageSize: 5,
        sorting: true,
        actions: {
            listAction: config.base_url + 'data_fetch/risks_by_project/' + project_id
        },
        messages: defaultRiskMessages,
        fields: AllRisksTableFields,
        toolbar: toolbar
    });
    $('#' + ContainerID).jtable('load');
}
function loadViewProjectTasksTable(ContainerID, project_id, modify) {
    var actions = {
        listAction: config.base_url + 'data_fetch/tasks/' + project_id
    };
    if (modify) {
        actions.deleteAction = config.base_url + 'data_fetch/delete_task/';
        actions.createAction = config.base_url + 'data_fetch/create_task/' + project_id;
        actions.updateAction = config.base_url + 'data_fetch/edit_task/';
    }
    fields = {
        has_risk: {
            title: "Risk?",
            display: function(data) {
                if (parseInt(data.record.active_risks) > 0 || parseInt(data.record.closed_risks) > 0)
                    return "<div style='width:100%;text-align:center;'><input type='checkbox' checked disabled=true></div>";
                else
                    return "<div style='width:100%;text-align:center;'><input type='checkbox' id='checkbox" + data.record.task_id + "' onclick='checkbox_clicked(" + data.record.task_id + ");'></div>";
            },
            width: "1%",
            columnResizable: false,
            sorting: false,
            edit: false,
            create: false
        }
    };
    fields = $.extend(true, fields, defaultTaskFields); //copy, don't reference

    var fancy_icon = {
        alpha_icon: {
            title: "",
            display: function(data) {
                if (parseInt(data.record.active_risks) > 0 || parseInt(data.record.closed_risks) > 0)
                    return "<div style='width:100%;text-align:center;'><img src='" + config.base_url + "assets/images/ic_filter_tilt_shift_black_24dp.png' title='Click to indentify another risk for this task and edit it.' style='height: 20px; width: 20px; cursor: pointer;' onclick='alphaIcon_clicked_again(" + data.record.task_id + ")'></div>";
                else
                    return "<div style='width:100%;text-align:center;'><img src='" + config.base_url + "assets/images/ic_adjust_black_24dp.png' title='Click to indentify risk and edit it.' style='height: 20px; width: 20px; cursor: pointer;' onclick='alphaIcon_clicked(" + data.record.task_id + ")'></div>";
            },
            width: "1%",
            columnResizable: false,
            sorting: false,
            edit: false,
            create: false
        }
    };
    fields = $.extend(true, fields, fancy_icon); //copy, don't reference
    $('#' + ContainerID).jtable({
        title: 'Tasks',
        paging: true,
        pageSize: 100,
        sorting: true,
        defaultSorting: 'WBS ASC',
        actions: actions,
        messages: defaultTaskMessages,
        fields: fields
    });
    $('#' + ContainerID).jtable('load');
}
function loadViewProjectTasksWithRisksTable(ContainerID, project_id, modify) {
    var actions = {
        listAction: config.base_url + 'data_fetch/tasks_with_risks/' + project_id
    };
    if (modify) {
        actions.deleteAction = config.base_url + 'data_fetch/delete_task/';
        //actions.createAction = config.base_url + 'data_fetch/create_task/' + project_id;
        actions.updateAction = config.base_url + 'data_fetch/edit_task/';
    }
    $('#' + ContainerID).jtable({
        title: 'Tasks With Risks',
        paging: true,
        pageSize: 100,
        sorting: true,
        defaultSorting: 'WBS ASC',
        actions: actions,
        toolbar: {
            items: [{
                    text: 'View Full Report',
                    click: function() {
                        window.open(config.base_url + 'project/task_report/' + project_id, '_blank');
                    },
                    cssClass: 'toolbar-button button button-flat'
                },
                {
                    text: 'View Report',
                    click: function() {
                        window.open(config.base_url + 'project/short_task_report/' + project_id, '_blank');
                    },
                    cssClass: 'toolbar-button button button-flat'
                },
                {
                    text: 'View PDF Report',
                    click: function() {
                        window.open(config.base_url + 'project/short_task_pdf_report/' + project_id, '_blank');
                    },
                    cssClass: 'toolbar-button button button-flat'
                }]
        },
        messages: defaultTaskMessages,
        fields: defaultTaskFields,
    });
    $('#' + ContainerID).jtable('load');
}
function loadViewProjectRisksTable(ContainerID, project_id, modify) {
    var actions = {
        listAction: config.base_url + 'data_fetch/risks_by_project/' + project_id
    };
    if (modify) {
        actions.deleteAction = config.base_url + 'data_fetch/delete_risk/';
        actions.updateAction = config.base_url + 'data_fetch/edit_risk/';
    }
    var fields = {
        FileUpload: {
            title: 'Media',
            list: false,
            create: true,
            edit: true,
            input: function (data) {
                return '<div id="FileUpload" name="FileUpload"></div>';
            }
        },
        Addtocal: {
            title: 'Add Risk Event',
            create: false,
            edit: false,
            sorting: false,
            width: '1%',
            columnResizable: false,
            display: function (data) {
                var impact_discussion_desc_table = "";
                var media_desc_table = "";
                if (data.record.impact_discussion) {
                    impact_discussion_desc_table = data.record.impact_discussion;
                }
                else {
                    impact_discussion_desc_table = 'No Impact Discussion.';
                }
                if (data.record.img_url) {
                    data.record.img_url.split(',').forEach(function (media_item) {
                        media_desc_table += 'http://v1.riskmp.com/assets/images/uploads/' + media_item + '\n';
                    });
                }
                else {
                    media_desc_table = 'No Uploaded Media items.';
                }
                var event_style = '<link rel="stylesheet" type="text/css" href="http://v1.riskmp.com/assets/css/addthiseventicon.css">';
                var event_adder = "<script type='text/javascript' src='http://v1.riskmp.com/assets/js/pagelevel/addtocal.js'></script>";
                var event_adder_1 = "<a href='' title='Add to Calendar' class='addthisevent'>";
                var event_adder_2 = "<span class='_start'>" + data.record.date_of_concern + "</span>";
                var event_adder_3 = '<span class="_end">' + data.record.date_of_concern + '</span>';
                var event_adder_4 = '<span class="_zonecode">15</span>';
                var event_adder_5 = '<span class="_summary">' + data.record.event + '</span>';
                var event_adder_6 = "<span class='_description'> Impact Discussion: \n" + impact_discussion_desc_table + "\n Risk Media: \n" + media_desc_table + "</span>";
                var event_adder_7 = '<span class="_organizer">Me</span>';
                var event_adder_8 = '<span class="_organizer_email"></span>';
                var event_adder_9 = '<span class="_all_day_event">true</span>';
                var event_adder_10 = '<span class="_date_format">DD/MM/YYYY</span>';
                var event_adder_11 = '</a>';
                var final_string = event_adder.concat(event_style,event_adder_1,event_adder_2,event_adder_3,event_adder_4,event_adder_5,event_adder_6,event_adder_7,event_adder_8,event_adder_9,event_adder_10,event_adder_11);
                return final_string;
            }
        },
        img_url: {
            title: 'Media',
            list: true,
            create: false,
            edit: false,
            width: '1%',
            columnResizable: false,
            sorting: false,
            type: "link",
            linkURL: config.base_url + "assets/images/uploads",
            addKeyToURL: true,
            isMedia: true
        },
        timestamp_of_update: {
            title: "timestamp",
            sorting: true,
            edit: false,
            create: false,
            list: false,
            width: "0%",
            columnResizable: false
        },
        start_date: {
            title: "Task Start Date",
            type: "date",
            edit: false,
            create: false,
            list: false,
            columnResizable: false,
            width: "0%"
        }
    };
    var media_uploaded = false;
    var all_files = "";
    var files_array = [];
    fields = $.extend(true, defaultRiskFields, fields); //copy, don't reference
    fields.date_of_update.visibility = 'hidden';
    fields.date_identified.visibility = 'hidden';
    $('#' + ContainerID).jtable({
        title: 'Risks',
        sorting: true,
        defaultSorting: 'timestamp_of_update DESC',
        actions: actions,
        messages: defaultRiskMessages,
        fields: fields,
        toolbar: {
            items: [{
                    text: 'Full Risk Identification Report',
                    click: function() {
                        window.open(config.base_url + 'project/risk_report/' + project_id, '_blank');
                    },
                    cssClass: 'toolbar-button button button-flat'
                },
                {
                    text: 'Risk Identification Report',
                    click: function() {
                        window.open(config.base_url + 'project/short_risk_report/' + project_id, '_blank');
                    },
                    cssClass: 'toolbar-button button button-flat'
                },
                {
                    text: 'Risk Identification PDF Report',
                    click: function() {
                        window.open(config.base_url + 'project/short_risk_pdf_report/' + project_id, '_blank');
                    },
                    cssClass: 'toolbar-button button button-flat'
                }]
        },
        formCreated: function (event, data){
            if (data.record.start_date) {
                // alert(data.record.start_date);
                fields.date_of_concern.defaultValue = data.record.start_date;
            }
            // var files_array = [];
            if ( data.record.img_url && data.record.img_url !== '' ) { 
                img_url_array = data.record.img_url.split(',');
                original_array_size = img_url_array.length;
            }
            else {
                original_array_size = 0;    
            }    
            risk_id = data.record.risk_id;
            data.form.attr('enctype','multipart/form-data');
            $("#FileUpload").uploadFile({
                url:config.base_url + "assets/files/uploader.php",
                fileName:"file",
                maxFileCount: 3,
                onSubmit: function (files) {
                    var file_exists = false;
                    var url = config.base_url + 'assets/images/uploads/' + files;
                    $.ajax({
                        async: false,
                        url: url,
                        type: 'HEAD',
                        success: function() {
                            file_exists = true;
                            alert('A file named: ' + files + ' already exists. Please rename your file and try again.');
                            $("#FileUpload").stopUpload();                            
                        },
                        error: function() {
                            file_exists =  false;
                        } 
                    });
                
                    if ( original_array_size >= 3 ) {
                        if ( confirm('3 media items already exist. Continuing with this upload will remove previous uploads!') ) {
                            original_array_size = 1;
                        }
                        else {
                            $("#FileUpload").stopUpload();
                            alert('Upload cancelled.');
                        }    
                    }
                    else {
                        original_array_size = original_array_size + 1;
                    }
                    if(/^[a-zA-Z0-9- ._]*$/.test(files) == false) {
                        alert('This file: ' + files + ' that you are trying to upload contains illegal characters in its name. Please rename the file and try again.');
                        original_array_size = original_array_size - 1;
                        $("#FileUpload").stopUpload();
                    }
                }, 
                onSuccess:function(files,data,xhr){
                    media_uploaded = true;
                        // all_files = files + ' ';
                        // console.log("files: " + files);
                        // console.log("all_files: " + all_files);
                    // files_array.push(new Date().getTime() + '_' + files);
                    files_array.push(files);
                    files_array.forEach( function(file_in_array) {
                       console.log(file_in_array); 
                    } );
                    UploadedFile = data;
                }
            });
            
            // var impact_discussion_desc = "";
            // var media_desc = "";
            // if (data.record.impact_discussion) {
            //     impact_discussion_desc = data.record.impact_discussion;
            // }
            // else {
            //     impact_discussion_desc = 'No Impact Discussion.';
            // }
            // if (data.record.img_url) {
            //     data.record.img_url.split(',').forEach(function (media_item) {
            //         media_desc += config.base_url + 'assets/images/uploads/' + media_item + '\n';
            //     });
            // }
            // else {
            //     media_desc = 'No Uploaded Media items.'
            // }
            // var $containerDiv = $('#Addtocal');
            // // var $script = $('<script type="text/javascript" src="http://v1.riskmp.com/assets/js/pagelevel/addtocal.js"></script>').appendTo($containerDiv);
            // var $anchor = $('<a href="" title="Add to Calendar" class="addthisevent">Add to Calendar</a>').appendTo($containerDiv);
            // var $span_start = $('<span class="_start">' + data.record.date_of_concern + '</span>').appendTo($anchor);
            // var $span_end = $('<span class="_end">' + data.record.date_of_concern + '</span>').appendTo($anchor);
            // var $span_zonecode = $('<span class="_zonecode">15</span>').appendTo($anchor);
            // var $span_summary = $('<span class="_summary">' + data.record.event + '</span>').appendTo($anchor);
            // var $span_description = $("<span class='_description'> Impact Discussion: \n" + impact_discussion_desc + "\n Risk Media: \n" + media_desc + "</span>").appendTo($anchor);
            // var $span_organizer = $('<span class="_organizer">Me</span>').appendTo($anchor);
            // var $span_organizer_email = $('<span class="_organizer_email"></span>').appendTo($anchor);
            // var $span_all_day_event = $('<span class="_all_day_event">true</span>').appendTo($anchor);
            // var $span_date_format = $('<span class="_date_format">DD/MM/YYYY</span>').appendTo($anchor);
        
        },
        formSubmitting: function (event, data) {
            if (media_uploaded) {
                $("#FileUpload").html('<input type="text" id="img_name" name="img_url" value="' + files_array.join(',') + '"><input type="text" id="img_name_size" name="img_url_size" value="' + files_array.length + '">');
                console.log(files_array.join(','));
                files_array = [];
                // location.assign(config.base_url + 'project/view/' + project_id + '/3');
            }
            else {
                $("#FileUpload").html('<input type="text" id="img_name" name="img_url" value=""><input type="text" id="img_name_size" name="img_url_size" value="0">');
                files_array = [];
                // location.assign(config.base_url + 'project/view/' + project_id + '/3');
            }    
        },
        rowsRemoved: function (event, data) {
            $('#TasksWithRisksTableContainer').jtable('reload');
            $('#TaskTableContainer').jtable('reload');
        }

    });
    $('#' + ContainerID).jtable('load');
}
function loadViewProjectUsersTable(ContainerID, project_id) {
    $('#' + ContainerID).jtable({
        title: 'Users',
        paging: false,
        sorting: true,
        defaultSorting: 'last_name ASC',
        messages: {
            addNewRecord: 'Add User'
        },
        actions: {
            listAction: config.base_url + 'data_fetch/project_users/' + project_id,
            deleteAction: config.base_url + 'data_fetch/delete_project_user/' + project_id,
            createAction: config.base_url + 'data_fetch/add_project_user/' + project_id
        },
        fields: {
            user_id: {
                key: true,
                list: false
            },
            username: {
                title: 'Username',
                create: true,
                edit: false
            },
            first_name: {
                title: 'First Name',
                create: false,
                edit: false
            },
            last_name: {
                title: 'Last Name',
                create: false,
                edit: false
            },
            permission: {
                title: 'Permission',
                edit: true,
                sort: false,
                options: {
                    'Read': 'Read',
                    'Write': 'Write',
                    'Admin': 'Admin'
                },
                display: function(data) {
                    if (data.record)
                        return data.record.permission;
                }
            }
        }});
    $('#' + ContainerID).jtable('load');
}

function loadViewProjectResponsesTable(ContainerID, project_id, modify) {
    $('#' + ContainerID).jtable({
        title: 'Responses',
        paging: true,
        pageSize: 25,
        sorting: true,
        defaultSorting: 'WBS ASC',
        actions: {
            listAction: config.base_url + 'data_fetch/responses_by_project/' + project_id,
            deleteAction: config.base_url + 'data_fetch/delete_response',
            updateAction: config.base_url + 'data_fetch/edit_response'
        },
        messages: defaultResponseMessages,
        fields: defaultResponseFields,
        toolbar: {
            items: [{
                    text: 'Full Response Report',
                    click: function() {
                        window.open(config.base_url + 'project/response_report/' + project_id, '_blank');
                    },
                    cssClass: 'toolbar-button button button-flat'
                },
                {
                    text: 'Response Report',
                    click: function() {
                        window.open(config.base_url + 'project/short_response_report/' + project_id, '_blank');
                    },
                    cssClass: 'toolbar-button button button-flat'
                },
                {
                    text: 'Response PDF Report',
                    click: function() {
                        window.open(config.base_url + 'project/short_response_pdf_report/' + project_id, '_blank');
                    },
                    cssClass: 'toolbar-button button button-flat'
                }]
                
            
        }
    });
    $('#' + ContainerID).jtable('load');
}
function loadViewTaskRisksTable(task_start_date, ContainerID, task_id, modify) {
    var actions = {
        listAction: config.base_url + 'data_fetch/risks_by_task/' + task_id
    };
    var toolbar = {};
    if (modify) {
        actions.updateAction = config.base_url + 'data_fetch/edit_risk';
        actions.deleteAction = config.base_url + 'data_fetch/delete_risk';
        actions.createAction = config.base_url + 'data_fetch/create_risk/' + task_id;
        toolbar = {
            items: [{
                    text: 'Risk Identification Wizard',
                    click: function() {
                        window.location.href = config.base_url + 'risk/wizard/' + task_id;
                    },
                    cssClass: 'toolbar-button button button-flat'
                }]
        };
    }
    var fields = {
        img_url: {
            title: 'Media',
            list: true,
            create: false,
            edit: false,
            width: '1%',
            columnResizable: false,
            sorting: false,
            type: "link",
            linkURL: config.base_url + "assets/images/uploads",
            addKeyToURL: true,
            isMedia: true
        },
        adder: {
            title: 'Add Risk Event',
            create: false,
            edit: false,
            sorting: false,
            width: '1%',
            columnResizable: false,
            display: function (data) {
                var impact_discussion_desc_table = "";
                var media_desc_table = "";
                if (data.record.impact_discussion) {
                    impact_discussion_desc_table = data.record.impact_discussion;
                }
                else {
                    impact_discussion_desc_table = 'No Impact Discussion.';
                }
                if (data.record.img_url) {
                    data.record.img_url.split(',').forEach(function (media_item) {
                        media_desc_table += 'http://v1.riskmp.com/assets/images/uploads/' + media_item + '\n';
                    });
                }
                else {
                    media_desc_table = 'No Uploaded Media items.';
                }
                var event_style = '<link rel="stylesheet" type="text/css" href="http://v1.riskmp.com/assets/css/addthiseventicon.css">';
                var event_adder = "<script type='text/javascript' src='http://v1.riskmp.com/assets/js/pagelevel/addtocal.js'></script>";
                var event_adder_1 = "<a href='' title='Add to Calendar' class='addthisevent'>";
                var event_adder_2 = "<span class='_start'>" + data.record.date_of_concern + "</span>";
                var event_adder_3 = '<span class="_end">' + data.record.date_of_concern + '</span>';
                var event_adder_4 = '<span class="_zonecode">15</span>';
                var event_adder_5 = '<span class="_summary">' + data.record.event + '</span>';
                var event_adder_6 = "<span class='_description'> Impact Discussion: \n" + impact_discussion_desc_table + "\n Risk Media: \n" + media_desc_table + "</span>";
                var event_adder_7 = '<span class="_organizer">Me</span>';
                var event_adder_8 = '<span class="_organizer_email"></span>';
                var event_adder_9 = '<span class="_all_day_event">true</span>';
                var event_adder_10 = '<span class="_date_format">DD/MM/YYYY</span>';
                var event_adder_11 = '</a>';
                var final_string = event_adder.concat(event_style,event_adder_1,event_adder_2,event_adder_3,event_adder_4,event_adder_5,event_adder_6,event_adder_7,event_adder_8,event_adder_9,event_adder_10,event_adder_11);
                return final_string;
            }
        }
    };
    fields = $.extend(true, defaultRiskFields, fields); //copy, don't reference
    if (task_start_date) {
        fields.date_of_concern.defaultValue = task_start_date;
    }
    fields.date_of_update.visibility = 'hidden';
    fields.date_identified.visibility = 'hidden';
    $('#' + ContainerID).jtable({
        title: 'Risks',
        paging: true,
        sorting: true,
        defaultSorting: 'WBS ASC',
        actions: actions,
        messages: defaultRiskMessages,
        fields: fields,
        toolbar: toolbar 
    });
    $('#' + ContainerID).jtable('load');
}
function loadViewRiskResponsesTable(ContainerID, risk_id, modify) {
    var fields = $.extend(true, {}, defaultResponseFields); //copy, don't reference
    fields.risk_statement.visibility = 'hidden';
    fields.WBS.visibility = 'hidden';
    var actions = {
        listAction: config.base_url + 'data_fetch/responses/' + risk_id
    };
    var toolbar = {};
    if (modify) {
        actions.updateAction = config.base_url + 'data_fetch/edit_response';
        actions.deleteAction = config.base_url + 'data_fetch/delete_response';
        actions.createAction = config.base_url + 'data_fetch/create_response/' + risk_id;
        toolbar = {
            items: [{
                    text: 'Create New Response',
                    click: function() {
                        window.location.href = config.base_url + 'response/create/' + risk_id;
                    },
                    cssClass: 'toolbar-button button button-flat'
                }]
        };
    }
    $('#' + ContainerID).jtable({
        title: 'Risk Responses',
        paging: true,
        sorting: true,
        defaultSorting: 'WBS ASC',
        actions: actions,
        messages: defaultResponseMessages,
        fields: fields,
        toolbar: toolbar
        ,
        formSubmitting: function (event, data) {
            location.reload();
        }
    });
    $('#' + ContainerID).jtable('load');
}
function loadViewResponseResponseUpdatesTable(ContainerID, response_id, modify) {
    var actions = {
        listAction: config.base_url + 'data_fetch/response_updates/' + response_id
    };
    var toolbar = {};
    if (modify) {
        actions.deleteAction = config.base_url + 'data_fetch/delete_response_update';
        actions.createAction = config.base_url + 'data_fetch/create_response_update/' + response_id;
        toolbar = {
            items: [{
                    text: 'Create New Update',
                    click: function() {
                        window.location.href = config.base_url + 'response/update/' + response_id;
                    },
                    cssClass: 'toolbar-button button button-flat'
                }]
        };
    }
    $('#' + ContainerID).jtable({
        title: 'Response Tracking',
        paging: true,
        sorting: true,
        recordAdded: function(event, data) {
            location.reload();
        },
        actions: actions,
        messages: defaultResponseUpdateMessages,
        fields: defaultResponseUpdateFields,
        toolbar: toolbar
    });
    $('#' + ContainerID).jtable('load');
}
function loadViewLessonsLearnedTable(ContainerID, project_id, modify, no_toolbar) {
    var fields = {
        occurred: {
            title: "Occurred?",
            display: function(data) {
                if ( data.record.occurred == 'yes' )
                    return "<div style='width:100%;text-align:center;'><input type='checkbox' checked='true' id='checkbox" + data.record.risk_id + "' onclick='occurred_checkbox_clicked(" + data.record.risk_id + ");'></div>";
                else if ( data.record.occurred == 'no' || data.record.occurred == '' || data.record.occurred == 'not_identified')
                    return "<div style='width:100%;text-align:center;'><input type='checkbox' id='checkbox" + data.record.risk_id + "' onclick='occurred_checkbox_clicked(" + data.record.risk_id + ");'></div>";
            },
            width: "0%",
            columnResizable: false,
            // sorting: false,
            edit: false,
            create: false
        }
    };

    fields = $.extend(true, fields, LessonsLearnedFields); //copy, don't reference
    fields.Responses = getLessonsLearnedResponseChildTable(ContainerID, project_id);

    fields.Responses.edit = false;
    fields.date_closed.width = '1%';
    fields.date_of_update.width = '1%';
    var toolbar;
    if (no_toolbar === true) {
        toolbar = {}
    } else {
        toolbar = {
            items: [
                    {
                        text: 'View PDF Report',
                        click: function() {
                            window.open(config.base_url + 'project/lessons_learned_pdf_risk_report/' + project_id, '_blank');
                        },
                        cssClass: 'toolbar-button button button-flat'
                    },
                    {
                        text: 'View Report',
                        click: function() {
                            window.open(config.base_url + 'project/short_lessons_learned_report/' + project_id, '_blank');
                        },
                        cssClass: 'toolbar-button button button-flat'
                    }
                    ,
                    {
                        text: 'View Full Report',
                        click: function() {
                            window.open(config.base_url + 'project/lessons_learned_report/' + project_id, '_blank');
                        },
                        cssClass: 'toolbar-button button button-flat'
                    }
                    ]
        };
    }
    $('#' + ContainerID).jtable({
        title: 'Lessons Learned',
        paging: true,
        pageSize: 100,
        sorting: true,
        defaultSorting: 'WBS ASC',
        actions: {
            listAction: config.base_url + 'data_fetch/risks_cause_by_project/' + project_id,
            deleteAction: config.base_url + 'data_fetch/delete_risk/',
            updateAction: config.base_url + 'data_fetch/update_lessons_learned_risk/' + project_id 
        },
        messages: defaultRiskMessages,
        fields: fields,
        formSubmitting: function(event,data){
            $('select[name=cause]', data.form).attr('name','cause[]');
            // location.assign(config.base_url + 'project/view/' + project_id + '/6');
            return data;
        },
        toolbar: toolbar   
    });
    $('#' + ContainerID).jtable('load');
}
function loadProjectLessonsLearnedShortReportTable(ContainerID, project_id) {
    var fields = $.extend(true, defaultRiskFields, LessonsLearnedFields); //copy, don't reference
    fields.Responses = getReportLessonsLearnedResponseChildTable(ContainerID, project_id);
    fields.WBS.visibility = 'hidden';
    fields.task_name.visibility = 'hidden';
    fields.date_of_concern.visibility = 'hidden';
    fields.impact.visibility = 'hidden';
    fields.date_identified.visibility = 'hidden';
    fields.type.visibility = 'hidden';
    fields.total_mitigation_cost.visibility = 'hidden';
    fields.impact_effect.visibility = 'hidden';
    fields.cost_impact.visibility = 'hidden';
    fields.overall_impact.visibility = 'hidden';
    fields.expected_cost.visibility = 'hidden';
    fields.impact_discussion.visibility = 'hidden';
    fields.priority_effect.visibility = 'hidden';
    fields.priority_monetary.visibility = 'hidden';
    fields.urgent.visibility = 'hidden';

    fields.cause.width = '55%';
    fields.occurred.width = '0%';
    fields.probability.width = '0%';
    fields.days_open.width = '0%';
    fields.event.width = '45%';
    fields.date_of_update.width = '0%';

    fields.cause.columnResizable = true;
    fields.occurred.columnResizable = true;
    fields.probability.columnResizable = true;
    fields.days_open.columnResizable = true;
    fields.event.columnResizable = true;
    fields.date_of_update.columnResizable = true;
    
    $('#' + ContainerID).jtable({
        paging: false,
        sorting: true,
        defaultSorting: 'WBS ASC',
        actions: {
            listAction: config.base_url + 'data_fetch/risks_cause_by_project/' + project_id
        },
        messages: defaultRiskMessages,
        fields: fields
    });
    $('#' + ContainerID).jtable('load');
}
function loadProjectLessonsLearnedReportTable(ContainerID, project_id) {
    var fields = $.extend(true, defaultRiskFields, LessonsLearnedFields); //copy, don't reference
    fields.Responses = getReportLessonsLearnedFullResponseChildTable(ContainerID, project_id);
    $('#' + ContainerID).jtable({
        paging: false,
        sorting: true,
        defaultSorting: 'WBS ASC',
        actions: {
            listAction: config.base_url + 'data_fetch/risks_cause_by_project/' + project_id
        },
        messages: defaultRiskMessages,
        fields: fields
    });
    $('#' + ContainerID).jtable('load');
}
function loadGlobalRisksReportTable(ContainerID) {
    var fields = $.extend(true, {project_name: defaultProjectFields.project_name, occurred: LessonsLearnedFields.occurred, cause: LessonsLearnedFields.cause}, defaultRiskFields); //copy, don't reference
    fields.cause.width = '5%';
    fields.occurred.width = '0%';
    // fields.occurred.list = true;
    $('#' + ContainerID).jtable({
        paging: false,
        sorting: true,
        defaultSorting: 'WBS ASC',
        actions: {
            listAction: config.base_url + 'data_fetch/risks_by_user/'
        },
        messages: defaultRiskMessages,
        fields: fields
    });
    $('#' + ContainerID).jtable('load');
}
function loadGlobalShortRisksReportTable(ContainerID) {
    var fields = $.extend(true, {project_name: defaultProjectFields.project_name, occurred: LessonsLearnedFields.occurred, cause: LessonsLearnedFields.cause}, default_ShortRiskFields); //copy, don't reference
    fields.cause.width = '5%';
    fields.occurred.width = '0%';
    // fields.occurred.list = true;
    $('#' + ContainerID).jtable({
        paging: false,
        sorting: true,
        defaultSorting: 'WBS ASC',
        actions: {
            listAction: config.base_url + 'data_fetch/risks_by_user/'
        },
        messages: defaultRiskMessages,
        fields: fields
    });
    $('#' + ContainerID).jtable('load');
}
function loadProjectShortResponsesReportTable(ContainerID, project_id) {
    $('#' + ContainerID).jtable({
        paging: false,
        sorting: true,
        defaultSorting: 'WBS ASC',
        actions: {
            listAction: config.base_url + 'data_fetch/responses_by_project/' + project_id
        },
        messages: defaultResponseMessages,
        fields: defaultShortResponseFields
    });
    $('#' + ContainerID).jtable('load');
}

function loadProjectResponsesReportTable(ContainerID, project_id) {
    $('#' + ContainerID).jtable({
        paging: false,
        sorting: true,
        defaultSorting: 'WBS ASC',
        actions: {
            listAction: config.base_url + 'data_fetch/responses_by_project/' + project_id
        },
        messages: defaultResponseMessages,
        fields: defaultResponseFields
    });
    $('#' + ContainerID).jtable('load');
}
function loadProjectRisksReportTable(ContainerID, project_id) {
    var fields = $.extend(true, {}, defaultRiskFields); //copy, don't reference
    fields.task_name.visibility = 'visible';
    $('#' + ContainerID).jtable({
        paging: false,
        sorting: true,
        defaultSorting: 'WBS ASC',
        actions: {
            listAction: config.base_url + 'data_fetch/risks_by_project/' + project_id
        },
        messages: defaultRiskMessages,
        fields: fields
    });
    $('#' + ContainerID).jtable('load');
}

function loadProjectShortRisksReportTable(ContainerID, project_id) {
    var fields = $.extend(true, {}, default_ShortRiskFields); //copy, don't reference
    fields.task_name.visibility = 'visible';
    $('#' + ContainerID).jtable({
        paging: false,
        sorting: true,
        defaultSorting: 'WBS ASC',
        actions: {
            listAction: config.base_url + 'data_fetch/risks_by_project/' + project_id
        },
        messages: defaultRiskMessages,
        fields: default_ShortRiskFields
    });
    $('#' + ContainerID).jtable('load');
}
function loadProjectTasksReportTable(ContainerID, project_id) {
    $('#' + ContainerID).jtable({
        paging: false,
        sorting: true,
        defaultSorting: 'WBS ASC',
        addRecordButton: $('#create_task'),
        actions: {
            listAction: config.base_url + 'data_fetch/tasks_with_risks/' + project_id
        },
        messages: defaultTaskMessages,
        fields: defaultTaskFields
    });
    $('#' + ContainerID).jtable('load');
}
function loadResponseTrackingReportTable(ContainerID, response_id) {
    $('#' + ContainerID).jtable({
        paging: false,
        sorting: true,
        actions: {
            listAction: config.base_url + 'data_fetch/response_updates/' + response_id
        },
        messages: defaultResponseUpdateMessages,
        fields: defaultResponseUpdateFields
    });
    $('#' + ContainerID).jtable('load');
}
function loadRiskAnalysisReportTable(ContainerID, risk_id) {
    var fields = $.extend(true, {}, defaultResponseFields); //copy, don't reference
    fields.risk_statement.visibility = 'hidden';
    fields.WBS.visibility = 'hidden';
    console.log(defaultResponseFields);
    $('#' + ContainerID).jtable({
        paging: false,
        sorting: true,
        defaultSorting: 'WBS ASC',
        actions: {
            listAction: config.base_url + 'data_fetch/responses/' + risk_id
        },
        messages: defaultResponseMessages,
        fields: fields
    });
    $('#' + ContainerID).jtable('load');
}

function loadInvoicesTable(ContainerID) {
    $('#' + ContainerID).jtable({
        paging: false,
        sorting: false,
        actions: {
            listAction: config.base_url + 'data_fetch/get_invoices'
        },
        messages: {
            loadingMessage: "Loading invoices...",
            noDataAvailable: "No invoices available."
        },
        fields: {
            transaction_id: {
                key: true,
                list: false
            },
            order_time: {
                title: "Date of Transaction",
                type: "link",
                linkURL: config.base_url + "user/receipt",
                addKeyToURL: true
            },
            amount: {
                title: "Amount",
                type: "currency"
            }
        }
    });
    $('#' + ContainerID).jtable('load');
}
