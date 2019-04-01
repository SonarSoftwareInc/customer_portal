        var historicalUsage = {!! $historicalUsage !!};
        var dataUsageLabel = '{{utrans("data_usage.usage")}}';
 
    var labels = [];
    var datasets = [
        {
            label: 'GB',
            borderColor: "rgba(243, 156, 18,1.0)",
            backgroundColor: "rgba(241, 196, 15,0.5)",
            borderWidth: 1,
            data: []
        }
    ];

    for (var k in historicalUsage) {
        labels.unshift(moment(historicalUsage[k]['timestamp']).utc().format("MMM Do, YYYY"));
        datasets[0].data.unshift(historicalUsage[k]['billable']);
    }

    new Chart('superChart', {
        type: 'bar',
        data: {
            labels: labels,
            datasets: datasets
        },
        options: {
            scales: {
                yAxes: [{
                    scaleLabel: {
                        display: true,
                        labelString: dataUsageLabel;
                    }
                }],
                xAxes: [{
                    stacked: true
                }],
                ticks: {
                    beginAtZero: true
                }
            }
        }
    });