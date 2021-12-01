<?php

//图片上传
function uploadImg($file,$path)
{
    $allow_ext = ['jpg','jpeg','png'];
    if($file->isValid()){
        $ext = $file->getClientOriginalExtension();

        if(!in_array($ext,$allow_ext)){
            return ['status'=>'fail','msg'=>'图片格式不允许'];
        }

        $new_filename = md5(time().rand(1000,9999)).".".$ext;
        $file->move($path,$new_filename);
        return ['status'=>'success','msg'=>'上传成功','url'=>"/".$path."/".$new_filename];
    }
}

//验证码
function sendMobileYzm($mobile,$yzm_code)
{
	$host = "https://dfsns.market.alicloudapi.com";
    $path = "/data/send_sms";
    $method = "POST";
    $appcode = "ea552e7910cb46b8b14658b38feba74c";
    $headers = array();
	$httpInfo = array();
    array_push($headers, "Authorization:APPCODE " . $appcode);
    //根据API的要求，定义相对应的Content-Type
    array_push($headers, "Content-Type".":"."application/x-www-form-urlencoded; charset=UTF-8");
    $querys = "";
    $bodys = "content=code:{$yzm_code}&phone_number={$mobile}&template_id=TPL_0000";
	
    $url = $host.$path;

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_FAILONERROR, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    if (1 == strpos("$".$host, "https://"))
    {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    }
    curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
    $response =  curl_exec($curl);
    return $response;
}

//获取JWT token的配置 
function getTokenConfig(){

$configuration = \Lcobucci\JWT\Configuration::forSymmetricSigner(
    new Lcobucci\JWT\Signer\Hmac\Sha256(),
	\Lcobucci\JWT\Signer\Key\InMemory::base64Encoded(env('API_KEY'))
	);
	
	return $configuration;
}

//创建 JWT token
function createToken($mobile)
{
	$config = getTokenConfig();

	$now   = new \DateTimeImmutable();
	$token = $config->builder()
                ->issuedBy('http://118.31.13.92:8084')
                ->issuedAt($now)
                ->expiresAt($now->modify('+24 hour'))
                ->withClaim('mobile', $mobile)
                ->getToken($config->signer(), $config->signingKey());
				
	return $token->toString();
}

//根据token获取手机号
function getMobile($token){
	$config = getTokenConfig();
	$token = $config->parser()->parse($token);
	return  $mobile = $token->claims()->get('mobile');
}
