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
      accountApi: '/js_training/ajax',
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

    /**
        * 初始化
        */
    var _initialize = function() {
      console.log('_initialize');

      // 讀取tag:h1內容字串
      var text = $('h1').text();
      // 在DevTools的console介面中印出
      console.log('----');
      console.log('tag:h1的內容為：' + text);
      console.log('----');

      // 寫入「測試字串」至id="ctrl-message"的元件
      $('#ctrl-message').text('測試字串');

      // 變更class="ctrl-btn"按鈕的元件的顏色為btn-danger
      $('.ctrl-btn').removeClass('btn-warning').addClass('btn-danger');

      // 移除按鈕
      // $('.ctrl-btn').remove();

      /**
 * 建立表格
 */
      // // 建立變數
      // var tmp, table, thead, tbody, tr, th, td;
      // // 建立暫存容器
      // tmp = $('<div></div>');
      // // 建立thead區塊資料，並放到tmp之中
      // thead = $('<thead></thead>').appendTo(tmp);
      // // 建立tr標籤，並放到thead之中
      // tr = $('<tr></tr>').appendTo(thead);
      // // 建立th標籤，並放到tr之中
      // th = $('<th></th>').appendTo(tr);
      // // 寫入th內容文字
      // th.text('Count');
      // th = $('<th></th>').appendTo(tr);
      // th.text('Name');
      // th = $('<th>Gender</th>').appendTo(tr);

      // // 建立tbody區塊資料
      // tbody = $('<tbody></tbody>').appendTo(tmp);
      // tr = $('<tr></tr>').appendTo(tbody);
      // td = $('<td></td>').appendTo(tr);
      // td.text('1');
      // td = $('<td></td>').appendTo(tr);
      // td.text('Joe');
      // td = $('<td>male</td>').appendTo(tr);

      // // 取得table元件
      // table = $('.ctrl-table');
      // // 將暫存容器內容移至table元件
      // tmp.children().appendTo(table);

      // // 印出table元件HTML碼
      // console.log(table.html());

      /**
 * 新增一筆
 */
      $.ajax({
        // 傳送方法
        method: 'POST',
        // 目標網址
        url: self._ajaxUrls.accountApi,
        // 傳送資料
        data: { name: 'John', location: 'Boston' },
        // 回傳資料格式
        dataType: 'json',
      })
        .done(function(data) {
          // 處理回傳資料 - 印出json字串
          $('<div>' + JSON.stringify(data) + '</div>').appendTo($('.ctrl-message'));
          // 輸出至console
          console.log(data);
        })
        .fail(function(jqXHR) {
          // 處理回傳資料
          $('<div>' + jqXHR.responseText + '</div>').appendTo($('.ctrl-message'));
          console.log(jqXHR);
        });

      /**
 * 讀取全部
 */
      $.ajax({
        method: 'GET',
        url: self._ajaxUrls.accountApi,
        dataType: 'json',
      }).done(function(data) {
        // 處理回傳資料 - 印出json字串
        $('<div>' + JSON.stringify(data) + '</div>').appendTo($('.ctrl-message'));

        /**
       * 陣列資料配合$.each建立表格
       */
        // 建立變數
        var tmp, table, thead, tbody, tr, th, td;
        // 建立暫存容器
        tmp = $('<div></div>');
        // 建立thead區塊資料
        thead = $('<thead></thead>').appendTo(tmp);
        // 建立tbody區塊資料
        tbody = $('<tbody></tbody>').appendTo(tmp);

        // 建立標題
        tr = $('<tr class="bg-info"></tr>').appendTo(thead);
        $.each(data.head, function(index, value) {
          th = $('<th>' + value + '</th>').appendTo(tr);
        });

        // 建立內容
        $.each(data.data, function(index1, value1) {
          tr = $('<tr></tr>').appendTo(tbody);
          $.each(value1, function(index2, value2) {
            td = $('<td>' + value2 + '</td>').appendTo(tr);
          });
        });

        // 取得table元件
        table = $('.ctrl-table');
        // 將暫存容器內容移至table元件
        tmp.children().appendTo(table);
      });

      /**
 * 讀取一筆
 */
      $.ajax({
        method: 'GET',
        // 讀取id為3的資料
        url: self._ajaxUrls.accountApi + '/3',
        dataType: 'json',
      }).done(function(data) {
        // 處理回傳資料
        $('<div>' + JSON.stringify(data) + '</div>').appendTo($('.ctrl-message'));
      });

      /**
 * 更新一筆
 */
      $.ajax({
        method: 'PUT',
        url: self._ajaxUrls.accountApi,
        data: { name: 'John', location: 'Boston' },
        dataType: 'json',
      }).done(function(data) {
        // 處理回傳資料
        $('<div>' + JSON.stringify(data) + '</div>').appendTo($('.ctrl-message'));
      });
      /**
 * 刪除錯誤 No Delete ID
 */
      $.ajax({
        method: 'DELETE',
        url: self._ajaxUrls.accountApi,
        dataType: 'json',
      })
        .done(function(data) {
          // 處理回傳資料
          $('<div>' + JSON.stringify(data) + '</div>').appendTo($('.ctrl-message'));
        })
        .fail(function(jqXHR) {
          // 錯誤處理
          $('<div>' + jqXHR.responseText + '</div>').appendTo($('.ctrl-message'));
          console.log(jqXHR);
        });

      /**
 * 刪除一筆
 */
      $.ajax({
        method: 'DELETE',
        // 刪除id為2的資料
        url: self._ajaxUrls.accountApi + '/2',
        dataType: 'json',
      })
        .done(function(data) {
          // 處理回傳資料
          $('<div>' + JSON.stringify(data) + '</div>').appendTo($('.ctrl-message'));
        })
        .fail(function(jqXHR) {
          // 錯誤處理
          $('<div>' + jqXHR.responseText + '</div>').appendTo($('.ctrl-message'));
          console.log(jqXHR);
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

      /**
          * 事件 - 增加
          */

      // 按下按鈕時，變更tag:h1文字為「** Javascript Training **」
      $('.ctrl-btn').on('click', function() {
        $('h1').text('** Javascript Training **');
      });

      // 偵測滑鼠座標
      $(document).on('mousemove', function(ev) {
        // 取得事件物件
        ev = ev || window.event;
        // 如果事件物件含有座標資訊
        if (ev.pageX || ev.pageY) {
          // 在ctrl-message元件中印出座標資訊
          $('.ctrl-message').html('X: ' + ev.pageX + '<br>Y: ' + ev.pageY);
        }
      });

      /**
          * 事件 - 清除規
          */
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
