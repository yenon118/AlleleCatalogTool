function convertJsonToPositionSeparatedCsv(jsonObject) {
    let csvString = '';

    // Table header
    let th_keys = Object.keys(jsonObject[0]);
    for (let i = 0; i < th_keys.length; i++) {
        if (th_keys[i] !== "Position" && th_keys[i] !== "Genotype" && th_keys[i] !== "Genotype_with_Description") {
            csvString += th_keys[i] + ',';
        }
    }
    for (let i = 0; i < th_keys.length; i++) {
        if (th_keys[i] === "Position") {
            let positionArray = String(jsonObject[0]["Position"]).split(" ");
            csvString += positionArray.join(',');
        }
    }
    csvString += '\n';

    // Table body
    for (let i = 0; i < jsonObject.length; i++) {
        let tr_keys = Object.keys(jsonObject[i]);

        for (let j = 0; j < tr_keys.length; j++) {
            if (tr_keys[j] !== "Position" && tr_keys[j] !== "Genotype" && tr_keys[j] !== "Genotype_with_Description") {
                csvString += jsonObject[i][tr_keys[j]] + ',';
            }
        }
        for (let j = 0; j < tr_keys.length; j++) {
            if (tr_keys[j] === "Genotype_with_Description") {
                let genotypeWithDescriptionArray = String(jsonObject[i][tr_keys[j]]).split(" ");
                csvString += genotypeWithDescriptionArray.join(',');
            }
        }
        csvString += '\n';
    }

    return csvString;
}


function convertJsonToCsv(jsonObject) {
    let csvString = '';
    let th_keys = Object.keys(jsonObject[0]);
    csvString += th_keys.join(',') + '\n';
    for (let i = 0; i < jsonObject.length; i++) {
        let tr_keys = Object.keys(jsonObject[i]);
        for (let j = 0; j < tr_keys.length; j++) {
            csvString += ((jsonObject[i][tr_keys[j]] === null) || (jsonObject[i][tr_keys[j]] === undefined)) ? '' : jsonObject[i][tr_keys[j]];
            csvString += ',';
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


function downloadAllCountsByGene(organism, dataset, gene, checkboxes) {
    checkboxes = checkboxes.split(";");

    $.ajax({
        url: 'downloadAllCountsByGene/'+organism,
        type: 'GET',
        contentType: 'application/json',
        data: {
            Gene: gene,
            Dataset: dataset,
            Organism: organism,
            Checkboxes: checkboxes
        },
        success: function (response) {
            let res = JSON.parse(response);

            if (res.length > 0) {
                let csvString = convertJsonToPositionSeparatedCsv(res);
                createAndDownloadCsvFile(csvString, dataset + "_" + gene + "_accession_count_data");
            }

        },
        error: function (xhr, status, error) {
            console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
        }
    });
}


function downloadAllByGene(organism, dataset, gene, checkboxes) {
    checkboxes = checkboxes.split(";");

    $.ajax({
        url: 'downloadAllByGene/'+organism,
        type: 'GET',
        contentType: 'application/json',
        data: {
            Gene: gene,
            Dataset: dataset,
            Organism: organism,
            Checkboxes: checkboxes
        },
        success: function (response) {
            let res = JSON.parse(response);

            if (res.length > 0) {
                let csvString = convertJsonToCsv(res);
                createAndDownloadCsvFile(csvString, dataset + "_" + gene + "_data");
            }

        },
        error: function (xhr, status, error) {
            console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
        }
    });
}


function downloadAllCountsByMultipleGenes(organism, dataset, genes, checkboxes) {
    genes = genes.split(";");
    checkboxes = checkboxes.split(";");

    $.ajax({
        url: 'downloadAllCountsByMultipleGenes/'+organism,
        type: 'GET',
        contentType: 'application/json',
        data: {
            Genes: genes,
            Dataset: dataset,
            Organism: organism,
            Checkboxes: checkboxes
        },
        success: function (response) {
            let res = JSON.parse(response);

            if (res.length > 0) {
                let csvString = convertJsonToCsv(res);
                createAndDownloadCsvFile(csvString, dataset + "__" + genes.join("_") + "__accession_count_data");
            }

        },
        error: function (xhr, status, error) {
            console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
        }
    });
}


function downloadAllByMultipleGenes(organism, dataset, genes, checkboxes) {
    genes = genes.split(";");
    checkboxes = checkboxes.split(";");

    $.ajax({
        url: 'downloadAllByMultipleGenes/'+organism,
        type: 'GET',
        contentType: 'application/json',
        data: {
            Genes: genes,
            Dataset: dataset,
            Organism: organism,
            Checkboxes: checkboxes
        },
        success: function (response) {
            let res = JSON.parse(response);

            if (res.length > 0) {
                let csvString = convertJsonToCsv(res);
                createAndDownloadCsvFile(csvString, dataset + "__" + genes.join("_") + "__data");
            }

        },
        error: function (xhr, status, error) {
            console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
        }
    });
}


function downloadAllByAccessionsAndGene(organism, dataset, accessions, gene) {
    filename = dataset + "__" + accessions.replace(/[^a-zA-Z0-9]/g, '_') + "__" + gene + "__data";

    accessions = accessions.split(";");

    $.ajax({
        url: 'downloadAllByAccessionsAndGene/'+organism,
        type: 'GET',
        contentType: 'application/json',
        data: {
            Gene: gene,
            Accessions: accessions,
            Dataset: dataset,
            Organism: organism
        },
        success: function (response) {
            let res = JSON.parse(response);

            if (res.length > 0) {
                let csvString = convertJsonToCsv(res);
                createAndDownloadCsvFile(csvString, filename);
            }

        },
        error: function (xhr, status, error) {
            console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
        }
    });
}
