<?php

/*
__PocketMine Plugin__
name=Chat Channels
description=Allows you to channel the chat
version=0.1.1
author=Falk
class=chatChannels
apiversion=10,11,12,13
*/
		
class chatChannels implements Plugin{	
	private $api;

	private $server;
		
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
		$server = ServerAPI::request();
	}
	
	public function init(){	
			$this->api->addHandler("player.chat", array($this, "eventHandler"), 100);
			$this->api->console->register("chan", "Select a chat channel", array($this, "selectChan"));
			$this->api->ban->cmdWhitelist("chan");
			}
	public function __destruct(){}
	public function selectChan($cmd, $params, $issuer, $alias, $args){
if(!($issuer instanceof Player)) return "Please run this command in game";
 if(isset($params[0])){
	if ($params[0] == "main") {
		unset($this->chan[$issuer->username]);
		return "You are now on main channel";
	}
	else {
	$this->chan[$issuer->username] = $params[0];
	return $params[0] . " is your new channel";
	}
}
else{
	if(!isset($this->chan[$issuer->username])) return "You are in the main channel.";
		$issuer->sendChat("Channel: " . $this->chan[$issuer->username]);
		if(count(array_keys($this->chan,$this->chan[$username])) > 1){
		$issuer->sendChat("Current chan members:");
		foreach(array_keys($this->chan,$this->chan[$issuer->username]) as $p) $ret .= ", " . $p;
		return $ret;
	}
	else return "You are alone in this channel";
}
}
	
	public function eventHandler($data, $event){
	if (isset($this->chan[$data['player']->username])) $this->api->chat->send($data['player']->username,$data['message'],array_keys($this->chan,$this->chan[$data['player']->username]));
    else return true;
    return false;

}
}