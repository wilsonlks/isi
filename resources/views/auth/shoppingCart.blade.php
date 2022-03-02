@extends('layouts.app')

@section('content')

<?php
        include("./php_file/dbConnect.php");
?>

<style type="text/css">
    .container .card-body {
        margin-bottom: 15px;
    }
    .container .form-text, .container .card-body {
        margin-top: 25px;
    }
</style>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('My Shopping Cart') }}</div>

                <div class="card-body">

                </div>
            </div>
        </div>
    </div>
</div>

@endsection