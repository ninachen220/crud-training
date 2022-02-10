<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * 練習物件導向設計的畫面用view
 * 
 * ======== 練習題 ========
 * 物件 A 
 * public $a = 0;
 * protected $b = null;
 * private $c = null;
 * 
 * 物件 B extends A
 * 觀察取得變數
 * public 和 private 有何差異(new 物件時可否取得)
 */

class testA
{
    public $a = 0;
    protected $b = null;
    private $c = null;
    //受保護的function showC
    protected function showC()
    {
        echo $this->c;
    }
}

class testB extends testA
{
    //protected的變數在extends裡面，需要用function的方式，使new可以取用
    function takeB()
    {
        echo $this->b;
    }
    //private的變數只能在父層使用，若extends要呼叫只能在父層使用function，extends呼叫該function
    function takeC()
    {
        $this->showC();

        //此方法也可以呼叫
        // parent::showC();
    }
}

$tryB = new testB();
$data = $tryB->takeC();
echo $data;
exit;
