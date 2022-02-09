<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 練習boorstrap的Controller
 */
class Bootstrap_example extends CI_Controller {

    public function index()
    {
        // 載入練習bootstrap的view
        $this->load->view('bootstrapExample');
    }
}