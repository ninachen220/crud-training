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

    public function getAllAccount($col = 'a_account,a_name,a_sex,a_birth,a_mail,a_note')
    {
        return $this->db->select($col)->from($this->table)->where('status', '1')->get()->result_array();
    }

    public function post($data)
    {
        // 過濾可用欄位資料
        $data = array_intersect_key($data, array_flip($this->tableColumns));

        // 寫入資料表
        $res = $this->db->insert($this->table, $data);

        // 寫入成功時回傳寫入主鍵鍵值，失敗時回傳 0
        return $res ? $this->db->insert_id() : 0;
    }
}
