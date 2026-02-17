<?php
 
/*
__PocketMine Plugin__
name=Spawn+
description=Set spawn in world
version=1.2
author=Topic
class=SpawnPlus
apiversion=9,10,11,12
*/

class SpawnPlus implements Plugin{  
    private $api;

    public function __construct(ServerAPI $api, $server = false){
        $this->api = $api;
        $this->server = ServerAPI::request();
    }
    public function init(){
        $this->api->console->register("setspawn", "Set Spawn of world", array($this, "commandHandler"));
        $this->api->console->register("spawn", "Teleport to spawn", array($this, "commandHandler"));
        $this->api->ban->cmdwhitelist("spawn");
        console(FORMAT_GREEN."[INFO] Spawn+ enabled");
    }
    public function __destruct() {}

    public function commandHandler($cmd, $args, $issuer, $params, $alias){
        switch($cmd){
            case "setspawn":
                 if(!($issuer instanceof Player)){
                    console("[Spawn+]: Run command in game");
                    break;
                }
                switch($args[0]){
                    case "":
                        $position = new Vector3($issuer->entity->x, $issuer->entity->y, $issuer->entity->z);
                        $level = $issuer->level->getName();
                        $this->api->level->get($level)->setSpawn($position);
                        $issuer->sendChat("[Spawn+]: Spawn set in world: ".$level);
                    break;
                    case "help":
                                $issuer->sendChat("------Spawn+ Help------");
                                $issuer->sendChat("/setspawn - set point of spawn in world\n");
                                $issuer->sendChat("/setspawn set <world> <x> <y> <z> - set spawn with coordinates\n");
                    break;
                    case "set":
                        $x = $args[2];
                        $y = $args[3];
                        $z = $args[4];
                        $world = $args[1];
                        if($x === null or $y === null or $z === null or $world === null)
                        {   
                            $issuer->sendChat("[Spawn+] Use /setspawn <world> <x> <y> <z>");
                        }
                        else
                        {
                            $position = new Vector3($x, $y, $z);
                            $this->api->level->get($world)->setSpawn($position);
                            $issuer->sendChat("[Spawn+] Spawn set world: ".$world." x:".$x."y:".$y."z:".$z);
                        }
                    break;
                }
                break;

            case "spawn":
            if(!($issuer instanceof Player)){
            console("[Spawn+]: Run command in game");
            break;
            }
                $level = $issuer->level->getName();
                     switch($args[0]){
                            case "":
                              $spawn = $this->api->level->get($level)->getSpawn();
                              $issuer->teleport($spawn);
                              $issuer->sendChat("[Spawn]: You teleported to spawn on ".$level);
                            break;
                            case "main":
                              $issuer->teleport($this->api->level->getDefault()->getSpawn());
                              $issuer->sendChat("[Spawn]: You teleport to main spawn");
                            break;
                            case "help":
                                $issuer->sendChat("------Spawn Help------");
                                $issuer->sendChat("/spawn - teleport to world spawn\n");
                                $issuer->sendChat("/spawn main - teleport to main spawn\n");
                                $issuer->sendChat("/spawn help - show this massage\n");
                            break;
                        }
            break;
        }
      }
}