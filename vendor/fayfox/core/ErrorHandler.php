<?php
namespace fayfox\core;

class ErrorHandler extends FBase{
	public $app;
	
	public function __construct(){
		$this->app = \F::app();
		$this->app || $this->app = new Controller();
	}
	
	/**
	 * 接管PHP自带报错
	 */
	public function register(){
		ini_set('display_errors', false);
		set_exception_handler(array($this, 'handleException'));
		set_error_handler(array($this, 'handleError'));
		register_shutdown_function([$this, 'handleFatalError']);
	}
	
	/**
	 * 处理未捕获的异常
	 * @param ErrorException $exception
	 */
	public function handleException($exception){
		if($exception instanceof HttpException){
			//404, 500等http错误
			if($this->config('debug') && $exception->statusCode != 200){
				$this->renderDebug($exception);
			}
			if($exception->statusCode == 404){
				$this->render404();
			}else if($exception->statusCode == 500){
				$this->render500();
			}else{
				$this->renderError($exception);
			}
		}
	}
	
	/**
	 * 处理php报错
	 */
	public function handleError($code, $message, $file, $line){
		$exception = new ErrorException($message, $code, $code, $file, $line);
		if($this->config('debug')){
			//debug模式，强制执行debug报错并停止程序执行
			$this->renderDebug($exception);
		}else{
			$this->renderPHPError($exception);
		}
	}
	
	/**
	 * 处理致命错误
	 */
	public function handleFatalError(){
		$error = error_get_last();
		if(ErrorException::isFatalError($error)){
			$exception = new ErrorException($error['message'], $error['type'], $error['type'], $error['file'], $error['line']);
			
			if($this->config('debug')){
				//debug模式，强制执行debug报错并停止程序执行
				$this->renderDebug($exception);
			}else{
				$this->render500();
			}
			die;
		}
	}
	
	/**
	 * @param ErrorException $exception
	 */
	protected function renderException($exception){
		echo '<br>renderException<br><pre>';
		print_r($exception);
		echo '</pre>';
	}
	
	/**
	 * @param ErrorException $exception
	 */
	public function renderDebug($exception){
		//清空缓冲区
		$this->clearOutput();
		
		$this->app->view->renderPartial('errors/debug', array(
			'exception'=>$exception,
		));
		die;
	}
	
	/**
	 * @param ErrorException $exception
	 */
	public function renderError($exception){
		echo '//@todo 常规报错:<br>';
		pr($exception);
	}

	/**
	 * @param ErrorException $exception
	 */
	public function renderPHPError($exception){
		$this->app->view->renderPartial('errors/php', array(
			'level'=>$exception->getLevel(),
			'message'=>$exception->getMessage(),
			'file'=>$exception->getFile(),
			'line'=>$exception->getLine(),
		));
	}
	
	/**
	 * 显示404页面（不包含错误信息）
	 */
	public function render404(){
		$this->clearOutput();
		$this->app->view->renderPartial('errors/404');
		die;
	}
	
	/**
	 * 显示500页面（不包含错误信息）
	 */
	public function render500(){
		$this->clearOutput();
		$this->app->view->renderPartial('errors/500');
		die;
	}
	
	/**
	 * 清楚所有未输出的缓冲区
	 */
	public function clearOutput()
	{
		for ($level = ob_get_level(); $level > 0; --$level) {
			if (!@ob_end_clean()) {
				ob_clean();
			}
		}
	}
}