$(function(){
    // invoicing
    $('#invoicing_type').change(function(){
        $('#invoicing_type_new').val(''); 
    });
    
    $('#invoicing_type_new').keyup(function(){
        $("#invoicing_type [value='0']").attr("selected", "selected");
    });    

    // payment
    $('#payment_type').change(function(){
        $('#payment_type_new').val(''); 
    });
    
    $('#payment_type_new').keyup(function(){
        $("#payment_type [value='0']").attr("selected", "selected");
    });
});
   
