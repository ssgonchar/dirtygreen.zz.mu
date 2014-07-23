$(function()
{ 
    $(".find-parametr").live("keyup", function()
    {
        $(this).keypress(function(event){
            if(event.keyCode == 13) 
            {
                $("input[value=Find]").click();
                return false;
                console.log("key press");
            }
 });
    });
});