<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 帳號資料庫存取範例
 */
class Crud_account extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        //建構子時，載入model
        $this->load->model('Crud_account_model');
    }

    public function index()
    {
        //載入header
        $this->load->view('bootstrapHeader');
        //載入view
        $this->load->view('crud_account');
    }

    public function ajax($id = null)
    {

        // 參數處理
        $method = strtoupper($_SERVER['REQUEST_METHOD']);
        // 此處應有對傳入參數$_POST消毒的處理，此處簡化
        //parse_str(file_get_contents('php://input'), $data);
        $data = $this->input->input_stream();
        // 行為分類
        switch ($method) {
            case 'POST':
                // 新增一筆資料
                $this->addAccount($data);
                break;
            case 'GET':

                if (empty($id)) {
                    //讀取全部資料
                    $this->getAllAccount($_GET);
                } else {
                    $id = UrlDecode(UrlDecode($id));
                    // 讀取特定資料
                    $this->getSpecificAccount($id, $_GET);
                }
                break;
            case 'PATCH':
            case 'PUT':
                // 更新一筆資料
                $this->editAccount($data);
                break;
            case 'DELETE':
                if (empty($id)) {

                    if (!empty($data['id'])) {
                        //批次刪除
                        $this->deleteSelectAccount($data);
                    } else {
                        // 錯誤
                        http_response_code(404);
                        echo '未選擇刪除帳號';
                        exit;
                    }
                } else {
                    // 刪除一筆資料
                    $this->deleteAccount($id, $data);
                }
                break;
        }
    }

    /**
     * 獲取所有資料
     *
     * @return json
     * 
     */
    function getAllAccount($data)
    {
        // 讀取全部資料
        $data = $this->Crud_account_model->getAllAccount($data);
        // 建立輸出陣列
        $opt = [
            // 行為：新增一筆
            'type' => '載入全部',
            // 前端AJAX傳過來的資料
            'data' => $data,
        ];

        // 輸出JSON
        echo json_encode($opt);
    }

    /**
     * 新增一筆帳號
     *
     * @return json
     */
    function addAccount($data)
    {
        try {
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

            // 輸出JSON
            echo json_encode($opt);
        } catch (\Exception $e) {
            http_response_code($e->getCode());
            echo $e->getMessage();
        }
    }

    /**
     * 修改帳號
     *
     * @return json
     */
    function editAccount($data)
    {
        try {
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

            // 輸出JSON
            echo json_encode($opt);
        } catch (\Exception $e) {
            http_response_code($e->getCode());
            echo $e->getMessage();
        }
    }

    /**
     * 刪除帳號
     *
     * @return json
     */
    function deleteAccount($id, $data)
    {
        try {
            // 刪除帳號資料
            $res = $this->Crud_account_model->deleteAccount($id, $data);
            if (!isset($res)) {
                throw new Exception("刪除失敗", 400);
            }
            // 建立輸出陣列
            $opt = [
                // 刪除一筆
                'type' => '刪除一筆',
                // 前端AJAX傳過來的資料
                'data' => $res,
            ];

            // 輸出JSON
            echo json_encode($opt);
        } catch (\Exception $e) {
            http_response_code($e->getCode());
            echo $e->getMessage();
        }
    }

    /**
     * 批次刪除帳號
     *
     * @return json
     */
    function deleteSelectAccount($data)
    {
        try {
            // 刪除帳號資料
            $res = $this->Crud_account_model->deleteSelectAccount($data);
            if (!isset($res)) {
                throw new Exception("刪除失敗", 400);
            }
            // 建立輸出陣列
            $opt = [
                // 刪除一筆
                'type' => '刪除' . $res . '筆',
                // 前端AJAX傳過來的資料
                'data' => $res,
            ];

            // 輸出JSON
            echo json_encode($opt);
        } catch (\Exception $e) {
            http_response_code($e->getCode());
            echo $e->getMessage();
        }
    }

    /**
     * 檢查格式
     *
     */
    function checkData($data)
    {
        // 預設回傳文字
        $default = [
            'a_name' => '姓名',
            'a_birth' => '生日',
            'a_account' => '帳號'
        ];
        $code = 400;
        foreach ($data as $key => $value) {
            $res = array_keys(array_flip($default), $key);
            // 判定是否為帳號
            if ($key == 'a_account' && !preg_match('/^[A-Za-z0-9]{5,15}$/', $value)) {
                throw new Exception('帳號限制為5~15個字元', $code);
            }
            // 判定是否為Email
            if ($key == 'a_mail' && !preg_match('/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/', $value)) {
                throw new Exception('請輸入正確信箱格式', $code);
            }
            // 判斷日期欄位是否為正確格式
            if ($key == 'a_birth' && !preg_match('/^[1-9]\d{3}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $value)) {
                throw new Exception('請輸入正確的日期格式', $code);
            }
            // 判斷欄位是否為空值
            if ($key !== 'a_note' && $value == '') {
                throw new Exception($default[$key] . '不能為空', $code);
            }
        }
    }

    /**
     * 查詢帳號
     *
     * @return json
     */
    function getSpecificAccount($id, $data)
    {
        try {
            // 查詢特定資料
            $res = $this->Crud_account_model->getSpecificAccount($id, $data);
            if (!isset($res)) {
                throw new Exception("查詢失敗", 400);
            }
            // 建立輸出陣列
            $opt = [
                // 查詢特定資料
                'type' => '查詢特定資料',
                // 前端AJAX傳過來的資料
                'data' => $res,
            ];

            // 輸出JSON
            echo json_encode($opt);
        } catch (\Exception $e) {
            http_response_code($e->getCode());
            echo $e->getMessage();
        }
    }
}
