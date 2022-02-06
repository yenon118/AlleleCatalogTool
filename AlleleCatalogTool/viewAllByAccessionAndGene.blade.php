@php
    include resource_path() . '/views/system/config.blade.php';

    $organism = $info['organism'];
    $dataset2 = $info['dataset2'];
    $accession = $info['accession'];
    $gene2 = $info['gene2'];

@endphp


@extends('system.header')


@section('content')

<div class="title1">Allele Catalog Tool</div>
<br />

{{ $dataset2 }}
{{ $accession }}
{{ $gene2 }}

@endsection


@section('javascript')

<script type="text/javascript">
</script>

@endsection
