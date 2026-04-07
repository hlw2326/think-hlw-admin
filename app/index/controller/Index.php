<?php

namespace app\index\controller;

use think\admin\Controller;

class Index extends Controller
{
    public function index(): void
    {
        $this->redirect(sysuri('admin/login/index'));
    }

}
