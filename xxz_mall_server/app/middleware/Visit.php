<?php
namespace app\middleware;
use app\BaseController;
use app\Request;
use think\facade\Log;
class Visit extends BaseController
{
    public function handle(Request $request, \Closure $next)
    {
        /**
         * 记录访问信息
         * -IP 请求方式 请求url
         * -头部参数
         * -请求参数
         */

        $requestInfo = [
            'ip' => $request->ip(),
            'method' => $request->method(),
            'host' => $request->host(),
            'uri' => $request->url(),
        ];

        $logInfo = [
            "{$requestInfo['ip']} {$requestInfo['method']} {$requestInfo['host']}{$requestInfo['uri']}",
            '[ HEADER ] ' . var_export($request->header(), true),
            '[ PARAM ] ' . var_export($request->param(), true),
            '---------------------------------------------------------------',
        ];
        $logInfo = implode(PHP_EOL, $logInfo) . PHP_EOL;
        Log::record($logInfo, 'info');
        return $next($request);
    }

}