google.load('visualization', '1.1', {'packages': ['calendar']});
google.setOnLoadCallback(drawCharts);
// project_id = "<?php echo $project_data['project_id']; ?>;";
// alert(project_id);
function drawCharts() {
    //draw calendar chart
    {
        //get data from server
        var url = config.base_url + "data_fetch/risk_by_date_data/" + project_for_cal;
        var posting = $.post(url, {});
        posting.done(function(data) {
            var result = JSON.parse(data);
            var riskdata = result.Records;
            //draw chart
            var data = new google.visualization.DataTable();
            data.addColumn({type: 'date', id: 'Date'});
            data.addColumn({type: 'number', id: 'Expected Cost'});
            var years = [];


                    var num_dates = riskdata.length;   
                    var max_year;
                    var dateObject = new Date();
                    var current_year = dateObject.getFullYear();

                    var current_item_year;
                    var prev_item_year;

                    var i;
                    for ( i = 0; i < num_dates; i++) {

                    }

                    riskdata.forEach(function (risk) {
                        // console.log('Unaltered date is: ' + risk.date_of_concern);
                        var d = risk.date_of_concern.split('-');
                        // console.log("Altered date is: " + d);
                        // console.log("Date of concern: " + d[0]);
                        if ( d[0] == current_year ) {
                            max_year = d[0];
                            // console.log('');
                        }
                        // else{
                        //     // console.log('no match');
                        //     current_year = d[0];

                        // }
                    });
            var count = 0;
            riskdata.forEach(function(risk) {
                var d = risk.date_of_concern.split('-');
                // console.log("Date of concern: " + d + " , Expected cost: " + risk.expected_cost);
                //Fixed offset by 1 for month
                data.addRow([new Date(d[0], d[1] - 1, d[2]), parseFloat(risk.expected_cost)]);
                count = count + 1;
                // console.log(count);
                if (years.indexOf(d[0]) == -1) {
                    years.push(d[0]);
                }
            });
            // years.forEach( function(year) {
            //     console.log(year);
            // })
            // console.log('The final value is: ' + years[years.length - 1]);
            var chart_height = years[years.length - 1] - years[0];
            // console.log('The range is: ' + chart_height);
            
            var formatter = new google.visualization.NumberFormat({prefix: '$'});
            formatter.format(data, 1);
             // years.forEach( function(year) {
             //     console.log('Item in array ' + year);
             // })
             
            var range = years[years.length - 1] - years[0];
            total_charts = range + 1;
            var factor;
            if (total_charts == 1) {
                factor = 200;
            }
            if (total_charts > 1) {
                factor = 200 + (total_charts -1)*(150);
            }
            // console.log(factor);
            var options = {
                height: factor,
                calendar: {
                    cellColor: {
                        stroke: '#C2C2D6',
                        strokeOpacity: 1,
                        strokeWidth: 2

                    },
                    focusedCellColor: {
                        stroke: '#5C85AD',
                        strokeOpacity: 1,
                        strokeWidth: 2
                    },
                    monthOutlineColor: {
                        stroke: '#000052',
                        strokeOpacity: 1,
                        strokeWidth: 1
                    },
                    yearLabel: {
                        color: '#222222'
                    },
                    monthLabel: {
                        color: '#222222'
                    },
                    dayOfWeekLabel: {
                        color: '#222222'
                    }
                },
                noDataPattern: {
                     backgroundColor: '#B5C3D1',
//                    backgroundColor: '#E5E5E5',
                    color: '#E5E5E%'
                },
                colorAxis: {
                    colors: ['#D1E8FF','#195B6C']
                }
            };
            var chart = new google.visualization.Calendar(document.getElementById('calendar_chart_div'));
            
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