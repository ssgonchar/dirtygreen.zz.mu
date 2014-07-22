/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function add_dictionary()
{
    /*Получаем массив словаря вида:
     * 
     * dictionary[0]['title'] = title;
     * dictionary[0]['description'] = description;
     * 
     */
    
            $.ajax({
                url     : "/dictionary/getdictionary",
                /*
                data    : {
                    maxrows : 25,
                    login   : request.term
                },*/
                success : function( data ) {
                    var dictionary = data.dictionary;
                    dictionary.forEach(integration_dictionary);
                }
            });    
}


/*
item — очередной элемент массива.
i — его номер.
arr — массив, который перебирается.
 */
function integration_dictionary(item, i, arr)
{
    //console.log(item['nomenclature']['title']);
    var word = item['nomenclature']['title'];
    var description = item['nomenclature']['description'];
    search_and_replase(word, description);
}

function parse_dictionary(dictionary)
{
    //var dictionary = get_dictionary();
    
    //console.log(dictionary);
    
    
    
    //add_tooltip();
}


function search_and_replase(word, description)
{
    console.log(word);
    var html = $('body').html();
    //$('body:contains("'+word+'")').wrap('<span class="dictionary glyphicon glyphicon-question-sign" title="'+description+'"></span>');
    var re = '/'+word+'/g';
    var result_replace = html.replace(re,'<span class="h1 dictionary glyphicon glyphicon-question-sign" title="'+description+'">'+word+'</span>');
    //document.write(result_replace);
    // var result_replace = html.replace(new RegExp(word,'g'),'<h1>'+word+'</h1>');
    //$('div:contains("'+word+'")')
    //var content = $('*').text().replace(word, '<span class="h1 dictionary glyphicon glyphicon-question-sign" title="'+description+'">'+word+'</span>');
    
    replace_word(word, description);
    
    //console.log(content);     
}   
    
function replace_word(word, description){
   //Задайте строку для поиска, замены и место поиска:
   var phrase = word;
   var replacement = '<span class="h1 dictionary glyphicon glyphicon-question-sign" title="'+description+'">'+word+'</span>';
   var context = $('body');
 
   context.text(
      context.text().replace('/'+phrase+'/gi', replacement)
   );
}

function add_tooltip()
{
    $('.dictionary ').tooltip();
}


var add_to_dictionary = function (){
var txt = '';
 if (window.getSelection)
{
    txt = window.getSelection();
         }
else if (document.getSelection)
{
    txt = document.getSelection();
        }
else if (document.selection)
{
    txt = document.selection.createRange().text;
        }
else return;

console.log(txt);
};

$( window ).load(function(){
    console.log('start select');
    $('body').mouseup( function(e) {
        var selected_text = (
        (window.getSelection && window.getSelection()) ||
        (document.getSelection && document.getSelection()) ||
        (document.selection && document.selection.createRange && document.selection.createRange().text)
        );
        console.log(selected_text);
    });
});

