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
        //預設前端id對應的欄位名稱
        $default = [
            'accountId' => 'a_account',
            'accountName' => 'a_name',
            'accountSex' => 'a_sex',
            'accountBirth' => 'a_birth',
            'accountMail' => 'a_mail',
            'accountNote' => 'a_note'
        ];

        //整理對應資料
        foreach ($data as $key => $row) {
            $data[$default[$key]] = $row;
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
        //預設前端id對應的欄位名稱
        $default = [
            'accountSeq' => 'a_id',
            'editAccountId' => 'a_account',
            'editAccountName' => 'a_name',
            'editAccountSex' => 'a_sex',
            'editAccountBirth' => 'a_birth',
            'editAccountMail' => 'a_mail',
            'editAccountNote' => 'a_note'
        ];

        //整理對應資料
        foreach ($data as $key => $row) {
            $data[$default[$key]] = $row;
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
}
