@php
include resource_path() . '/views/system/config.blade.php';

$organism = $info['organism'];
$dataset = $info['dataset2'];
$accession = $info['accession'];
$gene = $info['gene2'];
$result_arr = $info['result_arr'];


$accession_arr = preg_split("/[;,\n]+/", $accession);
for ($i = 0; $i < count($accession_arr); $i++) {
    $accession_arr[$i] = trim($accession_arr[$i]);
}
$accession_arr_str = strval(implode(";", $accession_arr));


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

<div class="title1">Allele Catalog Tool</div>
<br />

<!-- Back button -->
<a href="{{ route('system.tools.AlleleCatalogTool', ['organism'=>$organism]) }}"><button> &lt; Back </button></a>

<br />
<br />

<div style='width:100%; height:100%; border:3px solid #000; overflow:scroll; height:auto; max-height:1000px;'>
    <table style='border: 1px solid black; text-align:center;'>

        <tr>
            @foreach ($result_arr[0] as $key => $value)
                @if (strval($key) != "Position" && strval($key) != "Genotype" && strval($key) != "Genotype_with_Description" && strval($key) != "Imputation")
                    <th style='border: 1px solid black; min-width:180px; text-align:center;'>{{ strval($key) }}</th>
                @endif
            @endforeach

            @foreach ($result_arr[0] as $key => $value)
                @if (strval($key) == "Position")
                    @php
                        $positionArray = preg_split("/[;, |\n]+/", $value);
                        for ($i = 0; $i < count($positionArray); $i++) { 
                            echo "<th style='border: 1px solid black; min-width:180px; text-align:center;'>" . $positionArray[$i] . "</th>" ; 
                        } 
                    @endphp 
                @endif 
            @endforeach 
            
            @foreach ($result_arr[0] as $key=> $value)
                @if (strval($key) == "Imputation")
                    <th style='border: 1px solid black; min-width:180px; text-align:center;'>{{ strval($key) }}</th>
                @endif
            @endforeach
        </tr>

        @php

        // Table body
        for ($i = 0; $i < count($result_arr); $i++) {
            $tr_bgcolor = ($i % 2 ? "#FFFFFF" : "#DDFFDD");

            echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";

            foreach ($result_arr[$i] as $key => $value) {
                if (strval($key) != "Position" && strval($key) != "Genotype" && strval($key) != "Genotype_with_Description" && strval($key) != "Imputation") {
                    echo "<td style=\"border: 1px solid black; min-width:180px;\">" . $value . "</td>";
                }
            }
            foreach ($result_arr[$i] as $key => $value) {
                if (strval($key) == "Genotype_with_Description") {
                    $genotypeWithDescriptionArray = preg_split("/[ ]+/", $value);
                    for ($k = 0; $k < count($genotypeWithDescriptionArray); $k++) {
                        if (preg_match("/missense.variant/i", $genotypeWithDescriptionArray[$k])) {
                            $temp_value_arr = preg_split("/[;, |\n]+/", $genotypeWithDescriptionArray[$k]);
                            $temp_value = (count($temp_value_arr) > 2 ? $temp_value_arr[0] . "|" . $temp_value_arr[2] : $genotypeWithDescriptionArray[$k]);
                            echo "<td id=\"pos__" . $result_arr[$i]->Gene . "__" . $key . "__" . $i . "\" style=\"border: 1px solid black; min-width:180px; background-color:" . $missense_variant_color_code . "\">" . $temp_value . "</td>";
                        } else if (preg_match("/frameshift/i", $genotypeWithDescriptionArray[$k])) {
                            echo "<td id=\"pos__" . $result_arr[$i]->Gene . "__" . $key . "__" . $i . "\" style=\"border: 1px solid black; min-width:180px; background-color:" . $frameshift_variant_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                        } else if (preg_match("/exon.loss/i", $genotypeWithDescriptionArray[$k])) {
                            echo "<td id=\"pos__" . $result_arr[$i]->Gene . "__" . $key . "__" . $i . "\" style=\"border: 1px solid black; min-width:180px; background-color:" . $exon_loss_variant_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                        } else if (preg_match("/lost/i", $genotypeWithDescriptionArray[$k])) {
                            $temp_value_arr = preg_split("/[;, |\n]+/", $genotypeWithDescriptionArray[$k]);
                            $temp_value = (count($temp_value_arr) > 2 ? $temp_value_arr[0] . "|" . $temp_value_arr[2] : $genotypeWithDescriptionArray[$k]);
                            echo "<td id=\"pos__" . $result_arr[$i]->Gene . "__" . $key . "__" . $i . "\" style=\"border: 1px solid black; min-width:180px; background-color:" . $lost_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                        } else if (preg_match("/gain/i", $genotypeWithDescriptionArray[$k])) {
                            $temp_value_arr = preg_split("/[;, |\n]+/", $genotypeWithDescriptionArray[$k]);
                            $temp_value = (count($temp_value_arr) > 2 ? $temp_value_arr[0] . "|" . $temp_value_arr[2] : $genotypeWithDescriptionArray[$k]);
                            echo "<td id=\"pos__" . $result_arr[$i]->Gene . "__" . $key . "__" . $i . "\" style=\"border: 1px solid black; min-width:180px; background-color:" . $gain_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                        } else if (preg_match("/disruptive/i", $genotypeWithDescriptionArray[$k])) {
                            echo "<td id=\"pos__" . $result_arr[$i]->Gene . "__" . $key . "__" . $i . "\" style=\"border: 1px solid black; min-width:180px; background-color:" . $disruptive_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                        } else if (preg_match("/splice/i", $genotypeWithDescriptionArray[$k])) {
                            echo "<td id=\"pos__" . $result_arr[$i]->Gene . "__" . $key . "__" . $i . "\" style=\"border: 1px solid black; min-width:180px; background-color:" . $splice_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                        } else if (preg_match("/ref/i", $genotypeWithDescriptionArray[$k])) {
                            echo "<td id=\"pos__" . $result_arr[$i]->Gene . "__" . $key . "__" . $i . "\" style=\"border: 1px solid black; min-width:180px; background-color:" . $ref_color_code . "\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                        } else {
                            echo "<td id=\"pos__" . $result_arr[$i]->Gene . "__" . $key . "__" . $i . "\" style=\"border: 1px solid black; min-width:180px; background-color:#FFFFFF\">" . $genotypeWithDescriptionArray[$k] . "</td>";
                        }
                    }
                }
            }
            foreach ($result_arr[$i] as $key => $value) {
                if (strval($key) == "Imputation") {
                    echo "<td style=\"border: 1px solid black; min-width:180px; text-align:center;\">" . $value . "</td>";
                }
            }

            echo "</tr>";
        }

        @endphp

    </table>
</div>


<div style='margin-top:10px;' align='right'>
    <button onclick="downloadAllByAccessionsAndGene('{{ $organism }}', '{{ $dataset }}', '{{ $accession_arr_str }}', '{{ $gene }}')"> Download</button>
</div>

@endsection


@section('javascript')

<script type="text/javascript">
</script>

<script src="{{ asset('system/home/AlleleCatalogTool/js/download.js') }}" type="text/javascript"></script>

@endsection
