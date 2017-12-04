var gasReading = {
    Co: [],
    No: [],
    So: []
};

var canvas = document.querySelector('#ctx');
var ctx = canvas.getContext('2d');

var datasets = [{
    label: "Co",
    borderColor: "rgb(5, 0, 0)",
    fill: false,
    data: []
}, {
    label: "No",
    borderColor: "rgb(69, 169, 230)",
    fill: false,
    data: []
}, {
    label: "So",
    borderColor: "rgb(246, 250, 15)",
    fill: false,
    data: []
}]




fetch('/lastweek')
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        drawChart(data, datasets);
    })





function drawChart(gasData, datasets) {

    var finishedData = {
        labels: []
    }

    Object.keys(gasData).forEach(function(key) {
        finishedData.labels.push(key);
        datasets[0].data.push(gasData[key].Co)
        datasets[1].data.push(gasData[key].No);
        datasets[2].data.push(gasData[key].So);
    });

    finishedData.datasets = datasets;


    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: finishedData,
        options: {
            scales: {
                xAxes: [{
                    time: {
                        unit: 'day'
                    }
                }]
            }
        }
    });
}
