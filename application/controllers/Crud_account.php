<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 部門資料庫存取範例
 * 
 * 本Controller提供Model Dept_info_model 使用範例，請在觀察輸出時，也同步觀察資料庫中的資料
 * 
 * @author Mars.Hung 2020-02-29
 */
class Crud_account extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

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
                $this->post($data);
                break;
            case 'GET':
                if (empty($id)) {
                    // 
                    $this->getAllAccount();
                } else {
                    // 讀取一筆資料
                    // $this->Crud_account_model->getSpecificAccount($data);
                }
                break;
            case 'PATCH':
            case 'PUT':
                // 更新一筆資料
                // $this->Crud_account_model->updateAccount($data, $id);
                break;
            case 'DELETE':
                if (empty($id)) {
                    // 錯誤
                    http_response_code(404);
                    echo 'No Delete ID';
                    exit;
                } else {
                    // 刪除一筆資料
                    // $this->Crud_account_model->deleteAccount($data, $id);
                }
                break;
        }
    }

    /**
     * 獲取所有資料
     *
     * @return json
     */
    function getAllAccount()
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

        // 輸出JSON
        echo json_encode($opt);
    }

    /**
     * 新增一筆帳號
     *
     * @return json
     */
    function post($formData)
    {
        $default = [
            'accountId' => 'a_account',
            'accountName' => 'a_name',
            'accountSex' => 'a_sex',
            'accountBirth' => 'a_birth',
            'accountMail' => 'a_mail',
            'accountNote' => 'a_note'
        ];
        $data = [];
       foreach($formData as $row){
           foreach($row as $row2){
            $data[$default[$row2['name']]] = $row2['value'];
           }
       }
        // 讀取全部資料
        $res = $this->Crud_account_model->post($data);

        // 建立輸出陣列
        $opt = [
            // 行為：新增一筆
            'type' => '新增一筆',
            // 前端AJAX傳過來的資料
            'data' => $res,
        ];

        // 輸出JSON
        echo json_encode($opt);
    }
}
