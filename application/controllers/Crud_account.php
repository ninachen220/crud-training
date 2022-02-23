<?php
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
     * @param mixed $name
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
                    }
                    break;
                case 'PATCH':
                case 'PUT':
                    // 更新一筆資料
                    $result = $this->editAccount($data);
                    break;
                case 'DELETE':
                    if (empty($id)) {

                        if (!empty($data['id'])) {
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
                        $result = $this->deleteAccount($id, $data);
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
     * @param mixed $data 排序方式、排序欄位、搜尋文字
     * @return array
     */
    function getAllAccount($data)
    {
        // 讀取全部資料
        $data = $this->Crud_account_model->getAllAccount($data);

        // 建立輸出陣列
        $opt = [
            // 行為：載入全部
            'type' => '載入全部',
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
     * @param mixed $data 帳號資料
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
     * @param mixed $id 帳號id
     * @return array
     */
    function deleteAccount($id)
    {
        // 刪除帳號資料
        $res = $this->Crud_account_model->deleteAccount($id);

        // 判斷是否有回傳資料
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
     * @param mixed $data 帳號a_id
     * @return array
     */
    function deleteSelectAccount($data)
    {
        // 刪除帳號資料
        $res = $this->Crud_account_model->deleteSelectAccount($data);

        // 判斷是否有回傳值
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
    function checkData($data)
    {
        // 預設回傳文字
        $default = [
            'a_name' => '姓名',
            'a_birth' => '生日',
            'a_account' => '帳號'
        ];
        // 回傳錯誤代碼
        $code = 400;
        // 判斷資料是否有錯誤
        foreach ($data as $key => $value) {
            // 設定回傳訊息
            $message = '';

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
            if ($key !== 'a_note' && $value == '') {
                $message = $default[$key] . '不能為空';
            }
            if (!empty($message)) {
                throw new Exception($message, $code);
            }
        }
    }

    /**
     * 輸出資料庫檔案
     */
    function exportData()
    {
        try {
            // 預設撈取檔案順序為主鍵正序
            $sort = ['sortType' => 'ASC', 'text' => 'a_id'];
            // 撈取資料庫資料
            $data = $this->Crud_account_model->getAllAccount($sort);
            // 如果沒有資料
            if (!isset($data)) {
                // 拋出錯誤訊息
                throw new Exception("資料撈取失敗", 400);
            }

            // 結構定義-簡易模式
            $defined = array(
                'a_account' => '帳號',
                'a_name' => '姓名',
                'a_sex' => '性別',
                'a_birth' => '生日',
                'a_mail' => '信箱',
                'a_note' => '備註'
            );

            // IO物件建構
            $io = new \marshung\io\IO();

            // 手動建構相關物件
            $io->setConfig()
                ->setBuilder()
                ->setStyle();

            // 載入外部定義
            $conf = $io->getConfig()
                ->setTitle($defined)
                ->setContent($defined);

            // 必要欄位設定 - 提供讀取資料時驗証用 - 有設定，且必要欄位有無資料者，跳出 - 因各版本excel對空列定義不同，可能編輯過列，就會產生沒有結尾的空列，導致在讀取excel時有讀不完狀況。
            $conf->setOption([
                'a_id'
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
    function import()
    {
        // IO物件建構
        $io = new \marshung\io\IO();
        // 匯入處理 - 取得匯入資料
        $data = $io->import($builder = 'Excel', $fileArgu = 'fileupload');
        // 取得匯入config名子
        $configName = $io->getConfig()->getOption('configName');

        echo 'Config Name = ' . $configName . "<br>\n";
        echo 'Data = ';
        var_export($data);
    }
}
