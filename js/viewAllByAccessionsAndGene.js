function convertJsonToPositionSeparatedCsv(jsonObject) {
    let csvString = '';

    var header_array = [];
    let th_keys = Object.keys(jsonObject[0]);
    for (let i = 0; i < th_keys.length; i++) {
        if (th_keys[i] != "Position" && th_keys[i] != "Genotype" && th_keys[i] != "Genotype_Description" && th_keys[i] != "Imputation") {
            header_array.push("\"" + th_keys[i] + "\"");
        } else if (th_keys[i] == "Position") {
            let position_array = String(jsonObject[0]["Position"]).split(" ");
            for (let j = 0; j < position_array.length; j++) {
                header_array.push("\"" + position_array[j] + "\"");
            }
        } else if (th_keys[i] == "Imputation") {
            header_array.push("\"" + th_keys[i] + "\"");
        }
    }
    csvString += header_array.join(',') + '\n';
    for (let i = 0; i < jsonObject.length; i++) {
        var row_array = [];

        let tr_keys = Object.keys(jsonObject[i]);
        for (let j = 0; j < tr_keys.length; j++) {
            if (tr_keys[j] != "Position" && tr_keys[j] != "Genotype" && tr_keys[j] != "Genotype_Description" && tr_keys[j] != "Imputation") {
                var value = ((jsonObject[i][tr_keys[j]] === null) || (jsonObject[i][tr_keys[j]] === undefined) || (jsonObject[i][tr_keys[j]] == "")) ? String("\"\"") : String("\"" + jsonObject[i][tr_keys[j]] + "\"");
                row_array.push(value);
            } else if (tr_keys[j] == "Genotype_Description") {
                let genotype_description_array = String(jsonObject[i][tr_keys[j]]).split(" ");
                for (let k = 0; k < genotype_description_array.length; k++) {
                    row_array.push("\"" + genotype_description_array[k] + "\"");
                }
            } else if (tr_keys[j] == "Imputation") {
                var value = ((jsonObject[i][tr_keys[j]] === null) || (jsonObject[i][tr_keys[j]] === undefined) || (jsonObject[i][tr_keys[j]] == "")) ? String("\"\"") : String("\"" + jsonObject[i][tr_keys[j]] + "\"");
                row_array.push(value);
            }
        }

        csvString += row_array.join(',') + '\n';
    }
    return csvString;
}


function convertJsonToCsv(jsonObject) {
    let csvString = '';
    let th_keys = Object.keys(jsonObject[0]);
    for (let i = 0; i < th_keys.length; i++) {
        th_keys[i] = "\"" + th_keys[i] + "\"";
    }
    csvString += th_keys.join(',') + '\n';
    for (let i = 0; i < jsonObject.length; i++) {
        let tr_keys = Object.keys(jsonObject[i]);
        for (let j = 0; j < tr_keys.length; j++) {
            csvString += ((jsonObject[i][tr_keys[j]] === null) || (jsonObject[i][tr_keys[j]] === undefined)) ? '\"\"' : "\"" + jsonObject[i][tr_keys[j]] + "\"";
            if (j < (tr_keys.length-1)) {
                csvString += ',';
            }
        }
        csvString += '\n';
    }
    return csvString;
}


function createAndDownloadCsvFile(csvString, filename) {
    let dataStr = "data:text/csv;charset=utf-8," + encodeURI(csvString);
    let downloadAnchorNode = document.createElement('a');
    downloadAnchorNode.setAttribute("href", dataStr);
    downloadAnchorNode.setAttribute("download", filename + ".csv");
    document.body.appendChild(downloadAnchorNode); // required for firefox
    downloadAnchorNode.click();
    downloadAnchorNode.remove();
}


function queryAllByAccessionsAndGene(organism, dataset, gene, accession_array_string) {
    let accession_array = accession_array_string.split(";");

    if (dataset && gene && accession_array_string) {
        if(accession_array.length > 0) {
            $.ajax({
                url: 'queryAllByAccessionsAndGene/'+organism,
                type: 'GET',
                contentType: 'application/json',
                data: {
                    Organism: organism,
                    Dataset: dataset,
                    Gene: gene,
                    Accession_Array: accession_array
                },
                success: function (response) {
                    res = JSON.parse(response);

                    if (res.length > 0) {
                        let csvString = convertJsonToPositionSeparatedCsv(res);
                        createAndDownloadCsvFile(csvString, String(organism) + "_" + String(dataset) + "_" + gene + "_Data");

                    } else {
                        alert("Downloading data by " + gene + " gene of " + dataset + " is not available!!!");
                    }

                },
                error: function (xhr, status, error) {
                    console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
                    alert("Downloading data by " + gene + " gene of " + dataset + " is not available!!!");
                }
            });
        } else {
            alert("Downloading data by " + gene + " gene of " + dataset + " is not available!!!");
        }
    } else {
        alert("Downloading data by " + gene + " gene of " + dataset + " is not available!!!");
    }
}


function queryAccessionInformation(organism, dataset){

    if (dataset) {
        $.ajax({
            url: 'queryAccessionInformation/'+organism,
            type: 'GET',
            contentType: 'application/json',
            data: {
                Organism: organism,
                Dataset: dataset
            },
            success: function (response) {
                res = JSON.parse(response);

                if (res.length > 0) {
                    let csvString = convertJsonToCsv(res);
                    createAndDownloadCsvFile(csvString, String(organism) + "_" + String(dataset) + "_Accession_Information");

                } else {
                    alert("Accession information of the " + dataset + " dataset is not available!!!");
                }

            },
            error: function (xhr, status, error) {
                console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
                alert("Accession information of the " + dataset + " dataset is not available!!!");
            }
        });
    } else {
        alert("Accession information of the " + dataset + " dataset is not available!!!");
    }

}