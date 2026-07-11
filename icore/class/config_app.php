<?php

class AppConfig {
    private static $instance = null;
    
    private $internetAddress;
    private $localAddress;
    private $apiUsername;
    private $apiPassword;
    private $apiSecretKey;
    
    private function __construct() {
        // Initialize default values for addresses
        $this->internetAddress = "";
        $this->localAddress = "";
        $this->apiUsername = "borobe";
        $this->apiPassword = "Massbin44@2";
        $this->apiSecretKey = "a38d4ec2a38d4ec2";
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getInternetAddress() {
        return $this->internetAddress;
    }
    
    public function setInternetAddress($address) {
        $this->internetAddress = $address;
    }
    
    public function getLocalAddress() {
        return $this->localAddress;
    }
    
    public function setLocalAddress($address) {
        $this->localAddress = $address;
    }

	/**
	 * @return mixed
	 */
	public function getApiPassword() {
		return $this->apiPassword;
	}
	
	/**
	 * @param mixed $apiPassword 
	 * @return self
	 */
	public function setApiPassword($apiPassword): self {
		$this->apiPassword = $apiPassword;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getApiSecretKey() {
		return $this->apiSecretKey;
	}
	
	/**
	 * @param mixed $apiSecretKey 
	 * @return self
	 */
	public function setApiSecretKey($apiSecretKey): self {
		$this->apiSecretKey = $apiSecretKey;
		return $this;
	}

    	/**
	 * @return mixed
	 */
	public function getApiUsername() {
		return $this->apiUsername;
	}
	
	/**
	 * @param mixed $apiUsername 
	 * @return self
	 */
	public function setApiUsername($apiUsername): self {
		$this->apiUsername = $apiUsername;
		return $this;
	}
}