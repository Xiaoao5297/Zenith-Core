<?php
namespace ITouchSign\Listener;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;

use pocketmine\block\SignPost;
use ITouchSign\ITouchSign;

class SignDestroyListener implements Listener{
 
    private $plugin;

    public function __construct(ITouchSign $plugin){
        $this->plugin = $plugin;
    }

    public function onBlockDestroy(BlockBreakEvent $event){
		$player = $event->getPlayer();
		$block = $event->getBlock();
		if($block instanceof SignPost){
			if($this->plugin->dat->get($this->plugin->getIndex($block))==null){
				$player->sendMessage("§4> §7木牌已拆除( :");
			}else{
				if($player->isOp()){
					$this->plugin->removeSign($block);
					$player->sendMessage("§4> §7这个系统木牌已拆除( :");	
				}else{
					$event->setCancelled();
					$player->sendMessage("§4> §7系统牌子无拆除权限, 请勿痴心妄想( :");
				}
			}
		}
	}
}