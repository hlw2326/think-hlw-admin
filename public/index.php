<?php
declare(strict_types=1);

use think\admin\service\RuntimeService;

// 加载基础文件
require __DIR__ . '/../vendor/autoload.php';

// WEB应用初始化
RuntimeService::doWebsiteInit();
