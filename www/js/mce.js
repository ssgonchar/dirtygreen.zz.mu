var mce_editors     = {};

var mce_advanced    = {
                            // General options
                            mode : "exact",
                            theme : "advanced",
                            plugins : "paste,autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

                            // Theme options
                            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,forecolor,backcolor,|,bullist,numlist,|,outdent,indent,|,sub,sup,|,image,media,emotions,link,unlink,charmap,hr,|,tablecontrols,|,undo,redo,code,removeformat",
                            theme_advanced_buttons2 : "",
                            theme_advanced_buttons3 : "",
                            theme_advanced_buttons4 : "",
                            theme_advanced_toolbar_location : "top",
                            theme_advanced_toolbar_align : "center",
                            content_css : '/js/tiny_mce/themes/advanced/skins/default/content_email.css',

                            height: 500,

                            // Drop lists for link/image/media/template dialogs
                            template_external_list_url : "/js/template_list.js",
                            external_link_list_url : "/js/link_list.js",
                            external_image_list_url : "/js/image_list.js",
                            media_external_list_url : "/js/media_list.js",
                            
                            forced_root_block : '' // Needed for 3.x
                    };

var mce_chat    = {
                            // General options
                            mode : "exact",
                            theme : "advanced",
                            plugins : "paste,autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

                            // Theme options
                            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,forecolor,backcolor,|,bullist,numlist,|,sub,sup,|,image,media,emotions,link,unlink,charmap,hr,|,removeformat",
                            theme_advanced_buttons2 : "",
                            theme_advanced_buttons3 : "",
                            theme_advanced_buttons4 : "",
                            theme_advanced_toolbar_location : "top",
                            theme_advanced_toolbar_align : "left",
                            content_css : '/js/tiny_mce/themes/advanced/skins/default/content_email.css',

                            height: 500,

                            // Drop lists for link/image/media/template dialogs
                            template_external_list_url : "/js/template_list.js",
                            external_link_list_url : "/js/link_list.js",
                            external_image_list_url : "/js/image_list.js",
                            media_external_list_url : "/js/media_list.js",
                            
                            forced_root_block : '' // Needed for 3.x
                    };
                    
var mce_normal      = {
                            mode : "exact",
                            theme : "advanced",                            
                            plugins : "paste,table,emotions",
                            
                            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,forecolor,backcolor,|,bullist,numlist,|,tablecontrols,|,emotions,|,removeformat",
                            theme_advanced_buttons2 : "",
                            theme_advanced_buttons3 : "",
                            theme_advanced_buttons4 : "",
                            theme_advanced_toolbar_location : "top",
                            theme_advanced_toolbar_align : "left",
};

var mce_simple      = {
                            mode : "exact",
                            theme : "advanced",                            
                            plugins : "paste,emotions",
                            
                            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,forecolor,backcolor,|,bullist,numlist,|,emotions,|,removeformat",
                            theme_advanced_buttons2 : "",
                            theme_advanced_buttons3 : "",
                            theme_advanced_buttons4 : "",
};

// для emails
var mce_enormal      = {
                            mode : "exact",
                            theme : "advanced",                            
                            plugins : "paste,table,emotions",
                            
                            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,forecolor,backcolor,|,bullist,numlist,|,outdent,indent,|,tablecontrols,|,removeformat",
                            theme_advanced_buttons2 : "",
                            theme_advanced_buttons3 : "",
                            theme_advanced_buttons4 : "",
                            theme_advanced_toolbar_location : "top",
                            theme_advanced_toolbar_align : "left"
};

var add_mce_editor = function(id, param_theme, param_height)
{
    if (param_height === undefined) param_height = 200;
    mce_editors[id] = {theme : param_theme, height : param_height};
}

var add_mce_editor_auto_size = function(id, param_theme, param_height)
{
    param_height = $('.app-modal-inner').height()-$('.chat-form').height()-$('.pad3').height()-100-$('.biz-modal-footer').height();
	
    mce_editors[id] = {theme : param_theme, height : param_height};
	$('.modal-mce').width($('.app-modal-inner').width()-245);
	$('.chat-right').height(param_height + $('.chat-form').height());
}