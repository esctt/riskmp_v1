//JAVASCRIPT FOR VIEW_PROJECT PAGE
   
function getImgData(chartContainer) {
  var chartArea = chartContainer.getElementsByTagName('svg')[0].parentNode;
  var svg = chartArea.innerHTML;
  var doc = chartContainer.ownerDocument;
  var canvas = doc.createElement('canvas');
  canvas.setAttribute('width', chartArea.offsetWidth);
  canvas.setAttribute('height', chartArea.offsetHeight);

  canvas.setAttribute(
    'style',
    'position: absolute; ' +
    'top: ' + (-chartArea.offsetHeight * 2) + 'px; ' +
    'left: ' + (-chartArea.offsetWidth * 2) + 'px;');
  doc.body.appendChild(canvas);
  canvg(canvas, svg);
  var imgData = canvas.toDataURL("image/png");
  canvas.parentNode.removeChild(canvas);
  return imgData;
}

function saveAsImg(chartContainer) {
  var imgData = getImgData(chartContainer);
  var link = document.getElementById('linker'); 
  link.href = imgData;
  link.download = "calendar.png";
}

function alphaIcon_clicked(task_id) {
    document.getElementById('checkbox' + task_id).click();
    $('#FourthTab').trigger('click');
    // $('#tabs-Risks .jtable-data-row:first-child td:nth-last-of-type(2) button').trigger('click');
    setTimeout(function(){$('#tabs-Risks .jtable-data-row:first-child td:nth-last-of-type(2) button').trigger('click');},900);
//    $('#tabs-Risks .jtable-data-row:first-child td:nth-last-of-type(2) button').trigger('click');
    // checkbox_clicked(task_id);
    // alert('success!');
}

function alphaIcon_clicked_again(task_id) {
    $.post(config.base_url + "data_fetch/identify_risk/" + task_id).done(function(data) {
        $('#TasksWithRisksTableContainer').jtable('reload');
        $('#RiskTableContainer').jtable('reload');
        $('#LessonsLearnedTableContainer').jtable('reload');
        return;
    });
    $('#FourthTab').trigger('click');
    setTimeout(function(){$('#tabs-Risks .jtable-data-row:first-child td:nth-last-of-type(2) button').trigger('click');},900);
}

function checkbox_clicked(index) {
    var element = document.getElementById('checkbox' + index);
    if (!element.disabled) {
        element.disabled = true;
        $.post(config.base_url + "data_fetch/identify_risk/" + index).done(function(data) {
            $('#TasksWithRisksTableContainer').jtable('reload');
            $('#RiskTableContainer').jtable('reload');
            $('#LessonsLearnedTableContainer').jtable('reload');
            return;
                // $('#SeventhTab').trigger('click');
                // setTimeout(function(){document.getElementById('checkbox224').click();},700);
            // location.assign(location.href + '/6');
            // setTimeout(function(){document.getElementById('response_img_224').click();},3000);
            
        });
    }
}

function occurred_checkbox_clicked(index) {
    var element = document.getElementById('checkbox' + index);
    if (element.checked == true) {
        $.post(config.base_url + "data_fetch/occurred_risk/" + index).done(function(data) {
            $('#LessonsLearnedTableContainer').jtable('reload');
        });
    }
    else {
        $.post(config.base_url + "data_fetch/not_occurred_risk/" + index).done(function(data) {
            $('#LessonsLearnedTableContainer').jtable('reload');
        });    
    }
}

function successful_checkbox_clicked(index, img_id) {
    var element = document.getElementById('checkbox' + index);
    if (element.checked == true) {
        $.post(config.base_url + "data_fetch/successful_response/" + index).done(function(data) {
                // var deferred = $.Deferred( function () {
                //     $('#LessonsLearnedTableContainer').jtable('reload') 
                // }); 
                // deferred.done(function () {
                //     document.getElementById('response_img_' + img_id).click();
                // });
                
            $('#LessonsLearnedTableContainer').jtable('reload');
            setTimeout(function(){document.getElementById('response_img_' + img_id).click();},700);
        });
    }
    else {
        $.post(config.base_url + "data_fetch/unsuccessful_response/" + index).done(function(data) {
            $('#LessonsLearnedTableContainer').jtable('reload');
            setTimeout(function(){document.getElementById('response_img_' + img_id).click();},700);
                
                // var deferred = $.Deferred( function () {
                //     $('#LessonsLearnedTableContainer').jtable('reload') 
                // }); 
                // deferred.done(function () {
                //     document.getElementById('response_img_' + img_id).click();
                // });
        });    
    }
}

google.load('visualization', '1.1', {'packages': ['annotationchart', 'calendar']});
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
                    //Fixed offset by 1 for month
                    data.addRow([new Date(d[0], d[1] - 1, d[2]), parseFloat(risk.expected_cost), "Risk Event", risk.event]);
                }
            });
            var chart = new google.visualization.AnnotationChart(document.getElementById('chart_div'));
            // console.log('DRWAING!');
            chart.draw(data, {displayAnnotations: true});
        });
        posting.fail(function()
        {
            //show error message
            alert('Please reload the page to view content.');
            $('#tabs').tabs("option", "active", open_tab);
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