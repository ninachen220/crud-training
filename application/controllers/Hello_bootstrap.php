<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 測試下載bootstrap後的畫面用view
 */
class Hello_bootstrap extends CI_Controller {

    public function index()
    {
        // 載入測試的view
        $this->load->view('helloBootstrap');
    }
}