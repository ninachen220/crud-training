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

    //新增帳號
    var postAccout = function() {
      //從form取得所有填寫的資料
      var data = $('form').serializeArray();

      //二次確認是否新增帳號
      if (confirm('是否新增帳號?')) {
        //發送新增的資料到controller
        $.ajax({
          method: 'POST',
          url: self._ajaxUrls.accountApi,
          dataType: 'json',
          data: { data },
        }).done(function(data) {
          $('.table tbody').remove();
          $('#addAccount').modal('hide');
          getAllAccount();
        });
      }
    };

    //獲取所有資料庫資料
    var getAllAccount = function() {
      //發送GET需求到Controller
      $.ajax({
        method: 'GET',
        url: self._ajaxUrls.accountApi,
        dataType: 'json',
      }).done(function(data) {
        /**
           * 陣列資料配合$.each建立表格
           */
        // 建立變數
        var tmp, table, thead, tbody, tr, th, td;

        // 建立暫存容器
        tmp = $('<div></div>');

        // 建立tbody區塊資料
        tbody = $('<tbody></tbody>').appendTo(tmp);

        // 建立內容
        $.each(data.data, function(index1, value1) {
          //建立tr區塊資料
          tr = $('<tr></tr>').appendTo(tbody);
          //遍歷data資料後放進td
          $.each(value1, function(index2, value2) {
            td = $('<td>' + value2 + '</td>').appendTo(tr);
          });
        });

        // 取得table元件
        table = $('.table');
        // 將暫存容器內容移至table元件
        tmp.children().appendTo(table);
      });
    };

    //刪除帳號
    var postAccout = function() {
      var id = $('#hiddenId').val();
      //二次確認是否刪除帳號
      if (confirm('是否刪除帳號?')) {
        //發送刪除的ID到controller
        $.ajax({
          method: 'DELETE',
          url: self._ajaxUrls.accountApi,
          dataType: 'json',
          data: { id: id },
        }).done(function(data) {
          alert(data);
          $('.table tbody').remove();
          $('#addAccount').modal('hide');
          getAllAccount();
        });
      }
    };

    //更新帳號
    var postAccout = function() {
      var data = $('form').serializeArray();
      //二次確認是否更新帳號
      if (confirm('是否更新帳號?')) {
        //發送更新的資料到controller
        $.ajax({
          method: 'PUT',
          url: self._ajaxUrls.accountApi,
          dataType: 'json',
          data: { data },
        }).done(function(data) {
          alert(data);
          $('.table tbody').remove();
          $('#addAccount').modal('hide');
          getAllAccount();
        });
      }
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

      $('#search').on('click', getAllAccount);
      $('.addAccount').on('click', postAccout);
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
