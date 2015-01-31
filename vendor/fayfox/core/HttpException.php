<?php
namespace fayfox\core;

class HttpException extends \Exception
{
	/**
	 * @var integer HTTP 状态码, 例如403, 404, 500等
	 */
	public $statusCode;

	public function __construct($status, $message = null, $code = 0, \Exception $previous = null)
	{
		$this->statusCode = $status;
		parent::__construct($message, $code, $previous);
	}

	/**
	 * @return 返回一个状态码描述
	 */
	public function getName(){
		if (isset(Response::$httpStatuses[$this->statusCode])) {
			return Response::$httpStatuses[$this->statusCode];
		} else {
			return 'Error';
		}
	}
}
