/* global $ translate */
$(function() {
    "use strict";
    var table = $('#js-business-invoices-table');
    var search = $('#js-value');
    var column = $('#js-column');
    search.val('');
    column.val('select');

    function renderType(type)
    {
        switch (type) {
            case 'FIRST_PAYMENT':
                return translate("renderFirstPayment");
            case 'TRIP':
                return translate("renderTrip");
            case 'PENALTY':
                return translate("renderPenality");
            case 'BONUS_PACKAGE':
                return translate("renderBonusPackage");
        }
        return type;
    }

    table.dataTable({
        "processing": true,
        "serverSide": true,
        "bStateSave": false,
        "bFilter": false,
        "sAjaxSource": "/invoices/datatable",
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
            {data: 'bi.invoiceNumber'},
            {data: 'bi.invoiceDate'},
            {data: 'bi.type'},
            {data: 'bi.amount'},
            {data: 'bi.id'}
        ],
        "columnDefs": [
            {
                targets: 1,
                "render": function (data) {
                    return renderDate(data);
                }
            },
            {
                targets: 2,
                "render": function (data) {
                    return renderType(data);
                }
            },
            {
                targets: 3,
                className: "sng-dt-right",
                "render": function (data) {
                    return renderAmount(data);
                }
            },
            {
                targets: 4,
                sortable: false,
                className: "sng-dt-center",
                "render": function (data) {
                    return renderLink(data);
                }
            }
        ],
        "lengthMenu": [
            [100, 200, 300],
            [100, 200, 300]
        ],
        "pageLength": 100,
        "pagingType": "bootstrap_full_number",
        "language": {
            "sEmptyTable": translate("sInvoicesEmptyTable"),
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

    function renderDate(date)
    {
        return toStringKeepZero(date % 100) + '/' +
            toStringKeepZero(Math.floor((date / 100) % 100)) + '/' +
            (Math.floor(date / 10000));
    }

    function renderAmount(amount)
    {
        return (Math.floor(amount / 100)) +
            ',' +
            toStringKeepZero(amount % 100) +
            ' \u20ac';
    }

    function renderLink(id)
    {
        return '<a href=invoices/download/' + id + '><i class="fa fa-download"></i></a>';
    }

    function toStringKeepZero(value)
    {
        return ((value < 10) ? '0' : '') + value;
    }
});
