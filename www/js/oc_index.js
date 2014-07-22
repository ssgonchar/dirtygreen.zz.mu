$(document).ready(function(){	
	//обработчик событий для кнопки поиска
	//oc
    $('#search').click(function(){
		var url="/oc/filter/plate_id:" + $('#search_string').val() + ";";
		$(location).attr('pathname',url);
    });	
	
	$("#search_string").keypress(function(e){
		if(e.keyCode==13){
			//нажата клавиша enter
			var url="/oc/filter/plate_id:" + $('#search_string').val() + ";";
			$(location).attr('pathname',url);
			console.log('keypress');
			return false;
		}
	});
});