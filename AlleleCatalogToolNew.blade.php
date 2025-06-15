@php
include resource_path() . '/views/system/config.blade.php';

$organism = $info['organism'];
$dataset_array = $info['dataset_array'];
$gene_array = $info['gene_array'];
$accession_array = $info['accession_array'];
$key_column = $info['key_column'];
$improvement_status_array = $info['improvement_status_array'];
$accession_mapping_table = $info['accession_mapping_table'];

@endphp


@extends('system.header_new')


@section('content')


<div class="page-content">
    <section>
        <div class="pbmit-heading">
            <h2 class="pbmit-title">Allele Catalog Tool<br>
            </h2>
        </div><br><br><br><br>
        <!-- <div class="appoinment-four-bg"> -->
        <div>
            <div class="container">
                <div class="row">
                    <div class="col-md-6 p-0">
                    <div class="appoinment-four-box">
                            <h3>Search by Gene IDs</h3>
                            <form class="form-style-2" action="{{ route('system.tools.AlleleCatalogTool.viewAllByGenes', ['organism'=>$organism]) }}" method="get" target="_blank" >
                                <div class="row">
                                    <div class="col-md-12">
                                    <label for="dataset_1"><b>Dataset:</b></label>
                                        <select name="dataset_1" class="form-control" id="dataset_1"  onchange="updateSearchByGeneIDs('{{$organism}}', event)">
                                            @foreach($dataset_array as $dataset)
                                            <option value="{{ $dataset }}">{{ str_replace('_', ' ', $dataset) . " Allele Catalog" }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                <div class="col-md-12"> 
                                    <label for="gene1"><b>Gene IDs:</b></label><br />
                                    <span id="gene_examples_1" style="font-size:10pt">
                                        &nbsp;(eg
                                        @foreach($gene_array as $gene)
                                        {{ $gene->Gene }}
                                        @endforeach
                                        )
                                    </span><br />
                                    
                                    <textarea id="gene_1" name="gene_1" rows="8" cols="40" class="form-control"></textarea>
                                    </div>
                                    <div class="col-md-12">
                                    @if ($key_column != "" && !empty($improvement_status_array))
                                        <div id="improvement_status_div_1">
                                        <label><b>{{ str_replace('_', ' ', $key_column) }}:</b></label>
                                        <br />
                                        @foreach($improvement_status_array as $key => $improvement_status)
                                        <input class="form-check-input" type="checkbox" id="{{ $improvement_status->Key }}" name="improvement_status_1[]" value="{{ $improvement_status->Key }}" checked>
                                        <label for="{{ $improvement_status->Key }}" style="font-weight: normal;">{{ str_replace('_', ' ', $improvement_status->Key) }}</label>
                                        @if ($key != 0 && $key % 4 === 0)
                                            <br />
                                        @endif
                                        @endforeach
                                        </div>
                                        @endif
                                    </div>
                                    <div class="col-md-12">
                                        <br>
                                        <input type="submit" value="Search" class="pbmit-btn">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-md-6 ">
                        <div class="appoinment-four-box">
                            <h3>Search by Accessions and Gene ID</h3>
                            <form class="form-style-2" action="{{ route('system.tools.AlleleCatalogTool.viewAllByAccessionsAndGene', ['organism'=>$organism]) }}"  method="get" target="_blank" >
                                <div class="row"> 
                                    <div class="col-md-12" >
                                    <label for="dataset_2"><b>Dataset:</b></label>
                                        <select name="dataset_2" id="dataset_2" class="form-control" onchange="updateSearchByAccessionsandGeneID('{{$organism}}', event)">
                                            @foreach($dataset_array as $dataset)
                                            <option value="{{ $dataset }}">{{ str_replace('_', ' ', $dataset) . " Allele Catalog" }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                <div class="col-md-12">
                                    <label for="bindingTF1"><b>Accessions</b></label><br />
                                        <span style="font-size:10pt">
                                            &nbsp;(eg
                                            @foreach($accession_array as $accession)
                                            {{ $accession->Accession }}
                                            @endforeach
                                            )
                                        </span><br />
                                        <textarea id="accession_2" name="accession_2" rows="8" cols="40" class="form-control" cols="40"></textarea>
                                    </div>
                                    <div class="col-md-12">
                                    <label for="chromosome1"><b>Gene ID:</b></label>
                                    <span id="gene_example_2" style="font-size:10pt">&nbsp;(One gene ID only; eg {{ $gene_array[0]->Gene }})</span>
                                    <input type="text" id="gene_2" name="gene_2" size="40" class="form-control"></input>
                                    </div> 
                                    <div class="col-md-12">
                                        <input type="submit" value="SEARCH" class="pbmit-btn">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <br><br>
        <input type="submit" onclick="queryAccessionInformation('{{ $organism }}', '{{ $accession_mapping_table }}')" value="Download Accession Information" class="pbmit-btn">
        <input type="submit"  onclick="viewDemo()" value="View Demo" class="pbmit-btn">
        <br><br>
    <section>
        <div class="container g-0">
            <div class="counter-section-six pbmit-bgcolor-skincolor">	
                <div class="row">
                <div class="col-md-12">
                        <div class="pbmit-fidbox-style-3">
                            <div class="pbmit-fld-contents">
                                <h4 class="pbmit-fid-title"><span>If you use the Allele Catalog Tool in your work, please cite:<br></span></h4>
                                <h5 class="pbmit-fid-title"><span><p> Chan YO, Dietz N, Zeng S, Wang J, Flint-Garcia S, Salazar-Vidal MN, Škrabišová M, Bilyeu K, Joshi T: <b> The Allele Catalog Tool: a web-based interactive tool for allele discovery and analysis. </b> BMC Genomics 2023, 24(1):107. </p></span></h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection


@section('javascript')

<script src="{{ asset('system/home/AlleleCatalogTool/js/AlleleCatalogTool.js') }}" type="text/javascript"></script>

<script type="text/javascript">
    // View demo
    function viewDemo() {
		let downloadAnchorNode = document.createElement('a');
		downloadAnchorNode.setAttribute("href", "https://drive.google.com/file/d/1hpTYAwuRWh5MF9TpgBi721lyyWcDJ_5p/view");
		downloadAnchorNode.setAttribute("target", "_blank");
		document.body.appendChild(downloadAnchorNode); // required for firefox
		downloadAnchorNode.click();
		downloadAnchorNode.remove();
	}


    // Populate gene1 textarea placeholder
    let gene_array = <?php echo json_encode($gene_array); ?>;
    var gene_1_str = "\nPlease separate each gene into a new line.\n\nExample:\n";
    for (let i = 0; i < gene_array.length; i++) {
        gene_1_str += gene_array[i]['Gene'] + "\n";
    }
    document.getElementById('gene_1').placeholder = gene_1_str;


    // Populate accession textarea placeholder
    let accession_array = <?php echo json_encode($accession_array); ?>;
    var accession_2_str = "\nPlease separate each accession into a new line.\n\nExample:\n";
    for (let i = 0; i < accession_array.length; i++) {
        accession_2_str += accession_array[i]['Accession'] + "\n";
    }
    document.getElementById('accession_2').placeholder = accession_2_str;
</script>

@endsection