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
        // 載入header
        $this->load->view('bootstrapHeader');
        // 載入view
        $this->load->view('crud_account');
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
        $result = [];

        try {
            // 行為分類
            switch ($method) {
                case 'POST':
                    // 新增一筆資料
                    $result = $this->addAccount($data);
                    break;
                case 'GET':
                    if (empty($id)) {
                        // 讀取全部資料
                        $result = $this->getAllAccount($_GET);
                    } else {
                        $result = $this->getSpesificAccount($id);
                    }
                    break;
                case 'PATCH':
                case 'PUT':
                    // 更新一筆資料
                    $result = $this->editAccount($data);
                    break;
                case 'DELETE':
                    if (empty($id)) {
                        if (!empty($data['a_id'])) {
                            //批次刪除
                            $result = $this->deleteSelectAccount($data);
                        } else {
                            // 錯誤
                            http_response_code(404);
                            echo '未選擇刪除帳號';
                            exit;
                        }
                    } else {
                        // 刪除一筆資料
                        $result = $this->deleteAccount($id);
                    }
                    break;
            }
        } catch (\Exception $e) {
            // 回傳http代碼
            http_response_code($e->getCode());

            // 回傳
            $result = [
                'type' => $e->getMessage(),
            ];
        }

        // 輸出JSON
        echo json_encode($result);
        exit;
    }

    /**
     * 獲取所有資料
     *
     * @param array $data 排序方式及欄位、搜尋文字
     * @return array
     */
    function getAllAccount($data)
    {
        // 讀取全部資料
        $data = $this->Crud_account_model->getAllAccount($data);

        // 回傳陣列資料
        return $data;
    }

    /**
     * 獲取單筆資料
     * 
     * @param int $id
     * @return array
     */
    public function getSpesificAccount($id)
    {
        $data['id'] = $id;
        // 讀取全部資料
        $data = $this->Crud_account_model->getAllAccount($data);

        // 建立輸出陣列
        $opt = [
            // 行為：載入單筆
            'type' => '載入單筆',
            // 前端AJAX傳過來的資料
            'data' => $data,
        ];

        // 回傳陣列資料
        return $opt;
    }

    /**
     * 新增一筆帳號
     *
     * @param array $data 帳號資料
     * @return array
     */
    public function addAccount($data)
    {
        //檢查資料格式
        $this->checkData($data);

        // 讀取全部資料
        $res = $this->Crud_account_model->addAccount($data);

        // 建立輸出陣列
        $opt = [
            // 行為：新增一筆
            'type' => '新增一筆',
            // 前端AJAX傳過來的資料
            'data' => $res,
        ];

        // 回傳陣列資料
        return $opt;
    }

    /**
     * 修改帳號
     *
     * @param array $data 帳號資料
     * @return array
     */
    function editAccount($data)
    {
        // 檢查資料格式
        $this->checkData($data);
        // 修改帳號資料
        $res = $this->Crud_account_model->editAccount($data);

        // 建立輸出陣列
        $opt = [
            // 行為：修改一筆
            'type' => '修改一筆',
            // 前端AJAX傳過來的資料
            'data' => $res,
        ];

        // 回傳陣列資料
        return $opt;
    }

    /**
     * 刪除帳號
     *
     * @param array $id 主鍵
     * @return array
     */
    public function deleteAccount($id)
    {
        // 刪除帳號資料
        $res = $this->Crud_account_model->deleteAccount($id);
        // 判斷是否有值
        if (!isset($res)) {
            // 拋出錯誤訊息
            throw new Exception("刪除失敗", 400);
        }

        // 建立輸出陣列
        $opt = [
            // 刪除一筆
            'type' => '刪除一筆',
            // 前端AJAX傳過來的資料
            'data' => $res,
        ];

        // 回傳陣列資料
        return $opt;
    }

    /**
     * 批次刪除帳號
     *
     * @param array $data 帳號主鍵
     * @return array
     */
    public function deleteSelectAccount($data)
    {
        // 刪除帳號資料
        $res = $this->Crud_account_model->deleteSelectAccount($data);

        // 判斷是否有資料
        if (!isset($res)) {
            // 拋出錯誤訊息
            throw new Exception("刪除失敗", 400);
        }

        // 建立輸出陣列
        $opt = [
            // 刪除一筆
            'type' => '刪除' . $res . '筆',
            // 前端AJAX傳過來的資料
            'data' => $res,
        ];

        // 回傳陣列資料
        return $opt;
    }

    /**
     * 檢查格式
     *
     * @param mixed $data 帳號資料
     */
    public function checkData($data)
    {
        // 預設回傳文字
        $default = [
            'a_name' => '姓名',
            'a_birth' => '生日',
            'a_account' => '帳號'
        ];
        // 錯誤代碼
        $code = 400;
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
                throw new Exception($default[$key] . '不能為空', $code);
            }
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
            // 排序方式
            $sortType = $_POST['order'];
            // 顯示筆數
            $length = $_POST['length'];
            // 當前頁碼
            $page = $_POST['page'];
            // 設定排序欄位對照map
            $map = [1 => 'a_account', 2 => 'a_name', 3 => 'a_sex', 4 => 'a_birth', 5 => 'a_mail', 6 => 'a_note'];
            // 排序欄位
            $text = $map[$_POST['text']];
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
                    't1' => array(
                        'key' => 't1',
                        'value' => '主鍵',
                        'col' => '1',
                        'row' => '1',
                        'style' => array(),
                        'class' => '',
                        'default' => '',
                        'list' => ''
                    ),
                    't2' => array(
                        'key' => 't2',
                        'value' => '帳號',
                        'col' => '1',
                        'row' => '1',
                        'style' => array(),
                        'class' => '',
                        'default' => '',
                        'list' => ''
                    ),
                    't3' => array(
                        'key' => 't3',
                        'value' => '姓名',
                        'col' => '1',
                        'row' => '1',
                        'style' => array(),
                        'class' => '',
                        'default' => '',
                        'list' => ''
                    ),
                    't4' => array(
                        'key' => 't4',
                        'value' => '性別',
                        'col' => '1',
                        'row' => '1',
                        'style' => array(),
                        'class' => '',
                        'default' => '',
                        'list' => ''
                    ),
                    't5' => array(
                        'key' => 't5',
                        'value' => '生日',
                        'col' => '1',
                        'row' => '1',
                        'style' => array(),
                        'class' => '',
                        'default' => '',
                        'list' => ''
                    ),
                    't6' => array(
                        'key' => 't6',
                        'value' => '信箱',
                        'col' => '2',
                        'row' => '1',
                        'style' => array(),
                        'class' => '',
                        'default' => '',
                        'list' => ''
                    ),
                    't7' => array(
                        'key' => 't7',
                        'value' => '備註',
                        'col' => '2',
                        'row' => '1',
                        'style' => array(),
                        'class' => '',
                        'default' => '',
                        'list' => ''
                    )
                )
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
                    'a_id' => array(
                        'key' => 'a_id',
                        'value' => '主鍵',
                        'col' => '1',
                        'row' => '1',
                        'style' => array(),
                        'class' => '',
                        'default' => '',
                        'list' => ''
                    ),
                    'a_account' => array(
                        'key' => 'a_account',
                        'value' => '帳號',
                        'col' => '1',
                        'row' => '1',
                        'style' => array(),
                        'class' => '',
                        'default' => '',
                        'list' => ''
                    ),
                    'a_name' => array(
                        'key' => 'a_name',
                        'value' => '姓名',
                        'col' => '1',
                        'row' => '1',
                        'style' => array(),
                        'class' => '',
                        'default' => '',
                        'list' => ''
                    ),
                    'a_sex' => array(
                        'key' => 'a_sex',
                        'value' => '性別',
                        'col' => '1',
                        'row' => '1',
                        'style' => array(),
                        'class' => '',
                        'default' => '',
                        'list' => ''
                    ),
                    'a_birth' => array(
                        'key' => 'a_birth',
                        'value' => '生日',
                        'col' => '1',
                        'row' => '1',
                        'style' => array(),
                        'class' => '',
                        'default' => '',
                        'list' => ''
                    ),
                    'a_mail' => array(
                        'key' => 'a_mail',
                        'value' => '信箱',
                        'col' => '2',
                        'row' => '1',
                        'style' => array(),
                        'class' => '',
                        'default' => '1',
                        'list' => ''
                    ),
                    'a_note' => array(
                        'key' => 'a_note',
                        'value' => '備註',
                        'col' => '2',
                        'row' => '1',
                        'style' => array(),
                        'class' => '',
                        'default' => '1',
                        'list' => ''
                    )
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
                )
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
            $sort = ['sortType' => 'ASC', 'text' => 'a_id','col'=>'a_id'];
            // 抓取的資料
            $dbData = $this->Crud_account_model->getAllAccount($sort);
            // 取出所有的主鍵
            $a_idArr = array_column($dbData, 'a_id');
            // 將Excel的資料提出做判斷

            foreach ($data as $key => $row) {
                // 檢查資料格式
                $this->checkData($row);
                // 判斷是否有a_id與資料庫a_id相同
                if (in_array($row['a_id'], $a_idArr) && $row['a_id'] !== '') {
                    // 有則修改資料
                    $res = $this->Crud_account_model->editAccount($row);
                } else {
                    // 無則新增帳號
                    $res = $this->Crud_account_model->addAccount($row);
                }
                // 判斷是否有資料
                if (!isset($res)) {
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
