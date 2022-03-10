<?php

use function PHPSTORM_META\map;

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 帳號資料庫存取
 */
class Crud_account extends CI_Controller
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        // 建構子
        parent::__construct();

        // 建構子時，載入model
        $this->load->model('Crud_account_model');
    }

    /**
     * 預設頁面
     */
    public function index()
    {
        // 載入部門名稱
        $data['dept'] = $this->Crud_account_model->getAllDept();
        // 載入header
        $this->load->view('bootstrapHeader');
        // 載入view以及data
        $this->load->view('crud_account', $data);
    }

    /**
     * 處理AJAX 方法及分流
     *
     * @param int $id 主鍵
     */
    public function ajax($id = null)
    {

        // 參數處理
        $method = strtoupper($_SERVER['REQUEST_METHOD']);

        // 取得傳入資料
        $data = $this->input->input_stream();

        // 合併可使用的欄位
        $data = array_intersect_key($data, array_flip($this->Crud_account_model->tableColumns));
        // 預設結果arr
        $opt = [];

        try {
            // 行為分類
            switch ($method) {
                case 'POST':
                    // 檢查資料
                    $this->checkData($data);
                    // 新增一筆資料
                    $result = $this->Crud_account_model->addAccount($data);

                    // 建立輸出陣列
                    $opt = [
                        // 行為：新增一筆
                        'type' => '新增一筆',
                        // 前端AJAX傳過來的資料
                        'data' => $result,
                    ];

                    break;
                case 'GET':
                    if (empty($id)) {
                        // 讀取全部資料
                        $opt = $this->Crud_account_model->getAllAccount($_GET);
                    } else {
                        // 讀取單筆資料
                        $result = $this->Crud_account_model->getSpesificAccount($id);

                        // 建立輸出陣列
                        $opt = [
                            // 行為：載入單筆
                            'type' => '載入單筆',
                            // 前端AJAX傳過來的資料
                            'data' => $result,
                        ];
                    }
                    break;
                case 'PATCH':
                case 'PUT':
                    // 檢查資料格式
                    $this->checkData($data);

                    // 更新一筆資料
                    $result =  $this->Crud_account_model->editAccount($data);

                    // 建立輸出陣列
                    $opt = [
                        // 行為：修改一筆
                        'type' => '修改一筆',
                        // 前端AJAX傳過來的資料
                        'data' => $result,
                    ];

                    break;
                case 'DELETE':
                    if (empty($id)) {
                        if (!empty($data['a_id'])) {
                            //批次刪除
                            $result = $this->Crud_account_model->deleteSelectAccount($data);

                            // 判斷是否有資料
                            if (!isset($result)) {
                                // 拋出錯誤訊息
                                throw new Exception("刪除失敗", 400);
                            }

                            // 建立輸出陣列
                            $opt = [
                                // 刪除一筆
                                'type' => '刪除' . $result . '筆',
                                // 前端AJAX傳過來的資料
                                'data' => $result,
                            ];

                            // 回傳陣列資料
                        } else {
                            // 錯誤
                            http_response_code(404);
                            echo '未選擇刪除帳號';
                            exit;
                        }
                    } else {
                        // 刪除一筆資料
                        $result = $this->Crud_account_model->deleteAccount($id);

                        // 判斷是否有值
                        if (!isset($result)) {
                            // 拋出錯誤訊息
                            throw new Exception("刪除失敗", 400);
                        }

                        // 建立輸出陣列
                        $opt = [
                            // 刪除一筆
                            'type' => '刪除一筆',
                            // 前端AJAX傳過來的資料
                            'data' => $result,
                        ];
                    }
                    break;
            }
            // 輸出JSON
            echo json_encode($opt);
            exit;
        } catch (\Exception $e) {
            // 回傳http代碼
            http_response_code($e->getCode());
            // 回傳錯誤訊息
            echo $e->getMessage();
        }
    }

    /**
     * 檢查格式
     *
     * @param mixed $data 帳號資料
     */
    public function checkData($data, $id = null)
    {
        // 預設回傳文字
        $default = [
            'a_name' => '姓名',
            'a_birth' => '生日',
            'a_account' => '帳號',
            'a_sex' => '性別',
            'a_mail' => '信箱',
            'd_id' => '部門'
        ];
        // 錯誤代碼
        $code = 400;
        // 預設訊息
        $message = "";
        // 判斷資料是否有符合格式
        foreach ($data as $key => $value) {
            // 判定是否為帳號
            if ($key == 'a_account' && !preg_match('/^[A-Za-z0-9]{5,15}$/', $value)) {
                $message = '帳號限制為5~15個字元';
            }
            // 判定是否為Email
            if ($key == 'a_mail' && !preg_match('/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/', $value)) {
                $message = '請輸入正確信箱格式';
            }
            // 判斷日期欄位是否為正確格式
            if ($key == 'a_birth' && !preg_match('/^[1-9]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $value)) {
                $message = '請輸入正確的日期格式';
            }
            // 判斷欄位是否為空值
            if ($key !== 'a_note' && $key !== 'a_id' && $value == '') {
                $message = $default[$key] . '不能為空';
            }
        }
        // 如果錯誤訊息不為空且$id有值傳進來
        if (isset($id) && !empty($message)) {
            $message = '第' . ($id + 1) . '筆資料' . $message;
        }
        // 拋出錯誤訊息
        if (!empty($message)) {
            throw new Exception($message, $code);
        }
    }

    /**
     * 輸出資料庫檔案
     * 
     * @param array $_POST
     */
    public function exportData()
    {
        try {
            // 判定是否有獲取值
            if (empty($_GET)) {
                // 拋出錯誤訊息
                throw new Exception("未獲取參數", 400);
            }
            // 排序方式
            $sortType = $_GET['order'];
            // 顯示筆數
            $length = $_GET['length'];
            // 當前頁碼
            $page = $_GET['page'];
            // 設定排序欄位對照map
            $map = explode(",", $_GET['ids']);
            // 排序欄位
            $text = $map[$_GET['text']];
            // 撈取資料方式
            $sort = ['sortType' => $sortType, 'text' => $text, 'length' => $length, 'page' => $page];
            // 撈取資料庫資料
            $data = $this->Crud_account_model->getAllAccount($sort);
            // 判斷是否有資料
            if (!isset($data)) {
                // 拋出錯誤訊息
                throw new Exception("資料撈取失敗", 400);
            }
            // 結構定義-複雜模式

            // 預設標題
            $title1Arr = array(
                'key' => '',
                'value' => '',
                'col' => '1',
                'row' => '1',
                'style' => array(),
                'class' => '',
                'default' => '',
                'list' => ''
            );
            // 標題1
            $title1 = array(
                'config' => array(
                    'type' => 'title',
                    'name' => 'title1',
                    'style' => array(
                        'font-size' => '16'
                    ),
                    'class' => ''
                ),
                'defined' => array(
                    't1' => array_replace($title1Arr, array('key' => 't1', 'value' => '主鍵')),
                    't2' => array_replace($title1Arr, array('key' => 't2', 'value' => '帳號')),
                    't3' => array_replace($title1Arr, array('key' => 't3', 'value' => '姓名')),
                    't4' => array_replace($title1Arr, array('key' => 't4', 'value' => '性別')),
                    't5' => array_replace($title1Arr, array('key' => 't5', 'value' => '部門')),
                    't6' => array_replace($title1Arr, array('key' => 't6', 'value' => '生日')),
                    't7' => array_replace($title1Arr, array('key' => 't7', 'value' => '信箱')),
                    't8' => array_replace($title1Arr, array('key' => 't8', 'value' => '備註'))
                )
            );
            // 預設內容陣列
            $contentArr = array(
                'key' => '',
                'value' => '',
                'col' => '1',
                'row' => '1',
                'style' => array(),
                'class' => '',
                'default' => '',
                'list' => ''
            );
            // 內容
            $content = array(
                'config' => array(
                    'type' => 'content',
                    'name' => 'content',
                    'style' => array(),
                    'class' => ''
                ),
                'defined' => array(
                    'a_id' => array_replace($contentArr, array('key' => 'a_id', 'value' => '主鍵')),
                    'a_account' => array_replace($contentArr, array('key' => 'a_account', 'value' => '帳號')),
                    'a_name' => array_replace($contentArr, array('key' => 'a_name', 'value' => '姓名')),
                    'a_sex' => array_replace($contentArr, array('key' => 'a_sex', 'value' => '性別')),
                    'd_id' => array_replace($contentArr, array('key' => 'd_id', 'value' => '部門')),
                    'a_birth' => array_replace($contentArr, array('key' => 'a_birth', 'value' => '生日')),
                    'a_mail' => array_replace($contentArr, array('key' => 'a_mail', 'value' => '信箱')),
                    'a_note' => array_replace($contentArr, array('key' => 'a_note', 'value' => '備註'))
                )
            );

            // IO物件建構
            $io = new \marshung\io\IO();

            // 手動建構相關物件
            $io->setConfig()
                ->setBuilder()
                ->setStyle();

            // 載入外部定義
            $conf = $io->getConfig()
                ->setTitle($title1)
                ->setContent($content);

            // 獲取部門資料
            $dept = $this->Crud_account_model->getAllDept();
            // 變換key名稱與對應表相同
            $key = array("value", "text");
            foreach ($dept as $index => $row) {
                $dept[$index] = array_combine($key, $row);
            }
            // 建構外部對映表(下拉式選單)
            $listMap = array(
                'a_sex' => array(
                    array(
                        'value' => 'M',
                        'text' => '男生'
                    ),
                    array(
                        'value' => 'F',
                        'text' => '女生'
                    )
                ),
                'd_id' => $dept
            );

            // 載入外部對映表
            $conf->setList($listMap);

            // 必要欄位設定 - 提供讀取資料時驗証用 - 有設定，且必要欄位有無資料者，跳出 - 因各版本excel對空列定義不同，可能編輯過列，就會產生沒有結尾的空列，導致在讀取excel時有讀不完狀況。
            $conf->setOption([
                'a_account'
            ], 'requiredField');
            // 匯出處理 - 建構匯出資料 - 手動處理
            $io->setData($data)->exportBuilder();
        } catch (\Exception $e) {
            http_response_code($e->getCode());
            echo $e->getMessage();
        }
    }

    /**
     * 匯入資料
     */
    public function importData()
    {
        try {
            // IO物件建構
            $io = new \marshung\io\IO();
            // 匯入處理 - 取得匯入資料
            $data = $io->import($builder = 'Excel', $fileArgu = 'fileupload');
            // 預設抓取資料排序及排序欄位
            $sort = ['sortType' => 'ASC', 'text' => 'a_id', 'col' => 'a_id',];
            // 抓取的資料
            $dbData = $this->Crud_account_model->getAllAccount($sort);
            // 取出所有的主鍵
            $a_idArr = array_column($dbData, 'a_id');
            // 將Excel的資料提出做判斷
            foreach ($data as $key => $row) {
                // 檢查資料格式
                $this->checkData($row, $key);
                // 判斷是否有a_id與資料庫a_id相同
                if (in_array($row['a_id'], $a_idArr) && $row['a_id'] !== '') {
                    // 有則修改資料
                    $res = $this->Crud_account_model->editAccount($row, $key);
                } else {
                    // 無則新增帳號
                    $res = $this->Crud_account_model->addAccount($row, $key);
                }
                // 判斷是否有資料
                if (empty($res)) {
                    // 拋出錯誤訊息
                    throw new \Exception('主鍵:' . $row['a_id'] . '資料有誤', 400);
                }
            }
            // 拋出資料完成訊息
            throw new \Exception("資料匯入完成", 200);
        } catch (\Exception $e) {
            http_response_code($e->getCode());
            echo $e->getMessage();
        }
    }
}
