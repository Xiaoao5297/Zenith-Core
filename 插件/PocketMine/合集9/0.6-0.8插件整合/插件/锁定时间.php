<?php

/*
__PocketMine Plugin__
name=RealTime
description=Manage time
version=1.2.2
author=Guillaume351
class=TimeSet
apiversion=12
*/

class TimeSet implements Plugin {

   public function __construct(ServerAPI $api, $server = false) {
$this->api = $api;
}

   
   public function init(){
   	$this->path =$this->api->plugin->configPath($this);
   
	
	
	
    

	
	$Message =  "In the file config, put only number to correct the hour. For exemple, if for you server it's 8am but it is 2pm for you, put +6.
	If it is 2pm for your server but 8am for you, put -6.
	If Tell time is true, players will be tell the time each hours.
	To change manually the mode, in world-mode.txt : 0= real time, 1 = always day and 2 = always night, 3 = normal time, 4 = sunset, 5 = sunrise.
	/rtday : lock time to day
	/rtnight : lock time to night
	/rt : enable realtime
	/rtnormal : disable all time changes, back to normal time
	/rtsunset : lock time to sunset
	/rtsunrise : lock time to sunrise
	/hour : tell a player the time (real hour)"
	;
	
	$this->config = new Config($this->api->plugin->configPath($this)."config.txt", CONFIG_YAML, array(
	"Time changes" =>'+0',
	"Tell time" => "true"));
		
		$read =$this->api->plugin->readYAML($this->path . "config.txt");
		$ttime = $read["Tell time"];

		new Config($this->api->plugin->configPath($this)."READ ME.txt");
	
	file_put_contents($this->path . "READ ME.txt",$Message);
	
	$this->api->console->register("rtday", "have always day", array($this, "mode"));	
	$this->api->console->register("rt", "have real time", array($this, "mode"));	
	$this->api->console->register("rtnight", "have always day", array($this, "mode"));
	$this->api->console->register("rtsunset", "have always sunset", array($this, "mode"));	
	$this->api->console->register("rtsunrise", "have always sunrise", array($this, "mode"));	
	$this->api->console->register("rtnormal", "have normal time", array($this, "mode"));	
	$this->api->console->register("hour", "tell the hour", array($this, "tellMeTime"));		
	$this->api->ban->cmdWhitelist("hour");

	
$modify = ($this->config->get("Time changes"));


$test = date('H') + ($modify);

		if ($test > 23){
		$test = $test - 24;
	}
	
console("[RealTime] It is " . $test. " (20h is same as 8pm)");
$this->api->schedule(1,array($this,"changeTime"),true, 'server.schedule');
if ($ttime == "true"){
	$this->api->schedule(20*60*20,array($this,"tellTime"),true, 'server.schedule');

}else{}
   }
   
  
   public function mode ($cmd, $params, $issuer, $alias, $args, $issuer){

if(!($issuer instanceof Player)){
console("[RealTime] Please run this command in-game.\n");

}

		else{

   	$world = $issuer->entity->level->getName();
   					  switch($cmd){
   					  	
	    case "rtday":
			
   	file_put_contents($this->api->plugin->configPath($this) . $world."-mode.txt","1");
   	console("[RealTime] Time lock to day for the world ".$world);
	
	
   $reponse = ("[RealTime] Time lock to day for the world ".$world);
	return $reponse;
   	break;
    
case "rtnight":
   	file_put_contents($this->api->plugin->configPath($this) . $world."-mode.txt","2");
   	console("[RealTime] Time lock to night for the world ".$world);
		$reponse = ("[RealTime] Time lock to night for the world ".$world);
	return $reponse;
   	break;
   	
   	case "rtnormal":
   	file_put_contents($this->api->plugin->configPath($this) .$world. "-mode.txt","3");
   	console("[RealTime] Time unlock (back to normal time) for the world ".$world);
		$reponse = ("[RealTime] Time unlock (back to normal time) for the world ".$world);
	return $reponse;
   	break;
   	
   	case "rtsunset":
   	file_put_contents($this->api->plugin->configPath($this) .$world. "-mode.txt","4");
   	console("[RealTime] Time lock to sunset for the world ".$world);
		$reponse = ("[RealTime] Time lock to sunset for the world " . $world);
	return $reponse;
   	break;
	
	case "rtsunrise":
   	file_put_contents($this->api->plugin->configPath($this) .$world. "-mode.txt","5");
   	console("[RealTime] Time lock to sunrise for the world ".$world);
		$reponse = ("[RealTime] Time lock to sunrise for the world ". $world);
	return $reponse;
   	break;
   	
   	case "rt":
   	file_put_contents($this->api->plugin->configPath($this) . $world."-mode.txt","0");
   	console("[RealTime] RealTime enabled for the world ".$world);
		
		$reponse = ("[RealTime] RealTime enabled for the world ".$world);
	return $reponse;
   	break;

	}
	
	}
	
}
    
    public function changeTime(){
    	$this->path =$this->api->plugin->configPath($this);
 $modify = ($this->config->get("Time changes"));
	$times = date('H');
	$time = $times + ($modify);
	$ticks = 0;
	

	$monde = $this->api->level->getAll();
	foreach($monde as $w){
		if(!file_exists($this->path . $w->getName()."-mode.txt")) {
file_put_contents($this->path . $w->getName()."-mode.txt","3");
		$mode = 0;
		}
		
	else{
		$mode= file_get_contents($this->api->plugin->configPath($this) . $w->getName()."-mode.txt");
		
	
	
	
	if ($mode == 1){
		$ticks = 6001;
	}
	if ($mode == 4){
		$ticks = 10001;
	}
	if ($mode == 5){
		$ticks = 18551;
	}

	if ($mode == 2){
		$ticks = 13751;		
	}
	
	if ($mode == 0){
		if ($time > 23){
		$time = $time - 24;
	}
		
	if ($time == 0) {
		$ticks = 13750;
		}
			if ($time == 24) {
		$ticks = 13750;
		}
	if ($time == 1) {
		$ticks = 15001;
		}	
	if ($time == 2) {
		$ticks = 15501;
		}	
	if ($time == 3) {
		$ticks = 16001;
		}	
	if ($time == 4) {
		$ticks = 16501;
		}	
	if ($time == 5) {
		$ticks = 17001;
		}	
	if ($time == 6) {
		$ticks = 17501;
		}	
	if ($time == 7) {
		$ticks = 18001;
		}	
	if ($time == 8) {
		$ticks = 1;
		}	
	if ($time == 9) {
		$ticks = 1001;
		}	
	if ($time == 10) {
		$ticks = 2001;
		}	
	if ($time == 11) {
		$ticks = 3001;
		}	
	if ($time == 12) {
		$ticks = 4001;
		}	
	if ($time == 13) {
		$ticks = 5001;
		}	
	if ($time == 14) {
		$ticks = 6001;
		}	
	if ($time == 15) {
		$ticks = 7001;
		}	
	if ($time == 16) {
		$ticks = 8251;
		}	
	if ($time == 17) {
		$ticks = 9251;
		}	
	if ($time == 18) {
		$ticks = 9751;
		}	
	if ($time == 19) {
		$ticks = 10001;
		}	
	if ($time == 20) {
		$ticks = 11001;
		}	
	if ($time == 21) {
		$ticks = 12001;
		}	
	if ($time == 22) {
		$ticks = 12751;
		}	
	if ($time == 23) {
		$ticks = 13501;
		}
}

else{
	$this->api->time->set($ticks,$w);
	}
}

}
	}
    public function tellTime(){
    		
    	 $modify = ($this->config->get("Time changes"));
	$times = date('H');
	$minutes = date('i');
	$time = $times + ($modify);

		if ($time > 23){
		$time = $time - 24;
	}
	

	$this->api->chat->broadcast("[RealTime] It is ". $time .":". $minutes ."");
	}
	   public function tellMeTime($issuer){
	  

    		
    	 $modify = ($this->config->get("Time changes"));
	$times = date('H');
	$minutes = date('i');
	$time = $times + ($modify);
			if ($time > 23){
		$time = $time - 24;
	}
	$reponse = ("[RealTime] It is ". $time .":". $minutes ."");
	return $reponse;
	}
    
 public function __destruct(){}
    
}
 
?>