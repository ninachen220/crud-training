<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * 練習D3 boorstrap的Controller
 */
class Bootstrap_practice_d3 extends CI_Controller
{

    public function index()
    {
        // 載入練習D3 bootstrap的view
        $this->load->view('bootstrapHeader');
        $this->load->view('bootstrapPractice');
    }
}
