/* global $ document translate */
$(function() {
    'use strict';

    var orderSpecs = [[0, 'desc']];

    var languageSpecs = {
        "sEmptyTable": translate("sEmptyEmployeeTable"),
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
    };

    var columnDefs = [
        { targets: [4], sortable: false}
    ];

    $('#js-business-employees-table').dataTable({
        order: orderSpecs,
        language: languageSpecs,
        columnDefs: columnDefs
    });
    $('#js-pending-business-employees-table').dataTable({
        order: orderSpecs,
        language: languageSpecs,
        columnDefs: columnDefs
    });
});
