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