import $ from "jquery";
import ApexCharts from "apexcharts";
$(function(){
    const pvJson  =$('#pvJson').html()
    const reportDeviceJson = $('#reportDeviceJson').html()

    let options = {
        series: JSON.parse(pvJson),
        chart: {
            width: 380,
            type: 'pie',
        },
        labels: JSON.parse(reportDeviceJson),
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