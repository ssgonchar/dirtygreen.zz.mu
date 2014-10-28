//AJAX. получаем объект письма
var getEmail = function(email_id) {
    var email = $.ajax({
		url: '/email/getemailobj',
		data: {
		    email_id: email_id,
		},
		success: function(json) {
		    (function(){return json;})();             
		},
    });
    return JSON.parse(email.responseText);
};

$(function(){
    $('.mbox-toggler').on('click', function(){
        var toggler_element = $(this),
        rel = toggler_element.attr('rel');
        //$('.mbox-spoiler').hide('slow');
        $('.mbox-spoiler[rel="' + rel + '"]').toggle('slow');
    });
    
    $('.submit-filter').on('click', function(){
        var url = location.href;
        var keyword = $('.filter-keyword').val();
        
        if (url.match("\/~[0-9]+") != null)
        {
            url = url.replace(/\/~[0-9]+/, '');
        }
        
        var is_set_filter = url.match("filter\/") == null ? false : true;
        
        if (!is_set_filter)
        {
            location.href = url + '/filter/keyword:' + keyword;
            return false;
        }
        
        var is_set_keyword = url.match("keyword:") == null ? false : true;
        
        if (!is_set_keyword)
        {
            location.href = url + ';keyword:' + keyword;
            return false;
        }
        else
        {
            location.href = url.replace(/keyword:.+\/?/, "keyword:" + keyword);
            return false;
        }
        
        return false;
    });
	
	$('.single-checkbox').on('click', function() {
		//console.log($(this));
		//event.preventDefault();
		event.stopPropagation();  		
	});
    
    $('.delete-emails').live('click', function(e){
        var email_id = $(this).data('id');
        
        $.ajax({
                    url: '/emailmanager/delete',
                    data: {
                        email_id: email_id,
                    },
                    success: function(json) {
                                  
                    },
        });   
        
        $(this).parent().parent().remove();
    });
 
    $('.group-checkbox').on('click', function(){
        var _this = $(this),
            HTML_single_checkboxes = $('.single-checkbox');
            
        $('.manage-buttons').hide();
        HTML_single_checkboxes.removeAttr('checked');
        
        if (_this.hasClass('gc-all'))
        {
            HTML_single_checkboxes.attr('checked', 'checked');
        }
        if (_this.hasClass('gc-read'))
        {
            $('.single-checkbox.et-read').attr('checked', 'checked');
        }
        if (_this.hasClass('gc-unread'))
        {
            $('.single-checkbox.et-unread').attr('checked', 'checked');
        }
        if (_this.hasClass('gc-unselect'))
        {
            $('.choosen-items-stats').children('.cis-checked').html(0);
            $('.choosen-items-stats').hide();
            return true;
        }
        
        var checked_items = $('.single-checkbox:checked');
        
        if (checked_items.hasClass('et-notspam'))
        {
            if (checked_items.hasClass('et-unread'))
            {
                $('.manage-buttons.mb-type-read').show();
            }
            if (checked_items.hasClass('et-read'))
            {
                $('.manage-buttons.mb-type-unread').show();
            }
            $('.manage-buttons.mb-type-spam').show();
            $('.manage-buttons.mb-type-delete').show();
            $('.manage-buttons.mb-type-restore').show();
        }
        else
        {
            if (checked_items.length > 0)
            {
                $('.manage-buttons.mb-type-notspam').show();
                $('.manage-buttons.mb-type-delete').show();
                $('.manage-buttons.mb-type-restore').show();
            }
        }
        
        $('.choosen-items-stats').children('.cis-checked').html(checked_items.length);
        
        checked_items.length > 0 ? $('.choosen-items-stats').show() : $('.choosen-items-stats').hide();
    });
    
    $('.single-checkbox').on('click', function(){
        var HTML_checkall = $('.choose-all-checkboxes'),
            checked_items = $('.single-checkbox:checked');
            
        $('.manage-buttons').hide();
        HTML_checkall.removeAttr('checked');
        
        if (checked_items.length === $('.single-checkbox').length)
        {
            HTML_checkall.attr('checked', 'checked');
        }
        
        if (checked_items.hasClass('et-notspam'))
        {
            if (checked_items.hasClass('et-unread'))
            {
                $('.manage-buttons.mb-type-read').show();
            }
            if (checked_items.hasClass('et-read'))
            {
                $('.manage-buttons.mb-type-unread').show();
            }
            $('.manage-buttons.mb-type-spam').show();
            $('.manage-buttons.mb-type-delete').show();
            $('.manage-buttons.mb-type-restore').show();
        }
        else
        {
            if (checked_items.length > 0)
            {
                $('.manage-buttons.mb-type-notspam').show();
                $('.manage-buttons.mb-type-delete').show();
                $('.manage-buttons.mb-type-restore').show();
            }
        }
        
        $('.choosen-items-stats').children('.cis-checked').html(checked_items.length);
        
        checked_items.length > 0 ? $('.choosen-items-stats').show() : $('.choosen-items-stats').hide();
    });


    $('.bc-list-toggle-visibility').on('click', function (){
        $('.bc-list').toggleClass('expanded', 'collapsed');
        $('.expand, .collapse').toggleClass('on');

	event.preventDefault();
	event.stopPropagation(); 	
    });
    
    if ($.prettyPhoto != undefined)
    {
        try {$("a[rel^='pp_attachments']").prettyPhoto({gallery_markup: '',social_tools: ''});} catch(e) {}
    }
    
    $('.panel-heading').on('click', function() {
	    var panel_id = $(this).find("a").attr('href');
	    $(panel_id).collapse('toggle');
	    var destination = $(this).offset().top-$('.panel-heading').height()-$('.navbar-header').height();
//jQuery.fx.interval = 5;
	    if ($.browser.safari) {
		//$('body').css('position','relative');
		$('body').animate({ scrollTop: destination }, 800,'easeOutQuad');
	    } else {
		//$('html').css('position','relative');
		//$('html').animate({ scrollTop: destination }, 'slow');
		//$('html').slide('up','hide', destination,'slow');
		$('html').animate({ scrollTop: destination }, 800,'easeOutQuad');
	    }

	    //console.log($(this));
	    event.preventDefault();
	    event.stopPropagation(); 	    
    });
    
    $('.btn-hide-text').live('click', function(event){
	var email_id = $(this).data('emailId');
	var result = getEmail(email_id);
	var $email_description_block = $(this).parent().parent().find('.email-description');
	//var email_description = $email_description_block.html();
	$email_description_block.html(result.email.email.description.substring(0,301)+' ...');
	$email_description_block.append("<br/><button class='btn btn-default btn-read-more' data-email-id='"+email_id+"'>Full view</button>");
	event.preventDefault();
	event.stopPropagation();    
    });
    
    $('.btn-read-more').live('click', function(event) {
	var email_id = $(this).data('emailId');
	var result = getEmail(email_id);
	var $email_description = $(this).parent().parent().find('.email-description');
	//console.log(result);
	$email_description.html(result.email.email.description_html);
	$email_description.append("<br/><button class='btn btn-default btn-hide-text' data-email-id='"+email_id+"'>Short view</button>");
	event.preventDefault();
	event.stopPropagation();
    });
    
    $('.btn').live('click',function(event) {
	if ($(this).data('toggle')=='modal') {
	    event.preventDefault();
	    event.stopPropagation();
	}
    });
    
});
var attachments_counter = function()
{
    //данные, необходимые для получения кол-ва аттачей
    var uploader_object_alias = $('#uploader_object_alias').val();
    var uploader_object_id = $('#uploader_object_id').val();
    
    //счетчик shared docs обновляю из js (кол-во span в #shared-docs)
    //var shared_docs_count = $('#shared-docs span').length > 0 ? '('+$('#shared-docs span').length+')' : '';
    //$('#shared-docs-count').text(shared_docs_count);
    
    
    //счетчик аттачей беру из сессии
    $.ajax({
        url     : '/emailmanager/getattachcount',
        data    : {
            uploader_object_alias : uploader_object_alias,
            uploader_object_id    : uploader_object_id,
            
        },
        success : function(json){
            if(json.result == 'okay'){
                var attachments_count = json.attach_count > 0 ? '('+json.attach_count+')' : '';
                $('#attachments-count').text(attachments_count);
            }
        }
    });
    
};