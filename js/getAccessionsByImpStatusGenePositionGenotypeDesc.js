function constructInfoTable(arr) {
    let ref_color_code = "#D1D1D1";
    let missense_variant_color_code = "#7FC8F5";
    let frameshift_variant_color_code = "#F26A55";
    let exon_loss_variant_color_code = "#F26A55";
    let lost_color_code = "#F26A55";
    let gain_color_code = "#F26A55";
    let disruptive_color_code = "#F26A55";
    let splice_color_code = "#9EE85C";

    // Put data into modal
    document.getElementById('modal-content-div').innerHTML = "<table id='modal-content-table'><tr id='modal-content-table-head'></tr></table>";

    // Put header into the modal
    let th_keys = Object.keys(arr[0]);
    for (let i = 0; i < th_keys.length; i++) {
        if (th_keys[i] !== "Position" && th_keys[i] !== "Genotype" && th_keys[i] !== "Genotype_with_Description" && th_keys[i] !== "Imputation") {
            document.getElementById('modal-content-table-head').innerHTML += "<th style=\"border: 1px solid black;min-width:120px;text-align:center\">" + th_keys[i] + "</th>";
        }
    }
    position_arr = String(arr[0]['Position']).split(" ");
    for (let i = 0; i < position_arr.length; i++) {
        document.getElementById('modal-content-table-head').innerHTML += "<th style=\"border: 1px solid black;min-width:120px;text-align:center\">" + position_arr[i] + "</th>";
    }
    for (let i = 0; i < th_keys.length; i++) {
        if (th_keys[i] === "Imputation") {
            document.getElementById('modal-content-table-head').innerHTML += "<th style=\"border: 1px solid black;min-width:120px;text-align:center\">" + th_keys[i] + "</th>";
        }
    }

    // Put body into the modal
    for (let i = 0; i < arr.length; i++) {
        let modal_content_table_data_id = "modal-content-table-data-" + i;
        let tr_bgcolor = (i % 2 == 0) ? "#DDFFDD" : "#FFFFFF";

        document.getElementById('modal-content-table').innerHTML += "<tr id='" + modal_content_table_data_id + "' bgcolor='" + tr_bgcolor + "'></tr>"

        let tr_keys = Object.keys(arr[i]);

        for (let j = 0; j < tr_keys.length; j++) {
            if (tr_keys[j] !== "Position" && tr_keys[j] !== "Genotype" && tr_keys[j] !== "Genotype_with_Description" && tr_keys[j] !== "Imputation") {
                document.getElementById(modal_content_table_data_id).innerHTML += "<td style=\"border: 1px solid black;min-width:120px;text-align:left\">" + ((arr[i][tr_keys[j]] === null) ? "" : arr[i][tr_keys[j]]) + "</td>";
            }
        }
        for (let j = 0; j < tr_keys.length; j++) {
            if (tr_keys[j] === "Genotype_with_Description") {
                let genotypeWithDescriptionArray = String(arr[i][tr_keys[j]]).split(" ");
                for (let k = 0; k < genotypeWithDescriptionArray.length; k++) {
                    if (String(genotypeWithDescriptionArray[k]).search(/missense.variant/i) !== -1 && String(genotypeWithDescriptionArray[k]).search(/missense.variant/i) !== undefined) {
                        let temp_value_arr = String(genotypeWithDescriptionArray[k]).split('|');
                        let temp_value = (temp_value_arr.length > 2) ? temp_value_arr[0] + "|" + temp_value_arr[2] : genotypeWithDescriptionArray[k];
                        document.getElementById(modal_content_table_data_id).innerHTML += "<td style=\"border: 1px solid black;min-width:120px;text-align:center;background-color:" + missense_variant_color_code + "\">" + temp_value + "</td>";
                    } else if (String(genotypeWithDescriptionArray[k]).search(/frameshift/i) !== -1 && String(genotypeWithDescriptionArray[k]).search(/frameshift/i) !== undefined) {
                        document.getElementById(modal_content_table_data_id).innerHTML += "<td style=\"border: 1px solid black;min-width:120px;text-align:center;background-color:" + frameshift_variant_color_code + "\">" + genotypeWithDescriptionArray[k] + "</td>";
                    } else if (String(genotypeWithDescriptionArray[k]).search(/exon.loss/i) !== -1 && String(genotypeWithDescriptionArray[k]).search(/exon.loss/i) !== undefined) {
                        document.getElementById(modal_content_table_data_id).innerHTML += "<td style=\"border: 1px solid black;min-width:120px;text-align:center;background-color:" + exon_loss_variant_color_code + "\">" + genotypeWithDescriptionArray[k] + "</td>";
                    } else if (String(genotypeWithDescriptionArray[k]).search(/lost/i) !== -1 && String(genotypeWithDescriptionArray[k]).search(/lost/i) !== undefined) {
                        let temp_value_arr = String(genotypeWithDescriptionArray[k]).split('|');
                        let temp_value = (temp_value_arr.length > 2) ? temp_value_arr[0] + "|" + temp_value_arr[2] : genotypeWithDescriptionArray[k];
                        document.getElementById(modal_content_table_data_id).innerHTML += "<td style=\"border: 1px solid black;min-width:120px;text-align:center;background-color:" + lost_color_code + "\">" + temp_value + "</td>";
                    } else if (String(genotypeWithDescriptionArray[k]).search(/gain/i) !== -1 && String(genotypeWithDescriptionArray[k]).search(/gain/i) !== undefined) {
                        let temp_value_arr = String(genotypeWithDescriptionArray[k]).split('|');
                        let temp_value = (temp_value_arr.length > 2) ? temp_value_arr[0] + "|" + temp_value_arr[2] : genotypeWithDescriptionArray[k];
                        document.getElementById(modal_content_table_data_id).innerHTML += "<td style=\"border: 1px solid black;min-width:120px;text-align:center;background-color:" + gain_color_code + "\">" + temp_value + "</td>";
                    } else if (String(genotypeWithDescriptionArray[k]).search(/disruptive/i) !== -1 && String(genotypeWithDescriptionArray[k]).search(/disruptive/i) !== undefined) {
                        document.getElementById(modal_content_table_data_id).innerHTML += "<td style=\"border: 1px solid black;min-width:120px;text-align:center;background-color:" + disruptive_color_code + "\">" + genotypeWithDescriptionArray[k] + "</td>";
                    } else if (String(genotypeWithDescriptionArray[k]).search(/splice/i) !== -1 && String(genotypeWithDescriptionArray[k]).search(/splice/i) !== undefined) {
                        document.getElementById(modal_content_table_data_id).innerHTML += "<td style=\"border: 1px solid black;min-width:120px;text-align:center;background-color:" + splice_color_code + "\">" + genotypeWithDescriptionArray[k] + "</td>";
                    } else if (String(genotypeWithDescriptionArray[k]).search(/ref/i) !== -1 && String(genotypeWithDescriptionArray[k]).search(/ref/i) !== undefined) {
                        document.getElementById(modal_content_table_data_id).innerHTML += "<td style=\"border: 1px solid black;min-width:120px;text-align:center;background-color:" + ref_color_code + "\">" + genotypeWithDescriptionArray[k] + "</td>";
                    } else {
                        document.getElementById(modal_content_table_data_id).innerHTML += "<td style=\"border: 1px solid black;min-width:120px;text-align:center;background-color:#FFFFFF\">" + genotypeWithDescriptionArray[k] + "</td>";
                    }
                }
            }
        }
        for (let j = 0; j < tr_keys.length; j++) {
            if (tr_keys[j] === "Imputation") {
                document.getElementById(modal_content_table_data_id).innerHTML += "<td style=\"border: 1px solid black;min-width:120px;text-align:center\">" + ((arr[i][tr_keys[j]] === null) ? "" : arr[i][tr_keys[j]]) + "</td>";
            }
        }
    }

    document.getElementById("modal-content-comment").innerHTML = "<p>Total number of samples: " + arr.length + "</p>";
}


function getAccessionsByImpStatusGenePositionGenotypeDesc(organism, dataset, key, gene, position, genotypeWithDescription) {

    $.ajax({
        url: 'getAccessionsByImpStatusGenePositionGenotypeDesc/'+organism,
        type: 'GET',
        contentType: 'application/json',
        data: {
            Dataset: dataset,
            Key: key,
            Gene: gene,
            Position: position,
            GenotypeWithDescription: genotypeWithDescription,
            Organism: organism,
        },
        success: function (response) {
            res = JSON.parse(response);
            // res = processAccessionCounts(res);

            // Open modal
            document.getElementById("info-modal").style.display = "block";

            constructInfoTable(res);

        },
        error: function (xhr, status, error) {
            console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
        }
    });

}