/* 
 * Line chart 
 */
 
var chosenInit = function() {
    var arg = ({
        width: "95%",
        disable_search_threshold: 10,
        placeholder_text: 'Select Some'
    });
    $('select').addClass('chosen-select').chosen(arg);
};
$(function() {
    //Получаем стартовый сток ид и стилгрейды к нему
    var stock_id = $('.stoks').val();
    $.ajax({
        url: '/analytics/bindsearch',
        data: {
            stock_id: stock_id

        },
        success: function(json) {
            if (json.result == 'okay')
            {

                $('.steelgrades-container').html('').html(json.steelgrades);
                $('.location-container').html('').html(json.locations);
                $('.stockholders-container').html('').html(json.stockholders);
            }
        }
    });
    chosenInit();
    //Функция изменения сток ид и стил грейдов к ним
    $('.stoks').live('change', function() {
        var stock_id = $(this).val();
        $.ajax({
            url: '/analytics/bindsearch',
            data: {
                stock_id: stock_id
            },
            success: function(json) {
                if (json.result == 'okay') {
                    //console.log(json);
                    $('.steelgrades-container').html('').html(json.steelgrades);
                    $('.location-container').html('').html(json.locations);
                    $('.stockholders-container').html('').html(json.stockholders);
                    chosenInit();
                }
            }
        });
    });
// Datapikers
    $('.data_start , .data_end').datepicker({
        showWeek: true
    });


// company
    $(".customer_title").autocomplete({
        source: function(request, response) {
            $('.cutomer_id').val(0);
            var stock_id = $('.stoks').val();
            var date_end = $('.data_end').val().split('/').reverse().join('-');
            var date_start = $('.data_start').val().split('/').reverse().join('-');
            var where = "";
            if (stock_id > 0) {
                where += ' AND orders.stock_id="' + stock_id + '"';
            }

            if (date_end.length > 8) {
                where += ' AND orders.modified_at <= "' + date_end + '"';
            }

            if (date_start.length > 8) {
                where += ' AND orders.modified_at >= "' + date_start + '"';
            }

            console.log(where);
            $.ajax({
                //url: "/company/getlistbytitle",
                url: "/order/getcustomers",
                //url: "/order/getdeliverypoints",
                data: {
                    //maxrows: 25,
                    title: request.term,
                    where: where
                },
                success: function(data) {
                    console.log(data);
                    response($.map(data.customers, function(item) {
                        //var list_title = item.company.title+'('+item.company.city.title+')';
                        var list_title = item.title;
                        var list_id = item.id;
                        /*if (typeof item.company.city !== 'undefined') {
                         list_title += ' (' + item.company.city.title + ')';
                         */
                        //console.log(item);
                        //console.log(typeof item.company.city);
                        return {
                            label: list_title,
                            //value: item.company.id
                            value: list_title
                        }
                    }));
                }
            });
        },
        minLength: 1,
        select: function(event, ui) {

            if (ui.item)
            {
                $('.customer_title').val(ui.item.label);
                $('.cutomer_id').val(ui.item.value);
            }
            else
            {
                $('.cutomer_id').val(0);
            }

            return false;
        },
        open: function() {
        },
        close: function() {
        }
    });
// company
    $(".deliverypoint_title").autocomplete({
        source: function(request, response) {

//$('.cutomer_id').val(0);
//var stock_id = $('.stoks').val();

            $.ajax({
//url: "/company/getlistbytitle",
                url: "/order/getdeliverypoints",
                data: {
                    //maxrows: 25,
                    title: request.term
                            //stock_id: stock_id
                },
                success: function(data) {
                    console.log(data);
                    response($.map(data.deliverypoints, function(item) {
                        //var list_title = item.company.title+'('+item.company.city.title+')';
                        var list_title = item;
                        //console.log(item);
                        //console.log(typeof item.company.city);
                        return {
                            label: list_title,
                            value: list_title
                                    //value: list_title
                        }
                    }));
                }
            });
        },
        minLength: 1,
        select: function(event, ui) {

            if (ui.item)
            {
                $('.deliverypoint_title').val(ui.item.label); 
            }
            else
            {
                $('.deliverypoint_title').val('');
            }

            return false;
        },
        open: function() {
        },
        close: function() {
        }
    });



    


});


google.load("visualization", "1", {packages:["corechart"]});
google.setOnLoadCallback(drawChart);

function drawChart()
{
    if (typeof dataTable == 'undefined') return;
    
    if (dataTable)
    {
        //var data    = google.visualization.arrayToDataTable(chart_data.sales_volume);
        var data    = new google.visualization.DataTable(dataTable);
        var chart   = new google.visualization.LineChart($('.chart')[0]);
        chart.draw(data, chart_options);
    }
}
$('input[placeholder], textarea[placeholder]').each(function(){
  var $input = $(this);
  var value = $input.attr('placeholder');
  $input.removeAttr('placeholder').val(value);
  $input.bind({
   blur: function() {if (this.value=='') this.value=value;},
   focus: function() {if (this.value==value) this.value='';}
  });
 });