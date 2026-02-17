<?php

/*
__PocketMine Plugin__
name=DeathSwap
description=Inspired by Sethbling
version=1.1
author=SuperChipsLP
class=deathswap
apiversion=10,11,12
*/

    class deathswap implements plugin{
    private $api;
    private $started = false;
    private $time = 30;
    private $invincible = true;
    public function __construct(ServerAPI $api, $server = false){
		$this->api = $api;
	}
	public function init(){
	    $this->api->addHandler("player.connect", array($this, "eventHandler"), 100);
	    $this->api->addHandler("player.death", array($this, "eventHandler"), 100);
	    $this->api->addHandler("player.quit", array($this, "eventHandler"), 100);
	    $this->api->addHandler("entity.health.change", array($this, "eventHandler"), 100);
	    $this->api->console->register("ds", "Deathswap", array($this, "commandHandler"));
        console(FORMAT_YELLOW."[DeathSwap] Started Deathswap");
        console(FORMAT_RED."Tip: You should play with a PC Ported Map.");
        console(FORMAT_RED."This is not a must have, but, it will be");
        console(FORMAT_RED."much more fun that it would be with a default mcpe world."); 
    }
    public function __destruct(){

    }
    
    public function commandHandler($cmd, $params, $issuer, $alias){
        switch($params[0]){
            case '':
            return "Usage: /ds <start|stop>";
            break;
            case 'start':
                if($this->started === false){
                    if(count($this->api->player->getAll()) < 2){
                        return "[DeathSwap] You need 2 players or more to start!";
                    } else {
                        $this->api->chat->broadcast("[DeathSwap] DeathSwap has been started!");
                        $this->started = true;
                        $t = rand(40, 120);
                        $this->api->schedule(20 * 60, array($this, "schedule") ,true, 'server.schedule');
                        $this->api->schedule(20 * $t, array($this, "swap") ,true, 'server.schedule');
                        $this->time = 30;
                    }
                }else{
                    return "[DeathSwap] Game is already running!";        
                }
            break;
            case 'stop':
                if($this->started === true){
                    $this->started = false;
                    $this->time = 20;
                    $this->api->chat->broadcast("[DeathSwap] The game has ended!");    
                }else{
                    return "[DeathSwap] No game is running!";
                }
            break;
            case 'getmap':
                return "Not yet implemented. Visit the forums to check for updates.";
            break;    
        }
    }
    
    public function schedule(){
        if($this->started === false){   
        } else {
        $this->time--;
        if($this->time == 15){
            $this->invincible = false;
            $this->api->chat->broadcast("[DeathSwap] You are no longer invincible!");    
        }
        if($this->time == 0){
            $this->api->chat->broadcast("[DeathSwap] The game has ended!");
            $this->started = false;
            $this->time = 30;
        }
        console(FORMAT_YELLOW."[DeathSwap] DeathSwap ends in ".$this->time." minutes!");
        if(count($this->api->player->getAll()) == 1){
                        $this->api->console->run("ds stop");
        }   
    }
    }
    
    public function swap(){
        
    $all = $this->api->player->getAll();
    shuffle($all);
        $times = 0;

        foreach($all as $player){
            $times++;
            if($times == 1){
                $x = $player->entity->x;
                $y = $player->entity->y;
                $z = $player->entity->z;
                $l = $player->level->getName();
            }
            if($times == 2){
                $x2 = $player->entity->x;
                $y2 = $player->entity->y;
                $z2 = $player->entity->z;
                $l2 = $player->level->getName();
                
                $this->api->console->run("tp ".$all[0]->username." w:".$l2);
                $this->api->console->run("tp ".$all[0]->username." ".$x2." ".$y2." ".$z2);                  
                $this->api->console->run("tp ".$player->username." w:".$l);
                $this->api->console->run("tp ".$player->username." ".$x." ".$y." ".$z);
                $player->sendChat("You have swapped the position with ".$all[0]->username);
                $all[0]->sendChat("You have swapped the position with ".$player->username);
                console(FORMAT_BLUE."[DeathSwap] Swapped positions of ".$all[0]->username." and ".$player->username);
            }
        }
    }
    
    public function eventHandler($data, $event)
    {
        switch($event)
            {   
            case 'player.connect':
                if($this->started === true){
                    return false;
                }
            break;
            case 'player.death':
                if($this->started === true){
                    $data["player"]->close();
                    if(count($this->api->player->getAll()) == 1){
                        $this->api->console->run("ds stop");
                    }
                }
            break;
            case 'player.quit':
                if($this->started === true){
                    if(count($this->api->player->getAll()) == 1){
                        $this->api->console->run("ds stop");   
                    }
                }
            break;
            case 'entity.health.change':
                if($this->invincible === true){
                    return false;
                }
            break;
            }

    }    

}
