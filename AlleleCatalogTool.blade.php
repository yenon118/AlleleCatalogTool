@php
include resource_path() . '/views/system/config.blade.php';

$organism = $info['organism'];
$dataset_array = $info['dataset_array'];
$gene_array = $info['gene_array'];
$accession_array = $info['accession_array'];
$checkboxes = $info['checkboxes'];

@endphp


@extends('system.header')


@section('content')

<div class="title1">Allele Catalog Tool</div>
<br />

<table width="100%" cellspacing="14" cellpadding="14">
    <tr>
        <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
            <form action="{{ route('system.tools.AlleleCatalogTool.viewAllByGenes', ['organism'=>$organism]) }}" method="get" target="_blank">
                <h2>Search by Gene IDs</h2>
                <br />
                <label for="dataset1"><b>Dataset:</b></label>
                <select name="dataset1" id="dataset1">
                    @foreach($dataset_array as $dataset)
                    <option value="{{ $dataset }}">{{ str_replace('_', ' ', $dataset) }}</option>
                    @endforeach
                </select>
                <br />
                <br />
                <b>Gene IDs</b><br />
                <span style="font-size:10pt">
                    &nbsp;(eg
                    @foreach($gene_array as $gene)
                    {{ $gene->Gene }}
                    @endforeach
                    )
                </span>
                <br />
                <textarea id="gene1" name="gene1" rows="12" cols="40"></textarea>
                <br /><br />
                @foreach($checkboxes as $key => $checkbox)
                @if ($checkbox === "Imputed" || $checkbox === "Unimputed")
                <input type="checkbox" id="{{ $checkbox }}" name="{{ $checkbox }}" value="{{ $checkbox }}" hidden>
                <label for="{{ $checkbox }}" hidden>{{ str_replace('_', ' ', $checkbox) }}</label>
                @else
                <input type="checkbox" id="{{ $checkbox }}" name="{{ $checkbox }}" value="{{ $checkbox }}" checked>
                <label for="{{ $checkbox }}">{{ str_replace('_', ' ', $checkbox) }}</label>
                @endif
                @if ($key != 0 && $key % 5 === 0)
                    <br />
                @endif
                @endforeach
                <br /><br />
                <input type="submit" value="Search">
            </form>
        </td>
        <td width="50%" align="center" valign="top" style="border:1px solid #999999; padding:10px; background-color:#f8f8f8; text-align:left;">
            <form action="{{ route('system.tools.AlleleCatalogTool.viewAllByAccessionAndGene', ['organism'=>$organism]) }}" method="get" target="_blank">
                <h2>Search by Accessions and Gene ID</h2>
                <br />
                <label for="dataset2"><b>Dataset:</b></label>
                <select name="dataset2" id="dataset2">
                    @foreach($dataset_array as $dataset)
                    <option value="{{ $dataset }}">{{ str_replace('_', ' ', $dataset) }}</option>
                    @endforeach
                </select>
                <br />
                <br />
                <b>Accessions</b>
                <span style="font-size:10pt">
                    &nbsp;(eg
                    @foreach($accession_array as $accession)
                    {{ $accession->Accession }}
                    @endforeach
                    )
                </span>
                <br />
                <textarea id="accession" name="accession" rows="12" cols="40"></textarea>
                <br /><br />
                <b>Gene ID</b><span style="font-size:10pt">&nbsp;(One gene name only; eg {{ $gene_array[0]->Gene }})</span>
                <br />
                <input type="text" id="gene2" name="gene2" size="40"></input>
                <br /><br />
                <input type="submit" value="Search">
            </form>
        </td>
    </tr>
</table>

@if ($organism === "Zmays")
    <br />
    <br />
    <div style='margin-top:10px;' align='center'>
        <button type="submit" onclick="window.open('https://data.cyverse.org/dav-anon/iplant/home/soykb/KBCAlleleCatalogTool/Zmays_Panzea_AGPv3_accession_file.csv')" style="margin-right:20px;">Download Accession Information</button>
    </div>
@elseif ($organism === "Athaliana")
    <br />
    <br />
    <div style='margin-top:10px;' align='center'>
        <button type="submit" onclick="window.open('https://data.cyverse.org/dav-anon/iplant/home/soykb/KBCAlleleCatalogTool/Arabidopsis_TAIR10_accession_file.csv')" style="margin-right:20px;">Download Accession Information</button>
    </div>
@endif

@endsection


@section('javascript')

<script type="text/javascript">
    // Populate gene1 textarea placeholder
    let gene_array = <?php echo json_encode($gene_array); ?>;
    gene1_placeholder = "\nPlease separate each gene into a new line.\n\nExample:\n";
    for (let i = 0; i < gene_array.length; i++) {
        gene1_placeholder += gene_array[i]['Gene'] + "\n";
    }
    document.getElementById('gene1').placeholder = gene1_placeholder;


    // Populate accession textarea placeholder
    let accession_array = <?php echo json_encode($accession_array); ?>;
    accession_placeholder = "\nPlease separate each accession into a new line.\n\nExample:\n";
    for (let i = 0; i < accession_array.length; i++) {
        accession_placeholder += accession_array[i]['Accession'] + "\n";
    }
    document.getElementById('accession').placeholder = accession_placeholder;
</script>

@endsection