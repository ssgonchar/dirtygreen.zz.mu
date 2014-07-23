$(function(){
   
    // Quality Certificate
    $('#qctype_id').change(function(){
        $('#qctype_new').val(''); 
    });
    
    $('#qctype_new').keyup(function(){
        $("#qctype_id [value='0']").attr("selected", "selected");
    });    
    
});
