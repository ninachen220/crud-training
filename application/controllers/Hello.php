<?php
/* === 為了防止不是從index.php訪問的控制器 === */
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * 測試CI運作Controller
 */
class Hello extends CI_Controller
{

    public function index()
    {
        // 載入測試CI運作的view
        $this->load->view('hello');
    }
}
