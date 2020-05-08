<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ApiController extends Controller
{

    /**
     * 确认key function
     *
     * @param Request $request
     * @param string $id
     * @return void
     */
    public function checkKey(Request $request, $id = "")
    {
        $prefix = "user_id_";
        $data   = [
            "code" => 999,
            "msg"  => 'not working',
            "data" => [],
        ];
        $redis_key = str_replace($prefix, "", $id) . '_msg';
        $lists     = Redis::keys($redis_key);

        // $lists        = Redis::get($redis_key);
        $data['code'] = 0;
        $data['msg']  = 'success';
        $data['data'] = [
            "lists" => $lists,
        ];

        return response()->json($data);
    }

    /**
     * 玩家清单 function
     *
     * @param Request $request
     * @return void
     */
    public function lists(Request $request)
    {
        $prefix = "user_id_";
        $data   = [
            "code" => 999,
            "msg"  => 'not working',
            "data" => [],
        ];
        $lists        = Redis::keys($prefix . "*");
        $data['code'] = 0;
        $data['msg']  = 'success';
        $key          = array_search($prefix . "2", $lists);
        // print_r($lists);
        unset($lists[$key]);

        $lists = array_values($lists);
        ###
        $data['data'] = [
            "lists" => $lists,
        ];

        return response()->json($data);
    }
    

    /**
     * 对话清单 function
     *
     * 
     * 
     * @param Request $request
     * @param string $id
     * @return void
     */
    public function getlist(Request $request, $id = "")
    {
        $prefix = "user_id_";
        $data   = [
            "code" => 999,
            "msg"  => 'not working',
            "data" => [],
        ];

        $redis_key    = str_replace($prefix, "", $id) . '_msg';
        $lists        = Redis::lrange($redis_key, 0, 100);
        $data['code'] = 0;
        $data['msg']  = 'success';
        $data['data'] = [
            "lists" => array_reverse($lists),
        ];

        return response()->json($data);
    }

}
