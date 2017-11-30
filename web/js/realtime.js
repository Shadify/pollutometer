function arrayMax(arr) {
    var len = arr.length, max = -Infinity;
    while (len--) {
        if (arr[len] > max) {
            max = arr[len];
        }
    }
    return max;
};


const table = {
    Co: {
        breakpoints: [0, 4.4, 4.5, 9.4, 9.5, 12.4, 12.5, 15.4, 15.5, 30.4, 30.5, 40.4, 40.5, 50.4],
        aq: [0, 50, 51, 100, 101, 150, 151, 200, 201, 300, 301, 400, 401, 500]
    },
    So: {
        breakpoints: [0.000, 0.034, 0.035, 0.144, 0.145, 0.224, 0.225, 0.304, 0.305, 0.604, 0.605, 0.804, 0.805, 1.004],
        aq:[0, 50, 51, 100, 101, 150, 151, 200, 201, 300, 301, 400, 401, 500]
    },
    No: {
        breakpoints: [0.65, 1.24, 1.25, 1.64, 1.65, 2.04],
        aq:[201, 300, 301, 400, 401, 500]
    }
};


function calculateAQI(gasName, concentration) {
    var bpLow,bpHi;
    var bpLowIndex, bpHiIndex;

    table[gasName].breakpoints.forEach(function(value, index) {
        if(value < concentration && table[gasName].breakpoints[index + 1] > concentration) {
            bpLow = value;
            bpLowIndex = index;
        }

        if(value > concentration && table[gasName].breakpoints[index - 1] < concentration) {
            bpHi = value;
            bpHiIndex = index;
        }

    });



    var airQualityIndex = ((table[gasName].aq[bpHiIndex] - table[gasName].aq[bpLowIndex]) / (bpHi - bpLow)) * (concentration - bpLow) + table[gasName].aq[bpLowIndex];

    return airQualityIndex;

}


function update() {

    var data;

    fetch('/latest')
        .then(function(resp) {
            return resp.json();
        })
        .then(function(response) {
            data = response;
            var table = document.querySelector('#latest').children;
            table[0].textContent = data.TimeStamp;
            table[1].textContent = data.Co;
            table[2].textContent = data.No;
            table[3].textContent = data.So;

            var indexes = [];
            var CO = isNaN(calculateAQI("Co", data.Co)) ? 0 : calculateAQI("Co", data.Co);
            var SO = isNaN(calculateAQI("So", data.So)) ? 0 : calculateAQI("So", data.So);
            var NO = isNaN(calculateAQI("No", data.No)) ? 0 : calculateAQI("No", data.No);
            indexes.push(CO);
            indexes.push(NO);
            indexes.push(SO);

            var max = arrayMax(indexes);

            document.querySelector("#aq").innerHTML = `The current air quality index is <strong>${max}</strong>`;

        })
        .catch(function(error) {
            console.log(error);
        });




}

setInterval(update, 10000);













