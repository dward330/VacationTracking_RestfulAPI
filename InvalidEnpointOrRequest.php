<?php 
	

	Class InvalidEndpointOrRequest extends Exception{
		protected $message;
		
		public function __construct($message, $code = 0, Exception $previous = null){
			
			$this->message = $message;
			
			parent::__construct($message, $code, $previous);
		}
		
		public function __toString(){
			return ($this->message);
					
		}
		
	}
?>