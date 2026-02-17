<?php

/*
__PocketMine Plugin__
name=AdminTools
description=All Settings that admins need
version=1.4
author=SuperChipsLP
class=AdminSuit
apiversion=10
*/


class AdminSuit implements Plugin{

	private $api;
	
	private $damage = true;
	private $maintance = false;
	private $breakBlocks = true;
	private $placeBlocks = true;
	private $chatAllow = true;
	private $moveAllow = true;
	
	public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	
	public function init(){
		$this->api->addHandler("entity.health.change", array($this, "eventHandler"), 100);
		$this->api->addHandler("player.block.break", array($this, "eventHandler"), 100);
		$this->api->addHandler("player.block.place", array($this, "eventHandler"), 100);
		$this->api->addHandler("player.chat", array($this, "eventHandler"), 100);
		$this->api->addHandler("player.move", array($this, "eventHandler"), 100);
		$this->api->addHandler("player.quit", array($this, "eventHandler"), 100);
		$this->api->addHandler("player.connect", array($this, "eventHandler"), 100);
		$this->api->addHandler("player.death", array($this, "eventHandler"), 100);
		$this->api->addHandler("player.spawn", array($this, "eventHandler"), 100);
		$this->api->console->register("damage", "Toggles Damage", array($this, "pvpcmdHandler"));
		$this->api->console->register("break", "Toggles Block breaking", array($this, "breakHandler"));
		$this->api->console->register("chat", "Toggles Chat", array($this, "chatHandler"));
		$this->api->console->register("move", "Toggles Moving", array($this, "moveHandler"));
		$this->api->console->register("gm", "gamemode", array($this, "gmHandler"));
		$this->api->console->register("place", "Toggles Block placing", array($this, "placeHandler"));
		$this->api->console->register("maintance", "Toggles Maintance mode", array($this, "placeHandler"));
		$this->api->console->register("random", "Gives a random number", array($this, "rndHandler"));
		$this->config = new Config($this->api->plugin->configPath($this)."config.yml", CONFIG_YAML, array(
            "break-blocks" => true,
            "place-blocks" => true,
			"damage"       => true,
			"chat"		   => true,
			"welcomeMessage" => "Welcome to the server!",
			"deathMessage" => "A player has died. I feel sorry now :(",
			"leaveMessage" => "It makes me sad that a player left :(",
			"chatoffMessage" => "Sorry. Chat is currently disabled!",
			"owner" => "console",
			));
		 $this->config->reload();
		 
		$this->api->ban->cmdWhitelist("random"); 
		 
		console("[AdminSettings] Welcome Message: ".$this->config->get('welcomeMessage'));
		console("[AdminSettings] Death Message: ".$this->config->get('deathMessage'));
		console("[AdminSettings] Leave Message: ".$this->config->get('leaveMessage'));
		
		$this->damage = $this->config->get('damage');
		$this->breakBlocks = $this->config->get('break-blocks');
		$this->placeBlocks = $this->config->get('place-blocks');
		$this->chatAllow = $this->config->get('chat');
		
		console("[AdminSettings] Plugin started...");
	}
	
public function eventHandler($data, $event)
	{
		switch($event)
		{
	case 'entity.health.change':
	
	if($this->damage == false)
	{
	return false;
	}
	
	break;
	case 'player.connect':
	
	if($this->maintance == true)
	{
	return false;
	}
	
	break;
	case 'player.block.break':
	
	if($this->breakBlocks == false)
	{
	return false;
	}
	
	break;
	case 'player.chat':
	
	if($this->chatAllow == false)
	{
	
	$data['player']->sendChat($this->config->get('chatoffMessage')); 
	
	return false;
	
	}
	
	break;
	case 'player.block.place':
	
	if($this->placeBlocks == false)
	{
	return false;
	}
	
	break;
	case 'player.move':
	
	if($this->moveAllow == false)
	{
	return false;
	}
	
	break;
	case 'player.spawn':
	
	$data->sendChat("<".$this->config->get('owner')."> ".$this->config->get('welcomeMessage'));
	
	break;
	case 'player.death':
	
	$this->api->chat->broadcast("<".$this->config->get('owner')."> ".$this->config->get('deathMessage'));
	
	break;
	case 'player.quit':
	
	$this->api->chat->broadcast("<".$this->config->get('owner')."> ".$this->config->get('leaveMessage'));
	
	break;
	}
	}
	
	public function pvpcmdHandler($cmd, $params, $issuer, $alias){
	
	if($cmd = "damage")
	{
	
	switch(array_shift($params)){
			case "":
				$output = "Usage: /damage <on/off>";
				break;
			case "on":
				$output = "Damage is now on!";
				$this->api->chat->broadcast("[AS] Damage is now on!");
				$this->damage = true;
				break;
			case "off":
				$output = "Damage is now off!";
				$this->api->chat->broadcast("[AS] Damage is now off!");
				$this->damage = false;
				break;	
		}
		
		return $output;
	
	}
	
	}
	
	public function chatHandler($cmd, $params, $issuer, $alias){
	
	if($cmd = "chat")
	{
	
	switch(array_shift($params)){
			case "":
				$output = "Usage: /chat <on/off>";
				break;
			case "on":
				$output = "Chat is now on!";
				$this->api->chat->broadcast("[AS] Chat is now on!");
				$this->chatAllow = true;
				break;
			case "off":
				$output = "Chat is now off!";
				$this->api->chat->broadcast("[AS] Chat is now off!");
				$this->chatAllow = false;
				break;	
		}
		
		return $output;
	
	}
	
	}
	
	public function maintanceHandler($cmd, $params, $issuer, $alias){
	
	if($cmd = "maintance")
	{
	
	switch(array_shift($params)){
			case "":
				$output = "Usage: /maintance <on/off>";
				break;
			case "on":
				$this->api->chat->broadcast("Maintance has been enabled!");
				$this->maintance = true;
				break;
			case "off":
				$this->api->chat->broadcast("Maintance has been disabled!");
				$this->maintance = false;
				break;	
		}
		
		return $output;
	
	}
	
	}
	
	public function moveHandler($cmd, $params, $issuer, $alias){
	
	if($cmd = "move")
	{
	
	switch(array_shift($params)){
			case "":
				$output = "Usage: /move <on/off>";
				break;
			case "on":
				$output = "Moving is now enabled!";
				$this->api->chat->broadcast("[AS] Moving is enabled!");
				$this->moveAllow = true;
				break;
			case "off":
				$output = "Moving is now disabled!";
				$this->api->chat->broadcast("[AS] Moving is disabled!");
				$this->moveAllow = false;
				break;	
		}
		
		return $output;
	
	}
	
	}
	
	public function breakHandler($cmd, $params, $issuer, $alias){
	
	if($cmd = "break")
	{
	
	switch(array_shift($params)){
			case "":
				$output = "Usage: /break <on/off>";
				break;
			case "on":
				$output = "Players can now break Blocks!";
				$this->api->chat->broadcast("[AS] Players can now break Blocks!");
				$this->breakBlocks = true;
				break;
			case "off":
				$output = "Players can no longer break Blocks!";
				$this->api->chat->broadcast("[AS] Players can no longer break Blocks!");
				$this->breakBlocks = false;
				break;	
		}
		
		return $output;
	
	}
	
	}
	
	public function placeHandler($cmd, $params, $issuer, $alias){
	
	if($cmd = "place")
	{
	
	switch(array_shift($params)){
			case "":
				$output = "Usage: /place <on/off>";
				break;
			case "on":
				$output = "Players can now place Blocks!";
				$this->api->chat->broadcast("[AS] Players can now place Blocks!");
				$this->placeBlocks = true;
				break;
			case "off":
				$output = "Players can no longer place Blocks!";
				$this->api->chat->broadcast("[AS] Players can no longer place Blocks!");
				$this->placeBlocks = false;
				break;	
		}
		
		return $output;
	
	}
	
	}
	
		public function rndHandler($cmd, $params, $issuer, $alias){
	
	if($cmd = "random")
	{
	
	$arg1 = $params[0];
	$arg2 = $params[1];
	
	$rand = rand($arg1, $arg2);
	
	$output = "Your random number is: ".$rand;
		
	return $output;
	
	}
	
	}
	
	public function gmHandler($cmd, $params, $issuer, $alias){
	
	if($cmd = "gm")
	{
	
	switch(array_shift($params)){
			case "":
				$output = "Usage: /gm <0/1/2/3>";
				break;
			case "0":
				$issuer->setGamemode(0);
				break;
			case "1":
				$issuer->setGamemode(1);
				break;
			case "2":
				$issuer->setGamemode(2);
				break;
			case "3":
				$issuer->setGamemode(3);
				break;					
		}
		
		return $output;
	
	}
	
	}
	
	
	public function __destruct(){
	}

}