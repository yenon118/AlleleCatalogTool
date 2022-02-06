function checkbox_highlights(event) {

    let gene_n_loc = event.id;

    let gene_n_loc_arr = gene_n_loc.replace('no__', '').split('__');

    let gene = gene_n_loc_arr[0];
    let n = gene_n_loc_arr[1];
    let loc = gene_n_loc_arr[2];

    if (document.getElementById(event.id).checked) {

        if (loc === 'front') {
            document.getElementById('no__'+gene+'__'+n+'__back').checked = true;
        } else if (loc === 'back') {
            document.getElementById('no__'+gene+'__'+n+'__front').checked = true;
        }

        var matches = $("td[id^='pos__".concat(gene, "__']"));

        for (let i = 0; i < matches.length; i++) {
            matches_id_str_arr = matches[i].id.split('__');
            if (parseInt(matches_id_str_arr[matches_id_str_arr.length - 1]) == parseInt(n)) {
                matches[i].style.fontSize = "20px";
                // console.log(matches[i].style.fontSize);
            }
        }
    } else {

        if (loc === 'front') {
            document.getElementById('no__'+gene+'__'+n+'__back').checked = false;
        } else if (loc === 'back') {
            document.getElementById('no__'+gene+'__'+n+'__front').checked = false;
        }

        var matches = $("td[id^='pos__".concat(gene, "__']"));

        for (let i = 0; i < matches.length; i++) {
            matches_id_str_arr = matches[i].id.split('__');
            if (parseInt(matches_id_str_arr[matches_id_str_arr.length - 1]) == parseInt(n)) {
                matches[i].style.fontSize = "";
                // console.log(matches[i].style.fontSize);
            }
        }
    }

}