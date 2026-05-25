<?php

namespace app\index\controller;

use think\admin\Controller;

class Down extends Controller
{
    public function index(): void
    {
        $url = trim((string) $this->request->param('url', ''));
        $base64 = trim((string) $this->request->param('u', ''));
        if ($url === '' && $base64 !== '') {
            $decoded = base64_decode(strtr($base64, '-_', '+/'), true);
            $url = is_string($decoded) ? trim($decoded) : '';
        }

        if (!$this->safe($url)) {
            $this->fail('下载地址不合法', 400);
        }

        [$url, $headers] = $this->resolve($url);
        if (!$this->safe($url)) {
            $this->fail('下载地址不合法', 400);
        }

        ignore_user_abort(false);
        @set_time_limit(0);
        @ini_set('memory_limit', '-1');

        header('Content-Type: ' . ($headers['content-type'] ?? 'application/octet-stream'));
        if (!empty($headers['content-length'])) {
            header('Content-Length: ' . $headers['content-length']);
        }
        header('Content-Disposition: attachment; filename="video.mp4"');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_USERAGENT => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15',
            CURLOPT_HTTPHEADER => $this->proxyHeaders($headers),
            CURLOPT_WRITEFUNCTION => static function ($ch, string $chunk) {
                if (connection_aborted()) {
                    return 0;
                }
                echo $chunk;
                flush();
                return strlen($chunk);
            },
        ]);

        $ok = curl_exec($ch);
        $code = intval(curl_getinfo($ch, CURLINFO_RESPONSE_CODE));
        curl_close($ch);

        if ($ok === false || $code >= 400) {
            exit;
        }
    }

    private function resolve(string $url): array
    {
        $headers = [];
        for ($i = 0; $i < 3; $i++) {
            $next = '';
            $meta = [];
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_NOBODY => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15',
                CURLOPT_HEADERFUNCTION => static function ($ch, string $line) use (&$next, &$meta) {
                    $length = strlen($line);
                    $header = trim($line);
                    if (stripos($header, 'Location:') === 0) {
                        $next = trim(substr($header, 9));
                    } elseif (stripos($header, 'Content-Type:') === 0) {
                        $meta['content-type'] = trim(substr($header, 13));
                    } elseif (stripos($header, 'Content-Length:') === 0) {
                        $meta['content-length'] = trim(substr($header, 15));
                    }
                    return $length;
                },
            ]);
            curl_exec($ch);
            $code = intval(curl_getinfo($ch, CURLINFO_RESPONSE_CODE));
            curl_close($ch);

            $headers = $meta ?: $headers;
            if ($code < 300 || $code >= 400 || $next === '') {
                return [$url, $headers];
            }

            $url = $this->absolute($url, $next);
            if (!$this->safe($url)) {
                return ['', []];
            }
        }

        return [$url, $headers];
    }

    private function absolute(string $base, string $url): string
    {
        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }

        $info = parse_url($base);
        if (empty($info['scheme']) || empty($info['host'])) {
            return '';
        }

        if (strpos($url, '//') === 0) {
            return $info['scheme'] . ':' . $url;
        }

        $path = $url[0] === '/' ? $url : rtrim(dirname($info['path'] ?? '/'), '/') . '/' . $url;
        $port = empty($info['port']) ? '' : ':' . $info['port'];
        return "{$info['scheme']}://{$info['host']}{$port}{$path}";
    }

    private function safe(string $url): bool
    {
        $info = parse_url($url);
        if (empty($info['scheme']) || empty($info['host'])) {
            return false;
        }

        if (!in_array(strtolower($info['scheme']), ['http', 'https'], true)) {
            return false;
        }

        $host = strtolower(trim($info['host']));
        if ($host === 'localhost' || substr($host, -6) === '.local') {
            return false;
        }

        $ips = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : (gethostbynamel($host) ?: []);
        if (empty($ips)) {
            return false;
        }

        foreach ($ips as $ip) {
            if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return false;
            }
        }

        return true;
    }

    private function proxyHeaders(array $headers): array
    {
        $next = [];
        if (!empty($headers['content-type'])) {
            $next[] = 'Accept: ' . $headers['content-type'];
        }
        return $next;
    }

    private function fail(string $message, int $code): void
    {
        http_response_code($code);
        header('Content-Type: text/plain; charset=utf-8');
        echo $message;
        exit;
    }
}
