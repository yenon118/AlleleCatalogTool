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
                var value = ((jsonObject[i][tr_keys[j]] === null) || (jsonObject[i][tr_keys[j]] === undefined)) ? String("\"\"") : String("\"" + jsonObject[i][tr_keys[j]] + "\"");
                row_array.push(value);
            } else if (tr_keys[j] == "Genotype_Description") {
                let genotype_description_array = String(jsonObject[i][tr_keys[j]]).split(" ");
                for (let k = 0; k < genotype_description_array.length; k++) {
                    row_array.push("\"" + genotype_description_array[k] + "\"");
                }
            } else if (tr_keys[j] == "Imputation") {
                var value = ((jsonObject[i][tr_keys[j]] === null) || (jsonObject[i][tr_keys[j]] === undefined)) ? String("\"\"") : String("\"" + jsonObject[i][tr_keys[j]] + "\"");
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


function checkHighlight(event) {
    var id = event.id;
    var id = id.replace(/(_l)|(_r)/, '');

    let input_ids = document.querySelectorAll('input[id^="'+id+'_"]');
    let td_ids = document.querySelectorAll('td[id^="'+id+'_"]');

    if (document.getElementById(event.id).checked) {
        for (let i = 0; i < input_ids.length; i++) {
            input_ids[i].checked = true;
        }
    } else {
        for (let i = 0; i < input_ids.length; i++) {
            input_ids[i].checked = false;
        }
    }

    for (let i = 0; i < td_ids.length; i++) {
        if (td_ids[i].style.fontSize == "") {
            td_ids[i].style.fontSize = "20px";
        } else {
            td_ids[i].style.fontSize = "";
        }
    }
}


function constructMetadataTable(res, organism, dataset, key, gene, chromosome, position, genotype, genotypeDescription) {
    // Color for functional effects
    let ref_color_code = "#D1D1D1";
    let missense_variant_color_code = "#7FC8F5";
    let frameshift_variant_color_code = "#F26A55";
    let exon_loss_variant_color_code = "#F26A55";
    let lost_color_code = "#F26A55";
    let gain_color_code = "#F26A55";
    let disruptive_color_code = "#F26A55";
    let conservative_color_code = "#FF7F50";
    let splice_color_code = "#9EE85C";

    // Create table
    let detail_table = document.createElement("table");
    detail_table.setAttribute("style", "text-align:center; border:3px solid #000;");
    let detail_header_tr = document.createElement("tr");

    let header_array = Object.keys(res[0]);
    for (let i = 0; i < header_array.length; i++) {
        if (header_array[i] != "Position" && header_array[i] != "Genotype" && header_array[i] != "Genotype_Description") {
            var detail_th = document.createElement("th");
            detail_th.setAttribute("style", "border:1px solid black; min-width:80px; height:18.5px;");
            detail_th.innerHTML = header_array[i];
            detail_header_tr.appendChild(detail_th);
        } else if(header_array[i] == "Position"){
            var position_array = res[0]["Position"].split(" ");
            for (let j = 0; j < position_array.length; j++) {
                var detail_th = document.createElement("th");
                detail_th.setAttribute("style", "border:1px solid black; min-width:80px; height:18.5px;");
                detail_th.innerHTML = position_array[j];
                detail_header_tr.appendChild(detail_th);
            }
        }
    }

    detail_table.appendChild(detail_header_tr);

    for (let i = 0; i < res.length; i++) {
        var detail_tr = document.createElement("tr");
        detail_tr.style.backgroundColor = ((i%2) ? "#FFFFFF" : "#DDFFDD");
        for (let j = 0; j < header_array.length; j++) {
            if (header_array[j] != "Position" && header_array[j] != "Genotype" && header_array[j] != "Genotype_Description" && header_array[j] != "Imputation") {
                var detail_td = document.createElement("td");
                detail_td.setAttribute("style", "border:1px solid black; min-width:80px; height:18.5px;");
                detail_td.innerHTML = res[i][header_array[j]];
                detail_tr.appendChild(detail_td);
            }  else if(header_array[j] == "Genotype_Description"){
                var genotype_description_array = res[i][header_array[j]].split(" ");
                for (let k = 0; k < genotype_description_array.length; k++) {

                    // Check functional effect and decide color
                    var td_bg_color = "#FFFFFF";
                    if (String(genotype_description_array[k]).search(/missense.variant/i) !== -1 && String(genotype_description_array[k]).search(/missense.variant/i) !== undefined) {
                        td_bg_color = missense_variant_color_code;
                        let temp_value_arr = String(genotype_description_array[k]).split('|');
                        if (temp_value_arr.length > 3) {
                            genotype_description_array[k] = String(temp_value_arr[0] + "|" + temp_value_arr[2] + "|" + temp_value_arr[3]);
                        } else if (temp_value_arr.length > 2) {
                            genotype_description_array[k] = String(temp_value_arr[0] + "|" + temp_value_arr[2]);
                        }
                    } else if (String(genotype_description_array[k]).search(/frameshift/i) !== -1 && String(genotype_description_array[k]).search(/frameshift/i) !== undefined) {
                        td_bg_color = frameshift_variant_color_code;
                    } else if (String(genotype_description_array[k]).search(/exon.loss/i) !== -1 && String(genotype_description_array[k]).search(/exon.loss/i) !== undefined) {
                        td_bg_color = exon_loss_variant_color_code;
                    } else if (String(genotype_description_array[k]).search(/lost/i) !== -1 && String(genotype_description_array[k]).search(/lost/i) !== undefined) {
                        td_bg_color = lost_color_code;
                        let temp_value_arr = String(genotype_description_array[k]).split('|');
                        if (temp_value_arr.length > 3) {
                            genotype_description_array[k] = String(temp_value_arr[0] + "|" + temp_value_arr[2] + "|" + temp_value_arr[3]);
                        } else if (temp_value_arr.length > 2) {
                            genotype_description_array[k] = String(temp_value_arr[0] + "|" + temp_value_arr[2]);
                        }
                    } else if (String(genotype_description_array[k]).search(/gain/i) !== -1 && String(genotype_description_array[k]).search(/gain/i) !== undefined) {
                        td_bg_color = gain_color_code;
                        let temp_value_arr = String(genotype_description_array[k]).split('|');
                        if (temp_value_arr.length > 3) {
                            genotype_description_array[k] = String(temp_value_arr[0] + "|" + temp_value_arr[2] + "|" + temp_value_arr[3]);
                        } else if (temp_value_arr.length > 2) {
                            genotype_description_array[k] = String(temp_value_arr[0] + "|" + temp_value_arr[2]);
                        }
                    } else if (String(genotype_description_array[k]).search(/disruptive/i) !== -1 && String(genotype_description_array[k]).search(/disruptive/i) !== undefined) {
                        td_bg_color = disruptive_color_code;
                    } else if (String(genotype_description_array[k]).search(/conservative/i) !== -1 && String(genotype_description_array[k]).search(/conservative/i) !== undefined) {
                        td_bg_color = conservative_color_code;
                    } else if (String(genotype_description_array[k]).search(/splice/i) !== -1 && String(genotype_description_array[k]).search(/splice/i) !== undefined) {
                        td_bg_color = splice_color_code;
                    } else if (String(genotype_description_array[k]).search(/ref/i) !== -1 && String(genotype_description_array[k]).search(/ref/i) !== undefined) {
                        td_bg_color = ref_color_code;
                    }

                    var detail_td = document.createElement("td");
                    detail_td.setAttribute("style", "border:1px solid black; min-width:80px; height:18.5px;");
                    detail_td.innerHTML = genotype_description_array[k];
                    detail_td.style.backgroundColor = td_bg_color;
                    detail_tr.appendChild(detail_td);
                }
            } else if(header_array[j] == "Imputation"){
                var detail_td = document.createElement("td");
                detail_td.setAttribute("style", "border:1px solid black; min-width:80px; height:18.5px;");
                if (/[+]/.test(res[i][header_array[j]])){
                    detail_td.innerHTML = "+";
                } else {
                    detail_td.innerHTML = "-";
                }
                detail_tr.appendChild(detail_td);
            }
        }
        detail_table.appendChild(detail_tr);
    }

    return detail_table;
}


function queryMetadataByImprovementStatusAndGenotypeCombination(organism, dataset, key, gene, chromosome, position, genotype, genotypeDescription){

    $.ajax({
        url: 'queryMetadataByImprovementStatusAndGenotypeCombination/'+organism,
        type: 'GET',
        contentType: 'application/json',
        data: {
            Organism: organism,
            Dataset: dataset,
            Key: key,
            Gene: gene,
            Chromosome: chromosome,
            Position: position,
            Genotype: genotype,
            Genotype_Description: genotypeDescription
        },
        success: function (response) {
            res = JSON.parse(response);

            // Open modal
            document.getElementById("info-modal").style.display = "block";

            var metadata_table = constructMetadataTable(res, organism, dataset, key, gene, chromosome, position, genotype, genotypeDescription);

            document.getElementById('modal-content-div').appendChild(metadata_table);

            var paragraph = document.createElement("p");
            paragraph.innerHTML = "Total number of records: " + res.length;

            document.getElementById("modal-content-comment").appendChild(paragraph);
        },
        error: function (xhr, status, error) {
            console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
        }
    });

}


function queryAllCountsByGene(organism, dataset, gene, improvement_status_array_string){
    let improvement_status_array = improvement_status_array_string.split(";");

    if (dataset && gene) {
        $.ajax({
            url: 'queryAllCountsByGene/'+organism,
            type: 'GET',
            contentType: 'application/json',
            data: {
                Organism: organism,
                Dataset: dataset,
                Gene: gene,
                Improvement_Status_Array: improvement_status_array
            },
            success: function (response) {
                res = JSON.parse(response);

                if (res.length > 0) {
                    let csvString = convertJsonToPositionSeparatedCsv(res);
                    createAndDownloadCsvFile(csvString, String(organism) + "_" + String(dataset) + "_" + gene + "_Counts");

                } else {
                    alert("Downloading counts by " + gene + " gene of " + dataset + " is not available!!!");
                }

            },
            error: function (xhr, status, error) {
                console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
                alert("Downloading counts by " + gene + " gene of " + dataset + " is not available!!!");
            }
        });
    } else {
        alert("Downloading counts by " + gene + " gene of " + dataset + " is not available!!!");
    }
}


function queryAllByGene(organism, dataset, gene, improvement_status_array_string){
    let improvement_status_array = improvement_status_array_string.split(";");

    if (dataset && gene) {
        $.ajax({
            url: 'queryAllByGene/'+organism,
            type: 'GET',
            contentType: 'application/json',
            data: {
                Organism: organism,
                Dataset: dataset,
                Gene: gene,
                Improvement_Status_Array: improvement_status_array
            },
            success: function (response) {
                res = JSON.parse(response);

                if (res.length > 0) {
                    let csvString = convertJsonToCsv(res);
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
}


function queryAccessionInformation(organism, dataset){

    if (dataset) {
        $.ajax({
            url: 'queryAccessionInformation/'+organism,
            type: 'GET',
            contentType: 'application/json',
            data: {
                Oataset: organism,
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


function queryAllCountsByMultipleGenes(organism, dataset, gene_array_string, improvement_status_array_string){
    let gene_array = gene_array_string.split(";");
    let improvement_status_array = improvement_status_array_string.split(";");

    if (dataset && gene_array_string) {
        if(gene_array.length > 0) {
            $.ajax({
                url: 'queryAllCountsByMultipleGenes/'+organism,
                type: 'GET',
                contentType: 'application/json',
                data: {
                    Organism: organism,
                    Dataset: dataset,
                    Gene_Array: gene_array,
                    Improvement_Status_Array: improvement_status_array
                },
                success: function (response) {
                    res = JSON.parse(response);

                    if (res.length > 0) {
                        let csvString = convertJsonToCsv(res);
                        createAndDownloadCsvFile(csvString, String(organism) + "_" + String(dataset) + "_" + gene_array.join("_") + "_All_Counts");

                    } else {
                        alert("Downloading all counts by multiple genes of " + dataset + " is not available!!!");
                    }

                },
                error: function (xhr, status, error) {
                    console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
                    alert("Downloading all counts by multiple genes of " + dataset + " is not available!!!");
                }
            });
        } else {
            alert("Downloading all counts by multiple genes of " + dataset + " is not available!!!");
        }
    } else {
        alert("Downloading all counts by multiple genes of " + dataset + " is not available!!!");
    }

}


function queryAllByMultipleGenes(organism, dataset, gene_array_string, improvement_status_array_string){
    let gene_array = gene_array_string.split(";");
    let improvement_status_array = improvement_status_array_string.split(";");

    if (dataset && gene_array_string) {
        if(gene_array.length > 0) {
            $.ajax({
                url: 'queryAllByMultipleGenes/'+organism,
                type: 'GET',
                contentType: 'application/json',
                data: {
                    Organism: organism,
                    Dataset: dataset,
                    Gene_Array: gene_array,
                    Improvement_Status_Array: improvement_status_array
                },
                success: function (response) {
                    res = JSON.parse(response);

                    if (res.length > 0) {
                        let csvString = convertJsonToCsv(res);
                        createAndDownloadCsvFile(csvString, String(organism) + "_" + String(dataset) + "_" + gene_array.join("_") + "_All_Data");

                    } else {
                        alert("Downloading all data by multiple genes of " + dataset + " is not available!!!");
                    }

                },
                error: function (xhr, status, error) {
                    console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
                    alert("Downloading all data by multiple genes of " + dataset + " is not available!!!");
                }
            });
        } else {
            alert("Downloading all data by multiple genes of " + dataset + " is not available!!!");
        }
    } else {
        alert("Downloading all data by multiple genes of " + dataset + " is not available!!!");
    }
}