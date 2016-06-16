/* global $ translate document */
$(function() {
    "use strict";
    var table = $('#js-business-payments-table');
    var search = $('#js-value');
    var column = $('#js-column');
    var paymentType = $('#js-payment-type');
    var paymentStatus = $('#js-payment-status');
    var from = $('#js-date-from');
    var to = $('#js-date-to');
    var reportData = null;
    var searchByType = false;
    var searchByStatus = false;

    var reportTable = $("#report-table");
    var reportTotal = $("#report-total");

    function updateReportInfo(data) {
        reportData = data;
        if ($.isEmptyObject(reportData.payments)) {
            reportTable.hide();
            return;
        }

        var reportTotalHtml = '';
        $.each(data.totals, function (i, value) {
            if (i > 0) {
                reportTotalHtml += '<br>';
            }
            reportTotalHtml += value;
        });
        reportTotal.html(reportTotalHtml);
        reportTable.show();
    }

    search.val('');
    column.val('select');
    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/payments/datatable",
        "fnServerData": function ( sSource, aoData, fnCallback) {
                $.ajax({
                    "dataType": 'json',
                    "type": "POST",
                    "url": sSource,
                    "data": aoData,
                    "success": function(result) {
                        updateReportInfo(result.reportData);
                        fnCallback(result);
                    }
            } );
        },
        "fnServerParams": function ( aoData ) {
            aoData.push({ "name": "column", "value": $(column).val()});
            var value = $(search).val().trim();
            if (searchByType) {
                value = paymentType.val();
            }
            if (searchByStatus) {
                value = paymentStatus.val();
            }
            aoData.push({ "name": "searchValue", "value": value });

            aoData.push({ "name": "fromDate", "value": $(from).val().trim()});
            aoData.push({ "name": "toDate", "value": $(to).val().trim()});
            aoData.push({ "name": "columnFromDate", "value": "created_ts"});
            aoData.push({ "name": "columnToDate", "value": "created_ts"});
        },
        "order": [[0, 'asc']],
        "columns": [
            {data: 'created_ts'},
            {data: 'type'},
            {data: 'amount'},
            {data: 'status'},
            {data: 'payed_on_ts'},
            {data: 'details'}
        ],
        "columnDefs": [
            {
                targets: 5,
                sortable: false
            }
        ],
        "lengthMenu": [
            [100, 200, 300],
            [100, 200, 300]
        ],
        "pageLength": 100,
        "pagingType": "bootstrap_full_number",
        "language": {
            "sEmptyTable": translate("sEmptyTable"),
            "sInfo": translate("sInfo"),
            "sInfoEmpty": translate("sInfoEmpty"),
            "sInfoFiltered": translate("sInfoFiltered"),
            "sInfoPostFix": "",
            "sInfoThousands": ",",
            "sLengthMenu": translate("sLengthMenu"),
            "sLoadingRecords": translate("sLoadingRecords"),
            "sProcessing": translate("sProcessing"),
            "sSearch": translate("sSearch"),
            "sZeroRecords": translate("sZeroRecords"),
            "oPaginate": {
                "sFirst": translate("oPaginateFirst"),
                "sPrevious": translate("oPaginatePrevious"),
                "sNext": translate("oPaginateNext"),
                "sLast": translate("oPaginateLast")
            },
            "oAria": {
                "sSortAscending": translate("sSortAscending"),
                "sSortDescending": translate("sSortDescending")
            }
        }
    });

    $('#js-search').click(function() {
        table.fnFilter();
    });

    $('#js-clear').click(function() {
        search.val('');
        search.prop('disabled', false);
        paymentType.hide();
        column.val('select');
        search.show();
    });

    $('.date-picker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        weekStart: 1
    });

    $(column).change(function() {
        var value = $(this).val();
        searchByType = false;
        searchByStatus = false;
        paymentType.hide();
        paymentStatus.hide();
        search.hide();

        switch (value) {
            case 'type' :
                paymentType.show();
                searchByType = true;
                break;
            case 'status' :
                paymentStatus.show();
                searchByStatus = true;
                break;
            default:
                search.show();
                break;
        }
    });

    var reportDownload = $("#report-download");
    reportDownload.click(function() {
        var form = document.createElement("form");
        var input = document.createElement("input");

        form.method = "POST";
        form.action = "payments/report";

        input.value = JSON.stringify(reportData);
        input.name = "data";
        input.type = "hidden";
        form.appendChild(input);

        document.body.appendChild(form);

        form.submit();

    });
});
