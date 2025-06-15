@php
    include resource_path() . '/views/system/config.blade.php';

    $organism = $info['organism'];
    $chromosome = $info['chromosome'];
    $position = $info['position'];
    $gene = $info['gene'];
    $genotype_array = $info['genotype_array'];
    $phenotype = $info['phenotype'];
    $dataset = $info['dataset'];

@endphp


<head>
    <title>{{ $config_organism }}-KB</title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.plot.ly/plotly-3.0.0.min.js" charset="utf-8"></script>
</head>

<body>

    <h3>Queried CNV and Phenotype:</h3>
    <div style='width:auto; height:auto; overflow:visible; max-height:1000px;'>
        <table style='text-align:center; border:3px solid #000;'>
            <tr>
                <th style="border:1px solid black; min-width:80px;">Chromsome</th>
                <th style="border:1px solid black; min-width:80px;">Position</th>
                <th style="border:1px solid black; min-width:80px;">Genotype</th>
                <th style="border:1px solid black; min-width:80px;">Phenotype</th>
            </tr>
            <tr bgcolor="#DDFFDD">
                <td style="border:1px solid black; min-width:80px;">{{ $chromosome }}</td>
                <td style="border:1px solid black; min-width:80px;">{{ $position }}</td>
                <td style="border:1px solid black; min-width:80px;">{{ implode(',', $genotype_array) }}</td>
                <td style="border:1px solid black; min-width:80px;">{{ $phenotype }}</td>
            </tr>
        </table>
    </div>
    <br /><br />

    <h3>Figures:</h3>
    <div id="genotype_section_div">
        <div id="genotype_figure_div">Loading genotype plot...</div>
        <div id="genotype_summary_table_div">Loading genotype summary table...</div>
    </div>
    <hr />
    <div id="improvement_status_summary_figure_div"></div>

</body>

<script src="{{ asset('system/home/AlleleCatalogTool/js/viewVariantAndPhenotypeFigures.js') }}" type="text/javascript">
</script>

<script type="text/javascript" language="javascript">
    var organism = <?php if (isset($organism)) {
        echo json_encode($organism, JSON_INVALID_UTF8_IGNORE);
    } else {
        echo '';
    } ?>;
    var chromosome = <?php if (isset($chromosome)) {
        echo json_encode($chromosome, JSON_INVALID_UTF8_IGNORE);
    } else {
        echo '';
    } ?>;
    var position = <?php if (isset($position)) {
        echo json_encode($position, JSON_INVALID_UTF8_IGNORE);
    } else {
        echo '';
    } ?>;
    var gene = <?php if (isset($gene)) {
        echo json_encode($gene, JSON_INVALID_UTF8_IGNORE);
    } else {
        echo '';
    } ?>;
    var phenotype = <?php if (isset($phenotype)) {
        echo json_encode($phenotype, JSON_INVALID_UTF8_IGNORE);
    } else {
        echo '';
    } ?>;
    var genotype_array = <?php if (isset($genotype_array) && is_array($genotype_array) && !empty($genotype_array)) {
        echo json_encode($genotype_array, JSON_INVALID_UTF8_IGNORE);
    } else {
        echo '';
    } ?>;
    var dataset = <?php if (isset($dataset)) {
        echo json_encode($dataset, JSON_INVALID_UTF8_IGNORE);
    } else {
        echo '';
    } ?>;

    if (organism == "Osativa") {
        document.getElementById('improvement_status_summary_figure_div').innerHTML =
            "Loading subpopulation summary plot...";
        summaryPhenotype = "Subpopulation";
    } else if (organism == "Athaliana") {
        document.getElementById('improvement_status_summary_figure_div').innerHTML =
            "Loading admixture group summary plot...";
        summaryPhenotype = "Admixture_Group";
    } else if (organism == "Zmays") {
        document.getElementById('improvement_status_summary_figure_div').innerHTML =
            "Loading improvement status summary plot...";
        summaryPhenotype = "Improvement_Status";
    } else {
        document.getElementById('improvement_status_summary_figure_div').innerHTML =
            "Loading accession with phenotype summary plot...";
        summaryPhenotype = "";
    }

    if (organism && chromosome && position && gene && phenotype && genotype_array.length > 0) {
        $.ajax({
            url: 'queryVariantAndPhenotypeFigures/' + organism,
            type: 'GET',
            contentType: 'application/json',
            data: {
                Organism: organism,
                Chromosome: chromosome,
                Position: position,
                Gene: gene,
                Genotype: genotype_array,
                Phenotype: phenotype,
                Dataset: dataset
            },
            success: function(response) {
                res = JSON.parse(response);

                if (res && phenotype) {

                    document.getElementById("genotype_figure_div").style.minHeight = "800px";
                    document.getElementById("improvement_status_summary_figure_div").style.minHeight =
                        "800px";

                    // Summarize data
                    var result_dict = summarizeQueriedData(
                        JSON.parse(JSON.stringify(res)),
                        phenotype,
                        'Genotype'
                    );

                    var result_arr = result_dict['Data'];
                    var summary_array = result_dict['Summary'];

                    var genotypeData = collectDataForFigure(result_arr, phenotype, 'Genotype');
                    var genotypeAndImprovementStatusData = collectDataForFigure(result_arr,
                        summaryPhenotype, 'Genotype');

                    plotFigure(genotypeData, 'Genotype', 'Genotype', 'genotype_figure_div');
                    if (summaryPhenotype === "") {
                        plotFigure(genotypeAndImprovementStatusData, 'Genotype',
                            'Accession with Phenotype Summary', 'improvement_status_summary_figure_div');
                    } else {
                        plotFigure(genotypeAndImprovementStatusData, 'Genotype', summaryPhenotype +
                            '_Summary', 'improvement_status_summary_figure_div');
                    }

                    // Render summarized data
                    document.getElementById('genotype_summary_table_div').innerText = "";
                    document.getElementById('genotype_summary_table_div').innerHTML = "";
                    document.getElementById('genotype_summary_table_div').appendChild(
                        constructInfoTable(summary_array)
                    );
                    document.getElementById('genotype_summary_table_div').style.overflow = 'scroll';
                }
            },
            error: function(xhr, status, error) {
                console.log('Error with code ' + xhr.status + ': ' + xhr.statusText);
                document.getElementById('genotype_figure_div').innerText = "";
                document.getElementById('genotype_summary_table_div').innerHTML = "";
                document.getElementById('improvement_status_summary_figure_div').innerHTML = "";
                var p_tag = document.createElement('p');
                p_tag.innerHTML = "Genotype distribution figure is not available due to lack of data!!!";
                document.getElementById('genotype_figure_div').appendChild(p_tag);
                var p_tag = document.createElement('p');
                p_tag.innerHTML = "Genotype summary table is not available due to lack of data!!!";
                document.getElementById('genotype_summary_table_div').appendChild(p_tag);
                var p_tag = document.createElement('p');
                p_tag.innerHTML = summaryPhenotype +
                    " summary figure is not available due to lack of data!!!";
                document.getElementById('improvement_status_summary_figure_div').appendChild(p_tag);
            }
        });
    } else {
        document.getElementById('genotype_figure_div').innerText = "";
        document.getElementById('genotype_summary_table_div').innerHTML = "";
        document.getElementById('improvement_status_summary_figure_div').innerHTML = "";
        var p_tag = document.createElement('p');
        p_tag.innerHTML = "Genotype distribution figure is not available due to lack of data!!!";
        document.getElementById('genotype_figure_div').appendChild(p_tag);
        var p_tag = document.createElement('p');
        p_tag.innerHTML = "Genotype summary table is not available due to lack of data!!!";
        document.getElementById('genotype_summary_table_div').appendChild(p_tag);
        var p_tag = document.createElement('p');
        p_tag.innerHTML = summaryPhenotype + " summary figure is not available due to lack of data!!!";
        document.getElementById('improvement_status_summary_figure_div').appendChild(p_tag);
    }
</script>
