import $ from "jquery";
import ApexCharts from "apexcharts";
$(function(){
    const pvJson  =$('#pvJson').html()
    const reportWeekJson = $('#reportWeekJson').html()

    let options = {
        series: JSON.parse(pvJson),
        chart: {
            width: 380,
            type: 'pie',
        },
        colors:['#008ffb', '#00e396', '#feb019','#ff4560', '#775dd0', '#8bb519','#df65d2'],
        labels: JSON.parse(reportWeekJson),
        responsive: [{
            breakpoint: 480,
            options: {
                chart: {
                    width: 200
                },
                legend: {
                    position: 'bottom'
                }
            }
        }]
    };


    const chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render().then();
});