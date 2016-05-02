/* global $ translate */
$(function() {
    'use strict';

    var orderSpecs = [[0, 'asc']];

    var languageSpecs = {
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
    };

    $('#js-time-packages-table').dataTable({
        order: orderSpecs,
        language: languageSpecs
    });

});
