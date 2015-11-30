//JAVASCRIPT FOR VIEW_PROJECT PAGE

function checkbox_clicked(index) {
    var element = document.getElementById('checkbox' + index);
    if (!element.disabled) {
        element.disabled = true;
        $.post(config.base_url + "data_fetch/identify_risk/" + index);
        if (tasks_with_risks_table_loaded) {
            $('#TasksWithRisksTableContainer').jtable('reload');
        }
        if (risks_table_loaded) {
            $('#RiskTableContainer').jtable('reload');
        }
    }
}

google.load('visualization', '1.1', {'packages': ['annotatedtimeline', 'calendar']});
google.setOnLoadCallback(drawCharts);
function drawCharts() {
    //draw timeline chart
    {
        //get data from server
        var url = config.base_url + "data_fetch/expected_cost_graph/" + project_id;
        var posting = $.post(url, {});
        posting.done(function(data) {
            var result = JSON.parse(data);
            var riskdata = result.Records;
            //draw chart
            var data = new google.visualization.DataTable();
            data.addColumn('date', 'Date');
            data.addColumn('number', 'Expected Cost');
            data.addColumn('string', 'title1');
            data.addColumn('string', 'text1');
            riskdata.forEach(function(risk) {
                if (risk.date_of_concern != "0000-00-00" && risk.date_of_concern != null) {
                    var d = risk.date_of_concern.split('-');
                    data.addRow([new Date(d[0], d[1], d[2]), parseFloat(risk.expected_cost), "Risk Event", risk.event]);
                }
            });
            var chart = new google.visualization.AnnotatedTimeLine(document.getElementById('chart_div'));
            chart.draw(data, {displayAnnotations: true});
        });
        posting.fail(function()
        {
            //show error message
        });
    }
    //draw calendar chart
    {
        //get data from server
        var url = config.base_url + "data_fetch/risk_by_date_data/" + project_id;
        var posting = $.post(url, {});
        posting.done(function(data) {
            var result = JSON.parse(data);
            var riskdata = result.Records;
            //draw chart
            var data = new google.visualization.DataTable();
            data.addColumn({type: 'date', id: 'Date'});
            data.addColumn({type: 'number', id: 'Expected Cost'});
            var years = [];
            riskdata.forEach(function(risk) {
                var d = risk.date_of_concern.split('-');
                data.addRow([new Date(d[0], d[1], d[2]), parseFloat(risk.expected_cost)]);
                if (years.indexOf(d[0]) == -1) {
                    years.push(d[0]);
                }
            });
            var formatter = new google.visualization.NumberFormat({prefix: '$'});
            formatter.format(data, 1);
            var chart = new google.visualization.Calendar(document.getElementById('calendar_chart_div'));
            var options = {
                height: 200 * years.length
            };
            chart.draw(data, options);
            $('#tabs').tabs("option", "active", open_tab);
        });
        posting.fail(function()
        {
            //show error message
            $('#tabs').tabs("option", "active", open_tab);
        });
    }
}