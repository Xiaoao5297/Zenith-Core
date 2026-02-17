<?php

/*
__PocketMine Plugin__
name=NoStupidNames
version=0.1.1
author=Snake1999
class=NoStupidNames
apiversion=10
*/


/* 
Small Changelog
===============

0.0.1:
- First Release.

0.1.0:
- Add config for seraching "dirty words";
- Names have continuous repetition of characters won't come to server;
- Deleted sheld for empty names;
- Fixes.

0.1.1:
- Better config;
- Fixes.

0.1.2:
- Supposed to Alpha_1.3.9

*/

class NoStupidNames implements Plugin
{
	private $api, $config, $strs;
	
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function init(){
		$this->config = new Config($this->api->plugin->configPath($this) . "config.yml", CONFIG_YAML);
		if (!$this->config->exists("Dirtynames")) {
			$this->config->set("Dirtynames", array('Keywords' => "fuck|shit"));
		}
		$this->strs = explode("|", $this->config->get("Dirtynames")['Keywords']);
		$this->config->save();
		$this->api->addHandler("player.connect", array($this, "handler"), 5);
	}
	
	public function __destruct(){
		$this->config->save();
	}
	
	public function handler($data, $event){
		switch($event){
			case "player.connect":
                                $long = strlen($username);
				foreach($a as $strs){
					$pos = $pos && strpos($username, $a);
				}
				for($a = 0;$a < $long; $a++);{
					$b = substr($username, $a, 1);
					$pos = $pos && !strcmp(substr($username, $a + 1, 1),$b);
				}
				return $pos;
                }
	}
}