<?php


defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 帳號資料庫存取範例
 */
class Crud_account extends CI_Controller
{

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        // 
        parent::__construct();

        // 建構子時，載入model
        $this->load->model('Crud_account_model');
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function index()
    {
        // 載入header
        $this->load->view('bootstrapHeader');
        // 載入view
        $this->load->view('crud_account');
    }

    /**
     * Undocumented function
     *
     * @param [type] $id
     * @return void
     */
    public function ajax($id = null)
    {
        // 參數處理
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        // 取得傳入資料
        $data = $this->input->input_stream();

        // 清理傳入資料
        $data = array_intersect_key($data, array_flip($this->Crud_account_model->tableColumns));

        // 
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
                        //讀取全部資料
                        $result = $this->getAllAccount();
                    } else {
                        // 讀取一筆資料
                        // $this->Crud_account_model->getSpecificAccount($data);
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
                            $result = [
                                // 行為：No Delete ID
                                'type' => 'No Delete ID',
                            ];
                        }
                    } else {
                        // 刪除一筆資料
                        $result = $this->deleteAccount($id, $data);
                    }
                    break;
            }
        } catch (\Exception $e) {
            // 
            http_response_code($e->getCode());

            // 
            $result = [
                'type' => $e,
            ];
        }

        // 輸出JSON
        echo json_encode($result);
        exit;
    }

    /**
     * 獲取所有資料
     *
     * @return array
     */
    public function getAllAccount()
    {
        // 讀取全部資料
        $data = $this->Crud_account_model->getAllAccount();

        // 建立輸出陣列
        $opt = [
            // 行為：新增一筆
            'type' => '載入全部',
            // 前端AJAX傳過來的資料
            'data' => $data,
        ];

        // 
        return $opt;
    }

    /**
     * 新增一筆帳號
     *
     * @param mixed $name
     * @return array
     */
    public function addAccount($data)
    {
        // 
        $this->_checkData($data, 'add');

        // 讀取全部資料
        $res = $this->Crud_account_model->addAccount($data);

        // 建立輸出陣列
        $opt = [
            // 行為：新增一筆
            'type' => '新增一筆',
            // 前端AJAX傳過來的資料
            'data' => $res,
        ];

        // 
        return $opt;
    }

    /**
     * 修改帳號
     *
     * @param mixed $name
     * @return array
     */
    public function editAccount($data)
    {
        // 
        $this->_checkData($data, 'edit');

        // 修改帳號資料
        $res = $this->Crud_account_model->editAccount($data);

        // 建立輸出陣列
        $opt = [
            // 行為：修改一筆
            'type' => '修改一筆',
            // 前端AJAX傳過來的資料
            'data' => $res,
        ];

        // 
        return $opt;
    }

    /**
     * Undocumented function
     *
     * @param [type] $id
     * @param [type] $data
     * @return void
     */
    public function deleteAccount($id, $data)
    {
        // 刪除帳號資料
        $res = $this->Crud_account_model->deleteAccount($id, $data);
        
        // 
        if (!isset($res)) {
            // 
            throw new Exception("刪除失敗", 400);
        }

        // 建立輸出陣列
        $opt = [
            // 刪除一筆
            'type' => '刪除一筆',
            // 前端AJAX傳過來的資料
            'data' => $res,
        ];

        // 
        return $opt;
    }

    /**
     * Undocumented function
     *
     * @param [type] $data
     * @return void
     */
    public function deleteSelectAccount($data)
    {
        // 刪除帳號資料
        $res = $this->Crud_account_model->deleteSelectAccount($data['id']);
        
        // 
        if (!isset($res)) {
            // 
            throw new Exception("刪除失敗", 400);
        }

        // 建立輸出陣列
        $opt = [
            // 刪除一筆
            'type' => '刪除' . $res . '筆',
            // 前端AJAX傳過來的資料
            'data' => $res,
        ];

        // 
        return $opt;
    }

    /**
     * Undocumented function
     *
     * @param [type] $data
     * @param [type] $type
     * @return void
     */
    private function _checkData($data, $type)
    {
        // 如果狀態為新增
        if ($type == 'add') {
            // 移除a_id
            unset($data['a_id']);
        }

        // 預設回傳文字
        $default = [
            'a_name' => '姓名',
            'a_birth' => '生日',
            'a_account' => '帳號'
        ];

        // 
        $message = '';

        // 
        $code = 400;

        // 
        foreach ($data as $key => $value) {
            // 
            $res = array_keys(array_flip($default), $key);

            // switch() {
            //     case ''：
            // }
            // 判定是否為帳號
            if ($key == 'a_account') {
                // 
                if (!preg_match('/^[A-Za-z0-9]{5,15}$/', $value)) {
                    $message = '帳號限制為5~15個字元';
                }
                // 判定是否為主鍵
            } else if ($key == 'a_id') {
                // 
                $message = empty($value) ? '沒有主鍵欄位' : '';

                // 判定是否為性別
            } else if ($key == 'a_sex') {

                // 那一開始填入就要必定填入
                
                // 判定是否為Email
            } else if ($key == 'a_mail') {
                // 
                if (!preg_match('/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/', $value)) {
                    $message = '請輸入正確信箱格式';
                }
                // 判斷日期欄位是否為正確格式
            } else if ($key == 'a_birth') {
                // 
                if (!preg_match('/^[1-9]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $value)) {
                    $message = '請輸入正確的日期格式';
                }
            }

            // 選定欄位是否為空值
            if ($key !== 'a_note' && $value == '') {
                $message = $res[0] . '不能為空';
            }
        }

        // 判定資料正確回傳true
        if ($message === "") {
            return true;
        } else {
            // 
            throw new Exception($message, $code);
        }
    }
}
