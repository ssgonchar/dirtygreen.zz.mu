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