<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Bootstrap Practice</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <script src="/node_modules/jquery/dist/jquery.min.js"></script>
    <script src="/node_modules/popper.js"></script>
    <script src="/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <!-- Font Awesome Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css" />
</head>

<body>
    <div class="container">
        <h2>帳號管理</h2>
        <div class="row">
            <div class="col-4">
                <input type="password" class="form-control" id="password">
            </div>
            <div class="col-2">
                <button class="btn btn-primary">搜尋</button>
            </div>
            <div class="col-6" style="text-align: right;">
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    新增
                </button>
            </div>
            <table class="table table-striped table-bordered mt-4">
                <thead>
                    <tr>
                        <th scope="col"></th>
                        <th scope="col">Firstname</th>
                        <th scope="col">Lastname</th>
                        <th scope="col">Email</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><i class="fas fa-pen"></i> <i class="fas fa-trash text-danger ml-3"></i></td>
                        <td>John</td>
                        <td>Doe</td>
                        <td>john@example.com</td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-pen ml-3"></i> <i class="fas fa-trash text-danger ml-3"></i></td>
                        <td>Mary</td>
                        <td>Moe</td>
                        <td>mary@example.com</td>
                    </tr>
                    <tr>
                        <td><i class="fas fa-pen ml-3"></i> <i class="fas fa-trash text-danger ml-3"></i></td>
                        <td>July</td>
                        <td>Dooley</td>
                        <td>july@example.com</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="offset-md-9 col-3">
                <div class="btn-group" role="group" aria-label="Basic example">
                    <button type="button" class="btn btn-outline-primary">Previous</button>
                    <button type="button" class="btn btn-outline-primary">1</button>
                    <button type="button" class="btn btn-outline-primary">2</button>
                    <button type="button" class="btn btn-outline-primary">3</button>
                    <button type="button" class="btn btn-outline-primary">Next</button>
                </div>
            </div>
        </div>
    </div>
</body>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>

</html>