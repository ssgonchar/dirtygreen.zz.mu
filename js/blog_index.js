/**
 * Очищает поля формы фильтра
 * @version 20130213, zharkov
 */
var blog_clear_filter = function()
{
    $('input:text').val('');
    $('input:checkbox').removeAttr('checked');
};

/**
 * Обработчик, выводит ссылку с id и датой сообщения в блоге
 * 05.05.14 uskov
 */
$('.panel-heading span > i').on("click", function(event){
	
	var message_id = $(this).data('messageId');
   var created_at = $(this).data('createdAt');
	show_blog_message_ref(this, message_id, created_at);
});
