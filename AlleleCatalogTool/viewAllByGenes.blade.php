@php
    include resource_path() . '/views/system/config.blade.php';

    $organism = $info['organism'];
    $dataset1 = $info['dataset1'];
    $gene1 = $info['gene1'];

@endphp


@extends('system.header')


@section('content')

<div class="title1">Allele Catalog Tool</div>
<br />

{{ $dataset1 }}
{{ $gene1 }}


@endsection


@section('javascript')

<script type="text/javascript">
</script>

@endsection
