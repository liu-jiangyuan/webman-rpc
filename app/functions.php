<?php
/**
 * Here is your custom functions.
 */

if (!function_exists('getTree')){
    function getTree(array $data, int $pId)
    {
        $tree = [];
        foreach($data as $k => $v)
        {
            if((int)$v['pid'] == $pId)
            {
                $v['child'] = getTree($data, (int)$v['id']);
                $tree[] = $v;
            }
        }
        return $tree;
    }
}

if (!function_exists('dump')) {
    /**
     * 浏览器友好的变量输出
     * @param mixed $vars 要输出的变量
     * @return void
     */
    function dump(...$vars)
    {
        ob_start();
        var_dump(...$vars);

        $output = ob_get_clean();
        $output = preg_replace('/\]\=\>\n(\s+)/m', '] => ', $output);

        if (PHP_SAPI == 'cli') {
            $output = PHP_EOL . $output . PHP_EOL;
        } else {
            if (!extension_loaded('xdebug')) {
                $output = htmlspecialchars($output, ENT_SUBSTITUTE);
            }
            $output = '<pre>' . $output . '</pre>';
        }

        echo $output;
    }
}