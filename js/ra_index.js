$(function(){
    $('.td-href-view').on('click', function(){
        var raid = $(this).parents('tr:first').attr('raid');
        location.href = '/ra/' + raid;
    });
    
    $('.td-href-edit').on('click', function(){
        var raid = $(this).parents('tr:first').attr('raid');
        location.href = '/ra/' + raid + '/edit';
    });
});