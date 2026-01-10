<?php
namespace Ad5001\Critical;
use pocketmine\Player;
use pocketmine\event\player\PlayerEvent;
use pocketmine\event\Cancellable;
class CriticalEvent extends PlayerEvent implements Cancellable{
	
	public static $handlerList = null;
	
	protected $damager;
	protected $player;
	
	public function __construct(Player $damager, $player){
		$this->damager = $damager;
		$this->player = $player;
	}
	
	public function getDamager(){
		return $this->damager;
	}
	
	public function getPlayer(){
		return $this->player;
	}
}