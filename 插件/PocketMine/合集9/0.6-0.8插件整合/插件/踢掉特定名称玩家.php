<?php

/*
__PocketMine Plugin__
name=Nicknames
version=0.1
author=Junyi00
class=Nicknames
apiversion=10
*/

class Nicknames implements Plugin{
	private $api, $path, $config;
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function init(){
		$this->path = $this->api->plugin->configPath($this); 
		$this->config = new Config($this->path."config.yml", CONFIG_YAML, array());
		$this->api->console->register("nick", "<[blank]/[player name]>  Get your kill death count", array($this, "NickPlayer"));
		$this->api->console->register("realname", "<[blank]/[player name]>  Get your kill death count", array($this, "getRealName"));
		$this->api->addHandler("player.chat", array($this, "handler"), 5);
	}
    
    public function __destruct() {}
    
    public function CheckExists($nick) {
    	$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
    	foreach ($cfg as &$val) {
    		$name = $cfg["$val"];
    		if ($name === $nick) {
    			return true;
    		}
    	}
    	return false;
    }
    
    public function getNick($name) { 
    	$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
    	if (ARRAY_KEY_EXISTS("$name", $cfg)) {
    		$nick = $cfg["$name"];
    		return $nick;
    	}
    	else {
    		return false;	
    	}
    	return false;
    }
    
    public function NickPlayer($cmd, $arg, $issuer) {
    	$ms = "";
    	$name = $issuer->username;
    	$nick = $arg[0];
    	
    	if ($this->CheckExists === true) {
    		$ms = "Nickname already exists, please choose another nickname";
    		return $ms;
    	}
    	
    	$newData = array( "$name" => $nick );
    	$this->overwriteConfig($newData);
    	
    	$ms = "Nickname saved!";
    	return $ms;
    }
    
    public function getRealName($cmd, $arg, $issuer) {
    	$ms = "";
    	$nick = $arg[0];
    	$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
    	$realname = "";
    	$exists = false;
    	
    	foreach ($cfg as $name => $val) {
    		console($val." : ".$nick);
    		if ($val === $nick) {
    			$realname = $name;
    			$exists = true;
    		}
    	}
    	
    	if ($exists == false) {
    		$ms = "This nick does not exists";
    		return $ms;
    	}
    	$ms = "The user with nick *".$nick."* is ".$realname;
    	return $ms;
    }
    
    public function handler($data, $event) {
    	$name = $data['player']->username;
    	$message = $data['message'];
    	
    	$nick = $this->getNick($name);
    	if ($nick === false) {
    		return true;
    	}
    	else {
    		$ms = "<*".$nick."*> ".$message;
    		$this->api->chat->broadcast($ms);
    		return false;
    	}
    }
    
    private function overwriteConfig($dat){
		$cfg = array();
		$cfg = $this->api->plugin->readYAML($this->path . "config.yml");
		$result = array_merge($cfg, $dat);
		$this->api->plugin->writeYAML($this->path."config.yml", $result);
	}

}