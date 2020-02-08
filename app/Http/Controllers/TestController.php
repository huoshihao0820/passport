<?php

namespace App\Http\Controllers;
use App\models\RegiModel;
use App\models\GoodsModel;
use Illuminate\Support\Facades\Redis;

use Illuminate\Http\Request;
use PhpParser\Node\Expr\Empty_;

class TestController extends Controller
{
    public function register(){
        $data=$_POST;
//        dd($data);
        unset($data['_token']);
        $res=RegiModel::where('email',$data['email'])->first();
        $res2=RegiModel::where('mobile',$data['mobile'])->first();
        if ($res2){
            $arr=[
                'code' =>205,
                'msg'  =>'电话存在',
            ];
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);die;
        }
//        dd($res);
        if ($res){
                $arr=[
                'code' =>201,
                'msg'  =>'邮箱存在',
            ];
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);die;
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
                return json_encode($arr,JSON_UNESCAPED_UNICODE);die;
            }
        }
    }
    public function login(Request $request){
//        $data=$_POST;
        $data=$request->input();
        unset($data['_token']);
        if (!empty($data['mobile'])){
            $res=RegiModel::where('mobile',$data['mobile'])->first();
            if ($res){
                if ($data['password']==$res->password){
//                    $token=md5($data['password'].$data['email']);

//                    dd($res->id);
                    $token=$this->gettoken($res->id);
                    $redis_token_key='str:user:token'.$res->id;
                    Redis::set($redis_token_key,$token,3600*24);
                    $arr=[
                        'code' =>200,
                        'msg'  =>'登陆成功',
                        'data'=>[
                            'uid'=>$res->id,
                            'token'=>$token,
                        ]
                    ];
                    echo json_encode($arr,JSON_UNESCAPED_UNICODE);die;
                }else{
                    $arr=[
                        'code' =>204,
                        'msg'  =>'密码不正确',
                    ];
                    echo json_encode($arr,JSON_UNESCAPED_UNICODE);die;
                }
            }else{
                $arr=[
                    'code' =>203,
                    'msg'  =>'手机号不存在',
                ];
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);die;
            }
        }elseif(!empty($data['name'])){
            $res=RegiModel::where('name',$data['name'])->first();
//            dd($res);
            if ($res){
                if ($data['password']==$res->password){
                    $token=$this->gettoken($res->id);
                    $redis_token_key='str:user:token'.$res->id;
                    Redis::set($redis_token_key,$token, 3600*24);

                    $arr=[
                        'code' =>200,
                        'msg'  =>'登陆成功',
                        'data'=>[
                            'uid'=>$res->id,
                            'token'=>$token,
                        ]
                    ];
                    echo json_encode($arr,JSON_UNESCAPED_UNICODE);die;
                }else{
                    $arr=[
                        'code' =>2044,
                        'msg'  =>'密码不正确',
                    ];
                    echo json_encode($arr,JSON_UNESCAPED_UNICODE);die;
                }
            }else{
                $arr=[
                    'code' =>2033,
                    'msg'  =>'用户名不存在',
                ];
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);die;
            }
        }else{
            $res=RegiModel::where('email',$data['email'])->first();
//            dd($res);
            if ($res){
                if ($data['password']==$res->password){
                    $token=$this->gettoken($res->id);
                    $redis_token_key='str:user:token'.$res->id;
                    Redis::set($redis_token_key,$token, 3600*24);
                    $arr=[
                        'code' =>200,
                        'msg'  =>'登陆成功',
                        'data'=>[
                            'uid'=>$res->id,
                            'token'=>$token,
                        ]
                    ];
                    echo json_encode($arr,JSON_UNESCAPED_UNICODE);die;
                }else{
                    $arr=[
                        'code' =>2044,
                        'msg'  =>'密码不正确',
                    ];
                    echo json_encode($arr,JSON_UNESCAPED_UNICODE);die;
                }
            }else{
                $arr=[
                    'code' =>2033,
                    'msg'  =>'邮箱不存在',
                ];
                echo json_encode($arr,JSON_UNESCAPED_UNICODE);die;
            }
        }

    }
    public function showTime(Request $request){
//        dd($_SERVER);
        if (empty($_SERVER['HTTP_TOKEN']) || empty($_SERVER['HTTP_UID'])){
            $response=[
                'error'=>209,
                'msg'=>'token dont '
            ];
            return$response;
        }
        $token=$_SERVER['HTTP_TOKEN'];
        $uid=$_SERVER['HTTP_UID'];
//        dd($_SERVER);die;
        $redis_token_key='str:user:token:'.$uid;
        echo "redis key:".$redis_token_key;echo'</br>';
        $cache_token=Redis::get($redis_token_key);
        echo "cache_token:".$cache_token;echo'</br>';
        if ($token=$cache_token){
//            $data=date("Y-m-d H:i:s");
            $arr=[
                'code' =>2033,
                'msg'  =>'用户名不存在',
                'data'=>$token
            ];
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);die;
        }else{
            $arr=[
                'code' =>206,
                'msg'  =>'授权失败',
            ];
            echo json_encode($arr,JSON_UNESCAPED_UNICODE);die;
        }
    }

 public function check2()
	     {	            
		     $key = "1905";      // 计算签名的key 与发送端 保持一致
		     echo '<pre>';print_r($_POST);
	             //接收数据 和 签名
	             $json_data = $_POST['data'];
                     $sign = $_POST['sign'];
                     //计算签名
		     $sign2 = md5($json_data,$key);
 		     echo "接收端计算的签名：".$sign2;echo 
			     // 比较接收到的签名
			 if($sign2==$sign){
			    	  echo "验签成功";
		             }else{
			          echo "验签失败";
		          }
	   }


    public function create(){
        $data=$_POST;
//        unset($data['token']);
//        dd($data);
        if (empty($data['name'])||empty($data['price'])||empty($data['number'])){
            GoodsModel::insert($data);
            $arr=[
                'code' =>200,
                'msg'  =>'添加成功',
            ];
            echo json_encode($arr);
        }
    }
    protected function gettoken($uid){
        $token=md5(time().mt_rand(11111,99999).$uid);
        return substr($token,5,20);
    }


}
