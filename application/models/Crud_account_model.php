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

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        // 
        parent::__construct();

        // 載入資料連線
        $this->load->database();
    }

    /**
     * 搜尋全部帳號
     * 
     * @param string $col 查詢欄位字串
     * @return array
     */
    public function getAllAccount($col = 'a_id,a_account,a_name,a_sex,a_birth,a_mail,a_note')
    {
        // 預設性別名稱轉換
        $dataSexDefault = [
            'N' => '未選擇',
            'M' => '男生',
            'F' => '女生'
        ];

        // 執行sql後獲取的資料
        $datas = $this->db->select($col)->from($this->table)->where('status', '1')->get()->result_array();

        // 整理資料成對應名稱
        $datas = array_map(function ($data) use ($dataSexDefault) {
            // 轉換輸出的性別顯示
            $data['a_sex'] = $dataSexDefault[$data['a_sex']];

            return $data;
        }, $datas);

        // 
        return $datas;
    }

    /**
     * 新增帳號
     *
     * @param array $data 新增資料
     * @return int
     */
    public function addAccount($data)
    {
        //設定status = 1
        $data['status'] = 1;

        // 寫入資料表
        $res = $this->db->insert($this->table, $data);

        // 寫入成功時回傳寫入主鍵鍵值，失敗時回傳 0
        return $res ? $this->db->insert_id() : 0;
    }

    /**
     * 更新資料 - 從主鍵
     *
     * @param array $data 更新資料
     * @return int
     */
    public function editAccount($data)
    {
        // 
        $res = 0;

        // 檢查有無主鍵
        if (isset($data['a_id'])) {
            // 取出主鍵值
            $a_id = $data['a_id'];

            // 移除$data中主鍵欄位
            unset($data['a_id']);

            // 更新資料 - 成功時回傳主鍵鍵值，失敗時回傳 0
            $res = $this->db->where('a_id', $a_id)->update($this->table, $data) ? $a_id : 0;
        } else {
            // 報錯-沒有主鍵欄位
            throw new Exception('沒有主鍵欄位: a_id', 400);
        }

        // 
        return $res;
    }

    /**
     * 刪除帳號 - 從主鍵
     *
     * @param string $id 刪除id
     * @return int
     */
    function deleteAccount($id)
    {
        //預設Status = 0
        $data = [
            'status' => 0
        ];

        // 刪除條件
        $this->db->where_in('a_id', $id);

        // 
        return $this->db->update($this->table, $data);
    }

    /**
     * 批次刪除帳號 - 從主鍵
     *
     * @param array $data 刪除id 陣列
     * @return int
     */
    function deleteSelectAccount($data)
    {
        // 
        $res = [];

        // 將資料整理成批次要的樣式
        foreach ($data as $key => $value) {
            // 
            $res[] = array(
                'a_id' => $value,
                'status' => 0
            );
        }

        // 
        return $this->db->update_batch($this->table, $res, "a_id");
    }
}
