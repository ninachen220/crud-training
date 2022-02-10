<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Js_training extends CI_Controller
{

    /**
     * Index Page for this controller.
     */
    public function index()
    {
        $this->load->view('jsTraining');
    }
    /**
     * AJAX controller.
     */
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
                $this->_create($data);
                break;
            case 'GET':
                if (empty($id)) {
                    // 讀取全部資料
                    $this->_list();
                } else {
                    // 讀取一筆資料
                    $this->_read($id);
                }
                break;
            case 'PATCH':
            case 'PUT':
                // 更新一筆資料
                $this->_update($data, $id);
                break;
            case 'DELETE':
                if (empty($id)) {
                    // 錯誤
                    http_response_code(404);
                    echo 'No Delete ID';
                    exit;
                } else {
                    // 刪除一筆資料
                    $this->_delete($id);
                }
                break;
        }
    }

    /**
     * 新增一筆
     *
     * @param array $data
     * @return array
     */
    protected function _create($data)
    {
        // 建立輸出陣列
        $opt = [
            // 行為：新增一筆
            'type' => '新增一筆',
            // 前端AJAX傳過來的資料
            'data' => $data,
        ];

        // 輸出JSON
        echo json_encode($opt);
    }

    /**
     * 讀取全部
     *
     * @return array
     */
    protected function _list()
    {
        // 建立輸出陣列
        $opt = [
            // 行為：讀取全部
            'type' => '讀取全部',
            // 標題資料
            'head' => [
                'name',
                'location',
            ],
            // 多筆內容資料
            'data' => [
                [
                    'name' => 'John',
                    'location' => 'Boston',
                ],
                [
                    'name' => 'Joe',
                    'location' => 'New York',
                ],
                [
                    'name' => 'Gary',
                    'location' => 'Taipei',
                ],
            ],
        ];

        // 輸出JSON
        echo json_encode($opt);
    }

    /**
     * 讀取一筆
     *
     * @param int $id 目標資料id
     * @return array
     */
    protected function _read($id)
    {
        // 建立輸出陣列
        $opt = [
            // 行為：讀取一筆
            'type' => '讀取一筆',
            // 前端AJAX傳過來的資料
            'id' => $id,
        ];

        // 輸出JSON
        echo json_encode($opt);
    }

    /**
     * 更新一筆
     *
     * @param array $data 資料內容
     * @param int $id 目標資料id
     * @return array
     */
    protected function _update($data, $id)
    {
        // 建立輸出陣列
        $opt = [
            // 行為：更新一筆
            'type' => '更新一筆',
            // 前端AJAX傳過來的資料
            'data' => $data,
            'id' => $id,
        ];

        // 輸出JSON
        echo json_encode($opt);
    }

    /**
     * 刪除一筆
     *
     * @param int $id 目標資料id
     * @return string
     */
    protected function _delete($id)
    {
        // 建立輸出陣列
        $opt = [
            // 行為：刪除一筆
            'type' => '刪除一筆',
            // 前端AJAX傳過來的資料
            'id' => $id,
        ];

        // 輸出JSON
        echo json_encode($opt);
    }
}
