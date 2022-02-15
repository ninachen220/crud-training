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
            <div class="offset-md-6 col-6" style="text-align: right;">
                <button type="button" class="btn btn-warning" id="addAccountModel">
                    新增
                </button>
            </div>
            <div class="offset-md-9 col-3 mt-4">
                <div class="input-group">
                    <input type="text" class="form-control" aria-label="searchPlace" aria-describedby="search">
                    <button class="btn btn-primary" type="button" id="search"><i class="bi bi-search"></i></button>
                </div>
            </div>

        </div>
        <table class="table table-striped table-bordered mt-2" id="accountTable" style="text-align: center;">
            <thead>
                <tr>
                    <th scope="col">帳號</th>
                    <th scope="col">姓名</th>
                    <th scope="col">性別</th>
                    <th scope="col">生日</th>
                    <th scope="col">信箱</th>
                    <th scope="col">備註</th>
                    <th scope="col">編輯</th>
                    <th scope="col">刪除</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <nav aria-label="Page navigation example">
            <ul class="pagination justify-content-end">
                <li class="page-item"><a class="page-link" href="#">前一頁</a></li>
                <li class="page-item"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item"><a class="page-link" href="#">下一頁</a></li>
            </ul>
        </nav>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="addAccount" tabindex="-1" aria-labelledby="addAccountLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAccountLabel">新增帳號</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <form id="addAccountForm">
                        <div class="col-12 input-group ">
                            <span class="input-group-text" id="accountIdName">帳號<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="accountId" id="accountId">
                        </div>
                        <div class="col-12 input-group mt-2">
                            <span class="input-group-text" id="accountNameName">姓名<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="accountName" id="accountName">
                        </div>
                        <div class="col-12 input-group mt-2">
                            <span class="input-group-text" id="accountSexName">性別<span style="color: red;">*</span></span>
                            <select class="form-control" name="accountSex" id="accountSex">
                                <option value="N" selected>請選擇性別</option>
                                <option value="M">男</option>
                                <option value="F">女</option>
                            </select>
                        </div>
                        <div class="col-12 input-group mt-2">
                            <span class="input-group-text" id="accountBirthName">生日<span style="color: red;">*</span></span>
                            <input type="date" class="form-control" name="accountBirth" id="accountBirth">
                        </div>
                        <div class="col-12 input-group mt-2">
                            <span class="input-group-text" id="accountMailName">信箱<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="accountMail" id="accountMail">
                        </div>
                        <div class="col-12 input-group mt-2">
                            <span class="input-group-text" id="accountNoteName">備註</span>
                            <textarea class="form-control" name="accountNote" id="accountNote" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary addAccount">送出</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editAccount" tabindex="-1" aria-labelledby="editAccountLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAccountLabel">修改帳號</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body row">
                    <form id="editAccountForm">
                        <div class="col-12 input-group ">
                            <span class="input-group-text" id="editAccountIdName">帳號<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="editAccountId" id="editAccountId">
                            <input type="hidden" class="form-control" name="accountSeq" id="accountSeq">
                        </div>
                        <div class="col-12 input-group mt-2">
                            <span class="input-group-text" id="editAccountNameName">姓名<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="editAccountName" id="editAccountName">
                        </div>
                        <div class="col-12 input-group mt-2">
                            <span class="input-group-text" id="editAccountSexName">性別<span style="color: red;">*</span></span>
                            <select class="form-control" name="editAccountSex" id="editAccountSex">
                                <option value="N" selected>請選擇性別</option>
                                <option value="M">男</option>
                                <option value="F">女</option>
                            </select>
                        </div>
                        <div class="col-12 input-group mt-2">
                            <span class="input-group-text" id="editAccountBirthName">生日<span style="color: red;">*</span></span>
                            <input type="date" class="form-control" name="editAccountBirth" id="editAccountBirth">
                        </div>
                        <div class="col-12 input-group mt-2">
                            <span class="input-group-text" id="editAccountMailName">信箱<span style="color: red;">*</span></span>
                            <input type="text" class="form-control" name="editAccountMail" id="editAccountMail">
                        </div>
                        <div class="col-12 input-group mt-2">
                            <span class="input-group-text" id="editAccountNoteName">備註</span>
                            <textarea class="form-control" name="editAccountNote" id="editAccountNote" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary editAccount">送出</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">關閉</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>