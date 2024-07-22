const API_URL = 'https://apis.iolab.sk'
const USERNAME = 'xdrabm@stuba.sk'
const PASSWORD = '' //**heslo123

async function getAccessToken() {
    try {
        const url = API_URL + '/auth/access-token';

        const data = new URLSearchParams();
        data.append('username', USERNAME);
        data.append('password', PASSWORD);

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: data.toString(),
        });

        if (!response.ok) {
            throw new Error(`Request failed with status: ${response.status}`);
        }

        const responseData = await response.json(); // Assuming the response is in JSON format, adjust as needed
        return responseData;
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
}

function getRefreshToken() {

    const url = API_URL + '/auth/refresh-token'
    const xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            const responseData = JSON.parse(this.responseText);
            console.log()
            return responseData
        }
    }
    xmlhttp.open("POST", url, true);
    xmlhttp.setRequestHeader("Content-Type", "json");
    xmlhttp.send(data);
}

// /matlab - POST
async function uploadMatlabModel(modelName = 'model_drab.slx') {
    try {
        const url = 'file_upload.php';

        const data = new URLSearchParams();
        data.append('access_token', sessionStorage.getItem('access_token'),);
        data.append('file_name', modelName);

        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: data.toString()
        });

        if (!response.ok) {
            throw new Error(`Request failed with status: ${response.status}`);
        }

        // Assuming the response is in JSON format, adjust as needed
        return await response.json();
    } catch (error) {
        console.error('Error:', error);
        throw error;
    }
}

// /matlab/model-run/block/param - PUT
function updateParameters(newValue = 0) {
    const url = API_URL + '/matlab/model_drab.slx/Gain/Gain';
    const queryParams = $.param({new_value: newValue});
    const token = sessionStorage.getItem('access_token');

    $.ajax({
        url: `${url}?${queryParams}`,
        method: 'PUT',
        headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        success: function (data) {
            // Handle success
            console.log('Update successful', data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // Handle error
            console.error('Error:', textStatus, errorThrown);
        }
    });
}

// /matlab/model-run - POST
function getNewData() {
    const url = API_URL + '/matlab/model-run/model_drab.slx';
    const token = sessionStorage.getItem('access_token');

    $.ajax({
        url: url,
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Authorization': `Bearer ${token}`
        },
        success: function (data) {
            // Handle success
            console.log('Data retrieval successful', data);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            // Handle error
            console.error('Error:', textStatus, errorThrown);
        }
    });
}


var ws = null;

function connect() {
    var email = {
        value: 'xdrabm@stuba.sk'
    }
    ws = new WebSocket("ws://147.175.98.26/matlab/ws?email=" + email.value);
    ws.onopen = async (event) => {
        console.log('connected')
        sendMessage()
    }
    ws.onmessage = function (event) {
        var data_arr = event.data.split(":")
        console.log(data_arr)
    }
    ws.onerror = function (error) {
        console.error('WebSocket Error:', error)
    }
    ws.onclose = async (event) => {
        console.log('dis-connected')
    }
}

function sendMessage() {
    var modelName = {
        value: 'model_drab.slx'
    }
    ws.send(modelName.value)

    var blocks = {
        value: 'Gain'
    }


    ws.send(blocks.value)
    console.log(ws)

}

//FMU POST - model

//FMU
