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
  // 要補資料
  var name = '{name}';
  // Version
  // 要補資料
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
      accountApi: '/Crud_account/ajax',
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
    * 建構子
    */
    var _construct = function() {
      console.log('_construct');

      // 
      _initialize();
    };

    /**
     * 解構子
     */
    var _destruct = function() {};
    // 帳號所有資料
    var accountData = [];
    // 預設頁面
    var page = 1;
    // 預設顯示資料數量
    var perPageNum = $('#showData').val();

    // 建立內容
    var buildContent = function(page) {
      // 
      $('.table tbody').remove();

      // 
      perPageNum = $('#showData').val();

      // 
      var data = accountData.slice(perPageNum * page - perPageNum, perPageNum * page);

      // 建立變數
      var tmp, table, thead, tbody, tr, th, td;

      // 建立暫存容器
      tmp = $('<div></div>');
      // 建立tbody區塊資料
      tbody = $('<tbody></tbody>').appendTo(tmp);
      // 建立內容
      $.each(data, function(index1, value1) {
        //建立tr區塊資料
        tr = $('<tr data-id="' + value1.a_id + '"></tr>').appendTo(tbody);

        //建立checkbox
        td = $(
          '<td class = "checkBoxClick"><input type="checkbox" class="checkbox[' +
            value1.a_id +
            ']"></td>'
        ).appendTo(tr);

        // 遍歷data資料後放進td
        $.each(value1, function(index2, value2) {
          // 不放a_id進table裡顯示
          if (index2 !== 'a_id') {
            // 
            td = $('<td class="content">' + value2 + '</td>').appendTo(tr);
          }
        });

        // 修改按鈕
        td = $(
          '<td><button type="button" class="btn btn-outline-secondary edit"><i class="bi bi-pencil-fill"></i></button></td>'
        ).appendTo(tr);

        // 刪除按鈕
        td = $(
          '<td><button type="button" class="btn btn-outline-secondary delete"><i class="bi bi-trash3"></i></button></td>'
        ).appendTo(tr);
      });

      // 取得table元件
      table = $('.table');
      // 將暫存容器內容移至table元件
      tmp.children().appendTo(table);

      // 綁定刪除按鈕
      $('.delete').on('click', function() {
        // 
        deleteAccout($(this).parents('tr').data('id'));
      });

      // 綁定修改按鈕觸發modal
      $('.edit').on('click', function() {
        // 
        $('#editAccount').modal('show');

        // 這裡的 $(this)是什麼? 為什麼不是拿表單的資料?
        // 請改成拿取 modal 中的名字

        // 指定當前按鈕的tr
        var trData = $(this).parents('tr');

        // modal內放入tr的data-id(a_id)
        $('#accountSeq').val(trData.data('id'));

        // 將modal內的欄位id整理成array
        var arr = [];
        // 
        $('#editAccount').find('input,select,textarea').each(function() {
          // 
          arr.push($(this).attr('id'));
        });

        // 將放data-id的隱藏欄位移除
        // 請改成如果其他人表單添加欄位後，也不會影響移除
        arr.splice(1, 1);

        // 按照順序將tr內的td資料放入modal的欄位內
        trData.children('.content').each(function() {
          // td內的資料
          var dataText = $(this).text();

          // 判斷是否為性別欄位
          // 請改成如果其他人表單添加欄位後，也不會影響判斷
          if (arr[0] == 'editAccountSex') {
            // 修改td的資料與option相符
            switch (dataText) {
              case '男生':
                $('#' + arr[0]).val('M');
                break;
              case '女生':
                $('#' + arr[0]).val('F');
                break;
              default:
                $('#' + arr[0]).val('N');
                break;
            }
          } else {
            // 
            $('#' + arr[0]).val(dataText);
          }

          // 放完後的欄位從array中移除
          arr.splice(0, 1);
        });
      });
    };

    // 新增帳號
    var postAccout = function() {
      // 從form取得所有填寫的資料
      var data = $('#addAccountForm').serializeArray();

      // 二次確認是否新增帳號
      if (confirm('是否新增帳號?')) {
        // 
        var check = checkData(data, 'add');

        // 
        if (check) {
          // 發送新增的資料到controller
          $.ajax({
            method: 'POST',
            url: self._ajaxUrls.accountApi,
            dataType: 'json',
            data: {
              a_account: data[0].value,
              a_name: data[1].value,
              a_sex: data[2].value,
              a_birth: data[3].value,
              a_mail: data[4].value,
              a_note: data[5].value,
            },
          })
            .done(function(data) {
              // 顯示回傳的type
              alert(data.type);

              // 將modal欄位內的資料清除
              $('#addAccount').find('input,textarea').val('');
              // 
              $('#addAccount').find('select').val('N');

              // 將table的資料移除
              $('.table tbody').remove();

              // 隱藏modal
              $('#addAccount').modal('hide');

              // 重新獲取所有資料
              getAllAccount();
            })
            .fail(function(data) {
              // 
              alert(data.responseText);
            });
        } else {

          // 
          alert(check);
        }
      }
    };

    // 獲取所有資料庫資料
    var getAllAccount = function() {
      // 發送GET需求到Controller
      $.ajax({
        method: 'GET',
        url: self._ajaxUrls.accountApi,
        dataType: 'json',
      }).done(function(data) {
        // 資料放入變數中
        accountData = data.data;

        // 觸發變換顯示數量來顯示資料
        // 為什麼需要轉變數量?
        $('#showData').trigger('change');
      });
    };

    // 刪除帳號
    var deleteAccout = function(id) {
      // 二次確認是否刪除帳號
      if (confirm('是否刪除帳號?')) {
        // 發送刪除的ID到controller
        $.ajax({
          method: 'DELETE',
          url: self._ajaxUrls.accountApi + '/' + id,
          dataType: 'json',
          data: { status: 0 },
        })
          .done(function(data) {
            // 
            alert(data.type);

            // 
            $('.table tbody').remove();

            // 
            getAllAccount();
          })
          .fail(function(data) {
            // 
            alert(data.responseText);
          });
      }
    };

    /**
     * 刪除多筆帳號
     * 
     * @param array ids  待刪除 id 陣列
     */
    var deleteSelectAccount = function(id) {
      // 二次確認是否批次刪除帳號
      if (confirm('是否批次刪除帳號?')) {
        // 發送刪除的ID到controller
        $.ajax({
          method: 'DELETE',
          url: self._ajaxUrls.accountApi,
          dataType: 'json',
          data: { id },
        })
          .done(function(data) {
            // 
            alert(data.type);

            // 
            $('.table tbody').remove();

            // 
            getAllAccount();
          })
          .fail(function(data) {
            // 
            alert(data.responseText);
          });
      }
    };

    // 更新帳號
    var editAccout = function() {
      var data = $('#editAccountForm').serializeArray();
      // 二次確認是否更新帳號
      if (confirm('是否更新帳號?')) {
        // 
        var check = checkData(data, 'edit');

        // 
        if (check == true) {
          // 發送更新的資料到controller
          $.ajax({
            method: 'PUT',
            url: self._ajaxUrls.accountApi,
            dataType: 'json',
            data: {
              a_account: data[0].value,
              a_id: data[1].value,
              a_name: data[2].value,
              a_sex: data[3].value,
              a_birth: data[4].value,
              a_mail: data[5].value,
              a_note: data[6].value,
            },
          })
            .done(function(data) {
              // 顯示回傳的type
              alert(data.type);

              // 移除table的內容
              $('.table tbody').remove();

              // 將modal隱藏
              $('#editAccount').modal('hide');

              // 重新獲取所有資料
              getAllAccount();
            })
            .fail(function(data) {
              // 回傳錯誤訊息
              alert(data.responseText);
            });
        } else {
          // 
          alert(check);
        }
      }
    };

    // 確認資料格式
    var checkData = function(data, type) {
      // 預設狀態為true
      var status = true;
      // 
      var arr = ['帳號', 'a_id', '姓名', '性別', '生日', '信箱'];

      // 判斷字元是否為5~15個
      if (!/^[A-Za-z0-9]{5,15}$/.test(data[0].value)) {
        return '帳號限制為5~15個字元';
      }

      console.log(data);

      //因前端欄位不同下判定若為edit則為5
      // var num = 4;
      // if (type == 'edit') {
      //   num = 5;
      // }
      //判斷是否為正確的email格式
      if (!/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/.test(data[num].value)) {
        return '請輸入正確信箱格式';
      }

      // 判斷是否為正確的日期格式
      if (!/^[1-9]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/.test(data[num - 1].value)) {
        return '請輸入日期格式';
      }

      // 將data全部取出來判斷
      $.each(data, function(index, value) {
        // 
        var val = value.value;
        // 判斷val如果為空或為N
        if (val == '' || val == 'N') {
          //status回傳對應資料不能為空
          status = arr[index] + '不能為空';
          return false;
        }
      });

      // 
      return status;
    };

    // 顯示頁碼按鈕
    var addPageButton = function() {
      // 顯示的資料筆數
      perPageNum = $('#showData').val();
      // 資料總長度
      var dataLength = accountData.length;
      // 總頁數
      var pageTotal = Math.ceil(dataLength / perPageNum);

      // 
      var tmp = $('<div></div>');

      // 新增前一頁
      $('<li class="page-item"><a class="page-link">前一頁</a></li>').appendTo(tmp);

      // 判定資料數量是否小於每頁顯示數量
      if (dataLength < perPageNum) {
        $('<li class="page-item"><a class="page-link">1</a></li>').appendTo(tmp);
        // 如果總頁數大於五
      } else if (pageTotal > 5) {
        // 當前頁面如果大於1，開始的頁面按鈕則從前一個數字開始
        var end = pageTotal;

        // 
        start = page > 1 ? page - 1 : page;
        // 如果當前頁面小於總頁數-2，只顯示當前頁面左右兩個數字及最後一頁，中間審略不顯示
        if (page < pageTotal - 2) {
          end = page + 1;
          // 如果當前頁面等於總頁面-2，則顯示最後3個數字
        } else if (page == pageTotal - 2) {
          end = pageTotal;
        } else {
          // 
          start = pageTotal - 2;
          end = pageTotal;
        }

        // 頁碼按鈕
        for (var i = start; i <= end; i++) {
          // 設定當前頁碼顯示顏色
          if (i == page) {
            // 
            $('<li class="page-item"><a class="page-link" style ="font-weight:bolder;color:navy;">' + i + '</a></li>').appendTo(tmp);
          } else {
            // 
            $('<li class="page-item"><a class="page-link">' + i + '</a></li>').appendTo(tmp);
          }
        }
        // 顯示省略頁碼
        if (page < pageTotal - 2) {
          // 
          $('<li class="page-item"><a class="page-link">...</a></li>').appendTo(tmp);
          // 
          $('<li class="page-item"><a class="page-link">' + pageTotal + '</a></li>').appendTo(tmp);
        }
      } else {
        // 
        for (var i = 1; i <= pageTotal; i++) {
          // 
          $('<li class="page-item"><a class="page-link">' + i + '</a></li>').appendTo(tmp);
        }
      }

      // 新增下一頁
      $('<li class="page-item"><a class="page-link">下一頁</a></li>').appendTo(tmp);

      // 清除頁面按鈕並加入新的按鈕
      var pageNavBar = $('.pagination');
      // 
      $('.page-item').remove();
      // 
      tmp.children().appendTo(pageNavBar);

      // 
      pageButtonEvent();  

    };

    // 綁定按鈕事件
    var pageButtonEvent = function(){
      // 綁定分頁切換事件
      $('li').on('click', function() {
        // 當前頁面按鈕文字
        var pageText = $(this).find('a').text();
        // 資料總長度
        var dataLength = accountData.length;
        // 總頁數
        var pageTotal = Math.ceil(dataLength / perPageNum);

        /**
         * 改變當前所在分數數
         */
        // 判定數字是否需要更改頁碼
        if (
          // 當數字已經是第一頁
          (page == 1 && pageText == '前一頁') ||
          // 當頁碼已經是最後一頁
          (page == pageTotal && pageText == '下一頁') ||
          // 當點擊非數字li
          pageText == '...'
        ) {
          // 頁碼不變動
          page = page;
        } else {
          // 當文字為前一頁，頁碼-1
          if (pageText === '前一頁') {
            page = page - 1;
            // 當文字為下一頁，頁碼+1
          } else if (pageText === '下一頁') {
            page = page + 1;
            // 點擊數字則跳往當前頁碼
          } else {
            page = Number(pageText);
          }
        }

        // 
        if (pageTotal > 5) {
          // 重新設定動態頁碼按鈕
          addPageButton();
        }

        // 從新載入頁面資料
        buildContent(page);
      });
    }

    /**
     * 初始化
     */
    var _initialize = function() {
      console.log('_initialize');

      //獲取所有帳號
      getAllAccount();

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
      // 搜尋
      $('#search').on('click', getAllAccount);

      // 新增帳號
      $('.addAccount').on('click', postAccout);

      // 編輯帳號
      $('.editAccount').on('click', editAccout);

      // 顯示新增modal
      $('#addAccountModel').on('click', function() {
        $('#addAccount').modal('show');
      });

      // 批次刪除
      $('#oneByOneDelete').on('click', function() {
        // 
        var checkboxChecked = [];

        // 
        var checkbox = $(':checkbox');

        // 
        for (var i = 0; i < checkbox.length; i++) {
          // 
          if (checkbox[i].checked) {
            // 
            checkboxChecked.push(checkbox[i].className.replace(/[^0-9]/gi, ''));
          }
        }

        // 
        deleteSelectAccount(checkboxChecked);
      });

      // 變換顯示數量
      $('#showData').on('change', function() {
        // 
        page = 1;

        // 
        addPageButton();

        // 
        buildContent(page);
      });
    };

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
