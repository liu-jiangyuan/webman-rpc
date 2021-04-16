<?php
namespace support\middleware;


use app\admin\model\Auth;
use Webman\Http\Request;
use Webman\Http\Response;
use Webman\MiddlewareInterface;

class Admin implements MiddlewareInterface
{
    public function process(Request $request, callable $next): Response
    {
        $controller = explode('\\',$request->controller);
        $auth = array_pop($controller).'/'.$request->action;
        if ($auth != 'Login/login'){
            //判断有没有登陆
            $session = $request->session()->get('adminInfo');
            if (!$session){
                return redirect('/admin/Login/login');
            }
            $notCheck = ['Index/index','Index/welcome','Login/loginout'];
            //排除公共模块
            if (!in_array($auth,$notCheck)){
                //本次访问的功能详情
                $model = new Auth();
                $authInfo = $model->getRuleInfoByRule($auth);
                $adminAuth = $model->getAdminAuthRuleIds((int)$session['id']);
                //本次访问的权限规则是否在用户权限内
                if (!in_array($authInfo['id'],$adminAuth)){
                    if ($request->method() == 'POST') return json(['code'=>0,'msg'=>'没有访问权限']);
                    else return redirect('/admin/Index/index');
                }
            }
        }
        return $next($request);
    }
}