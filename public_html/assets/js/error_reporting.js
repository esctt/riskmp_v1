function openErrorReportDialog(event) {
    $("#error_report_div").dialog({modal: true});
}

function show_result(message) {
    $("#error_report_div").dialog("destroy");
    $("#error_result_div").dialog({modal: true, buttons: [{text: "Close", click: function() {
                    $(this).dialog("close");
                }}]});
    $("#error_result_div").html(message);
}

$('#submit_error').click(function(event) {
    event.preventDefault();
    var url = config.base_url + "data_fetch/error_report";
    var posting = $.post(url, {error_text: $('#error_text').val()});
    posting.done(function(data) {
        show_result(data);
    });
    posting.fail(function()
    {
        show_result('Could not submit error report. Please try again later.');
    });
});