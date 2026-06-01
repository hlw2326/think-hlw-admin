<?php
declare(strict_types=1);

namespace plugin\base\name;

use plugin\base\name\components\Age;
use plugin\base\name\components\Career;
use plugin\base\name\components\Details;
use plugin\base\name\components\Gender;
use plugin\base\name\components\Hot;
use plugin\base\name\components\Rate;
use plugin\base\name\components\Score;
use plugin\base\name\components\Star;
use plugin\base\service\AiService;

/**
 * 姓名综合分析主体类 (Primary Class)
 * @class Name
 * @package plugin\base\name
 */
class Name
{
    private string $name;
    private string $surname;
    private string $givenName;
    private int $hash;
    private int $count;

    private ?array $aiResult = null;
    private bool $aiAttempted = false;

    /**
     * Name 构造函数，初始化姓名状态及基础属性
     */
    public function __construct(string $name)
    {
        $this->name = trim($name);
        $this->hash = Parser::calcHash($this->name);
        
        // 使用 Parser 解析姓氏、名字及估算全国人口
        $parsed = Parser::parse($this->name);
        $this->surname = $parsed['surname'];
        $this->givenName = $parsed['givenName'];
        $this->count = Parser::estimateCount($this->name, $this->hash, $parsed);
    }

    /**
     * 静态工厂方法，提供统一分析入口（向下兼容）
     * @param string $name
     * @return array
     */
    public static function calculate(string $name): array
    {
        $instance = new self($name);
        return $instance->analyze();
    }

    /**
     * 调用各分析组件，返回综合分析结果
     * @return array
     */
    public function analyze(): array
    {
        return [
            'name'     => $this->name,
            'count'    => $this->count,
            'rate'     => (new Rate())->calculate($this),
            'score'    => (new Score())->calculate($this),
            'hot'      => (new Hot())->calculate($this),
            'details'  => (new Details())->calculate($this),
            'gender'   => (new Gender())->calculate($this),
            'age'      => (new Age())->calculate($this),
            'star'     => (new Star())->calculate($this),
            'career'   => (new Career())->calculate($this),
            'is_double' => $this->checkDoubleSurname(),
        ];
    }

    /**
     * 检测是否为双姓（首字与第二字均为合法姓氏，且名字仍有剩余字符）
     * @return bool
     */
    public function checkDoubleSurname(): bool
    {
        $nameLen = mb_strlen($this->name, 'UTF-8');
        if ($nameLen < 3) {
            return false;
        }

        $surname = $this->surname;
        $givenName = $this->givenName;

        if (empty($surname) || empty($givenName)) {
            return false;
        }

        // 获取名字部分的第一个字
        $secondSurname = mb_substr($givenName, 0, 1, 'UTF-8');
        $realGivenName = mb_substr($givenName, 1, null, 'UTF-8');

        if (empty($realGivenName)) {
            return false;
        }

        try {
            // 动态查询数据库以验证第二个字是否也是一个合法姓氏
            $record = \plugin\base\model\BaseSurname::mk()
                ->where(['surname' => $secondSurname])
                ->findOrEmpty();

            return !$record->isEmpty();
        } catch (\Throwable $e) {
            return false;
        }
    }

    // Getters
    public function getName(): string
    {
        return $this->name;
    }

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getGivenName(): string
    {
        return $this->givenName;
    }

    public function getHash(): int
    {
        return $this->hash;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * 获取并缓存统一的 AI 深度分析结果
     * @return array|null
     */
    public function getAiResult(): ?array
    {
        if ($this->aiAttempted) {
            return $this->aiResult;
        }

        $this->aiAttempted = true;
        try {
            $config = AiService::config();
            if (!empty($config['enabled'])) {
                $prompt = "你是一个姓名学、社会人口统计与性格心理学专家。请对姓名「{$this->name}」进行以下两项深度分析：
1. 分析该姓名重名的男女性别比例倾向。估算其重名的男女性别百分比比例（两者相加必须等于100），并生成一句20字以内、文学底蕴深厚、克制文雅的性别分布分析评语。
2. 分析该姓名所蕴含的独特性格特征。生成 4 个极具文雅深度、与该姓名气质高度契合的四字性格标签。

请严格仅返回 JSON 数据，不要包含任何 Markdown 格式（如 ```json 等）或任何多余解释。格式 must 如下：
{
  \"gender\": {
    \"male\": 80,
    \"female\": 20,
    \"comment\": \"评语\"
  },
  \"career\": [
    \"性格标签1\",
    \"性格标签2\",
    \"性格标签3\",
    \"性格标签4\"
  ]
}";
                $reply = AiService::chat($prompt);

                $cleanReply = trim($reply);
                if (preg_match('/\{[\s\S]*\}/', $cleanReply, $matches)) {
                    $cleanReply = $matches[0];
                }

                $data = json_decode($cleanReply, true);
                if (is_array($data) && isset($data['gender'], $data['career'])) {
                    $this->aiResult = $data;
                }
            }
        } catch (\Throwable $e) {
            if (class_exists(\think\facade\Log::class)) {
                try {
                    \think\facade\Log::error("Name AI combined calculation failed: " . $e->getMessage() . "\n" . $e->getTraceAsString());
                } catch (\Throwable $logError) {
                    // Safely ignore logging failure if logging is uninitialized
                }
            }
        }

        return $this->aiResult;
    }
}
