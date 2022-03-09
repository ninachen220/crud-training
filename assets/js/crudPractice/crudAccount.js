/**
 * 說明：
 * <li>1. 頁面函式只會初始化一次
 * <li>2. 如果是多頁面組合時，可能被其他頁面呼叫，因此需使用namespane:Page，以方便外部呼叫或試調
 * 
 * 執行順序：
 * 1. 註冊$(document).ready()函式，但先不執行
 * 2. $(document).ready()之外的程式碼依序執行 - 建構變數、函式obj
 * 3. 執行$(document).ready()內註冊的函式
 * 4. 確定window.Page是否存在，不存在則初始化
 * 5. 執行obj()物件，並將結果存入window.Page[name]
 * 6. obj()回傳內容為 new obj.fn.init(options);
 * 7. 實例化obj.fn.init(options);並在最後執行函式 _construct(_options);
 */

// IIFE 立即執行函式
(function(window, document, $, undefined) {
  // 使用嚴格模式
  'use strict';
  // DOM下載完後執行
  $(document).ready(function() {
    // init this page
    window.Page = window.Page || new function() {}();
    window.Page[name] = obj();
  });

  // Class Name
  var name = '{name}';
  // Version
  var version = '{version}';
  // Default options
  var defaults = {};

  /**
        * *************** Object Build ***************
        */

  // Define a local copy of Object
  var obj = function(options) {
    return new obj.fn.init(options);
  };

  // Prototype arguments
  obj.fn = obj.prototype = {
    // Object Name
    _name: name,

    // Default options
    _defaults: defaults,

    // AJAX URL
    _ajaxUrls: {
      // Account CRUD AJAX server side url.
      // AJAX
      accountApi: '/Crud_account/ajax',
      // 資料匯入
      importApi: '/Crud_account/importData',
      // 資料匯出
      exportApi: '/Crud_account/exportData',
    },
  };

  /**
        * Javascript物件
        */
  obj.fn.init = function(options) {
    /**
          * *************** Object Argument Setting ***************
          */
    var self = this;
    var _options = options || {};
    // Ajax Response - jqXHR(s)
    var _jqXHRs;

    /**
          * *************** 屬性設定 ***************
          */

    /**
          * *************** 物件必要函式 ***************
          */

    /**
          * 建構子
          */
    var _construct = function() {
      console.log('_construct');
      _initialize();
      myTable;
    };

    var myTable = $('#accountTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: self._ajaxUrls.accountApi,
        method: 'GET',
      },
      // 設定顯示欄位為中文
      language: {
        processing: '處理中...',
        loadingRecords: '載入中...',
        lengthMenu: '顯示 _MENU_ 項結果',
        zeroRecords: '沒有符合的結果',
        info: '顯示第 _START_ 至 _END_ 項結果，共 _TOTAL_ 項',
        infoEmpty: '顯示第 0 至 0 項結果，共 0 項',
        infoFiltered: '(從 _MAX_ 項結果中過濾)',
        infoPostFix: '',
        search: '搜尋:',
        paginate: {
          first: '第一頁',
          previous: '上一頁',
          next: '下一頁',
          last: '最後一頁',
        },
        aria: {
          sortAscending: ': 升冪排列',
          sortDescending: ': 降冪排列',
        },
        select: {
          rows: {
            _: '已選擇 %d 筆資料',
            0: '',
          },
        },
      },

      // 預設全域按鈕事件
      drawCallback: function() {
        // 取得當前資料
        var tableApi = this.api();
        // 設定當前資料的內文
        var $tbody = $(tableApi.table().body());
        // 設定每個欄位的編輯按鈕事件
        $tbody.find('.edit').on('click', function() {
          // 找到當前元素開始的tr
          var $tr = $(this).closest('tr');
          // 找到當前的行
          var row = tableApi.row($tr);
          // 找到當前行的資料
          var rowData = row.data();
          // 解綁送出按鈕的事件
          $('.account_button').unbind();

          // 重新綁新的事件
          $('.account_button').on('click', row, editAccout);

          // 從後端撈取資料
          var res = getSpesificAccount(rowData['a_id']);
          $('#addAccount').modal('show');
          $('#addAccountLabel').text('編輯帳號');
          $('#accountId').val(res['a_account']);
          $('#accountSeq').val(rowData['a_id']);
          $('#accountName').val(res['a_name']);
          $('#accountSex').val(res['a_sex']);
          $('#accountBirth').val(res['a_birth']);
          $('#accountMail').val(res['a_mail']);
          $('#accountNote').val(res['a_note']);
          if (res['d_id'] == 0) {
            res['d_id'] = '';
          }
          $('#accountDept').val(res['d_id'] == 0 ? '' : res['d_id']);
        });

        // 設定刪除按鈕的事件
        $tbody.find('.delete').on('click', function() {
          // 找到當前元素開始的tr
          var $tr = $(this).closest('tr');
          // 找到當前的行
          var row = tableApi.row($tr);
          // 找到當前行的資料
          var rowData = row.data();
          // 刪除資料的id
          deleteAccout(rowData['a_id'], row);
        });
      },
      // 設定選擇的checkbox
      columnDefs: [
        {
          orderable: false,
          className: 'select-checkbox',
          targets: 0,
        },
      ],
      // 選擇的細項設定
      select: {
        style: 'multi',
        selector: 'td:first-child',
      },
      order: [[1, 'asc']],
      // 設定資料來源
      data: [],
      // 放入對應table的資料欄位
      columns: [
        {
          // 設定空欄位放checkbox
          data: null,
          render: function(data, type, full, meta) {
            return null;
          },
        },
        { data: 'a_account' },
        { data: 'a_name' },
        {
          data: 'a_sex',
          // 轉換性別為中文
          render: function(data, type, full, meta) {
            var sex = '男生';
            // 判斷是否資料為F
            if (data == 'F') {
              sex = '女生';
            }
            return sex;
          },
        },
        { data: 'd_id' },
        { data: 'a_birth' },
        { data: 'a_mail' },
        { data: 'a_note' },
        {
          orderable: false,
          data: null,
          // 設定編輯按鈕
          render: function(data, type, full, meta) {
            var btn;
            btn = $('<button>', {
              class: 'btn btn-default bi bi-pencil edit',
            }).prop('outerHTML');
            return btn;
          },
        },
        {
          orderable: false,
          data: null,
          // 設定刪除按鈕
          render: function(data, type, full, meta) {
            var btn;
            btn = $('<button>', {
              class: 'btn btn-default bi bi-trash3 delete',
            }).prop('outerHTML');
            return btn;
          },
        },
      ],
    });
    /**
     * 解構子
     */
    var _destruct = function() {};

    // 設定datatable
    var setTable = function() {};

    // 新增帳號
    var addAccout = function() {
      try {
        // 二次確認是否新增帳號
        if (confirm('是否新增帳號?')) {
          // form所有欄位的資料
          var data = $('#addAccountForm').serializeArray();
          // 前端判斷是否符合格式
          checkData(data);
          var a_account = $('#accountId').val();
          var a_name = $('#accountName').val();
          var a_sex = $('#accountSex').val();
          var a_birth = $('#accountBirth').val();
          var a_mail = $('#accountMail').val();
          var a_note = $('#accountNote').val();
          var d_id = $('#accountDept').val();
          $.ajax({
            method: 'POST',
            url: self._ajaxUrls.accountApi,
            dataType: 'json',
            data: {
              a_account: a_account,
              a_name: a_name,
              a_sex: a_sex,
              a_birth: a_birth,
              a_mail: a_mail,
              a_note: a_note,
              d_id: d_id,
            },
          })
            .done(function(data) {
              // 用dialog顯示回傳的type
              BootstrapDialog.show({
                // 設定標題
                title: '訊息',
                // 設定內文
                message: data.type,
                // 設定按鈕
                buttons: [
                  {
                    label: 'OK',
                    action: function(dialogItself) {
                      dialogItself.close();
                    },
                  },
                ],
              });

              // 將modal欄位內的資料清除
              $('#addAccount').find('input,textarea').val('');
              $('#addAccount').find('select').val('');

              // 隱藏modal
              $('#addAccount').modal('hide');
              // 重新渲染datatable
              myTable.draw();
            })
            .fail(function(data) {
              // 顯示錯誤訊息
              $.rustaMsgBox({ content: data.responseText });
            });
        }
      } catch (error) {
        // 用dialog顯示錯誤訊息
        BootstrapDialog.show({
          // 設定標題
          title: '錯誤訊息',
          // 設定內文
          message: error.message,
          // 設定按鈕
          buttons: [
            {
              label: 'OK',
              action: function(dialogItself) {
                dialogItself.close();
              },
            },
          ],
        });
      }
    };

    // 獲取單筆資料
    var getSpesificAccount = function(id) {
      var res;
      // 發送刪除的ID到controller
      $.ajax({
        method: 'GET',
        url: self._ajaxUrls.accountApi + '/' + id,
        dataType: 'json',
        async: false,
      }).done(function(data) {
        // 設定回傳資料
        res = data.data[0];
      });
      // 回傳資料
      return res;
    };

    // 刪除帳號
    var deleteAccout = function(id, event) {
      var row = event.data;
      var check = false;
      // 二次確認是否刪除帳號
      BootstrapDialog.show({
        title: '確認訊息',
        message: '是否刪除帳號?',
        buttons: [
          // 確認送出
          {
            label: '確認',
            // no title as it is optional
            cssClass: 'btn-primary',
            action: function(dialogItself) {
              // 關閉dialog二次確認
              dialogItself.close();
              // 發送刪除的ID到controller
              $.ajax({
                method: 'DELETE',
                url: self._ajaxUrls.accountApi + '/' + id,
                dataType: 'json',
                async: false,
              })
                .done(function(data) {
                  BootstrapDialog.show({
                    title: '訊息',
                    message: data.type,
                    buttons: [
                      {
                        label: 'OK',
                        action: function(dialogItself) {
                          dialogItself.close();
                        },
                      },
                    ],
                  });
                  // 呼叫datatable
                  var table = $('#accountTable').DataTable();
                  // 重新整理datatable
                  table.row().draw();
                })
                .fail(function(data) {
                  // 回傳錯誤訊息
                  $.rustaMsgBox({ content: data.responseText });
                });
            },
          },
          // 不要送出並關閉
          {
            label: '關閉',
            action: function(dialogItself) {
              dialogItself.close();
            },
          },
        ],
      });
      return check;
    };
    /**
     * 刪除多筆帳號
     * 
     * @param array ids  待刪除 id 陣列
     */
    var deleteSelectAccount = function(id) {
      // 二次確認是否批次刪除帳號
      BootstrapDialog.show({
        title: '確認訊息',
        message: '是否確定批次刪除帳號?',
        buttons: [
          {
            //確認送出
            label: '確認',
            cssClass: 'btn-primary',
            action: function(dialogItself) {
              // 關閉二次確認視窗
              dialogItself.close();
              // 發送刪除的ID到controller
              $.ajax({
                method: 'DELETE',
                url: self._ajaxUrls.accountApi,
                dataType: 'json',
                data: { a_id: id },
              })
                .done(function(data) {
                  BootstrapDialog.show({
                    title: '訊息',
                    message: data.type,
                    buttons: [
                      {
                        label: 'OK',
                        action: function(dialogItself) {
                          // 關閉確認視窗
                          dialogItself.close();
                        },
                      },
                    ],
                  });
                  var table = $('#accountTable').DataTable();
                  table.rows({ selected: true }).remove().draw();
                })
                .fail(function(data) {
                  // 顯示錯誤訊息
                  $.rustaMsgBox({ content: data.responseText });
                });
            },
          },
          {
            // 不要送出並關閉
            label: '關閉',
            action: function(dialogItself) {
              dialogItself.close();
            },
          },
        ],
      });
    };

    // 更新帳號
    var editAccout = function(event) {
      var row = event.data;

      // 二次確認是否更新帳號
      BootstrapDialog.show({
        title: '確認訊息',
        message: '是否更新帳號?',
        buttons: [
          {
            // 確認送出
            label: '確認',
            // no title as it is optional
            cssClass: 'btn-primary',
            action: function(dialogItself) {
              dialogItself.close();
              try {
                // form的所有資料
                var data = $('#addAccountForm').serializeArray();
                // 確認格式
                checkData(data);
                var a_account = $('#accountId').val();
                var a_id = $('#accountSeq').val();
                var a_name = $('#accountName').val();
                var a_sex = $('#accountSex').val();
                var a_birth = $('#accountBirth').val();
                var a_mail = $('#accountMail').val();
                var a_note = $('#accountNote').val();
                var d_id = $('#accountDept').val();
                // 發送更新的資料到controller
                $.ajax({
                  method: 'PUT',
                  url: self._ajaxUrls.accountApi,
                  dataType: 'json',
                  data: {
                    a_account: a_account,
                    a_id: a_id,
                    a_name: a_name,
                    a_sex: a_sex,
                    a_birth: a_birth,
                    a_mail: a_mail,
                    a_note: a_note,
                    d_id: d_id,
                  },
                })
                  .done(function(data) {
                    // 顯示回傳的type
                    BootstrapDialog.show({
                      title: '訊息',
                      message: data.type,
                      buttons: [
                        {
                          label: 'OK',
                          action: function(dialogItself) {
                            // 關閉視窗
                            dialogItself.close();
                          },
                        },
                      ],
                    });
                    // 查詢更新後的資料
                    var res = getSpesificAccount(a_id);
                    // 隱藏modal
                    $('#addAccount').modal('hide');
                    // 呼叫datatable
                    var table = $('#accountTable').DataTable();
                    // 更新特定欄位的資料
                    table.row(row).data(res).draw();
                  })
                  .fail(function(data) {
                    // 回傳錯誤訊息
                    $.rustaMsgBox({ content: data.responseText });
                  });
              } catch (error) {
                // 回傳錯誤訊息
                BootstrapDialog.show({
                  title: '錯誤訊息',
                  message: error.message,
                  buttons: [
                    {
                      label: 'OK',
                      action: function(dialogItself) {
                        // 關閉視窗
                        dialogItself.close();
                      },
                    },
                  ],
                });
              }
            },
          },
          {
            // 不要送出並關閉
            label: '關閉',
            action: function(dialogItself) {
              dialogItself.close();
            },
          },
        ],
      });
    };

    // 確認資料格式
    var checkData = function(data) {
      // 將data全部取出來判斷
      $.each(data, function(index, row) {
        // 欄位名稱
        var name = row.name;
        // 欄位資料
        var value = row.value;
        // 判斷字元是否為5~15個
        if (name == 'accountId' && !/^[A-Za-z0-9]{5,15}$/.test(value)) {
          throw new Error('帳號限制為5~15個字元', 404);
        }

        // 判斷是否為正確的email格式
        if (
          name == 'accountMail' &&
          !/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test(value)
        ) {
          throw new Error('請輸入正確信箱格式', 404);
        }

        // 判斷是否為正確的日期格式
        if (
          name == 'accountBirth' &&
          !/^[1-9]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/.test(value)
        ) {
          throw new Error('請輸入日期格式', 404);
        }
        // 判斷欄位資料為空
        if (name != 'accountNote' && name != 'accountSeq' && value == '') {
          switch (name) {
            case 'accountId':
              var text = '帳號';
              break;
            case 'accountMail':
              var text = '信箱';
              break;
            case 'accountBirth':
              var text = '生日';
              break;
            case 'accountSex':
              var text = '性別';
              break;
            case 'accountDept':
              var text = '部門';
              break;
            case 'accountName':
              var text = '姓名';
              break;
          }
          throw new Error(text + '不能為空', 404);
        }
      });
    };

    // 匯入資料
    var importData = function(event, form) {
      try {
        // 取消表單預設提交
        event.preventDefault();
        // 單個檔案
        var file = $('#importData')[0].files[0];
        // 沒有選擇檔案則回傳錯誤訊息
        if (!file) {
          throw new Error('尚未選擇資料', 400);
        }
        // 建立一個新的 FormData 物件
        var formData = new FormData(form);

        // 將file加入formData裡
        formData.append('fileupload', file);

        // 二次確認是否匯入
        BootstrapDialog.show({
          title: '確認訊息',
          message: '是否確認匯入資料?',
          buttons: [
            {
              // 確認並送出
              label: '確認',
              cssClass: 'btn-primary',
              action: function(dialogItself) {
                // 關閉確認視窗
                dialogItself.close();
                $.ajax({
                  url: self._ajaxUrls.importApi,
                  type: 'POST',
                  contentType: false,
                  processData: false,
                  data: formData,
                })
                  .done(function(data) {
                    // 顯示回傳訊息
                    BootstrapDialog.show({
                      title: '訊息',
                      message: data,
                      buttons: [
                        {
                          label: 'OK',
                          action: function(dialogItself) {
                            dialogItself.close();
                          },
                        },
                      ],
                    });
                  })
                  .fail(function(data) {
                    // 顯示錯誤訊息
                    BootstrapDialog.show({
                      title: '訊息',
                      message: data.responseText,
                      buttons: [
                        {
                          label: 'OK',
                          action: function(dialogItself) {
                            dialogItself.close();
                          },
                        },
                      ],
                    });
                  });
              },
            },
            {
              // 不要送出並關閉
              label: '關閉',
              action: function(dialogItself) {
                dialogItself.close();
              },
            },
          ],
        });
      } catch (error) {
        // 顯示錯誤訊息
        BootstrapDialog.show({
          title: '錯誤訊息',
          message: error.message,
          buttons: [
            {
              label: 'OK',
              action: function(dialogItself) {
                // 關閉訊息視窗
                dialogItself.close();
              },
            },
          ],
        });
      }
    };

    // 匯出資料
    var exportData = function() {
      var table = $('#accountTable').DataTable();
      // 取得頁面資料
      var info = table.page.info();
      // 取得顯示筆數
      var length = info.length;
      // 取得當前頁碼
      var page = info.page;
      // 取得排序方式
      var order = table.order()[0];
      window.open(
        self._ajaxUrls.exportApi +
          '?length=' +
          length +
          '&page=' +
          page +
          '&order=' +
          order[1] +
          '&text=' +
          order[0]
      );
    };
    /**
     * 初始化
     */
    var _initialize = function() {
      console.log('_initialize');

      // 取得file
      let importForm = document.forms.namedItem('importForm');
      importForm.addEventListener('submit', function(event) {
        importData(event, importForm);
      });
      /**
     * 事件綁定
     */
      _evenBind();
    };

    /**
     * 事件綁定
     */
    var _evenBind = function() {
      console.log('_evenBind');
      // 顯示新增modal
      $('#addAccountModel').on('click', function() {
        // 解綁送出按鈕的事件
        $('.account_button').unbind();

        // 重新綁新的事件
        $('.account_button').on('click', addAccout);
        $('#addAccount').modal('show');

        // 將內容清空
        $('#addAccount').find('select,textarea,input').val('');
        $('.modal-title').text('新增帳號');
      });

      // 批次刪除
      $('#oneByOneDelete').on('click', function() {
        // 呼叫datatable
        var table = $('#accountTable').DataTable();
        // 設定空陣列
        const arr = [];
        // 找尋有選擇的欄位並加入陣列中;
        table.rows({ selected: true }).data().each(function(rows, num) {
          arr[num] = rows.a_id;
        });
        // 批次刪除
        deleteSelectAccount(arr);
      });

      // 匯出資料
      $('#export').on('click', function() {
        exportData();
      });
    };

    /**
          * *************** 功能函式 ***************
          */

    /**
          * *************** 事件函式 ***************
          */

    /**
          * 事件 - 送出
          */
    var _submit = function(e) {
      return this;
    };

    /**
          * 事件 - 清除
          */
    var _clear = function(e) {
      return this;
    };

    /**
          * 事件 - 增加
          */
    var _add = function(e) {
      return this;
    };

    /**
          * *************** 私有函式 ***************
          */

    /**
          * *************** Run Constructor ***************
          */
    _construct();
  };

  // Give the init function the Object prototype for later instantiation
  obj.fn.init.prototype = obj.prototype;

  // Alias prototype function
  $.extend(obj, obj.fn);
})(window, document, $);
