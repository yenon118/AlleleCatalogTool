@php
    include resource_path() . '/views/system/config.blade.php';

    $organism = $info['organism'];
    $chromosome = $info['chromosome'];
    $position = $info['position'];
    $dataset = $info['dataset'];
    $gene = $info['gene'];
    $genotype_selection_arr = $info['genotype_selection_arr'];
    $phenotype_selection_arr = $info['phenotype_selection_arr'];

    $phenotype_categories = [];
    for ($i = 0; $i < count($phenotype_selection_arr); $i++) {
        if (!in_array($phenotype_selection_arr[$i]->Phenotype_Group, $phenotype_categories)) {
            array_push($phenotype_categories, $phenotype_selection_arr[$i]->Phenotype_Group);
        }
    }

@endphp


<head>
    <title>{{ $config_organism }}-KB</title>

    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    </link>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.min.js"
        integrity="sha256-AlTido85uXPlSyyaZNsjJXeCs07eSv3r43kyCVc8ChI=" crossorigin="anonymous"></script>
    <script>
        $(function() {
            $("#accordion").accordion({
                active: false,
                collapsible: true
            });
        });
    </script>
</head>


<body>
    <div id="accordion">
        <h3>Coordinate</h3>
        <div>
            <label for="chromosome_1">Chromosome:</label>
            <input type="text" id="chromosome_1" name="chromosome_1" size="30" value="{{ $chromosome }}"
                style="margin-right:50px;">

            <label for="position_1">Position:</label>
            <input type="text" id="position_1" name="position_1" size="30" value="{{ $position }}"
                style="margin-right:50px;">

            <label for="gene_1">Gene:</label>
            <input type="text" id="gene_1" name="gene_1" size="30" value="{{ $gene }}"
                style="margin-right:50px;">
        </div>
        @php
            if (
                isset($genotype_selection_arr) &&
                is_array($genotype_selection_arr) &&
                !empty($genotype_selection_arr)
            ) {
                echo '<h3>Genotype</h3>';
                echo "<div id=\"div_genotype_in_accordion\">";
                for ($i = 0; $i < count($genotype_selection_arr); $i++) {
                    echo "<input type=\"checkbox\" id=\"genotype_" .
                        $genotype_selection_arr[$i]->Genotype .
                        "\" name=\"genotype_" .
                        $genotype_selection_arr[$i]->Genotype .
                        "\" value=\"" .
                        $genotype_selection_arr[$i]->Genotype .
                        "\"><label for=\"genotype_" .
                        $genotype_selection_arr[$i]->Genotype .
                        "\" style=\"margin-right:10px;\">" .
                        $genotype_selection_arr[$i]->Genotype .
                        '</label>';
                }
                echo '</div>';
            }
            if (
                isset($phenotype_selection_arr) &&
                is_array($phenotype_selection_arr) &&
                !empty($phenotype_selection_arr)
            ) {
                for ($i = 0; $i < count($phenotype_categories); $i++) {
                    echo "<h3>$phenotype_categories[$i]</h3>";
                    echo '<div>';
                    for ($j = 0; $j < count($phenotype_selection_arr); $j++) {
                        if ($phenotype_selection_arr[$j]->Phenotype_Group == $phenotype_categories[$i]) {
                            echo "<input type=\"checkbox\" id=\"" .
                                $phenotype_selection_arr[$j]->ID .
                                "\" name=\"" .
                                $phenotype_selection_arr[$j]->ID .
                                "\" value=\"" .
                                $phenotype_selection_arr[$j]->Phenotype .
                                "\"><label for=\"" .
                                $phenotype_selection_arr[$j]->ID .
                                "\" style=\"margin-right:10px;\">" .
                                $phenotype_selection_arr[$j]->Phenotype .
                                '</label>';
                        }
                    }
                    echo '</div>';
                }
            }
        @endphp
    </div>

    <br />
    <br />

    <div style='margin-top:10px;' align='center'>
        <button onclick="uncheck_all_genotype()" style="margin-right:20px; background-color:#FFFFFF;">Uncheck All
            Genotypes</button>
        <button onclick="check_all_genotype()" style="margin-right:20px; background-color:#FFFFFF;">Check All
            Genotypes</button>
        @php
            if (
                isset($phenotype_selection_arr) &&
                is_array($phenotype_selection_arr) &&
                !empty($phenotype_selection_arr)
            ) {
                echo "<button onclick=\"uncheck_all_phenotypes('" .
                    $organism .
                    "')\" style=\"margin-right:20px; background-color:#FFFFFF;\">Uncheck All Phenotypes</button>";
                echo "<button onclick=\"check_all_phenotypes('" .
                    $organism .
                    "')\" style=\"margin-right:20px; background-color:#FFFFFF;\">Check All Phenotypes</button>";
                echo "<button onclick=\"queryPhenotypeDescription('" .
                    $organism .
                    "', '" .
                    $dataset .
                    "')\" style=\"margin-right:20px; background-color:#FFFFFF;\">Download Phenotype Description</button>";
            }
        @endphp
        <button onclick="queryVariantAndPhenotype('{{ $organism }}', '{{ $dataset }}')"
            style="margin-right:20px; background-color:#99DDFF;">View Data</button>
        <button onclick="downloadVariantAndPhenotype('{{ $organism }}', '{{ $dataset }}')"
            style="margin-right:20px; background-color:#FFFFFF;">Download Data</button>
    </div>
    <br /><br />

    <div id="Variant_and_Phenotye_detail_table" style='width:auto; height:auto; overflow:scroll; max-height:1000px;'>
    </div>

</body>

<script src="{{ asset('system/home/AlleleCatalogTool/js/viewVariantAndPhenotype.js') }}" type="text/javascript">
</script>

<script type="text/javascript"></script>
