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
    public $tableColumns = [
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

    /**
     * 搜尋全部帳號、查詢帳號
     * 
     * @param array $data 排序方式及欄位、搜尋文字
     * @return array
     */
    public function getAllAccount($data)
    {
        // 預設欄位
        $col = 'a_id,a_account,a_name,a_sex,a_birth,a_mail,a_note';

        // 預設性別名稱轉換
        $dataSexDefault = [
            '' => '未選擇',
            'M' => '男生',
            'F' => '女生'
        ];

        // 寫入select sql
        $this->db->select($col)->from($this->table);

        // 如果有搜尋文字
        if (isset($data['id'])) {
            $this->db->where('a_id', $data['id']);
        }

        // 寫入條件sql並回傳資料
        $data = $this->db->where('status', '1')->get()->result_array();
        return $data;
    }

    /**
     * 新增帳號
     *
     * @param array $data 帳號資料
     * @return int
     */
    public function addAccount($data)
    {
        // 設定狀態為存在
        $data['status'] = 1;

        // 寫入資料表
        $res = $this->db->insert($this->table, $data);

        // 寫入成功時回傳寫入主鍵鍵值，失敗時回傳 0
        return $res ? $this->db->insert_id() : 0;
    }

    /**
     * 更新資料 - 從主鍵
     *
     * @param array $data 帳號資料
     * @return int
     */
    public function editAccount($data)
    {
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
    /**
     * 刪除帳號 - 從主鍵
     *
     * @param int $id 帳號主鍵
     * @return int
     */
    public function deleteAccount($id)
    {
        // 預設狀態為刪除
        $data = [
            'status' => 0
        ];

        // 刪除條件
        $this->db->where_in('a_id', $id);
        // 成功時回傳主鍵鍵值
        return $this->db->update($this->table, $data);
    }

    /**
     * 批次刪除帳號 - 從主鍵
     *
     * @param array $data 帳號主鍵
     * @return int
     */
    public function deleteSelectAccount($data)
    {
        // 將資料整理成批次要的樣式
        foreach ($data['a_id'] as $key => $value) {
            $res[] = array(
                'a_id' => $value,
                'status' => 0
            );
        }
        // 成功時回傳主鍵鍵值
        return $this->db->update_batch($this->table, $res, "a_id");
    }
}
