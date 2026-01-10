<?php
    
    namespace AP;
    
    use pocketmine\plugin\PluginBase;
    use pocketmine\event\Listener;
    use pocketmine\event\player\PlayerKickEvent;
    use pocketmine\Server;
    use pocketmine\Player;
    
    class Main extends PluginBase implements Listener {
        
        public function onEnable(){
            $this->getServer()->getLogger()->info("§6KAntiPanel 已启动");
            $this->getServer()->getPluginManager()->registerEvents($this, $this);
        }
        
        public function onDisable(){
            $this->getServer()->getLogger()->info("§6KAntiPanel 已关闭");
        }
        
        public function onJoinSer(PlayerKickEvent $event){
            if($event->getReason() == "disconnectionScreen.serverFull"){
                $event->setCancelled();
                $this->getServer()->getLogger()->info("反面板限制人数插件成功帮你破解上限人数,让玩家".$event->getPlayer()->getName()."在服务器满人时进入了服务器");
            }
        }
        
    }