$( document ).ready(function(){
    $('.datepicker').datepicker();
    $('.chat-archive-dateto').datepicker({
        maxDate     : '+0d',
        showWeek    : true,
        dateFormat  : 'yy-mm-dd',
        defaultDate : new Date($('.chat-archive-dateto').data('date')),
        //gotoCurrent : true,
        changeMonth : true,
        changeYear  : true,
        yearRange   : '-12',
        onSelect: function(dateString)
        {
            document.location.href = '/mainchat/archive/' + dateString;
        }
    }); 
	get_pending_counter();

});

/********************* jQuery UI datepicker ****/
$( document ).ready(function(){
    $( "#datepicker" ).datepicker({
      changeMonth: true,
      changeYear: true
    });
  });


$(document).ready(function(){
    $('.filterable .btn-filter').click(function(){
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

    $('.filterable .filters input').keyup(function(e){
        /* Ignore tab key */
        var code = e.keyCode || e.which;
        if (code == '9') return;
        
        /* Useful DOM data and selectors */
        var $input = $(this),
        inputContent = $input.val().toLowerCase(),
        $panel = $input.parents('.filterable'),
        column = $panel.find('.filters th').index($input.parents('th')),
        $table = $panel.find('.table'),
       // $rows = $table.find('tbody tr');
        $rows = $table.find('tbody .fl');
        /* Dirtiest filter function ever ;) */
        var $filteredRows = $rows.filter(function(){
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
            $table.find('tbody').prepend($('<tr class="no-result text-center"><td colspan="'+ $table.find('.filters th').length +'">No result found</td></tr>'));
        }
    });
});
