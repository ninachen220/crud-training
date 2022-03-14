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
  var name = 'company_info';
  // Version
  var version = '1';
  // Default options
  var defaults = {};

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
      // AJAX
      companyApi: '/Company_crud/ajax',
      // 資料匯入
      importApi: '/Company_crud/importData',
      // 資料匯出
      exportApi: '/Company_crud/exportData',
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

    /**
     * 建構子
     */
    var _construct = function() {
      console.log('_construct');
      _initialize();
      // 
      myTable;
    };

    // 
    var closeTitle = '關閉';
    var checkOk = 'OK';
    var checkCurrect = '確認';
    var checkTitle = '確認訊息';

    /**
     * 建立datatable
     */
    var myTable = $('#companyTable').DataTable({
      // 
      processing: true,
      serverSide: true,
      // 
      ajax: {
        url: self._ajaxUrls.companyApi,
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
          // 從後端撈取資料
          sentCompany('編輯資料', 'edit', rowData['id']);
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
          deleteCompany(rowData['id']);
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
        style: '',
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
        { data: 'name' },
        { data: 'contact' },
        { data: 'email' },
        {
          data: 'scale',
          // 
          render: function(data, type, full, meta) {
            var text;
            switch (data) {
              case 'big':
                text = '大型';
                break;
              case 'medium':
                text = '中型';
                break;
              case 'small':
                text = '小型';
                break;
              default:
                break;
            }
            return text;
          },
        },
        { data: 't_id' },
        { data: 'remark' },
        {
          // 不排序
          orderable: false,
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
          // 不排序
          orderable: false,
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

    // 新增公司資料
    var addCompany = function() {
      try {
        // modal-body內的form所有欄位的資料
        var data = $('.modal-body').find('#companyForm').serializeArray();
        // 前端判斷是否符合格式
        checkData(data);
        // 將dialog內的資料撈出並放入變數中
        var name = $('.modal-body').find('#companyName').val();
        var contact = $('.modal-body').find('#companycContact').val();
        var email = $('.modal-body').find('#companycMail').val();
        var scale = $('.modal-body').find('#companycScale').val();
        var type = $('.modal-body').find('#companyType').val();
        var remark = $('.modal-body').find('#companycRemark').val();
        // 
        $.ajax({
          method: 'POST',
          url: self._ajaxUrls.companyApi,
          dataType: 'json',
          data: {
            name: name,
            contact: contact,
            email: email,
            scale: scale,
            t_id: type,
            remark: remark,
          },
        })
          .done(function(data) {
            // 顯示回傳訊息
            alert(data.type);
            // 重新渲染datatable
            myTable.draw();
            // 關閉所有dialog
            BootstrapDialog.closeAll();
          })
          .fail(function(data) {
            // 顯示錯誤訊息
            wrongInfo(data.responseText);
          });
      } catch (error) {
        // 顯示錯誤訊息
        wrongInfo(error.message);
      }
    };

    // 獲取單筆公司資料
    var getSpesificCompany = function(modalBody, id) {
      // 搜尋單筆的公司資料
      $.ajax({
        method: 'GET',
        url: self._ajaxUrls.companyApi + '/' + id,
        dataType: 'json',
        async: false,
      }).done(function(data) {
        data = data.data[0];
        // 將資料放入dialog input中
        modalBody.find('#companyName').val(data['name']);
        modalBody.find('#companySeq').val(data['id']);
        modalBody.find('#companycContact').val(data['contact']);
        modalBody.find('#companycMail').val(data['email']);
        modalBody.find('#companycScale').val(data['scale']);
        // 如果公司類別為0，則改為null
        var type = data['t_id'] == 0 ? null : data['t_id'];
        modalBody.find('#companyType').val(type);
        modalBody.find('#companycRemark').val(data['remark']);
      });
    };

    // 更新公司資料
    var editCompany = function() {
      try {
        // form的所有資料
        var data = $('.modal-body').find('#companyForm').serializeArray();
        // 確認格式
        checkData(data);
        var name = $('.modal-body').find('#companyName').val();
        var id = $('.modal-body').find('#companySeq').val();
        var contact = $('.modal-body').find('#companycContact').val();
        var email = $('.modal-body').find('#companycMail').val();
        var scale = $('.modal-body').find('#companycScale').val();
        var type = $('.modal-body').find('#companyType').val();
        var remark = $('.modal-body').find('#companycRemark').val();
        // 發送更新的資料到controller
        $.ajax({
          method: 'PUT',
          url: self._ajaxUrls.companyApi,
          dataType: 'json',
          data: {
            name: name,
            id: id,
            contact: contact,
            email: email,
            scale: scale,
            t_id: type,
            remark: remark,
          },
        })
          .done(function(data) {
            alert(data.type);
            // 顯示回傳的type
            var res = data.data[0];

            // 呼叫datatable
            var table = $('#companyTable').DataTable();

            // 更新特定欄位的資料
            table.row().data(res).draw();

            // 將所有dialog關閉
            BootstrapDialog.closeAll();
          })
          .fail(function(data) {
            // 回傳錯誤訊息
            wrongInfo(data.responseText);
          });
      } catch (error) {
        // 顯示錯誤訊息
        wrongInfo(error.message);
      }
    };

    // 刪除單筆公司資料
    var deleteCompany = function(id) {
      var check = false;
      // 二次確認是否刪除公司資料
      BootstrapDialog.show({
        title: checkTitle,
        message: '是否刪除公司資料?',
        buttons: [
          // 確認送出
          {
            label: checkCurrect,
            cssClass: 'btn-primary',
            action: function(dialogItself) {
              // 關閉dialog二次確認
              dialogItself.close();
              // 發送刪除的ID
              $.ajax({
                method: 'DELETE',
                url: self._ajaxUrls.companyApi + '/' + id,
                dataType: 'json',
                async: false,
              })
                .done(function(data) {
                  // 顯示回傳訊息
                  correctInfo(data.type);
                  // 呼叫datatable
                  var table = $('#companyTable').DataTable();
                  // 重新整理datatable
                  table.row().draw();
                })
                .fail(function(data) {
                  // 回傳錯誤訊息
                  $.rustaMsgBox({ content: data.responseText });
                });
            },
          },
          // 取消並關閉
          {
            label: closeTitle,
            action: function(dialogItself) {
              dialogItself.close();
            },
          },
        ],
      });
      return check;
    };

    // 刪除多筆公司資料
    var deleteSelectAccount = function(id) {
      // 二次確認是否批次刪除公司資料
      BootstrapDialog.show({
        title: checkTitle,
        message: '是否確定批次刪除公司資料?',
        buttons: [
          {
            //確認送出
            label: checkCurrect,
            cssClass: 'btn-primary',
            action: function(dialogItself) {
              // 關閉二次確認視窗
              dialogItself.close();
              // 發送刪除的IDs
              $.ajax({
                method: 'DELETE',
                url: self._ajaxUrls.companyApi,
                dataType: 'json',
                data: { id: id },
              })
                .done(function(data) {
                  // 顯示回傳訊息
                  correctInfo(data.type);
                  // 呼叫datatable
                  var table = $('#companyTable').DataTable();
                  // 將已選擇的行移除
                  table.rows({ selected: true }).remove().draw();
                })
                .fail(function(data) {
                  // 顯示錯誤訊息
                  $.rustaMsgBox({ content: data.responseText });
                });
            },
          },
          {
            // 取消並關閉
            label: closeTitle,
            action: function(dialogItself) {
              // 關閉當前dialog
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

        // 判斷欄位資料為空
        if (name != 'companycRemark' && name != 'companySeq' && value == '') {
          switch (name) {
            case 'companyName':
              var text = '公司名稱';
              break;
            case 'companycMail':
              var text = '信箱';
              break;
            case 'companycContact':
              var text = '公司聯絡人';
              break;
            case 'companycScale':
              var text = '公司規模';
              break;
            case 'companyType':
              var text = '公司類別';
              break;
          }
          throw new Error(text + '不能為空', 404);
        }
        // 判斷公司名稱超過10個字元
        // 如果有特殊符號超過11個 [0-9a-zA-Z]{11,}將不會作用
        if (name == 'companyName' && !/^[0-9a-zA-Z]{0,10}$/.test(value)) {
          throw new Error('公司名稱長度不可超過10個字元', 404);
        }

        // 判斷是否為正確的email格式
        if (
          name == 'companycMail' &&
          !/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test(value)
        ) {
          throw new Error('請輸入正確信箱格式', 404);
        }
        // 預設規模arr
        var scale = ['big', 'medium', 'small'];

        // 判斷公司規模是否為特定格式
        if (name == 'companycScale' && $.inArray(value, scale) == -1) {
          throw new Error('請輸入正確公司規模格式', 404);
        }
      });
    };

    // 回傳正確的資料狀態
    var correctInfo = function(data) {
      // 用dialog顯示回傳的type
      BootstrapDialog.show({
        // 設定標題
        title: '訊息',
        // 設定內文
        message: data,
        // 設定按鈕
        buttons: [
          {
            label: checkOk,
            action: function(dialogItself) {
              dialogItself.close();
            },
          },
        ],
      });
    };

    // 顯示錯誤訊息
    var wrongInfo = function(error) {
      BootstrapDialog.show({
        title: '錯誤訊息',
        message: error,
        buttons: [
          {
            label: checkOk,
            action: function(dialogItself) {
              // 關閉訊息視窗
              dialogItself.close();
            },
          },
        ],
      });
    };

    // 編輯與新增dialog
    var sentCompany = function(title, method, id) {
      BootstrapDialog.show({
        title: title,
        message: $('#companyForm').clone(),
        buttons: [
          {
            label: '送出',
            action: function(dialogItself) {
              switch (method) {
                case 'edit':
                  editCompany();
                  break;
                case 'add':
                  addCompany();
                  break;

                default:
                  break;
              }
            },
          },
          {
            label: '取消',
            action: function(dialogItself) {
              // 關閉訊息視窗
              dialogItself.close();
            },
          },
        ],
        onshow: function(dialogRef) {
          if (method == 'edit') {
            // 抓取 Modal 中的表單位置
            var modalBody = dialogRef.getModalBody();

            // 讀取修改資料
            getSpesificCompany(modalBody, id);
          }
        },
      });
    };

    // 匯入資料
    var importData = function(event, form) {
      try {
        // 取消表單預設提交
        event.preventDefault();

        // 上傳檔案
        var file = $('#importData')[0].files[0];

        // 沒有選擇檔案則回傳錯誤訊息
        if (!file) {
          throw new Error('尚未選擇資料', 400);
        }
        // 建立新的formData物件
        var formData = new FormData(form);

        // 將file加入formData裡
        formData.append('fileupload', file);
        // 拋出Ajax
        $.ajax({
          url: self._ajaxUrls.importApi,
          type: 'POST',
          contentType: false,
          processData: false,
          data: formData,
        })
          .done(function(data) {
            // 顯示回傳訊息
            correctInfo(data);

            // 重新載入資料
            myTable.draw();
          })
          .fail(function(data) {
            // 顯示錯誤訊息
            wrongInfo(data.responseText);
          });
      } catch (error) {
        wrongInfo(error);
      }
    };

    // 匯出資料
    var exportData = function() {
      var table = $('#companyTable').DataTable();
      // 取得頁面資料
      var info = table.page.info();
      // 取得顯示筆數
      var length = info.length;
      // 取得當前頁碼
      var page = info.page;
      // 取得排序方式
      var order = table.order()[0];
      // 取得資料鍵值順序，並將資料用逗號相隔為字串
      var ids = table.columns().dataSrc().join(',');
      // 網頁開起並塞入參數
      window.open(
        self._ajaxUrls.exportApi +
          '?length=' +
          length +
          '&page=' +
          page +
          '&order=' +
          order[1] +
          '&text=' +
          order[0] +
          '&ids=' +
          ids
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

      _evenBind();
    };

    /**
     * 事件綁定
     */
    var _evenBind = function() {
      console.log('_evenBind');

      // 顯示新增
      $('#addCompany').on('click', function() {
        sentCompany('新增資料', 'add', null);
      });

      // 批次刪除
      $('#rowDelete').on('click', function() {
        // 呼叫datatable
        var table = $('#companyTable').DataTable();
        // 設定空陣列
        const arr = [];
        // 找尋有選擇的欄位並加入陣列中;
        table.rows({ selected: true }).data().each(function(rows, num) {
          arr[num] = rows.id;
        });
        // 批次刪除
        deleteSelectAccount(arr);
      });

      // 匯出資料
      $('#export').on('click', function() {
        exportData();
      });
    };

    _construct();
  };

  // Give the init function the Object prototype for later instantiation
  obj.fn.init.prototype = obj.prototype;

  // Alias prototype function
  $.extend(obj, obj.fn);
})(window, document, $);
