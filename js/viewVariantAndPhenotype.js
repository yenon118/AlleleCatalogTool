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


function uncheck_all_genotype() {
    let ids = document.querySelectorAll('input[id^=genotype]');

    for (let i = 0; i < ids.length; i++) {
        if (ids[i].checked) {
            ids[i].checked = false;
        }
    }
}


function check_all_genotype() {
    let ids = document.querySelectorAll('input[id^=genotype]');

    for (let i = 0; i < ids.length; i++) {
        if (!ids[i].checked) {
            ids[i].checked = true;
        }
    }
}


function uncheck_all_phenotypes(organism) {
    let ids = document.querySelectorAll('input[id^='+organism+'_phenotype_]');

    for (let i = 0; i < ids.length; i++) {
        if (ids[i].checked) {
            ids[i].checked = false;
        }
    }
}


function check_all_phenotypes(organism) {
    let ids = document.querySelectorAll('input[id^='+organism+'_phenotype_]');

    for (let i = 0; i < ids.length; i++) {
        if (!ids[i].checked) {
            ids[i].checked = true;
        }
    }
}


function constructInfoTable(organism, res, dataset, chromosome, position, genotype_array) {

    // Create table
    let detail_table = document.createElement("table");
    detail_table.setAttribute("style", "text-align:center; border:3px solid #000;");
    let detail_header_tr = document.createElement("tr");

    let header_array = Object.keys(res[0]);
    for (let i = 0; i < header_array.length; i++) {

        if (header_array[i] == "Chromosome" || header_array[i] == "Position" || header_array[i] == "Accession" || header_array[i] == "GRIN_Accession" || header_array[i] == "Accession_Name" || header_array[i] == "IRIS_ID" || header_array[i] == "TAIR_Accession" || header_array[i] == "Name" || header_array[i] == "Genotype" || header_array[i] == "Functional_Effect" || header_array[i] == "Category" || header_array[i] == "Imputation" || header_array[i] == "Improvement_Status" || header_array[i] == "Admixture_Group" || header_array[i] == "Subpopulation") {
            var detail_th = document.createElement("th");
            detail_th.setAttribute("style", "border:1px solid black; min-width:80px; height:18.5px;");
            detail_th.innerHTML = header_array[i];
            detail_header_tr.appendChild(detail_th);
        } else {
            // Create clickable links in the header
            var detail_th = document.createElement("th");
            detail_th.setAttribute("style", "border:1px solid black; min-width:80px; height:18.5px;");
            var detail_a = document.createElement('a');
            detail_a.target = "_blank";
            detail_a.href = "../viewVariantAndPhenotypeFigures/"+organism+"?dataset_1=" + dataset + "&chromosome_1=" + chromosome + "&position_1=" + position + "&phenotype_1=" + header_array[i] + "&genotype_1=" + genotype_array.join("%0D%0A");
            detail_a.innerHTML = header_array[i];
            detail_th.appendChild(detail_a);
            detail_header_tr.appendChild(detail_th);
        }

    }

    detail_table.appendChild(detail_header_tr);

    for (let i = 0; i < res.length; i++) {
        var detail_tr = document.createElement("tr");
        detail_tr.style.backgroundColor = ((i%2) ? "#FFFFFF" : "#DDFFDD");
        for (let j = 0; j < header_array.length; j++) {
            var detail_td = document.createElement("td");
            detail_td.setAttribute("style", "border:1px solid black; min-width:80px; height:18.5px;");
            detail_td.innerHTML = res[i][header_array[j]];
            detail_tr.appendChild(detail_td);
        }
        detail_table.appendChild(detail_tr);
    }

    return detail_table;
}


function queryPhenotypeDescription(organism, dataset) {
    $.ajax({
        url: 'queryPhenotypeDescription/'+organism,
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
                createAndDownloadCsvFile(csvString, String(organism) + "_phenotype_description");
            }

        },
        error: function (xhr, status, error) {
            console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
        }
    });
}


function queryVariantAndPhenotype(organism, dataset) {

    // Clear data appended to the div tags, if there is any
    if (document.getElementById('Variant_and_Phenotye_detail_table').innerHTML) {
        document.getElementById('Variant_and_Phenotye_detail_table').innerHTML = null;
    }

    let chromosome_1 = document.getElementById('chromosome_1').value;
    let position_1 = document.getElementById('position_1').value;

    let genotype_ids = document.querySelectorAll('input[id^=genotype]');
    let genotype_array = [];

    let phenotype_ids = document.querySelectorAll('input[id^='+organism+'_phenotype_]');
    let phenotype_array = [];

    for (let i = 0; i < genotype_ids.length; i++) {
        if (genotype_ids[i].checked) {
            genotype_array.push(genotype_ids[i].value);
        }
    }

    for (let i = 0; i < phenotype_ids.length; i++) {
        if (phenotype_ids[i].checked) {
            phenotype_array.push(phenotype_ids[i].value);
        }
    }

    if (chromosome_1 && position_1 && genotype_array.length > 0) {
        $.ajax({
            url: 'queryVariantAndPhenotype/'+organism,
            type: 'GET',
            contentType: 'application/json',
            data: {
                Chromosome: chromosome_1,
                Position: position_1,
                Genotype: genotype_array,
                Phenotype: phenotype_array,
                Dataset: dataset
            },
            success: function (response) {
                res = JSON.parse(response);

                if (res.length > 0) {
                    document.getElementById('Variant_and_Phenotye_detail_table').appendChild(
                        constructInfoTable(organism, res, dataset, chromosome_1, position_1, genotype_array)
                    );
                    document.getElementById('Variant_and_Phenotye_detail_table').style.overflow = 'scroll';
                } else {
                    let error_message = document.createElement("p");
                    error_message.innerHTML = "Please select genotype to view data!!!";
                    document.getElementById('Variant_and_Phenotye_detail_table').appendChild(error_message);
                    document.getElementById('Variant_and_Phenotye_detail_table').style.overflow = 'visible';
                }
                
            },
            error: function (xhr, status, error) {
                console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
            }
        });
    } else {
        let error_message = document.createElement("p");
        error_message.innerHTML = "Please select genotype to view data!!!";
        document.getElementById('Variant_and_Phenotye_detail_table').appendChild(error_message);
        document.getElementById('Variant_and_Phenotye_detail_table').style.overflow = 'visible';
    }
}


function downloadVariantAndPhenotype(organism, dataset) {

    let chromosome_1 = document.getElementById('chromosome_1').value;
    let position_1 = document.getElementById('position_1').value;

    let genotype_ids = document.querySelectorAll('input[id^=genotype]');
    let genotype_array = [];

    let phenotype_ids = document.querySelectorAll('input[id^='+organism+'_phenotype_]');
    let phenotype_array = [];

    for (let i = 0; i < genotype_ids.length; i++) {
        if (genotype_ids[i].checked) {
            genotype_array.push(genotype_ids[i].value);
        }
    }

    for (let i = 0; i < phenotype_ids.length; i++) {
        if (phenotype_ids[i].checked) {
            phenotype_array.push(phenotype_ids[i].value);
        }
    }

    if (chromosome_1 && position_1 && genotype_array.length > 0) {
        $.ajax({
            url: 'queryVariantAndPhenotype/'+organism,
            type: 'GET',
            contentType: 'application/json',
            data: {
                Chromosome: chromosome_1,
                Position: position_1,
                Genotype: genotype_array,
                Phenotype: phenotype_array,
                Dataset: dataset
            },
            success: function (response) {
                res = JSON.parse(response);

                if (res.length > 0) {
                    let csvString = convertJsonToCsv(res);
                    createAndDownloadCsvFile(csvString, String(organism) + "_" + String(chromosome_1) + "_" + String(position_1) + "_data");
                } else {
                    alert("Please select genotype to download data!!!");
                }
            },
            error: function (xhr, status, error) {
                console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
            }
        });
    } else {
        alert("Please select genotype to download data!!!");
    }
}