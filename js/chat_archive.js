$(document).ready(function() {
    try{

    $('.datepicker').datepicker();

    $('.make-ref').zclip({
        path: "http://www.steamdev.com/zclip/js/ZeroClipboard.swf",
        copy: function(event) {
            return $(this).data('ref');
        }
    });

    $('.chat-archive-dateto').datepicker({
        maxDate: '+0d',
        showWeek: true,
        dateFormat: 'yy-mm-dd',
        defaultDate: new Date($('.chat-archive-dateto').data('date')),
        changeMonth: true,
        changeYear: true,
        yearRange: '-12',
    });


    $('.search-chat-archive').live('click', search_chat_archive);

    if ($('.column-side').length < 1) {
        $('.icon-hide').hide();
    }

    jQuery(window).resize(function() {
        //console.log($("#chat-toolbox").position());
    });
    get_pending_counter();

    $('#loader').hide('slow');
    $('body').css('overflow', 'auto');
    $('input').tooltip();
    $('.timeline-badge img').addClass('img-circle');
    $('.timeline-badge img').width($('.timeline-badge img').width() + 6);
    $('.timeline-badge img').height($('.timeline-badge img').height() + 5);
    $('.timeline-badge').width($('.timeline-badge img').width());
    $('.timeline-badge').height($('.timeline-badge img').height());
    $('.timeline-badge').css('opacity', '1');
    $('.timeline-badge img').css('opacity', '1');
    } catch(e) {
        console.log(e.name);
        console.log(e);
        console.log('---');
    } finally {
        console.log('app start');
    }
});


var show_user_icons = function() {
    $('.timeline-badge:first img').addClass('img-circle');
    $('.timeline-badge:first img').css('margin-top', '0px');
    $('.timeline-badge:first img').width($('.timeline-badge img').width() + 6);
    $('.timeline-badge:first img').height($('.timeline-badge img').height() + 5);
    $('.timeline-badge:first').width($('.timeline-badge img').width());
    $('.timeline-badge:first').height($('.timeline-badge img').height());
    $('.timeline-badge:first').css('opacity', '1');
    $('.timeline-badge:first img').css('opacity', '1');
};

var search_chat_archive = function(event) {
    event.preventDefault();
    event.stopPropagation();

    var date = $('.chat-archive-dateto').val();
    if (date == '') {
        return false;
    }
    var esc_date = date.replace(/\//gi, "-");
    document.location.href = '/touchline/archive/' + esc_date;
};

$(document).ready(function() {
    $('.filterable .btn-filter').click(function() {
        var $panel = $(this).parents('.filterable'),
                $filters = $panel.find('.filters input'),
                $tbody = $panel.find('.table tbody');
        if ($filters.prop('disabled') == true) {
            $filters.prop('disabled', false);
            $filters.first().focus();
            $(this).html('Remove filters');
        } else {
            $filters.val('').prop('disabled', true);
            $tbody.find('.no-result').remove();
            $tbody.find('tr').show();
            $(this).html('Use table filters');
        }
        return false;
    });

    $('.filterable .filters input').keyup(function(e) {
        /* Ignore tab key */
        var code = e.keyCode || e.which;
        if (code == '9')
            return;

        /* Useful DOM data and selectors */
        var $input = $(this),
                inputContent = $input.val().toLowerCase(),
                $panel = $input.parents('.filterable'),
                column = $panel.find('.filters th').index($input.parents('th')),
                $table = $panel.find('.table'),
                // $rows = $table.find('tbody tr');
                $rows = $table.find('tbody .fl');
        /* Dirtiest filter function ever ;) */
        var $filteredRows = $rows.filter(function() {
            var value = $(this).find('td').eq(column).text().toLowerCase();
            return value.indexOf(inputContent) === -1;
        });
        /* Clean previous no-result if exist */
        $table.find('tbody .no-result').remove();
        /* Show all rows, hide filtered ones (never do that outside of a demo ! xD) */
        $('.fl + tr').show();
        $rows.show();
        $filteredRows.hide();
        $('.fl:hidden + tr').hide();
        //console.log($('tbody .fl + tr'));

        /* Prepend no-result row if all rows are filtered */
        if ($filteredRows.length === $rows.length) {
            $table.find('tbody').prepend($('<tr class="no-result text-center"><td colspan="' + $table.find('.filters th').length + '">No result found</td></tr>'));
        }
    });
});

/*
 * Change trigger bootstrap 3 dropdown menu from click on hover
 */

/* ----- Detect touch or no-touch */
/*  Detects touch support and adds appropriate classes to html and returns a JS object
 */

$(document).ready(function() {
    $(document).ready(function() {
      $('.js-activated').dropdownHover().dropdown();
    });
 });