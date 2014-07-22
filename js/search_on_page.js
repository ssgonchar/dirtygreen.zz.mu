/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


jQuery(document).ready(function(){

	var search_number = 0;
	var search_count = 0;
	var count_text = 0;
	var srch_numb = 0;
           
        //console.log($('.always_show').attr('checked'));  
          
    
	function scroll_to_word(){
		var pos = $(' .search-target .selectHighlight').position();
		jQuery.scrollTo(".selectHighlight", 500, {offset:-150});
	}

	$('#search_text').bind('keyup oncnange', function() {
            var query = $('#search_text').val();
                $.ajax({
                    url     : "/service/savesettings",
                    data    : {
                            set_module : 'search_on_page',
                            set_action : 'query',
                            set_value   : query
                    },
                    success : function( data ) {
                        console.log(data);
                    },
                    error: function( data ) {
                        console.log(data);
                    }
                });               
            
                //console.log('seach string: '+$('#search_text').val());
                $(' .search-target').removeHighlight();
		txt = $('#search_text').val();
		if (txt == '') {
                    $('.search-on-page .btn').attr('disabled', 'true');
                    return;
                }
                $('.search-on-page .btn').removeAttr('disabled');
		//$('#text').highlight(txt);
		$(' .search-target').highlight(txt);
		//search_count = $('#text span.highlight').size() - 1;
		search_count = $(' .search-target span.highlight').size() - 1;
		count_text = search_count + 1;
		search_number = 0;
		$(' .search-target').selectHighlight(search_number);
		if ( search_count >= 0 ) scroll_to_word();
		if ( search_count > 1 ) {
                    //$('.search-on-page .btn').removeAttr('disabled');
                    $('#count').html('Search results <b class="label label-primary">'+count_text+'</b>');
                } else {
                    
                    $('#count').html('Search result <b class="label label-primary">'+count_text+'</b>');
                }
	});
            
        if($('.always_show').attr('checked')==='checked'){
            $('.btn-search-on-page').trigger('click');
            $('#search_text').trigger('oncnange');
            console.log('open search');
        }            
            
	$('#clear_button').click(function() {
		$(' .search-target').removeHighlight();
		$('#search_text').val('');
		$('#count').html('');
                $('.search-on-page .btn').attr('disabled', 'true');
		jQuery.scrollTo(0, 500, {queue:true});
	});
            
	$('#prev_search').click(function() {
		if (search_number == 0) return;
		$(' .search-target .selectHighlight').removeClass('selectHighlight');
		search_number--;
		srch_numb = search_number + 1;
		$(' .search-target').selectHighlight(search_number);
		if ( search_count >= 0 ) { 
			scroll_to_word();
			$('#count').html('Search results <b class="label label-primary">'+srch_numb+'</b> from '+$('.search-target span.highlight').size());
		}
	});
            
	$('#next_search').click(function() {
		if (search_number == search_count) return;
		$(' .search-target .selectHighlight').removeClass('selectHighlight');
		search_number++;
		srch_numb = search_number + 1;
		$(' .search-target').selectHighlight(search_number);
		if ( search_count >= 0 ) { 
			scroll_to_word();
			$('#count').html('Search results <b class="label label-primary">'+srch_numb+'</b> from '+$('.search-target span.highlight').size());
		}
	});
       
       $('.search-on-page-dropdown').on('hide.bs.dropdown', function(event){
            if($('.always_show').attr('checked')==='checked'){
                event.preventDefault();
                console.log(event);
            }
        });
    $('.always_show').on('click', function(event){
            console.log(event);
            event.stopPropagation();   
            //event.preventDefault();   
            search_on_page_always_open(this.checked);
    });
        });

        function search_on_page_always_open(always_show) {
            
            //if(always_show === 'true') {
                  
            $.ajax({
                url     : "/service/savesettings",
                data    : {
                        set_module : 'search_on_page',
                        set_action : 'always_show',
                        set_value   : always_show
                },
                success : function( data ) {
                    console.log(data);
                },
                error: function( data ) {
                    console.log(data);
                }
            });                    
            //}
            console.log(always_show);
            //$preventDe
        }