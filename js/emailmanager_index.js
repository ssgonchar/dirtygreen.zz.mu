$(document).ready(function(){
        tinymce.init({
		selector : '#email_text',
		plugins: [
		    "advlist autolink lists link image charmap print preview anchor",
		    "searchreplace visualblocks code fullscreen",
		    "insertdatetime media table contextmenu paste textcolor"
		],
		toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | fontselect | fontsizeselect | forecolor | backcolor"
	});    
});	
