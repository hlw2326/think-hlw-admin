<?php

namespace app\index\controller;

use think\admin\Controller;

class Index extends Controller
{
    public function index()
    {
        echo "hlw2326";
    }

    public function sapi()
    {
        echo php_sapi_name();
    }

}
