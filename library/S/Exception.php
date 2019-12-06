<?php
/**
 * Главният клас за създаване на изключения
 * 
 * @author Pafel
 * @version 1.0.0
 */
class S_Exception extends Zend_Exception
{
	/**
     * Construct the exception
     *
     * @param  string $msg
     * @param  int $code
     * @param  Exception $previous
     * @return void
     */
	public function __construct($message, int $code, $previous)
	{
		parent::__construct($message, $code, $previous);
	}
}