//document.ready function
$(function()
{
    /**
     * запрещает ввод веса позиции пользователем
     * стирает значения веса unitweight и weight если хоть один параметр не введен
     * @version 20140619, Uskov
     */
    //селекторы
    var thickness = $(".thickness-input");
    var width = $(".width-input");
    var length = $(".length-input");
    var unitweight = $(".unitweight-input");
    var qtty = $(".qtty-input");
    var weight = $(".weight-input");
    //делаем неактивными поля unitweight и weight
    unitweight.attr("readonly", true);
    weight.attr("readonly", true);
    //стираем значения веса unitweight если хоть один параметр не введен
    //толщина
    thickness.live("keyup", function()
    {
        //получаем id текущей строки
        var this_id = parseInt($(this).attr("id").replace(/\D+/g,""));
        if($(this).val() == ""){ $("#unitweight-" + this_id).val(""); }
    });
    //ширина
    width.live("keyup", function()
    {
        var this_id = parseInt($(this).attr("id").replace(/\D+/g,""));
        if($(this).val() == ""){ $("#unitweight-" + this_id).val(""); }
    });
    //длина
    length.live("keyup", function()
    {
        var this_id = parseInt($(this).attr("id").replace(/\D+/g,""));
        if($(this).val() == ""){ $("#unitweight-" + this_id).val(""); }
    });
    //стираем значения веса weight если параметр qtty не введен
    //количество итемов
    qtty.live("keyup", function()
    {
        //получаем id текущей строки
        var this_id = parseInt($(this).attr("id").replace(/\D+/g,""));
        if($(this).val() == ""){
            $("#weight-" + this_id).val("0.00");
        }
    });
    /*******************************/
    
});

/**
 * Подсчитывает стоимость позиции
 * @version 20130209, zharkov
 */
var addpos_calc_value = function(index)
{
    calc_value(index); 
    calc_total();
};

/**
 * Подсчитывает вес позиции
 * @version 20130209, zharkov
 */
var addpos_calc_weight = function(index)
{
    calc_weight(index); 
    addpos_calc_value(index); 
};

/**
 * Подсчитывает вес айтема
 * @version 20130209, zharkov
 */
var addpos_calc_unitweight = function(index)
{
    calc_unitweight(index);
    addpos_calc_weight(index); 
};