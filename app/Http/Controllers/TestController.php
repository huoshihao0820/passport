<?php

namespace App\Http\Controllers;
use App\models\RegiModel;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function register(){
        $data=$_POST;
        unset($data['_token']);
        $res=RegiModel::where('email',$data['email'])->first();
//        dd($res);
        if ($res){
            $arr=[
                'code' =>201,
                'msg'  =>'邮箱存在',
            ];
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);
        }else{
            if ($data['password']==$data['password2']){
                unset($data['password2']);
                RegiModel::insert($data);
                $token=md5($data['email'].$data['password'].'huoshihao');
//                dd($token);
                $arr=[
                    'code' =>200,
                    'msg'  =>'注册成功',
                    'token'=>$token,
                ];
                echo json_encode($arr);
            }else{
                $arr=[
                    'code' =>202,
                    'msg'  =>'密码不一致',
                ];
                return json_encode($arr);
            }
        }
    }
    public function login(){
        $data=$_POST;
        unset($data['_token']);
        $res=RegiModel::where('email',$data['email'])->first();
        if ($res){
            if ($data['password']==$res->password){
                $token=md5($data['password'].$data['email']);
                $arr=[
                    'code' =>200,
                    'msg'  =>'登陆成功',
                    'token'=>$token,
                ];
                echo json_encode($arr);
            }else{
                $arr=[
                    'code' =>204,
                    'msg'  =>'密码不正确',
                ];
                echo json_encode($arr);
            }
        }else{
            $arr=[
                'code' =>203,
                'msg'  =>'邮箱不存在',
            ];
            echo json_encode($arr);
        }
    }
}
