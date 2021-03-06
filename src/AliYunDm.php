<?php
namespace whalephp\tool;

include_once dirname(dirname(__FILE__)) . '/aliyun-php-sdk-core/Config.php';
use Dm\Request\V20151123 as Dm;                

class AliYunDm
{
	private $regionId;
	private $accessKeyId;
	private $accessSecret;
	
	public function __construct($regionId, $accessKeyId, $accessSecret)
	{
		$this->regionId 	= $regionId;
		$this->accessKeyId 	= $accessKeyId;
		$this->accessSecret = $accessSecret;
	}
	
	public function sendMail($opt)
	{
        //需要设置对应的region名称，如华东1（杭州）设为cn-hangzhou，新加坡Region设为ap-southeast-1，澳洲Region设为ap-southeast-2。
		//$iClientProfile = DefaultProfile::getProfile("cn-hangzhou", "<your accessKey>", "<your accessSecret>");
		$iClientProfile = \DefaultProfile::getProfile($this->regionId, $this->accessKeyId, $this->accessSecret);

        //新加坡或澳洲region需要设置服务器地址，华东1（杭州）不需要设置。
		//$iClientProfile::addEndpoint("ap-southeast-1","ap-southeast-1","Dm","dm.ap-southeast-1.aliyuncs.com");
		//$iClientProfile::addEndpoint("ap-southeast-2","ap-southeast-2","Dm","dm.ap-southeast-2.aliyuncs.com");
		$client = new \DefaultAcsClient($iClientProfile);
		$request = new Dm\SingleSendMailRequest();
		//新加坡或澳洲region需要设置SDK的版本，华东1（杭州）不需要设置。
		//$request->setVersion("2017-06-22");
		//$request->setAccountName("控制台创建的发信地址");
		$request->setAccountName($opt['account_name']);
		$request->setFromAlias($opt['from_alias']);
		$request->setAddressType(1);
		//$request->setTagName("控制台创建的标签");
		$request->setReplyToAddress("true");
		//$request->setToAddress("目标地址");
		$request->setToAddress($opt['receiver']);
		//可以给多个收件人发送邮件，收件人之间用逗号分开,若调用模板批量发信建议使用BatchSendMailRequest方式
		//$request->setToAddress("邮箱1,邮箱2");
		$request->setSubject($opt['subject']);
		$request->setHtmlBody($opt['body']);        
		try {
			$response = $client->getAcsResponse($request);
			return array('err'=>0,'messages'=>'');
		}
		catch (\ClientException  $e) {
			 return array('err'=>1,'messages'=>"ErrorCode:".$e->getErrorCode()."ErrorMessage:".$e->getErrorMessage());
		}
		catch (\ServerException  $e) {
			 return array('err'=>1,'messages'=>"ErrorCode:".$e->getErrorCode()."ErrorMessage:".$e->getErrorMessage());
		}
	}
}
?>