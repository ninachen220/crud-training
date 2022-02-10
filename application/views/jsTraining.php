<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Bootstrap 4 Example</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <script src="/node_modules/jquery/dist/jquery.min.js"></script>
    <script src="/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css" />

    <script src="<?= JS_DIR; ?>jsTraining/jsTraining.js"></script>
</head>

<body>

    <div class="container">
        <!-- Title -->
        <div class="row">
            <div class="col-sm-12">
                <h1>Javascript Training</h1>
                <hr>
            </div>
        </div>

        <!-- Controll Form -->
        <div class="row">
            <div class="col-sm-12"><button class="btn btn-warning float-right ctrl-btn">新增</button></div>
        </div>

        <!-- Table -->
        <div class="row">
            <table class="table table-striped table-bordered table-hover ctrl-table">
            </table>
        </div>

        <!-- Message box -->
        <div class="row">
            <div id="ctrl-message" class="text-danger ctrl-message"></div>
        </div>

    </div>

</body>

</html>