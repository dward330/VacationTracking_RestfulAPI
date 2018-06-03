<?php 
Class DatabaseDisconnectedException extends Exception{
		protected $message;
		
		public function __construct($message, $code = 0, Exception $previous = null){
			
			$this->message = $message;
			
			parent::__construct($message, $code, $previous);
		}
		
		public function __toString(){
			return ($this->message.
					"\n<br />File: ".$this->getTrace()[0]['file'].
					"\n<br />Line Number: ".$this->getTrace()[0]['line'].
					"\n<br />Function: ".$this->getTrace()[0]['function'].
					"\n<br />Class: ".$this->getTrace()[0]['class']);
		}
		
	}
?>