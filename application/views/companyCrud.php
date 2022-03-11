<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <script src="/node_modules/popper.js/dist/popper.min.js"></script>
    <script src="/node_modules/popper.js/dist/popper.js"></script>
    <script src="/node_modules/jquery/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.4/css/select.dataTables.min.css">
    <script src="https://cdn.datatables.net/select/1.3.4/js/dataTables.select.min.js"></script>
    <link rel="stylesheet" href="/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <script src="/node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" integrity="sha512-Oy+sz5W86PK0ZIkawrG0iv7XwWhYecM3exvUtMKNJMekGFJtVAhibhRPTpmyTj8+lJCkmWfnpxKgT2OopquBHA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/css/bootstrap-dialog.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap3-dialog/1.34.7/js/bootstrap-dialog.min.js"></script>
    <script src="/node_modules/jquery/dist/jquery.rustaMsgBox.js"></script>

    <script src="<?= JS_DIR; ?>companyExam/companyCrud.js"></script>
</head>

<body>
    <div class="container mt-2">
        <h2>公司資料管理</h2>
        <div class="row">
            <div class="col-md-4">
                <form method="POST" enctype='multipart/form-data' name='importForm'>
                    <div class="row">
                        <div class="col-md-9">
                            <input type="file" id="importData">
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-warning" id="import">匯入</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger" id="export">匯出</button>
            </div>
            <div class="col-md-6" style="text-align: right;">
                <button type="button" class="btn btn-success" id="addCompany">
                    新增
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-md-offset-9" style="text-align: right;  margin-top:4px;margin-bottom:8px;">
                <button class="btn btn-warning" type="button" id="rowDelete">批次刪除</button>
            </div>
        </div>
        <!-- datatable -->
        <table class="table table-striped table-bordered" id="companyTable" style="text-align: center;">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col" style="text-align: center;">公司名稱</th>
                    <th scope="col" style="text-align: center;">公司聯絡人</th>
                    <th scope="col" style="text-align: center;">信箱</th>
                    <th scope="col" style="text-align: center;">公司規模</th>
                    <th scope="col" style="text-align: center;">公司類別</th>
                    <th scope="col" style="text-align: center;">備註</th>
                    <th scope="col" style="text-align: center;">編輯</th>
                    <th scope="col" style="text-align: center;">刪除</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <!-- dialog -->
    <div style="visibility:hidden">
        <form id="companyForm">
            <div class="companyData">
                <div class="col-md-12 ">
                    <div class="input-group">
                        <span class="input-group-addon" id="companyNameId">公司名稱<span style="color: red;">*</span></span>
                        <input type="text" class="form-control" name="companyName" id="companyName">
                    </div>
                    <input type="hidden" class="form-control" name="companySeq" id="companySeq">

                </div>
                <div class="col-md-12" style="margin-top: 5px;">
                    <div class="input-group">
                        <span class="input-group-addon" id="companycContactId"> 公司聯絡人<span style="color: red;">*</span></span>
                        <input type="text" class="form-control" name="companycContact" id="companycContact">
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 5px;">
                    <div class="input-group">
                        <span class="input-group-addon" id="companycMailId">信箱<span style="color: red;">*</span></span>
                        <input type="text" class="form-control" name="companycMail" id="companycMail">

                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 5px;">
                    <div class="input-group">
                        <span class="input-group-addon" id="companycScaleId">公司規模<span style="color: red;">*</span></span>
                        <select class="form-control" name="companycScale" id="companycScale">
                            <option value="" selected>請選擇規模</option>
                            <option value="big">大型</option>
                            <option value="medium">中型</option>
                            <option value="small">小型</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 5px;">
                    <div class="input-group">
                        <span class="input-group-addon" id="companyTypeId">公司類別<span style="color: red;">*</span></span>
                        <select class="form-control" name="companyType" id="companyType">
                            <option value="" selected>請選擇類別</option>
                            <?php foreach ($type as $row) : ?>
                                <option value="<?php echo $row['t_id'] ?>"><?php echo $row['t_name'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12" style="margin-top: 5px;margin-bottom: 5px;">
                    <div class="input-group">
                        <span class="input-group-addon" id="companycRemarkId">備註</span>
                        <textarea class="form-control" name="companycRemark" id="companycRemark" rows="3"></textarea>
                    </div>
                </div>
            </div>
        </form>
    </div>

</html>