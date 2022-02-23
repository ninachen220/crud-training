<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <script src="<?= JS_DIR; ?>crudPractice/crudAccount.js"></script>
</head>

<body>
    <div class="container mt-2">
        <h2>帳號管理</h2>
        <div class="row">
            <div class="col-md-4">
                <form method="POST" enctype='multipart/form-data'name = 'importForm'>
                    <div class="row">
                        <div class="col-md-8">
                            <input type="file" id="importData">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary" id="import">匯入</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-2">
                <form action="Crud_account/exportData">
                    <button type="submit" class="btn btn-info" id="export">匯出</button>
                </form>
            </div>
            <div class="col-md-6" style="text-align: right;">
                <button type="button" class="btn btn-warning" id="addAccountModel">
                    新增
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 row" style="text-align: left;">
                <div class="col-md-3" style="text-align: right;margin-top:10;"><span>顯示</span></div>
                <div class="col-md-5">
                    <select class="form-control" style="text-align: center;margin-top:5;" id="showData">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="20">20</option>
                    </select>
                </div>
                <div class="col-md-4" style="margin-top:10;"><span>筆</span></div>

            </div>
            <div class="col-md-3 col-md-offset-3" style="text-align: right;  margin-top:4;">
                <button class="btn btn-primary" type="button" id="oneByOneDelete">批次刪除</button>
            </div>
            <div class="col-md-3" style="float: right; margin-top:4;">
                <div class="input-group">
                    <input type="text" class="form-control" id="searchPlace" placeholder="請輸入搜尋文字">
                    <span class="input-group-btn"> <button class="btn btn-primary" type="button" id="search" style="padding: 9;"><i class="bi bi-search"></i></button></span>
                </div>
            </div>

        </div>
        <table class="table table-striped table-bordered" id="accountTable" style="text-align: center;  margin-top:8;">
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col" style="text-align: center;">帳號 <i class="bi bi-sort-down"></i></th>
                    <th scope="col" style="text-align: center;">姓名 <i class="bi bi-filter-left"></i></th>
                    <th scope="col" style="text-align: center;">性別 <i class="bi bi-filter-left"></i></th>
                    <th scope="col" style="text-align: center;">生日 <i class="bi bi-filter-left"></i></th>
                    <th scope="col" style="text-align: center;">信箱 <i class="bi bi-filter-left"></i></th>
                    <th scope="col" style="text-align: center;">備註 <i class="bi bi-filter-left"></i></th>
                    <th scope="col" style="text-align: center;">編輯</th>
                    <th scope="col" style="text-align: center;">刪除</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <nav aria-label="Page navigation" style="float: right;">
            <ul class="pagination">
            </ul>
        </nav>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="addAccount" tabindex="-1" role="dialog" aria-labelledby="addAccountLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="addAccountLabel">Modal title</h4>
                </div>
                <div class="modal-body row">
                    <form id="addAccountForm">
                        <div class="col-md-12 ">
                            <div class="input-group">
                                <span class="input-group-addon" id="accountIdName">帳號<span style="color: red;">*</span></span>
                                <input type="text" class="form-control" name="accountId" id="accountId">
                            </div>
                            <input type="hidden" class="form-control" name="accountSeq" id="accountSeq">

                        </div>
                        <div class="col-md-12" style="margin-top: 5;">
                            <div class="input-group">
                                <span class="input-group-addon" id="accountNameName">姓名<span style="color: red;">*</span></span>
                                <input type="text" class="form-control" name="accountName" id="accountName">
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top: 5;">
                            <div class="input-group">
                                <span class="input-group-addon" id="accountSexName">性別<span style="color: red;">*</span></span>
                                <select class="form-control" name="accountSex" id="accountSex">
                                    <option value="" selected>請選擇性別</option>
                                    <option value="M">男</option>
                                    <option value="F">女</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top: 5;">
                            <div class="input-group">
                                <span class="input-group-addon" id="accountBirthName">生日<span style="color: red;">*</span></span>
                                <input type="date" class="form-control" name="accountBirth" id="accountBirth">
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top: 5;">
                            <div class="input-group">
                                <span class="input-group-addon" id="accountMailName">信箱<span style="color: red;">*</span></span>
                                <input type="text" class="form-control" name="accountMail" id="accountMail">
                            </div>
                        </div>
                        <div class="col-md-12" style="margin-top: 5;">
                            <div class="input-group">
                                <span class="input-group-addon" id="accountNoteName">備註</span>
                                <textarea class="form-control" name="accountNote" id="accountNote" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary account_button">送出</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">關閉</button>
                </div>
            </div>
        </div>
    </div>

</html>