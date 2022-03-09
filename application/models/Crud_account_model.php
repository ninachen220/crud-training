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
    protected $dept = "dept_info";

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
        'd_id',
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
    public function getAllAccount($pack)
    {
        if (!isset($pack['col'])) {
            // 預設欄位
            $col = 'a_id,a_account,a_name,a_sex,a_birth,a_mail,a_note,d_id';
            $this->db->where('status', '1');
        } else {
            $col = $pack['col'];
        }
        // 寫入select sql
        $this->db->select($col)->from($this->table);

        // 判定是否有顯示筆數
        if (isset($pack['length'])) {
            $length = $pack['length'];
            // 判定是否有起始抓取欄位
            if (isset($pack['start'])) {
                $start = $pack['start'];
                $text = $pack['order'][0]['column'];
                // 預設排序欄位map
                $map = [0 => 'a_id', 1 => 'a_account', 2 => 'a_name', 3 => 'a_sex', 4 => 'd_id', 5 => 'a_birth', 6 => 'a_mail', 7 => 'a_note'];
                // 排序欄位
                $text = $map[$text];
                // 設定排序方式
                $sortType = $pack['order'][0]['dir'];
                $draw = $pack['draw'];
            } else {
                $page = $pack['page'];
                $sortType = $pack['sortType'];
                $text = $pack['text'];
                // 設定起始撈取欄位
                $start = $page * $length;
            }
            // 資料庫總資料筆數
            $total = $this->db->count_all_results('', FALSE);
            // 設定撈取筆數及開始欄位數
            $this->db->limit($length, $start);
            // 設定排序方式及欄位
            $this->db->order_by($text, $sortType);
        }
        // 判定search是否有值
        if (isset($pack['search']['value']) && !empty($pack['search']['value'])) {
            $arr = [];
            foreach ($this->tableColumns as $row) {
                $arr[$row] = $pack['search']['value'];
            }
            $this->db->group_start()->or_like($arr)->group_end();
        }
        // 寫入條件sql並回傳資料
        $accountData = $this->db->get()->result_array();
        // 判定是否有查詢到搜尋資料
        if (!empty($accountData)) {
            // 撈出所有的a_id
            $deptIds = array_unique(array_column($accountData, 'd_id'));
            if (!empty($deptIds)) {
                // 設定搜尋欄位
                $col = ['d_id', 'd_name'];
                // 查詢有指定d_id部門名稱
                $this->db->select($col)->from($this->dept);
                $this->db->where_in('d_id', $deptIds);
                $deptData = $this->db->get()->result_array();

                // 將部門id變成map陣列
                $map = array_column($deptData, 'd_name', 'd_id');
            }
        }
        $map[0] = '未選擇';
        // 判定是否有前端丟過來的指定欄位
        if (isset($pack['draw'])) {
            // 設定指定參數
            $recordsTotal = $total;
            $recordsFiltered = $total;
            // 將部門代碼換成中文名稱
            foreach ($accountData as $key => $row) {
                $id = $row['d_id'];
                // 設定部門中文
                $accountData[$key]['d_id'] = $map[$id];
            }
            // 暫存資料
            $tmp = $accountData;
            $accountData = [];
            // 設定回傳參數
            $accountData['draw'] = $draw;
            // 設定回傳資料
            $accountData['data'] = $tmp;
            // 設定回傳資料總筆數
            $accountData['recordsFiltered'] = $recordsFiltered;
            // 設定回傳資料總筆數
            $accountData['recordsTotal'] = $recordsTotal;
        }
        return $accountData;
    }

    /**
     * 獲取單筆資料
     * 
     * @param array $id
     * @return json
     */
    public function getSpesificAccount($id)
    {
        // 預設欄位
        $col = 'a_id,a_account,a_name,a_sex,a_birth,a_mail,a_note,d_id';
        $this->db->where('status', '1');
        // 寫入select sql
        $this->db->select($col)->from($this->table);
        $this->db->where('a_id', $id);
        // 回傳資料
        return $this->db->get()->result_array();
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
        return $res ? $this->db->insert_id() : null;
    }

    /**
     * 更新資料 - 從主鍵
     *
     * @param array $data 帳號資料
     * @return int
     */
    public function editAccount($data)
    {
        // 檢查有無主鍵
        if (isset($data['a_id'])) {
            // 取出主鍵值並移除$data中主鍵欄位
            $a_id = $data['a_id'];
            unset($data['a_id']);
            // 更新資料 - 成功時回傳主鍵鍵值，失敗時回傳 0
            $res = $this->db->where('a_id', $a_id)->update($this->table, $data) ? $a_id : null;
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

    /**
     * 搜尋部門名稱
     * 
     * @return array
     */
    public function getAllDept()
    {
        // 設定搜尋欄位
        $col = ['d_id', 'd_name'];
        // 查詢有指定d_id部門名稱
        $this->db->select($col)->from($this->dept);
        $deptData = $this->db->get()->result_array();
        return $deptData;
    }
}
