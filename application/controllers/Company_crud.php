<?php

use function PHPSTORM_META\map;

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 公司資料庫存取
 */
class Company_crud extends CI_Controller
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        // 建構子
        parent::__construct();

        // 建構子時，載入model
        $this->load->model('Company_crud_model');
    }

    /**
     * 預設頁面
     */
    public function index()
    {
        //
        $data['type'] = $this->Company_crud_model->getAllType();
        // 載入view以及data
        $this->load->view('companyCrud', $data);
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
        $data = array_intersect_key($data, array_flip($this->Company_crud_model->tableColumns));
        try {
            // 行為分類
            switch ($method) {
                case 'POST':
                    // 檢查資料
                    $this->checkData($data);
                    // 新增資料
                    $this->_add($data);
                    break;
                case 'GET':
                    if (empty($id)) {
                        // 讀取全部資料
                        $this->_getAll($_GET);
                    } else {
                        // 獲取單筆資料
                        $this->_getOne($id);
                    }
                    break;
                case 'PATCH':
                case 'PUT':
                    // 檢查資料格式
                    $this->checkData($data);
                    // 更新資料
                    $this->_update($data);
                    break;
                case 'DELETE':
                    // 判定是否有參數$id
                    if (empty($id)) {
                        // 判定拋出資料是否有id
                        if (!empty($data['id'])) {
                            // 刪除選取資料
                            $this->_deleteSelect($data);
                        } else {
                            // 錯誤
                            http_response_code(404);
                            echo '未選擇刪除公司資料';
                            exit;
                        }
                    } else {
                        // 刪除單筆資料
                        $this->_delete($id);
                    }
                    break;
            }
        } catch (\Exception $e) {
            // 回傳http代碼
            http_response_code($e->getCode());
            // 回傳錯誤訊息
            echo $e->getMessage();
        }
    }

    /**
     * 新增公司資料資料
     * 
     * @param array $data 公司資料
     * @return json
     */
    private function _add($data)
    {
        // 新增一筆資料
        $result = $this->Company_crud_model->addCompany($data);

        // 建立輸出陣列
        $opt = [
            // 行為：新增一筆
            'type' => '新增一筆',
            // 前端AJAX傳過來的資料
            'data' => $result,
        ];

        // 輸出JSON
        echo json_encode($opt);
    }

    /**
     * 查詢公司全部資料
     *
     * @param array $data datatable參數
     * @return json
     */
    private function _getAll($data)
    {
        // 查詢全部資料
        $opt = $this->Company_crud_model->getAllCompany($data);
        // 輸出JSON
        echo json_encode($opt);
    }

    /**
     * 查詢公司單筆資料
     * 
     * @param array $id 公司資料主鍵
     * @return json
     */
    private function _getOne($id)
    {
        // 讀取單筆資料
        $result = $this->Company_crud_model->getSpesificCompany($id);

        // 建立輸出陣列
        $opt = [
            // 行為：載入單筆
            'type' => '載入單筆',
            // 前端AJAX傳過來的資料
            'data' => $result,
        ];

        // 輸出JSON
        echo json_encode($opt);
    }

    /**
     * 更新公司資料
     *
     * @param array $data 公司資料
     * @return json
     */
    private function _update($data)
    {
        // 更新一筆資料
        $result =  $this->Company_crud_model->editCompany($data);

        // 建立輸出陣列
        $opt = [
            // 行為：修改一筆
            'type' => '修改一筆',
            // 前端AJAX傳過來的資料
            'data' => $result,
        ];
        // 輸出JSON
        echo json_encode($opt);
    }

    /**
     * 更新已選擇公司資料
     *
     * @param array $data 公司資料
     * @return json
     */
    private function _deleteSelect($data)
    {
        //批次刪除
        $result = $this->Company_crud_model->deleteSelectCompany($data);

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

        // 輸出JSON
        echo json_encode($opt);
    }

    /**
     * 刪除單筆資料
     *
     * @param int $id 公司資料主鍵
     * @return json
     */
    private function _delete($id)
    {
        // 刪除一筆資料
        $result = $this->Company_crud_model->deleteCompany($id);

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
        // 輸出JSON
        echo json_encode($opt);
    }

    /**
     * 檢查格式
     * 
     * db 可加上 index email 等於 Unique
     * 
     * @param mixed $data 公司資料
     */
    public function checkData($data, $id = null)
    {
        // 預設回傳文字
        $default = [
            'name' => '公司名稱',
            'email' => '信箱',
            'contact' => '公司聯絡人',
            'scale' => '公司規模',
            't_id' => '公司類別'
        ];
        // 錯誤代碼
        $code = 400;
        // 預設訊息
        $message = "";
        // 判斷資料是否有符合格式
        foreach ($data as $key => $value) {
            // 判定是否為公司名稱
            // 如果有特殊符號超過11個 [0-9a-zA-Z]{11,}將不會作用
            if ($key == 'name' && !preg_match('/^[0-9a-zA-Z]{0,10}$/', $value)) {
                $message = '公司名稱長度不可超過10個字元';
            }
            // 判定是否為Email
            if ($key == 'email' && !preg_match('/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/', $value)) {
                $message = '請輸入正確信箱格式';
            }
            // 預設規模arr
            $scale = ['big', 'medium', 'small'];
            // 判斷欄位是否為空值
            if ($key == 'scale' && !in_array($value, $scale)) {
                $message = '請輸入正確公司規模格式';
            }

            // 判斷欄位是否為空值
            if ($key !== 'remark' && $key !== 'id' && $value == '') {
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
            $data = $this->Company_crud_model->getAllCompany($sort);
            // 判斷是否有資料
            if (!isset($data)) {
                // 拋出錯誤訊息
                throw new Exception("資料撈取失敗", 400);
            }
            // 預設陣列
            $Arr = array(
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
                    't1' => array_replace($Arr, array('key' => 't1', 'value' => '主鍵')),
                    't2' => array_replace($Arr, array('key' => 't2', 'value' => '公司名稱')),
                    't3' => array_replace($Arr, array('key' => 't3', 'value' => '公司聯絡人')),
                    't4' => array_replace($Arr, array('key' => 't4', 'value' => '信箱')),
                    't5' => array_replace($Arr, array('key' => 't5', 'value' => '公司規模')),
                    't6' => array_replace($Arr, array('key' => 't6', 'value' => '公司類別')),
                    't7' => array_replace($Arr, array('key' => 't7', 'value' => '備註')),
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
                    'id' => array_replace($Arr, array('key' => 'id', 'value' => '主鍵')),
                    'name' => array_replace($Arr, array('key' => 'name', 'value' => '公司名稱')),
                    'contact' => array_replace($Arr, array('key' => 'contact', 'value' => '公司聯絡人')),
                    'email' => array_replace($Arr, array('key' => 'email', 'value' => '信箱')),
                    'scale' => array_replace($Arr, array('key' => 'scale', 'value' => '公司規模')),
                    't_id' => array_replace($Arr, array('key' => 't_id', 'value' => '公司類別')),
                    'remark' => array_replace($Arr, array('key' => 'remark', 'value' => '備註')),
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

            // 獲取類別資料
            $type = $this->Company_crud_model->getAllType();

            // 變換key名稱與對應表相同
            $key = array("value", "text");
            foreach ($type as $index => $row) {
                $type[$index] = array_combine($key, $row);
            }
            // 建構外部對映表(下拉式選單)
            $listMap = array(
                'scale' => array(
                    array(
                        'value' => 'big',
                        'text' => '大型'
                    ),
                    array(
                        'value' => 'medium',
                        'text' => '中型'
                    ), array(
                        'value' => 'small',
                        'text' => '小型'
                    ),
                ),
                't_id' => $type
            );

            // 載入外部對映表
            $conf->setList($listMap);

            // 必要欄位設定 - 提供讀取資料時驗証用 - 有設定，且必要欄位有無資料者，跳出 - 因各版本excel對空列定義不同，可能編輯過列，就會產生沒有結尾的空列，導致在讀取excel時有讀不完狀況。
            $conf->setOption([], 'requiredField');
            // 匯出處理 - 建構匯出資料 - 手動處理
            $io->setData($data)->exportBuilder();
        } catch (\Exception $e) {
            // 回傳錯誤代碼
            http_response_code($e->getCode());
            // 回傳文字
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
            // 判斷資料是否為格式錯誤導致無法取出資料或資料為空
            if (empty($data)) {
                // 拋出錯誤訊息
                throw new \Exception("資料格式不正確或資料為空", 404);
            }
            // 預設抓取資料排序及排序欄位
            $sort = ['sortType' => 'ASC', 'text' => 'id', 'col' => 'id',];
            // 抓取的資料
            $dbData = $this->Company_crud_model->getAllCompany($sort);
            // 取出所有的主鍵
            $idArr = array_column($dbData, 'id');
            // 將Excel的資料遍歷
            foreach ($data as $key => $row) {
                $key = $key + 1;
                // 檢查資料格式
                $this->checkData($row, $key);
                // 判斷是否有id與資料庫id相同
                if (in_array($row['id'], $idArr) && $row['id'] !== '') {
                    // 有則修改資料
                    $res = $this->Company_crud_model->editCompany($row, $key);
                } else {
                    // 無則新增資料
                    $res = $this->Company_crud_model->addCompany($row, $key);
                }
                // 判斷是否有資料
                if (empty($res)) {
                    // 拋出錯誤訊息
                    throw new \Exception('主鍵:' . $row['id'] . '資料有誤', 400);
                }
            }
            // 拋出資料完成訊息
            throw new \Exception("資料匯入完成", 200);
        } catch (\Exception $e) {
            // 回傳錯誤代碼
            http_response_code($e->getCode());
            // 回傳文字
            echo $e->getMessage();
        }
    }
}
