import $ from "jquery";
import ApexCharts from "apexcharts";
$(function(){
    const pvJson  =$('#pvJson').html()
    const reportAreaJson = $('#reportAreaJson').html()

    let options = {
        series: JSON.parse(pvJson),
        chart: {
            width: 380,
            type: 'pie',
        },
        labels: JSON.parse(reportAreaJson),
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