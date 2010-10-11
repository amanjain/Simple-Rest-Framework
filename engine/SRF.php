<?php
class SRF
{
	function __construct()
	{
		$this->request_variable="SRF_REQUEST";
		$this->app_directory="app";	
	}
	
	function run()
	{
		$request=$this->parse_request();
		$script_path=$this->get_script_path($request['script']);
		if(file_exists($script_path))
		{
			include_once($script_path);
			if(class_exists($request['class']))
			{
				$SRF_Controller=new $request['class']();
				if(method_exists($SRF_Controller, $request['method']))
				{
					call_user_func_array(array($SRF_Controller, $request['method']), $request['url_params']);
				}
				else
				{
					$this->send_status(404);
				}
			}
			else
			{
				$this->send_status(404);
			}
		}
		else
		{
			$this->send_status(404);
		}
	}
	
	/* Generate the path of script containing the requested resource */
	function get_script_path($script)
	{
		return '.'.DS.$this->app_directory.DS.'controller'.DS.strtolower($script).".php";
	}
	
	/* Status message array taken from CI */
	function send_status($code = 200)
	{
		$status=array
				(
				200	=> 'OK',
				201	=> 'Created',
				202	=> 'Accepted',
				203	=> 'Non-Authoritative Information',
				204	=> 'No Content',
				205	=> 'Reset Content',
				206	=> 'Partial Content',

				300	=> 'Multiple Choices',
				301	=> 'Moved Permanently',
				302	=> 'Found',
				304	=> 'Not Modified',
				305	=> 'Use Proxy',
				307	=> 'Temporary Redirect',

				400	=> 'Bad Request',
				401	=> 'Unauthorized',
				403	=> 'Forbidden',
				404	=> 'Not Found',
				405	=> 'Method Not Allowed',
				406	=> 'Not Acceptable',
				407	=> 'Proxy Authentication Required',
				408	=> 'Request Timeout',
				409	=> 'Conflict',
				410	=> 'Gone',
				411	=> 'Length Required',
				412	=> 'Precondition Failed',
				413	=> 'Request Entity Too Large',
				414	=> 'Request-URI Too Long',
				415	=> 'Unsupported Media Type',
				416	=> 'Requested Range Not Satisfiable',
				417	=> 'Expectation Failed',

				500	=> 'Internal Server Error',
				501	=> 'Not Implemented',
				502	=> 'Bad Gateway',
				503	=> 'Service Unavailable',
				504	=> 'Gateway Timeout',
				505	=> 'HTTP Version Not Supported'
				);
		header('HTTP/1.1 '.$code.' '.$status[$code]);
	}
	
	function parse_request()
	{
		$init_script="main";
		$init_class="main";
		$url_params=array();
		$request=trim($_GET[$this->request_variable], "/");
		unset($_GET[$this->request_variable]);
		if($request!=="")
		{
			$request=explode("/", $request);
			$init_script=$request[0];
			if(isset($request[1]))
			{
				$init_class=$request[1];
				if(sizeOf($request>2))
				{
					array_splice($request, 0, 2);
					$url_params=$request;
				}
			}
		}
		return(array('script'=>$init_script, 'class'=>$init_class, 'url_params'=>$url_params, 'method'=>$_SERVER['REQUEST_METHOD']));
	}
}


class Controller
{

	function params()
	{
		$params=array();
		$method=$_SERVER['REQUEST_METHOD'];
		if($method=="GET")
		{
			$params=$_GET;
		}
		else if($method=="POST")
		{
			$params=$_POST;
		}
		else if($method=="PUT")
		{
			parse_str(file_get_contents('php://input'), $params);
		}
		else if($method=="DELETE")
		{
			$params=array();
		}
		return $params;
	}
	
	function DB()
    {   
		include_once('.'.DS.'conf'.DS.'db.php');
		if(isset($db['active']) && is_array($db[$db['active']]))
		{ 
			include_once('.'.DS.'engine'.DS.'ez_sql.php');
			$dbobj;
			if($db[$db['active']]['type']=='mysql')
			{
				$dbobj = new ezSQL_mysql(
									$db[$db['active']]['user'],
									$db[$db['active']]['pass'],
									$db[$db['active']]['db'],
									$db[$db['active']]['host']
									);
			}
			return $dbobj;	
		}
		return false;
    }
}

$SRF=new SRF();
$SRF->run();