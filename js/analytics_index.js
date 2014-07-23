/* 
 * Line chart 
 */

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
                   
                $('.steelgrades').html(json.steelgrades);
                }
            }
        });
    //Функция изменения сток ид и стил грейдов к ним
    $('.stoks').live('change', function() {
        var stock_id = $(this).val();
        
        $.ajax({
            url: '/analytics/bindsearch',
            data: {
                stock_id: stock_id
            },
            success: function(json) {
                if (json.result == 'okay')
                {
                    console.log(json);
                $('.steelgrades').html(json.steelgrades);
                $('.location-container').html('').html(json.locations);
                }
            }
        });
    });
// Datapikers
    $('.data_start , .data_end').datepicker({
        showWeek: true
    });




    $.getJSON('http://www.highcharts.com/samples/data/jsonp.php?filename=large-dataset.json&callback=?', function(data) {

        // Create a timer
        var start = +new Date();

        // Create the chart
        $('#chart').highcharts('StockChart', {
            chart: {
                events: {
                    load: function(chart) {
                        this.setTitle(null, {
                            text: 'Built chart in ' + (new Date() - start) + 'ms'
                        });
                    }
                },
                zoomType: 'x'
            },
            rangeSelector: {
                inputEnabled: $('#chart').width() > 480,
                buttons: [{
                        type: 'day',
                        count: 3,
                        text: '3d'
                    }, {
                        type: 'week',
                        count: 1,
                        text: '1w'
                    }, {
                        type: 'month',
                        count: 1,
                        text: '1m'
                    }, {
                        type: 'month',
                        count: 6,
                        text: '6m'
                    }, {
                        type: 'year',
                        count: 1,
                        text: '1y'
                    }, {
                        type: 'all',
                        text: 'All'
                    }],
                selected: 3
            },
            yAxis: {
                title: {
                    text: 'Price, $'
                }
            },
            title: {
                text: 'Sales chart, 2004-2010'
            },
            subtitle: {
                text: 'Built chart in ...' // dummy text to reserve space for dynamic subtitle
            },
            series: [{
                    name: 'Price, $',
                    data: data,
                    pointStart: Date.UTC(2004, 3, 1),
                    pointInterval: 3600 * 1000,
                    tooltip: {
                        valueDecimals: 1,
                        valueSuffix: ' per Ton'
                    }
                }]

        });
    });
});

/*
 * Circke chart
 */
$(function() {
    var chart;

    $(document).ready(function() {

        // Build the chart
        $('#circle-chart').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Must popular steelgrades, 2014'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            series: [{
                    type: 'pie',
                    name: 'Steelgrade share',
                    data: [
                        ['C45', 45.0],
                        ['P355GH', 26.8],
                        {
                            name: 'P355GH',
                            y: 12.8,
                            sliced: true,
                            selected: true
                        },
                        ['P460NL2', 8.5],
                        ['S235JL', 6.2],
                        ['S355JR', 0.7]
                    ]
                }]
        });
    });

});