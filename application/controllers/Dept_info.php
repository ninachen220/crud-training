<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * 部門資料庫存取範例
 * 
 * 本Controller提供Model Dept_info_model 使用範例，請在觀察輸出時，也同步觀察資料庫中的資料
 * 
 * @author Mars.Hung 2020-02-29
 */
class Dept_info extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        // 開啟session功能
        session_start();
    }

    public function index()
    {
        // 載入部門資料庫
        $this->load->model('Dept_info_model');

        echo "<pre>";

        /**
         * ========== 範例-新增 ==========
         */
        // 新增一筆資料 - 使用session記錄計數
        if (!isset($_SESSION['count'])) {
            $_SESSION['count'] = 0;
        }
        $count = ++$_SESSION['count'];

        // 建構新增範例資料
        $data = [
            'd_code' => 'd' . str_pad($count, 4, '0', STR_PAD_LEFT),
            'd_name' => '部門1',
            'd_level' => '部',
            'date_start' => '2020-01-01',
            'remark' => '部門' . $count,
        ];

        // 使用Dept_info_model中的post函式新增資料
        $d_id = $this->Dept_info_model->post($data);

        echo "新增：" . $d_id;
        echo "\n";
        echo "\n";



        /**
         * ========== 範例-讀取 ==========
         */
        // 讀取剛才新增的資料
        $data = $this->Dept_info_model->get($d_id);
        echo "讀取：";
        var_export($data);
        echo "\n";



        /**
         * ========== 範例-修改 =========
         */
        // 建構修改範例資料 - 修改剛才新增的資料
        $data = [
            'd_id' => $d_id,
            'd_name' => '部門' . mt_rand(0000, 9999),
            'd_level' => '組',
        ];

        // 使用Dept_info_model中的put函式修改資料
        $d_id = $this->Dept_info_model->put($data);
        // 讀回修改的資料
        $data = $this->Dept_info_model->get($d_id, 'd_id,d_name,d_level');
        echo "修改後：";
        var_export($data);
        echo "\n";



        /**
         * ========== 範例-刪除 ==========
         */
        // 使用Dept_info_model中的put函式修改資料 - 軟刪
        $this->Dept_info_model->delete($d_id);
        // 使用Dept_info_model中的put函式修改資料 - 強刪
        // $this->Dept_info_model->delete($d_id, true);

        // 讀回刪除的資料
        $data = $this->Dept_info_model->get($d_id);
        echo "刪除後：";
        var_export($data);
        echo "\n";
        echo "請至資料庫中查看d_id=" . $d_id . "資料狀態";
        echo "\n";
        echo "\n";


        /**
         * ========== 範例-取得資料-從查詢條件 ==========
         * 請先新增好一些資料再來查詢
         */
        $where = [
            'd_id' => ['31', '32'],
            'd_level' => '部',
        ];
        $data = $this->Dept_info_model->getBy($where);
        var_export($data);
    }
}