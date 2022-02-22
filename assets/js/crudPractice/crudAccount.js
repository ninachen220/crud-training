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
      $('.table tbody').remove();
      perPageNum = $('#showData').val();
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
        deleteAccout($(this).parents('tr').data('id'));
      });
      // 綁定修改按鈕觸發modal
      $('.edit').on('click', function() {
        $('#addAccount').modal('show');
        $('.modal-title').text('修改帳號');

        // 解綁新增事件
        $('.account_button').unbind();
        // 重新綁定編輯事件
        $('.account_button').on('click', editAccout);

        // 指定當前按鈕的tr
        var trData = $(this).parents('tr');

        // modal內放入tr的data-id(a_id)
        $('#accountSeq').val(trData.data('id'));

        // 將modal內的欄位id整理成array
        var arr = [];
        $('#addAccount').find('input,select,textarea').each(function() {
          arr.push($(this).attr('id'));
        });

        // 將放data-id的隱藏欄位移除
        arr.splice(1, 1);

        // 按照順序將tr內的td資料放入modal的欄位內
        trData.children('.content').each(function() {
          // td內的資料
          var dataText = $(this).text();

          // 判斷是否為性別欄位
          if (arr[0] == 'accountSex') {
            // 修改td的資料與option相符
            switch (dataText) {
              case '男生':
                $('#' + arr[0]).val('M');
                break;
              case '女生':
                $('#' + arr[0]).val('F');
                break;
              default:
                $('#' + arr[0]).val('');
                break;
            }
          } else {
            $('#' + arr[0]).val(dataText);
          }

          // 放完後的欄位從array中移除
          arr.splice(0, 1);
        });
      });
    };
    // 新增帳號
    var postAccout = function() {
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
            },
          })
            .done(function(data) {
              //顯示回傳的type
              alert(data.type);

              //將modal欄位內的資料清除
              $('#addAccount').find('input,textarea').val('');
              $('#addAccount').find('select').val('');

              //將table的資料移除
              $('.table tbody').remove();

              //隱藏modal
              $('#addAccount').modal('hide');

              //重新獲取所有資料
              getAllAccount();
            })
            .fail(function(data) {
              alert(data.responseText);
            });
        }
      } catch (error) {
        alert(error.message);
      }
    };

    // 獲取所有資料庫資料
    var getAllAccount = function() {
      var sortType;
      // 獲取正序排序欄位並排除th空格
      var text = $('.bi-sort-down').parents('th').text().replace(/ /g, '');
      // 判斷是否有欄位為正序
      if (text == '') {
        sortType = 'DESC';
        text = $('.bi-sort-up').parents('th').text().replace(/ /g, '');
      } else {
        sortType = 'ASC';
      }
      switch (text) {
        case '帳號':
          text = 'a_account';
          break;
        case '姓名':
          text = 'a_name';
          break;
        case '性別':
          text = 'a_sex';
          break;
        case '生日':
          text = 'a_birth';
          break;
        case '信箱':
          text = 'a_mail';
          break;
        case '備註':
          text = 'a_note';
          break;
      }
      // 防止中文字串亂碼加密
      text = encodeURI(encodeURI(text));
      // 發送GET需求到Controller
      $.ajax({
        method: 'GET',
        url: self._ajaxUrls.accountApi + '?sortType=' + sortType + '&text=' + text,
        dataType: 'json',
      }).done(function(data) {
        // 資料放入變數中
        accountData = data.data;
        // 觸發變換顯示數量來顯示資料
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
            alert(data.type);
            $('.table tbody').remove();
            getAllAccount();
          })
          .fail(function(data) {
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
            alert(data.type);
            $('.table tbody').remove();
            getAllAccount();
          })
          .fail(function(data) {
            alert(data.responseText);
          });
      }
    };

    // 更新帳號
    var editAccout = function() {
      try {
        // 二次確認是否更新帳號
        if (confirm('是否更新帳號?')) {
          // fomr的所有資料
          var data = $('#addAccountForm').serializeArray();
          checkData(data);
          var a_account = $('#accountId').val();
          var a_id = $('#accountSeq').val();
          var a_name = $('#accountName').val();
          var a_sex = $('#accountSex').val();
          var a_birth = $('#accountBirth').val();
          var a_mail = $('#accountMail').val();
          var a_note = $('#accountNote').val();
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
            },
          })
            .done(function(data) {
              // 顯示回傳的type
              alert(data.type);

              // 移除table的內容
              $('.table tbody').remove();

              // 將modal隱藏
              $('#addAccount').modal('hide');

              // 重新獲取所有資料
              getAllAccount();
            })
            .fail(function(data) {
              // 回傳錯誤訊息
              alert(data.responseText);
            });
        }
      } catch (error) {
        alert(error.message);
      }
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
            case 'accountName':
              var text = '姓名';
              break;
          }
          throw new Error(text + '不能為空', 404);
        }
      });
    };

    // 新增頁碼按鈕
    var addPageButton = function() {
      // 顯示的資料筆數
      perPageNum = $('#showData').val();
      // 資料總長度
      var dataLength = accountData.length;
      // 總頁數
      var pageTotal = Math.ceil(dataLength / perPageNum);

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
        if (page > 1) {
          var start = page - 1;
        } else {
          start = page;
        }
        // 如果當前頁面小於總頁數-2，只顯示當前頁面左右兩個數字及最後一頁，中間審略不顯示
        if (page < pageTotal - 2) {
          end = page + 1;
          // 如果當前頁面等於總頁面-2，則顯示最後3個數字
        } else if (page == pageTotal - 2) {
          end = pageTotal;
        } else {
          start = pageTotal - 2;
          end = pageTotal;
        }
        // 頁碼按鈕
        for (var i = start; i <= end; i++) {
          // 設定當前頁碼顯示顏色
          if (i == page) {
            $(
              '<li class="page-item"><a class="page-link" style ="font-weight:bolder;color:navy;">' +
                i +
                '</a></li>'
            ).appendTo(tmp);
          } else {
            $('<li class="page-item"><a class="page-link">' + i + '</a></li>').appendTo(tmp);
          }
        }
        // 顯示省略頁碼
        if (page < pageTotal - 2) {
          $('<li class="page-item"><a class="page-link">...</a></li>').appendTo(tmp);
          $('<li class="page-item"><a class="page-link">' + pageTotal + '</a></li>').appendTo(tmp);
        }
      } else {
        for (var i = 1; i <= pageTotal; i++) {
          $('<li class="page-item"><a class="page-link">' + i + '</a></li>').appendTo(tmp);
        }
      }

      // 新增下一頁
      $('<li class="page-item"><a class="page-link">下一頁</a></li>').appendTo(tmp);

      // 清除頁面按鈕並加入新的按鈕
      var pageNavBar = $('.pagination');
      $('.page-item').remove();
      tmp.children().appendTo(pageNavBar);

      pageButtonEvent();
    };

    // 綁定按鈕事件
    var pageButtonEvent = function() {
      $('li').on('click', function() {
        // 當前頁面按鈕文字
        var pageText = $(this).find('a').text();
        // 資料總長度
        var dataLength = accountData.length;
        // 總頁數
        var pageTotal = Math.ceil(dataLength / perPageNum);

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

        // 重新設定動態頁碼按鈕
        if (pageTotal > 5) {
          addPageButton();
        }
        // 從新載入頁面資料
        buildContent(page);
      });
    };

    // 搜尋帳號
    var getSpecificAccount = function(sortType, text) {
      // 搜尋欄文字
      var searchText = $('#searchPlace').val();

      // 防止中文字亂碼加密
      searchText = encodeURI(encodeURI(searchText));
      sortType = sortType;
      // 防止中文字亂碼加密
      text = encodeURI(encodeURI(text));
      $.ajax({
        method: 'GET',
        url:
          self._ajaxUrls.accountApi +
          '/' +
          searchText +
          '?sortType= ' +
          sortType +
          '&text= ' +
          text,
        dataType: 'json',
      })
        .done(function(data) {
          accountData = data.data;
          $('#showData').trigger('change');
        })
        .fail(function(data) {
          // 回傳錯誤訊息
          alert(data.responseText);
        });
    };

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
      // 顯示新增modal
      $('#addAccountModel').on('click', function() {
        // 解綁送出按鈕的事件
        $('.account_button').unbind();

        // 重新綁新的事件
        $('.account_button').on('click', postAccout);
        $('#addAccount').modal('show');

        // 將內容清空
        $('#addAccount').find('select,textarea,input').val('');
        $('.modal-title').text('新增帳號');
      });

      // 批次刪除
      $('#oneByOneDelete').on('click', function() {
        var checkboxChecked = [];
        var checkbox = $(':checkbox');
        for (var i = 0; i < checkbox.length; i++) {
          if (checkbox[i].checked) {
            checkboxChecked.push(checkbox[i].className.replace(/[^0-9]/gi, ''));
          }
        }
        deleteSelectAccount(checkboxChecked);
      });

      // 變換顯示數量
      $('#showData').on('change', function() {
        page = 1;
        addPageButton();
        buildContent(page);
      });

      // 搜尋帳號
      $('#search').on('click', function() {
        var sortType;
        // 預設正序的標題為預設文字，並且移除空格
        var text = $('.bi-sort-down').parents('th').text().replace(/ /g, '');
        // 判斷是否有選擇正序的標題
        if (text == '') {
          sortType = 'DESC';
          text = $('.bi-sort-up').parents('th').text().replace(/ /g, '');
        } else {
          sortType = 'ASC';
        }

        // 判斷排序的欄位
        switch (text) {
          case '帳號':
            text = 'a_account';
            break;
          case '姓名':
            text = 'a_name';
            break;
          case '性別':
            text = 'a_sex';
            break;
          case '生日':
            text = 'a_birth';
            break;
          case '信箱':
            text = 'a_mail';
            break;
          case '備註':
            text = 'a_note';
            break;
        }
        getSpecificAccount(sortType, text);
      });

      // 排序選擇
      $('thead th').on('click', function() {
        var text, setClass, sortType;
        // 找出點選做排序的i class
        setClass = $(this).find('i').attr('class');
        // 變換class
        switch (setClass) {
          // 如果是箭頭朝下，則換成上箭頭，並排序法選擇為倒序
          case 'bi bi-sort-down':
            setClass = 'bi bi-sort-up';
            sortType = 'DESC';
            break;
          // 如果是箭頭朝上，則換成下箭頭，並排序法選擇為正序
          case 'bi bi-sort-up':
            setClass = 'bi bi-sort-down';
            sortType = 'ASC';
            break;
          // 如果是沒有箭頭，則換成下箭頭，並排序法選擇為正序
          case 'bi bi-filter-left':
            setClass = 'bi bi-sort-down';
            sortType = 'ASC';
            break;
        }
        // 判斷是否有點選有i標籤的th
        if ($(this).find('i').length >= 1) {
          $(this).parents('thead').find('i').attr('class', 'bi bi-filter-left');
          // 排除th字串的空格
          text = $(this).text().replace(/ /g, '');
          switch (text) {
            case '帳號':
              text = 'a_account';
              break;
            case '姓名':
              text = 'a_name';
              break;
            case '性別':
              text = 'a_sex';
              break;
            case '生日':
              text = 'a_birth';
              break;
            case '信箱':
              text = 'a_mail';
              break;
            case '備註':
              text = 'a_note';
              break;
          }
          // 回傳排序方法及排序欄位
          getSpecificAccount(sortType, text);
        }
        // 設定th i標前的class
        $(this).find('i').attr('class', setClass);
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
