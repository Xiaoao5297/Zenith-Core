<?php
/*
__PocketMine Plugin__
name=TrollPlus
author=LDX
version=1.3
apiversion=12
class=TrollPlus
*/
class TrollPlus implements Plugin {
private $api;
public function __construct(ServerAPI $api,$server = false) {
$this->api = $api;
}
public function init() {
$currentVersion = "1.3";
$this->api->console->register("t"," <f|h|g|m|v|o|r|b>",array($this, "cmdHandler"));
$path = $this->api->plugin->configPath($this);
if(file_exists($path . "version.txt") && file_get_contents($path . "version.txt") == $currentVersion) {
$version = file_get_contents($path . "version.txt"); 
} else {
if(file_exists($path . "version.txt")) {
$version = file_get_contents($path . "version.txt");
file_put_contents($path . "version.txt",$currentVersion);
console("[INFO] TrollPlus updated from " . $version . " to " . $currentVersion . "!");
} else {
file_put_contents($path . "version.txt",$currentVersion);
}
}
console("[INFO] [TrollPlus] TrollPlus Enabled!");
}
public function cmdHandler($cmd,$args,$issuer) {
 $path = $this->api->plugin->configPath($this);
 if($args[0] == "freeze" || $args[0] == "f") {
  if(isset($args[1]) && ($this->api->player->get($args[1]) != false)) {
   $uTT = $this->api->player->get($args[1]);
   if(isset($uTT->blocked) && $uTT->blocked == true) {
    $uTT->blocked = false;
    return $this->api->player->get($args[1])->username . " has been unfrozen!";
   } else {
    $uTT->blocked = true;
    return $this->api->player->get($args[1])->username . " has been frozen!";
   }
  } else {
   return "Freezes the specified player. Usage: /t f <player>";
  }
 } else if($args[0] == "herobrine" || $args[0] == "h") {
  if(isset($args[1])) {
   array_shift($args);
   $words = "";
   foreach($args as $word) {
    $words = $words . " " . $word;
   }
   $this->api->chat->broadcast("<Herobrine>" . $words);
  } else {
   return "Broadcasts a message as Herobrine. Usage: /t h <message>";
  }
 } else if($args[0] == "god" || $args[0] == "g") {
  if(isset($args[1])) {
   array_shift($args);
   $words = "";
   foreach($args as $word) {
    $words = $words . " " . $word;
   }
   $this->api->chat->broadcast("<God>" . $words);
  } else {
   return "Broadcasts a message as God. Usage: /t h <message>";
  }
 } else if($args[0] == "message" || $args[0] == "m") {
  if(isset($args[1]) && isset($args[2])) {
   $sentBy = $args[1];
   array_shift($args);
   array_shift($args);
   $words = "";
   foreach($args as $word) {
    $words = $words . " " . $word;
   }
   $this->api->chat->broadcast("<" . $sentBy . ">" . $words);
  } else {
   return "Broadcasts a message as any player. Usage: /t m <player> <message>";
  }
 } else if($args[0] == "void" || $args[0] == "v") {
  if(isset($args[1])) {
   $tpr = $this->api->console->run("tp " . $args[1] . " ~0 -99 ~0","console");
   return $args[1] . " has been thrown into the void!";
  } else {
   return "Teleports a player to the void. Usage: /t v <player>";
  }
 } else if($args[0] == "op" || $args[0] == "o") {
  if(isset($args[1])) {
   if($this->api->player->get($args[1]) == false) {
    return "Player not connected.";
   } else {
    $this->api->player->get($args[1])->sendChat("You are now op.");
    return "Fake op message sent!";
   }
  } else {
   return "Fake op message. Usage: /t o <player>";
  }
 } else if($args[0] == "deop" || $args[0] == "d") {
  if(isset($args[1])) {
   if($this->api->player->get($args[1]) == false) {
    return "Player not connected.";
   } else {
    $this->api->player->get($args[1])->sendChat("You are no longer op.");
    return "Fake deop message sent!";
   }
  } else {
   return "Fake op message. Usage: /t o <player>";
  }
 } else if($args[0] == "raw" || $args[0] == "r") {
  if(isset($args[1])) {
   array_shift($args);
   $words = "";
   foreach($args as $word) {
    $words = $words . $word . " ";
   }
   $this->api->chat->broadcast($words);
  } else {
   return "Broadcasts a raw message. Usage: /t r <message>";
  }
 }  else if($args[0] == "block" || $args[0] == "b") {
  if(isset($args[1]) && ($this->api->player->get($args[1]) != false)) {
   $uTT = $this->api->player->get($args[1]);
   $pk = new UpdateBlockPacket;
   $pk->x = $uTT->entity->x;
   $pk->y = $uTT->entity->y;
   $pk->z = $uTT->entity->z;
   $pk->block = 57;
   $pk->meta = 0;
   $uTT->dataPacket($pk);
  } else {
   return "Spawns a fake diamond block in front of the specified player. Usage: /t b <player>";
  }
 } else {
  return "TrollPlus main command. Usage: /t <f|h|g|m|v|o|r|b>";
 }
}
public function __destruct() { }
}
?>
