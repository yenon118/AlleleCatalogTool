@php
include resource_path() . '/views/system/config.blade.php';

$organism = $info['organism'];
$dataset1 = $info['dataset1'];
$gene1 = $info['gene1'];
$result_arr = $info['result_arr'];
$checkboxes = $info['checkboxes'];

$gene_arr = preg_split("/[;, \n]+/", $gene1);
for ($i = 0; $i < count($gene_arr); $i++) {
    $gene_arr[$i] = trim($gene_arr[$i]);
}
$gene_arr_str = strval(implode(";", $gene_arr));


$ref_color_code = "#D1D1D1";
$missense_variant_color_code = "#7FC8F5";
$frameshift_variant_color_code = "#F26A55";
$exon_loss_variant_color_code = "#F26A55";
$lost_color_code = "#F26A55";
$gain_color_code = "#F26A55";
$disruptive_color_code = "#F26A55";
$splice_color_code = "#9EE85C";

@endphp


@extends('system.header')


@section('content')


<link rel="stylesheet" href="{{ asset('system/home/AlleleCatalogTool/css/modal.css') }}">


<div class="title1">Allele Catalog Tool</div>
<br />

<!-- Back button -->
<a href="{{ route('system.tools.AlleleCatalogTool', ['organism'=>$organism]) }}"><button> &lt; Back </button></a>

<br />
<br />


<!-- Modal -->
<div id="info-modal" class="info-modal">
    <!-- Modal content -->
    <div class="modal-content">
        <span class="modal-close">&times;</span>
        <div id="modal-content-div" style='width:auto;height:auto; border:3px solid #000; overflow:scroll;max-height:1000px;'></div>
        <div id="modal-content-comment"></div>
    </div>
</div>


@php
for ($i = 0; $i < count($result_arr); $i++) {
    $segment_arr = $result_arr[array_keys($result_arr)[$i]];

    echo "<div style=\"width:100%; height:100%; border:3px solid #000; height:auto; max-height:1000px; overflow:scroll;\">";
    echo "<table style=\"text-align:center;\">";

    // Table header
    echo "<tr>";
    echo "<th></th>";
    foreach ($segment_arr[0] as $key => $value) {
        if ($key != "Position" && $key != "Genotype" && $key != "Genotype_with_Description") {
            echo "<th style=\"border: 1px solid black; text-align:center; min-width:120px;\">" . $key . "</th>";
        }
    }
    foreach ($segment_arr[0] as $key => $value) {
        if ($key == "Position") {
            $positionArray = preg_split("/[;, |\n]+/", $value);
            for ($j = 0; $j < count($positionArray); $j++) {
                echo "<th style=\"border: 1px solid black; text-align:center; min-width:120px;\">" . $positionArray[$j] . "</th>";
            }
        }
    }
    echo "<th></th>";
    echo "</tr>";

    // Table body
    for ($j = 0; $j < count($segment_arr); $j++) {
        $tr_bgcolor = ($j % 2 ? "#FFFFFF" : "#DDFFDD");

        echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";
        echo "<td><input type=\"checkbox\" id=\"no__" . $segment_arr[$j]->Gene . "__" . $j . "__front\" onclick=\"checkbox_highlights(this)\"></td>";

        foreach ($segment_arr[$j] as $key => $value) {
            if (strval($key) != "Position" && strval($key) != "Genotype" && strval($key) != "Genotype_with_Description") {
                if (!is_numeric($key) && intval($key) == 0 && is_numeric($value) && intval($value) > 0 && strval($key) != "Gene") {
                    echo "<td style=\"border: 1px solid black; min-width:120px;\"><a href=\"javascript:void(0);\" onclick=\"getAccessionsByImpStatusGenePositionGenotypeDesc('" . strval($organism) . "', '" . strval($dataset1) . "', '" . strval($key) . "', '" . $segment_arr[$j]->Gene . "', '" . $segment_arr[$j]->Position . "', '" . $segment_arr[$j]->Genotype_with_Description . "');\">" . $value . "</a></td>";
                } else if (!is_numeric($key) && intval($key) == 0 && is_numeric($value) && intval($value) == 0 && strval($key) != "Gene") {
                    echo "<td style=\"border: 1px solid black; min-width:120px;\">" . $value . "</td>";
                } else if (!is_numeric($key) && intval($key) == 0 && !is_numeric($value) && intval($value) == 0 && strval($key) == "Gene") {
                    echo "<td style=\"border: 1px solid black; min-width:120px;\">" . $value . "</td>";
                }
            }
        }
        foreach ($segment_arr[$j] as $key => $value) {
            if ($key == "Genotype_with_Description") {
                $genotypeWithDescriptionArray = preg_split("/[ ]+/", $value);
                for ($k = 0; $k < count($genotypeWithDescriptionArray); $k++) {
                    if (preg_match("/missense.variant/i", $genotypeWithDescriptionArray[$k])) {
                        $temp_value_arr = preg_split("/[;, |\n]+/", $genotypeWithDescriptionArray[$k]);
                        $temp_value = (count($temp_value_arr) > 2 ? $temp_value_arr[0] . "|" . $temp_value_arr[2] : $genotypeWithDescriptionArray[$k]);
                        echo "<td id=\"pos__" . $segment_arr[$j]->Gene . "__" . $key . "__" . $j . "\" style=\"border: 1px solid black; min-width:120px; background-color:" . $missense_variant_color_code . "\">" . $temp_value . "</td>";
                    } else if (preg_match("/frameshift/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $segment_arr[$j]->Gene . "__" . $key . "__" . $j . "\" style=\"border: 1px solid black; min-width:120px; background-color:" . $frameshift_variant_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/exon.loss/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $segment_arr[$j]->Gene . "__" . $key . "__" . $j . "\" style=\"border: 1px solid black; min-width:120px; background-color:" . $exon_loss_variant_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/lost/i", $genotypeWithDescriptionArray[$k])) {
                        $temp_value_arr = preg_split("/[;, |\n]+/", $genotypeWithDescriptionArray[$k]);
                        $temp_value = (count($temp_value_arr) > 2 ? $temp_value_arr[0] . "|" . $temp_value_arr[2] : $genotypeWithDescriptionArray[$k]);
                        echo "<td id=\"pos__" . $segment_arr[$j]->Gene . "__" . $key . "__" . $j . "\" style=\"border: 1px solid black; min-width:120px; background-color:" . $lost_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/gain/i", $genotypeWithDescriptionArray[$k])) {
                        $temp_value_arr = preg_split("/[;, |\n]+/", $genotypeWithDescriptionArray[$k]);
                        $temp_value = (count($temp_value_arr) > 2 ? $temp_value_arr[0] . "|" . $temp_value_arr[2] : $genotypeWithDescriptionArray[$k]);
                        echo "<td id=\"pos__" . $segment_arr[$j]->Gene . "__" . $key . "__" . $j . "\" style=\"border: 1px solid black; min-width:120px; background-color:" . $gain_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/disruptive/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $segment_arr[$j]->Gene . "__" . $key . "__" . $j . "\" style=\"border: 1px solid black; min-width:120px; background-color:" . $disruptive_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/splice/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $segment_arr[$j]->Gene . "__" . $key . "__" . $j . "\" style=\"border: 1px solid black; min-width:120px; background-color:" . $splice_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else if (preg_match("/ref/i", $genotypeWithDescriptionArray[$k])) {
                        echo "<td id=\"pos__" . $segment_arr[$j]->Gene . "__" . $key . "__" . $j . "\" style=\"border: 1px solid black; min-width:120px; background-color:" . $ref_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    } else {
                        echo "<td id=\"pos__" . $segment_arr[$j]->Gene . "__" . $key . "__" . $j . "\" style=\"border: 1px solid black; min-width:120px; background-color:#FFFFFF\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                    }
                }
            }
        }

        echo "<td><input type=\"checkbox\" id=\"no__" . $segment_arr[$j]->Gene . "__" . $j . "__back\" onclick=\"checkbox_highlights(this)\"></td>";
        echo "</tr>";
    }

    echo "</table>";
    echo "</div>";

    echo "<div style='margin-top:10px;' align='right'>";
    echo "<button onclick=\"downloadAllCountsByGene('" . $organism . "', '" . $dataset1 . "', '" . $segment_arr[0]->Gene . "', '" . implode(";", $checkboxes) . "')\" style=\"margin-right:20px;\"> Download (Accession Counts)</button>";
    echo "<button onclick=\"downloadAllByGene('" . $organism . "', '" . $dataset1 . "', '" . $segment_arr[0]->Gene . "', '" . implode(";", $checkboxes) . "')\"> Download (All Accessions)</button>";
    echo "</div>";

    echo "<br />";
    echo "<br />";
}

echo "<br/><br/>";
echo "<div style='margin-top:10px;' align='center'>";
echo "<button onclick=\"downloadAllCountsByMultipleGenes('" . $organism . "', '" . $dataset1 . "', '" . implode(";", $gene_arr) . "', '" . implode(";", $checkboxes) . "')\" style=\"margin-right:20px;\"> Download All (Accession Counts)</button>";
echo "<button onclick=\"downloadAllByMultipleGenes('" . $organism . "', '" . $dataset1 . "', '" . implode(";", $gene_arr) . "', '" . implode(";", $checkboxes) . "')\" style=\"margin-right:20px;\"> Download All (All Accessions)</button>";
echo "</div>";
echo "<br/><br/>";

@endphp


@endsection


@section('javascript')

<script type="text/javascript">
</script>

<script src="{{ asset('system/home/AlleleCatalogTool/js/dataProcessor.js') }}" type="text/javascript"></script>
<script src="{{ asset('system/home/AlleleCatalogTool/js/getAccessionsByImpStatusGenePositionGenotypeDesc.js') }}" type="text/javascript"></script>
<script src="{{ asset('system/home/AlleleCatalogTool/js/download.js') }}" type="text/javascript"></script>
<script src="{{ asset('system/home/AlleleCatalogTool/js/modal.js') }}" type="text/javascript"></script>
<script src="{{ asset('system/home/AlleleCatalogTool/js/checkboxHighlight.js') }}" type="text/javascript"></script>

@endsection
