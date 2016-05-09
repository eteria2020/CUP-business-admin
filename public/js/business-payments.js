/* global $ translate */
$(function() {
    "use strict";
    var table = $('#js-business-payments-table');
    var search = $('#js-value');
    var column = $('#js-column');
    search.val('');
    column.val('select');

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/payments/datatable",
        "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
            oSettings.jqXHR = $.ajax( {
                "dataType": 'json',
                "type": "POST",
                "url": sSource,
                "data": aoData,
                "success": fnCallback
            } );
        },
        "fnServerParams": function ( aoData ) {
            aoData.push({ "name": "column", "value": $(column).val()});
            aoData.push({ "name": "searchValue", "value": $(search).val().trim()});
        },
        "order": [[0, 'asc']],
        "columns": [
            {data: 'bp.createdTs'},
            {data: 'bp.type'},
            {data: 'bp.amount'},
            {data: 'bp.payedOnTs'}
        ],
        "columnDefs": [],
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
        column.val('select');
        search.show();
    });

    $(column).change(function() {
        search.show();
        search.val('');
    });

});
