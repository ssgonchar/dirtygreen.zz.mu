var ddt_fill_invoices = function(owner_id)
{
    if (owner_id > -1)
    {
        $('#invoices').prepend($('<option selected="" value="0">Loading...</option>'));        
        error = false;
        
        $.ajax({
            url: '/invoice/getlist',
            data : {
                owner_id : owner_id
            },
            success: function(json){
                if (json.result == 'okay') 
                {
                    fill_select("#invoices", json.list, {'value' : 0, 'name' : "--"});
                }
                else
                {
                    error = true;
                }                
            }
        });        
    }
    
    if (owner_id == -1 || error)
    {
        $('#invoices').empty();
        $('#invoices').prepend($('<option value="0">--</option>'));
    }    
};