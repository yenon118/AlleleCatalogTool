@php
include resource_path() . '/views/system/config.blade.php';

$organism = $info['organism'];
$dataset = $info['dataset'];
$gene = $info['gene'];
$accession = $info['accession_array'];
$result_arr = $info['result_arr'];

if (is_string($accession)) {
    $accession_array = preg_split("/[;, \n]+/", $accession);
    for ($i = 0; $i < count($accession_array); $i++) {
        $accession_array[$i] = trim($accession_array[$i]);
    }
} elseif (is_array($accession)) {
    $accession_array = $accession;
    for ($i = 0; $i < count($accession_array); $i++) {
        $accession_array[$i] = trim($accession_array[$i]);
    }
}

@endphp



<head>
    <title>{{ $config_organism }}-KB</title>

    <link rel="shortcut icon" href="{{ asset('css/images/Header/kbcommons_icon.ico') }}">
    <link rel="stylesheet" href="{{ asset('system/home/AlleleCatalogTool/css/modal.css') }}">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
</head>


<body>
    <!-- Back button -->
    <a href="{{ route('system.tools.AlleleCatalogTool', ['organism'=>$organism]) }}"><button> &lt; Back </button></a>

    <br />
    <br />

    @php

    // Color for functional effects
    $ref_color_code = "#D1D1D1";
    $missense_variant_color_code = "#7FC8F5";
    $frameshift_variant_color_code = "#F26A55";
    $exon_loss_variant_color_code = "#F26A55";
    $lost_color_code = "#F26A55";
    $gain_color_code = "#F26A55";
    $disruptive_color_code = "#F26A55";
    $conservative_color_code = "#FF7F50";
    $splice_color_code = "#9EE85C";

    // Render result to a table
    if(isset($result_arr) && is_array($result_arr) && !empty($result_arr)) {

        // Make table
        echo "<div style='width:100%; height:auto; border:3px solid #000; overflow:scroll; max-height:1000px;'>";
        echo "<table style='text-align:center;'>";

        // Table header
        echo "<tr>";
        foreach ($result_arr[0] as $key => $value) {
            if ($key != "Gene" && $key != "Chromosome" && $key != "Position" && $key != "Genotype" && $key != "Genotype_Description") {
                // Improvement status count section
                echo "<th style=\"border:1px solid black; min-width:80px;\">" . $key . "</th>";
            } elseif ($key == "Gene") {
                echo "<th style=\"border:1px solid black; min-width:80px;\">" . $key . "</th>";
            } elseif ($key == "Chromosome") {
                echo "<th style=\"border:1px solid black; min-width:80px;\">" . $key . "</th>";
            } elseif ($key == "Position") {
                // Position and genotype_description section
                $position_array = preg_split("/[;, \n]+/", $value);
                for ($j = 0; $j < count($position_array); $j++) {
                    if ($organism == "Osativa" && $dataset == "Rice3000") {
                        echo "<th style=\"border:1px solid black; min-width:80px;\"><a href=\"../viewVariantAndPhenotype/" . $organism . "?Dataset=" . $dataset . "&Chromosome=" . $result_arr[0]->Chromosome . "&Position=" . $position_array[$j] . "&Gene=" . $result_arr[0]->Gene . "\" target=\"_blank\">" . $position_array[$j] . "</a></th>";
                    } elseif ($organism == "Athaliana" && $dataset == "Arabidopsis1135") {
                        echo "<th style=\"border:1px solid black; min-width:80px;\"><a href=\"../viewVariantAndPhenotype/" . $organism . "?Dataset=" . $dataset . "&Chromosome=" . $result_arr[0]->Chromosome . "&Position=" . $position_array[$j] . "&Gene=" . $result_arr[0]->Gene . "\" target=\"_blank\">" . $position_array[$j] . "</a></th>";
                    } elseif ($organism == "Ptrichocarpa" && $dataset == "PopulusTrichocarpa882") {
                        echo "<th style=\"border:1px solid black; min-width:80px;\"><a href=\"../viewVariantAndPhenotype/" . $organism . "?Dataset=" . $dataset . "&Chromosome=" . $result_arr[0]->Chromosome . "&Position=" . $position_array[$j] . "&Gene=" . $result_arr[0]->Gene . "\" target=\"_blank\">" . $position_array[$j] . "</a></th>";
                    } else {
                        echo "<th style=\"border:1px solid black; min-width:80px;\">" . $position_array[$j] . "</th>";
                    }
                }
            }
        }
        echo "</tr>";

        // Table body
        for ($j = 0; $j < count($result_arr); $j++) {
            $tr_bgcolor = ($j % 2 ? "#FFFFFF" : "#DDFFDD");

            $row_id_prefix = $result_arr[$j]->Gene . "_" . $result_arr[$j]->Chromosome . "_" . $j;

            echo "<tr bgcolor=\"" . $tr_bgcolor . "\">";

            foreach ($result_arr[$j] as $key => $value) {
                if ($key != "Position" && $key != "Genotype" && $key != "Genotype_Description" && $key != "Imputation") {
                    if (intval($value) > 0) {
                        echo "<td style=\"border:1px solid black;min-width:80px;\">";
                        echo $value;
                        echo "</td>";
                    } else {
                        echo "<td style=\"border:1px solid black;min-width:80px;\">" . $value . "</td>";
                    }
                } elseif ($key == "Genotype_Description") {
                    // Position and genotype_description section
                    $position_array = preg_split("/[;, \n]+/", $result_arr[$j]->Position);
                    $genotype_description_array = preg_split("/[;, \n]+/", $value);
                    for ($k = 0; $k < count($genotype_description_array); $k++) {

                        // Change genotype_description background color
                        $td_bg_color = "#FFFFFF";
                        if (preg_match("/missense.variant/i", $genotype_description_array[$k])) {
                            $td_bg_color = $missense_variant_color_code;
                            $temp_value_arr = preg_split("/[;, |\n]+/", $genotype_description_array[$k]);
                            if (count($temp_value_arr) > 3) {
                                $genotype_description_array[$k] = $temp_value_arr[0] . "|" . $temp_value_arr[2] . "|" . $temp_value_arr[3];
                            } elseif (count($temp_value_arr) > 2) {
                                $genotype_description_array[$k] = $temp_value_arr[0] . "|" . $temp_value_arr[2];
                            }
                        } else if (preg_match("/frameshift/i", $genotype_description_array[$k])) {
                            $td_bg_color = $frameshift_variant_color_code;
                        } else if (preg_match("/exon.loss/i", $genotype_description_array[$k])) {
                            $td_bg_color = $exon_loss_variant_color_code;
                        } else if (preg_match("/lost/i", $genotype_description_array[$k])) {
                            $td_bg_color = $lost_color_code;
                            $temp_value_arr = preg_split("/[;, |\n]+/", $genotype_description_array[$k]);
                            if (count($temp_value_arr) > 3) {
                                $genotype_description_array[$k] = $temp_value_arr[0] . "|" . $temp_value_arr[2] . "|" . $temp_value_arr[3];
                            } elseif (count($temp_value_arr) > 2) {
                                $genotype_description_array[$k] = $temp_value_arr[0] . "|" . $temp_value_arr[2];
                            }
                        } else if (preg_match("/gain/i", $genotype_description_array[$k])) {
                            $td_bg_color = $gain_color_code;
                            $temp_value_arr = preg_split("/[;, |\n]+/", $genotype_description_array[$k]);
                            if (count($temp_value_arr) > 3) {
                                $genotype_description_array[$k] = $temp_value_arr[0] . "|" . $temp_value_arr[2] . "|" . $temp_value_arr[3];
                            } elseif (count($temp_value_arr) > 2) {
                                $genotype_description_array[$k] = $temp_value_arr[0] . "|" . $temp_value_arr[2];
                            }
                        } else if (preg_match("/disruptive/i", $genotype_description_array[$k])) {
                            $td_bg_color = $disruptive_color_code;
                        } else if (preg_match("/conservative/i", $genotype_description_array[$k])) {
                            $td_bg_color = $conservative_color_code;
                        } else if (preg_match("/splice/i", $genotype_description_array[$k])) {
                            $td_bg_color = $splice_color_code;
                        } else if (preg_match("/ref/i", $genotype_description_array[$k])) {
                            $td_bg_color = $ref_color_code;
                        }

                        echo "<td id=\"" . $row_id_prefix . "_" . $position_array[$k] . "\" style=\"border:1px solid black;min-width:80px;background-color:" . $td_bg_color . "\">" . $genotype_description_array[$k] . "</td>";
                    }
                } elseif ($key == "Imputation") {
                    if (preg_match("/\\+/i", $value)) {
                        echo "<td style=\"border:1px solid black;min-width:80px;\">+</td>";
                    } else {
                        echo "<td style=\"border:1px solid black;min-width:80px;\">-</td>";
                    }
                }
            }

            echo "</tr>";
        }

        echo "</table>";
        echo "</div>";

        echo "<div style='margin-top:10px;' align='right'>";
        echo "<button onclick=\"queryAllByAccessionsAndGene('" . $organism . "', '" . $dataset . "', '" . $result_arr[0]->Gene . "', '" . implode(";", $accession_array) . "')\"> Download</button>";
        echo "</div>";

        echo "<br />";
        echo "<br />";

        echo "<br/><br/>";
        echo "<div style='margin-top:10px;' align='center'>";
        echo "<button onclick=\"queryAccessionInformation('" . $organism . "', '" . $dataset . "')\" style=\"margin-right:20px;\">Download Accession Information</button>";
        echo "</div>";
        echo "<br/><br/>";
    } else {
        echo "<p>No Allele Catalog data found in database!!!</p>";
    }

    @endphp

    <div class="footer" style="margin-top:20px;float:right;">© Copyright 2022 KBCommons</div>
</body>


<script src="{{ asset('system/home/AlleleCatalogTool/js/viewAllByAccessionsAndGene.js') }}" type="text/javascript"></script>

<script type="text/javascript">
</script>
