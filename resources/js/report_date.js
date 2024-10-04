import $ from "jquery";
import ApexCharts from "apexcharts";
$(function(){
    const pvJson  =$('#pvJson').html()
    const uvJson = $('#uvJson').html()
    const reportDateJson = $('#reportDateJson').html()

    let options = {
        series: [{
            name: "방문자수",
            data: JSON.parse(uvJson)
        },
            {
                name: "페이지뷰",
                data: JSON.parse(pvJson)
            }
        ],
        chart: {
            height: 250,
            type: 'line',
            zoom: {
                enabled: false
            },
            toolbar:{
                show : false
            },
            fontFamily: 'Regular'
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'straight'
        },
        grid: {
            row: {
                colors: ['#ffffff', 'transparent'],
                opacity: 0.5
            },
        },
        xaxis: {
            categories: JSON.parse(reportDateJson),
        },
        yaxis: [{
            labels:{
                formatter: function(value) {
                    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }
            }
        }, {
            opposite: true,
            labels:{
                formatter: function(value) {
                    return value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }
            }
        }]
    };


    const chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render().then();
});