/* global $ */
$(function() {
    'use strict';
    function updateClockpickers() {
        $('.clockpicker').clockpicker({
            placement: 'bottom',
            autoclose: true
        });
    }
    updateClockpickers();

    var saveLimitsBtn = $("#save-limits");
    var wrapper = $(".input-field-wrap"); //Fields wrapper
    var addButton = $(".add-field-btn"); //Add button ID
    var ruleTemplate = $('#rule-template');

    $(addButton).click(function(e){ //on add input button click
        e.preventDefault();
        var day = $(this).data('day');
        var clone = ruleTemplate
            .clone()
            .removeClass('hide')
            .removeAttr('id')
            .addClass('limit-rules')
            .addClass(day + '-rules');
        $(this).closest(".input-field-wrap").append(clone); //add input box

        updateClockpickers();
    });

    $(wrapper).on("click", ".remove-times", function(e){
        e.preventDefault();
        $(this).closest('.rule-div').remove();
    });

    $(saveLimitsBtn).on("click", function() {
        var mo = [];
        var tu = [];
        var we = [];
        var th = [];
        var fr = [];
        var sa = [];
        var su = [];
        $('.limit-rules').each(function() {
            var from = $(this).find('input.from').val();
            var to = $(this).find('input.to').val();
            var rule = from + "-" + to;
            if ($(this).hasClass('mo-rules')) {
                mo.push(rule);
            } else if ($(this).hasClass('tu-rules')) {
                tu.push(rule);
            } else if ($(this).hasClass('we-rules')) {
                we.push(rule);
            } else if ($(this).hasClass('th-rules')) {
                th.push(rule);
            } else if ($(this).hasClass('fr-rules')) {
                fr.push(rule);
            } else if ($(this).hasClass('sa-rules')) {
                sa.push(rule);
            } else if ($(this).hasClass('su-rules')) {
                su.push(rule);
            }
        });

        $('#mo').val(mo.join());
        $('#tu').val(tu.join());
        $('#we').val(we.join());
        $('#th').val(th.join());
        $('#fr').val(fr.join());
        $('#sa').val(sa.join());
        $('#su').val(su.join());
    });
});


