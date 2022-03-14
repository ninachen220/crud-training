<?php

/**
 * 公司資料管理Model
 */
class Company_crud_model extends CI_Model
{

    // 公司資料表
    protected $table = "company_info";
    // 類別資料表
    protected $typeTable = "type_info";

    // 欄位資料
    public $tableColumns = [
        'id',
        'name',
        'contact',
        'email',
        'scale',
        't_id',
        'remark',
        'date_create',
        'date_update',
        'date_delete',
        'rec_status'
    ];

    // 預設搜尋欄位
    protected $col = 'id,name,contact,email,scale,remark,t_id';

    public function __construct()
    {
        parent::__construct();

        // 載入資料連線
        $this->load->database();
    }

    /**
     * 搜尋全部公司資料
     * 
     * @param array $pack 排序方式及欄位、搜尋文字
     * @return array
     */
    public function getAllCompany($pack)
    {
        // 如果前端沒有帶此參數必定錯誤
        if (!isset($pack['draw'])) {
            // 拋出例外
            throw new \Exception("未帶必須參數", 400);
        }

        // 判斷是否有欄位
        if (!isset($pack['col'])) {
            // 預設欄位
            $col = $this->col;
            // sql下搜尋資料狀態為1的資料
            $this->db->where('rec_status', '1');
        } else {
            // $col預設前端拋過來的資料
            $col = $pack['col'];
        }
        // 寫入select sql
        $this->db->select($col)->from($this->table);

        // 判定是否有顯示筆數
        if (isset($pack['length'])) {
            $length = $pack['length'];
            // 判定是否有起始抓取欄位
            if (isset($pack['start'])) {
                // 起始值
                $start = $pack['start'];
                // 前端拋過來的排序欄位
                $text = $pack['order'][0]['column'];
                // 預設排序欄位map
                $map = [0 => 'id', 1 => 'name', 2 => 'contact', 3 => 'email', 4 => 'scale', 5 => 't_id', 6 => 'remark'];
                // 使用map對照排序欄位
                $text = $map[$text];
                // 設定排序方式
                $sortType = $pack['order'][0]['dir'];
                $draw = $pack['draw'];
            } else {
                // 前端拋過來的頁碼
                $page = $pack['page'];
                // 前端拋過來的排序方式
                $sortType = $pack['sortType'];
                // 前端拋過來的排序欄位
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
            // 預設$arr
            $arr = [];
            // 將所有欄位為$arr的鍵值，搜尋文字為值
            foreach ($this->tableColumns as $row) {
                $arr[$row] = $pack['search']['value'];
            }
            // 搜尋sql資料
            $this->db->group_start()->or_like($arr)->group_end();
        }

        // 寫入條件sql並回傳資料
        $accountData = $this->db->get()->result_array();

        // 判定是否有查詢到搜尋資料
        if (!empty($accountData)) {
            // 撈出所有的a_id
            $typeIds = array_unique(array_column($accountData, 't_id'));
            if (!empty($typeIds)) {
                // 設定搜尋欄位
                $col = ['t_id', 't_name'];
                // 查詢有指定t_id類別名稱
                $this->db->select($col)->from($this->typeTable);
                $this->db->where_in('t_id', $typeIds);
                $typeData = $this->db->get()->result_array();

                // 將類別d變成map陣列
                $map = array_column($typeData, 't_name', 't_id');
            }
        }
        $map[0] = '未選擇';

        // 判定是否有前端丟過來的指定欄位
        foreach ($accountData as $key => $row) {
            $id = $row['t_id'];
            // 設定類別中文
            $accountData[$key]['t_id'] = $map[$id];
        }

        // 預設回傳陣列
        $resault = [];
        // 設定回傳參數
        $resault['draw'] = $draw;
        // 設定回傳資料
        $resault['data'] = $accountData;
        // 設定回傳資料總筆數
        $resault['recordsFiltered'] = $total;
        // 設定回傳資料總筆數
        $resault['recordsTotal'] = $total;

        // 回傳資料
        return $resault;
    }

    /**
     * 獲取單筆公司資料
     * 
     * @param array $id
     * @return array
     */
    public function getSpesificCompany($id)
    {
        // 搜尋狀態為1的資料
        $this->db->where('rec_status', '1');
        // 寫入select sql
        $this->db->select($this->col)->from($this->table);
        $this->db->where('id', $id);
        // 回傳資料
        return $this->db->get()->result_array();
    }

    /**
     * 新增公司資料
     *
     * @param array $data 公司資料
     * @param int $key 資料筆數
     * @return int
     */
    public function addCompany($data, $key = null)
    {
        // 檢查信箱是否重複
        $this->_cheackEmail($data['email'], $key);

        // 設定狀態為1
        $data['rec_status'] = 1;
        // 設定創立時間
        $data['date_create'] = date('Y-m-d H:i:s');
        // 寫入資料表
        $res = $this->db->insert($this->table, $data);

        // 寫入成功時回傳寫入主鍵鍵值，失敗時回傳 null
        return $res ? $this->db->insert_id() : null;
    }

    /**
     * 更新公司資料 - 從主鍵
     *
     * @param array $data 公司資料
     * @param int $key 資料筆數
     * @return array
     */
    public function editCompany($data, $key = null)
    {
        // 檢查信箱是否重複
        $this->_cheackEmail($data['email'], $key, $data['id']);

        // 檢查有無主鍵
        if (isset($data['id'])) {
            // 取出主鍵值並移除$data中主鍵欄位(不更新主鍵)
            $id = $data['id'];
            unset($data['id']);
            // 設定更新時間
            $data['date_update'] = date('Y-m-d H:i:s');
            // 成功時回傳主鍵鍵值，失敗時回傳 null
            $res = $this->db->where('id', $id)->update($this->table, $data) ? $id : null;
            // 將id放回去
            $data['id'] = $id;
        } else {
            // 回傳錯誤訊息 沒有主鍵欄位
            throw new Exception('沒有主鍵欄位', 400);
        }
        // 判斷是否成功更新資料
        if (!empty($res)) {
            // 搜尋更新id的資料
            $this->db->select($this->col)->from($this->table);
            $this->db->where('id', $data['id']);
            $res = $this->db->get()->result_array();
        }
        // 回傳更新資料
        return $res;
    }
    /**
     * 刪除公司資料 - 從主鍵
     *
     * @param int $id 公司資料主鍵
     * @return int
     */
    public function deleteCompany($id)
    {
        // 預設狀態為刪除及刪除日期時間
        $data = [
            'rec_status' => '0',
            'date_delete' => date('Y-m-d H:i:s')
        ];

        // 刪除條件
        $this->db->where_in('id', $id);
        // 成功時回傳主鍵鍵值
        return $this->db->update($this->table, $data);
    }

    /**
     * 批次刪除公司資料 - 從主鍵
     *
     * @param array $data 公司資料主鍵
     * @return int
     */
    public function deleteSelectCompany($data)
    {
        // 將資料整理成批次要的樣式
        foreach ($data['id'] as $key => $value) {
            $res[] = array(
                // 設定id
                'id' => $value,
                // 設定狀態為0
                'rec_status' => 0,
                // 設定刪除時間
                'date_delete' => date('Y-m-d H:i:s')
            );
        }
        // 成功時回傳主鍵鍵值
        return $this->db->update_batch($this->table, $res, "id");
    }

    /**
     * 搜尋類別名稱
     * 
     * @return array
     */
    public function getAllType()
    {
        // 設定搜尋欄位
        $col = ['t_id', 't_name'];
        // 查詢有指定t_id類別名稱
        $this->db->select($col)->from($this->typeTable);
        $typeData = $this->db->get()->result_array();
        return $typeData;
    }

    /**
     * 檢查信箱是否重複
     * 
     * 若有多個邏輯判斷重複，推薦還是提出程式，少量則可不提出
     *
     * @param [type] $email
     * @param [type] $key
     * @param [type] $id
     * @return void
     */
    private function _cheackEmail($email, $key, $id = null)
    {
        // 搜尋信箱
        $this->db->select('email')->from($this->table);
        // 搜尋相同信箱
        $this->db->where('email', $email);
        // 搜尋尚未軟刪除的信箱
        $this->db->where('rec_status', '1');
        // 搜尋更新時其他 id
        if ($id) {
            $this->db->where_not_in('id', $id);
        }
        // 獲取結果
        $repeat = $this->db->get()->result_array();

        // 判定信箱是否重複
        if (!empty($repeat) && $repeat > 0) {
            // 判定若有key則顯示第幾筆資料，無則不顯示
            $keyNum = !empty($key) ? "第" . $key . "筆" : null;
            throw new \Exception($keyNum . "信箱已重複，請重新確認", 404);
        }
    }
}
