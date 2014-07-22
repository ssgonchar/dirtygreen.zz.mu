/**
 * Отменяет изменение картинки
 * @version: 20120928, zharkov
 */
var person_change_pic_cancel = function()
{
    $('#person-pic-new').hide();
    $('#person-pic').show();    
};

/**
 * Показывает блок изменения картинки
 * @version: 20120928, zharkov
 */
var person_change_pic = function()
{
    $('#person-pic-new').show();
    $('#person-pic').hide();
};