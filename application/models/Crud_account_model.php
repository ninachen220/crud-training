<?php

/**
 * CRUD資料管理Model
 */
class Crud_account_model extends CI_Model
{

    /**
     * 資料表名稱
     */
    protected $table = "account_info";

    /**
     * 欄位資料
     */
    protected $tableColumns = [
        'a_id',
        'a_account',
        'a_name',
        'a_sex',
        'a_birth',
        'a_mail',
        'a_note',
        'status'
    ];

    public function __construct()
    {
        parent::__construct();

        // 載入資料連線
        $this->load->database();
    }

    //搜尋全部帳號
    public function getAllAccount($col = 'a_id,a_account,a_name,a_sex,a_birth,a_mail,a_note')
    {
        //預設性別名稱轉換
        $dataSexDefault = [
            'N' => '未選擇',
            'M' => '男生',
            'F' => '女生'
        ];

        //執行sql後獲取的資料
        $data = $this->db->select($col)->from($this->table)->where('status', '1')->get()->result_array();

        //整理資料成對應名稱
        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['a_sex'] = $dataSexDefault[$data[$i]['a_sex']];
        }

        return $data;
    }

    //新增帳號
    public function addAccount($data)
    {
        try {
            $this->checkData($data,'add');
        } catch (\Exception $e) {
            echo 'Message:' .$e->getMessage();
        }
        //設定status = 1
        $data['status'] = 1;

        // 過濾可用欄位資料
        $data = array_intersect_key($data, array_flip($this->tableColumns));

        // 寫入資料表
        $res = $this->db->insert($this->table, $data);

        // 寫入成功時回傳寫入主鍵鍵值，失敗時回傳 0
        return $res ? $this->db->insert_id() : 0;
    }

    /**
     * 更新資料 - 從主鍵
     *
     * @return int
     */
    public function editAccount($data)
    {
        try {
            $this->checkData($data,'edit');
        } catch (\Exception $e) {
            echo 'Message:' .$e->getMessage();
        }
        // 過濾可用欄位資料
        $data = array_intersect_key($data, array_flip($this->tableColumns));

        $res = 0;

        // 檢查有無主鍵
        if (isset($data['a_id'])) {
            // 取出主鍵值並移除$data中主鍵欄位
            $a_id = $data['a_id'];
            unset($data['a_id']);

            // 更新資料 - 成功時回傳主鍵鍵值，失敗時回傳 0
            $res = $this->db->where('a_id', $a_id)->update($this->table, $data) ? $a_id : 0;
        } else {
            // 報錯-沒有主鍵欄位
            throw new Exception('沒有主鍵欄位: a_id', 400);
        }

        return $res;
    }

    function checkData($data,$type)
    {
        if($type =='add'){
            unset($data['a_id']);
        }

        foreach($data as $key =>$value){
            if($key =='a_account'){
                if(!preg_match('/^[A-Za-z0-9]{5,15}$/', $value)){
                    throw new Exception("帳號限制為5~15個字元");
                }
            }else if($key =='a_id'){
                if($value ==''){
                    throw new Exception('沒有主鍵欄位: a_id', 400);
                }

            }else if($key =='a_name'){
                if($value ==''){
                    throw new Exception('姓名不能為空');
                }
                
            }else if($key =='a_sex'){
                if($value ==''){
                    throw new Exception('請選擇性別');
                }
                
            }else if($key =='a_birth'){
                if($value ==''){
                    throw new Exception('請選擇生日');
                }
                
            }else if($key =='a_mail'){
                if(!preg_match('/\w[-\w.+]*@([A-Za-z0-9][-A-Za-z0-9]+\.)+[A-Za-z]{2,14}/', $value)){
                    throw new Exception("請輸入正確信箱格式");
                }
                
            }

        }
    }
}
