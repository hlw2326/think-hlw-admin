<?php
declare(strict_types=1);

namespace plugin\base\controller\api\v1;

use plugin\base\model\BaseUser;

/**
 * 登录验证 API
 * @class Auth
 */
class Auth extends Base
{
    protected function initialize(): void
    {
        parent::initialize();
        $this->checkToken();
    }
}
