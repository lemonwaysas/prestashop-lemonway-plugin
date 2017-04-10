<?php
abstract class Method{
	
	protected $code = null;
	protected $template = null;
	protected $data = array();
	
	protected $context=null;
	protected $module=null;
	
	protected  $isSplitpayment = false;
	
	
	public function __construct(){
		if(!$this->code){
			throw new Exception('You must to set code to your payment method!', 500);
		}
		
		if(!$this->template){
			throw new Exception('You must to define a template for your payment method!', 500);
		}
		
		$this->context = Context::getContext();
		$this->module =  Module::getInstanceByName('lemonway');
		
		$this->code = strtoupper($this->code);
	}
	
	protected function prepareData(){
		return $this;
	}
	
	public function getCode(){
		return $this->code;
	}
	
	public function isActive(){
		return $this->getConfig('enabled');
	}
	
	public  function getTitle(){
		return $this->getConfig('title');
	}
	
	public function isValid(){
		return $this->isActive() && $this->getTitle();
	}
	
	public function getData($key){
		$this->prepareData();
		return isset($this->data[$key]) ? $this->data[$key] : null;
	}
	
	public function getTemplate(){
		return _PS_MODULE_DIR_ . 'lemonway/views/templates/front/methods/' . $this->template;
	}
	
	public function getConfig($key){
		return Configuration::get('LEMONWAY_' . $this->code . '_' . strtoupper($key));
	}
	
	/**
	 * @return Lemonway
	 */
	public function getModule(){
		return $this->module;
	}
	
	public function isSplitPayment(){
		return $this->isSplitpayment;
	}
	
	
	public function isAllowed(){
		 
		if(!$this->isActive()){
			return false;
		}
		 
		switch( $this->getCode()){
	
			case "creditcard_xtimes":
				if(!in_array(Tools::getValue('splitpayment_profile_id'),$this->getModule()->getSplitpaymentProfiles())){
					return false;
				}
				else{
					return true;
				}
				 
			default:
				return true;
	
		}
		 
		return false;
	}
}