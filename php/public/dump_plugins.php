<?php
namespace think;

// Mock $_SERVER['SCRIPT_FILENAME'] to simulate web server execution
$_SERVER['SCRIPT_FILENAME'] = 'index.php';

use think\admin\service\RuntimeService;
use think\admin\Plugin;

require __DIR__ . '/../vendor/autoload.php';

$app = new App();
RuntimeService::init($app);
$app->initialize();

$_GET = [
    'appid' => 'wxf9a59329864c370d',
    'device_brand' => 'devtools',
    'device_model' => 'iPhone 12/13 (Pro)',
    'device_id' => '17797069943539464089',
    'device_type' => 'phone',
    'device_orientation' => 'portrait',
    'platform' => 'devtools',
    'system' => 'iOS 10.0.1',
    'os' => 'iOS',
    'version' => '8.0.5',
    'sdk_version' => '2.32.3',
    'host_name' => 'WeChat',
    'host_version' => '8.0.5',
    'host_language' => 'zh-CN',
    'language' => 'zh_CN',
    'app_version' => '1.0.0',
    'app_version_code' => '100',
    'screen_width' => '390',
    'screen_height' => '844',
    'sig' => 'b46c2ff0a5b9201a433bec0860c91c74',
];

$request = $app->make(Request::class);
$request->setPathinfo('plugin-test/api.v1.config/index');
$app->instance('request', $request);

echo "--- Tracing parseMultiApp --- \n";
$defaultApp = $app->config->get('route.default_app') ?: 'index';
echo "defaultApp: $defaultApp\n";

$file = $_SERVER['SCRIPT_FILENAME'] ?? ($_SERVER['argv'][0] ?? '');
$script = empty($file) ? '' : pathinfo($file, PATHINFO_FILENAME);
$pathinfo = $request->pathinfo();
echo "script: $script, pathinfo: $pathinfo\n";

if ($script && !in_array($script, ['index', 'router', 'think'])) {
    echo "Branch 1 (Script bound)\n";
} else {
    echo "Branch 2 (Direct path)\n";
    $domains = $app->config->get('app.domain_bind', []);
    echo "domains count: " . count($domains) . "\n";
    
    $name = current(explode('/', $pathinfo));
    if (strpos($name, '.')) $name = strstr($name, '.', true);
    echo "name parsed: $name\n";
    
    $addons = Plugin::get();
    echo "addons keys: " . implode(', ', array_keys($addons)) . "\n";
    
    $appmap = $app->config->get('app.app_map', []);
    echo "appmap keys: " . implode(', ', array_keys($appmap)) . "\n";
    
    if (isset($appmap[$name])) {
        echo "isset(appmap[name]) is true\n";
    } elseif ($name && (in_array($name, $appmap) || in_array($name, $app->config->get('app.deny_app_list', [])))) {
        echo "in_array(name, appmap) or deny_app_list is true\n";
    } elseif ($name && isset($appmap['*'])) {
        echo "isset(appmap[*]) is true\n";
    } else {
        echo "else branch for name\n";
        $appName = $name ?: $defaultApp;
        echo "appName computed: $appName\n";
        if (!isset($addons[$appName]) && !is_dir($app->getBasePath() . $appName)) {
            echo "Addon is NOT set AND app directory NOT exists\n";
            echo "basePath + appName: " . $app->getBasePath() . $appName . "\n";
            echo "app_express config: " . ($app->config->get('app.app_express', false) ? 'true' : 'false') . "\n";
        } else {
            echo "Addon IS set OR app directory EXISTS\n";
            echo "isset(addons[appName]): " . (isset($addons[$appName]) ? 'true' : 'false') . "\n";
            echo "is_dir(basePath + appName): " . (is_dir($app->getBasePath() . $appName) ? 'true' : 'false') . "\n";
            
            // Let's emulate the binding
            $thisAppPath = $addons[$appName]['path'] ?? '';
            $thisAppSpace = $addons[$appName]['space'] ?? '';
            echo "addons[appName] path: " . $thisAppPath . "\n";
            echo "addons[appName] space: " . $thisAppSpace . "\n";
            
            // Emulate setMultiApp
            echo "\n--- Emulating setMultiApp --- \n";
            $appPath = $thisAppPath ?: syspath("app/{$appName}/");
            echo "appPath for setMultiApp: " . $appPath . "\n";
            if (is_dir($appPath)) {
                echo "appPath directory exists!\n";
                $appSpace = $thisAppSpace ?: NodeService::space($appName);
                $app->setNamespace($appSpace)->setAppPath($appPath);
                $app->http->name($appName)->path($appPath)->setRoutePath($appPath . 'route' . DIRECTORY_SEPARATOR);
                
                // Let's load the app's files
                $ext = $app->getConfigExt();
                echo "Config Extension: " . $ext . "\n";
                if (is_file($file = "{$appPath}common{$ext}")) {
                    echo "Found common file: $file\n";
                }
            } else {
                echo "appPath directory DOES NOT exist!\n";
            }
        }
    }
}
