/* global $ translate */
$(function() {
    "use strict";
    var table = $('#js-business-trips-table');
    var search = $('#js-value');
    var column = $('#js-column');
    var from = $('#js-date-from');
    var to = $('#js-date-to');
    search.val('');
    column.val('select');


    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/trips/datatable",
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

            aoData.push({ "name": "fromDate", "value": $(from).val().trim()});
            aoData.push({ "name": "toDate", "value": $(to).val().trim()});
            aoData.push({ "name": "columnFromDate", "value": "t.timestampBeginning"});
            aoData.push({ "name": "columnToDate", "value": "t.timestampEnd"});
        },
        "order": [[7, 'desc']],
        "columns": [
            {data: 't.id'},
            {data: 'e.surname'},
            {data: 'e.name'},
            {data: 'g.name'},
            {data: 't.carPlate'},
            {data: 't.duration'},
            {data: 't.parkSeconds'},
            {data: 't.timestampBeginning'},
            {data: 't.timestampEnd'},
            {data: 't.addressBeginning'},
            {data: 't.addressEnd'}
        ],
        "columnDefs": [
            {
                targets: [4, 5, 6],
                searchable: false,
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
            "sEmptyTable": translate("sTripEmptyTable"),
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
        from.val('');
        to.val('');
        column.val('select');
        search.prop('disabled', false);
        search.show();
    });

    $('.date-picker').datepicker({
        autoclose: true,
        format: 'yyyy-mm-dd',
        weekStart: 1
    });

    $(column).change(function() {
        search.show();
        search.val('');
    });
});
