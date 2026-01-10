<?php
#######################################
#                                     #
#        简单结婚插件 by: Him188      #
# 			     V1.1.3               #
#			  2015年3月20日           #
#									  #
#######################################
/*
	出现什么BUG或有建议请反馈给我QQ（小黑）：1040400290
	我的群：367188734
	服务器：mcpesv.oicp.net
	端口：17170
	（可能有点卡）
*/
namespace SimpleMarry;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerRespawnEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class SimpleMarry extends PluginBase implements Listener {
    public $Version = "V1.1.3";
    /**@var SimpleMarry */
    public static $instance;

    public function onEnable() {
        self::$instance = $this;
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        @date_default_timezone_set('Etc/GMT-8');
        if (!file_exists(getcwd() . "\\plugins\\SimpleMarry")) {
            @mkdir(getcwd() . "\\plugins\\SimpleMarry");
        }
        if (!file_exists(getcwd() . "\\plugins\\SimpleMarry\\Request")) {
            @mkdir(getcwd() . "\\plugins\\SimpleMarry\\Request");
        }
        if (!file_exists(getcwd() . "\\plugins\\SimpleMarry\\NeedRequest")) {
            @mkdir(getcwd() . "\\plugins\\SimpleMarry\\NeedRequest");
        }
        if (!file_exists(getcwd() . "\\plugins\\SimpleMarry\\Marry")) {
            @mkdir(getcwd() . "\\plugins\\SimpleMarry\\Marry");
        }
        //$this->getServer()->getPluginManager()->registerEvents($this, $this);
        //$this->getServer()->getScheduler()->scheduleRepeatingTask(new BroadcastPluginTask($this), 120);
        $this->getLogger()->info(TextFormat::DARK_GREEN . "[结婚] " . $this->Version . " 成功加载 ！作者 ： Him188");
    }

    public function onDisable() {
        $this->getLogger()->info(TextFormat::DARK_RED . "[结婚] 成功卸载 ！");
    }

    public static function YAMLR_Request($name) { //读取玩家正在向谁请求 //参数说明：name，玩家名
        $data = new Config(getcwd() . "\\plugins\\SimpleMarry\\Request\\" . strtolower($name) . ".yml", Config::YAML);
        $a = $data->get("Request", "");
        $data->save();
        return $a;
    }

    public static function YAMLW_Request($name, $type) { //写入玩家正在向谁请求 //参数说明：name，玩家名；type配置名称
        //$this->plugin->getDataFolder() . "players/xxx.yml"
        $data = new Config(getcwd() . "\\plugins\\SimpleMarry\\Request\\" . strtolower($name) . ".yml", Config::YAML);
        $data->set("Request", $type);
        $data->save();
    }

    public static function YAMLR_NeedRequest($name) { //读取玩家是否被请求 //参数说明：name，玩家名
        $data = new Config(getcwd() . "\\plugins\\SimpleMarry\\NeedRequest\\" . strtolower($name) . ".yml", Config::YAML);
        $a = $data->get("NeedRequest", "");
        $data->save();
        return $a;
    }

    public static function YAMLW_NeedRequest($name, $type) { //写入玩家是否被请求 //参数说明：name，玩家名；type配置名称
        //$this->plugin->getDataFolder() . "players/xxx.yml"
        $data = new Config(getcwd() . "\\plugins\\SimpleMarry\\NeedRequest\\" . strtolower($name) . ".yml", Config::YAML);
        $data->set("NeedRequest", $type);
        $data->save();
    }

    public static function YAMLR_Marry($name) { //读取玩家的对象的名字 //参数说明：name，玩家名
        $data = new Config(getcwd() . "\\plugins\\SimpleMarry\\Marry\\" . strtolower($name) . ".yml", Config::YAML);
        $a = $data->get("Marry", "");
        $data->save();
        return $a;
    }

    public static function YAMLW_Marry($name, $type) { //写入玩家的对象的名字 //参数说明：name，玩家名；type对方
        //$this->plugin->getDataFolder() . "players/xxx.yml"
        $data = new Config(getcwd() . "\\plugins\\SimpleMarry\\Marry\\" . strtolower($name) . ".yml", Config::YAML);
        $data->set("Marry", $type);
        $data->save();
    }

    public static function isMarry($name) {//是否已结婚 //参数说明：name，玩家名
        //$this->plugin->getDataFolder() . "players/xxx.yml"
        $data = new Config(getcwd() . "\\plugins\\SimpleMarry\\Marry\\" . strtolower($name) . ".yml", Config::YAML);
        $a = $data->get("Marry", "");
        $data->save();
        return !($a == "");
    }

    public static function getMarryWith($name){
        $data = new Config(getcwd() . "\\plugins\\SimpleMarry\\Marry\\" . strtolower($name) . ".yml", Config::YAML);
        $a = $data->get("Marry", "");
        $data->save();
        return $a;
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        switch ($command->getName()) {

            case "结婚":


                //求婚
                //取消
                //接受
                //拒绝
                //
                if (count($args) == 0) {
                    $sender->sendMessage("------------------------\n" . "结婚插件命令 ：\n /结婚 求婚 <对方ID>\n /结婚 接受\n /结婚 拒绝\n /结婚 离婚 \n /结婚 取消\n /结婚 tp\n /结婚 情侣" . "\n------------------------");
                    return true;
                }

                switch ($args[0]) { //判断要执行哪个子命令

                    case "求婚":
                        $sender_name = $sender->getName();

                        $nl = $this->YAMLR_NeedRequest($sender_name);
                        if ($nl !== "") {
                            $sender->sendMessage("------------------------\n" . "[ " . $nl . " ] 向你求婚啦 ！\n接受还是拒绝？\n* 接受 ：/结婚 接受\n* 拒绝 :/结婚 拒绝" . "\n------------------------");
                            return true;
                        }
                        if ($this->isMarry(strtolower($sender_name))) {
                            $sender->sendMessage("------------------------\n" . "你都已经结婚了 ，还要求婚 ？" . "\n------------------------");
                            return true;
                        }

                        $n = $this->YAMLR_Request(strtolower($sender_name));
                        if (!($n == "")) {//如果$sender已向其他人求婚
                            //$this->YAMLW_Request(strtolower($Pl->getName()));
                            $sender->sendMessage("------------------------\n" . "你已经向 [ " . $n . " ] 求婚了\n难道你想....\n* 取消本次求婚请输入 ：\n /结婚 取消" . "\n------------------------");
                            return true;
                        }

                        $Player;

                        //结婚 求婚 <Ta的昵称>
                        if (count($args) == 2) {
                            //$args :
                            //0,求婚
                            //1,昵称
                            if ($this->getServer()->getPlayer($args[1]) instanceof Player) {
                                $E = $this->getServer()->getPlayer($args[1]);
                                $E_name = $E->getName();

                                $nr = $this->YAMLR_NeedRequest(strtolower($E_name));

                                if (!($nr == "")) {//如已经被其他人求婚
                                    $sender->sendMessage("------------------------\n" . "糟糕 ！ \n[ " . $nr . " ] 提早向 " . "[ " . $n . " ] 求婚了\n请先等待 [ " . $n . " ] 做出决定吧" . "\n------------------------");
                                    return true;
                                }

                                $nr = $this->YAMLR_Marry(strtolower($E_name));
                                if (!($nr == "")) {
                                    $sender->sendMessage("------------------------\n" . "糟糕 ！ \n[ " . $nr . " ] 已经和 " . "[ " . $n . " ] 结婚了" . "\n------------------------");
                                    return true;
                                }

                                //寻找对方是否在线：
                                $find = false;
                                foreach ($this->getServer()->getOnlinePlayers() as $p) {
                                    if (strtolower($args[1]) == strtolower($p->getName()) && $find == false) {
                                        $find = true;
                                    }
                                }
                                ///////////////////////////////


                                if ($find == true) { //对方在线
                                    $E->sendMessage("------------------------\n" . "[ " . strtolower($sender_name) . " ] 向你求婚啦 ！\n接受还是拒绝？\n* 接受 ：/结婚 接受\n* 拒绝 :/结婚 拒绝" . "\n------------------------");
                                }

                                $this->YAMLW_Request(strtolower($sender_name), strtolower($E_name));
                                $this->YAMLW_NeedRequest(strtolower($E_name), strtolower($sender_name));
                                $sender->sendMessage("------------------------\n" . "成功向 [ " . strtolower($args[1]) . " ] 求婚啦 ！\n等待Ta的答复吧 ~~\n* 取消操作请输入/结婚 取消" . "\n------------------------");
                                return true;
                            }
                        } else {
                            $sender->sendMessage("------------------------\n" . "若要向Ta求婚 ，请输入 ：\n /结婚 求婚 <Ta的昵称>" . "\n------------------------");
                        }
                        return true;
                        break;
                    case "接受":
                        $sender_name = $sender->getName();
                        if ($this->isMarry(strtolower($sender_name))) {
                            $sender->sendMessage("------------------------\n" . "你都已经结婚了 ，还接受什么 ？" . "\n------------------------");
                            return true;
                        }

                        $nr = $this->YAMLR_NeedRequest(strtolower($sender_name));
                        if ($nr == "") {
                            $Pl->sendMessage("------------------------\n" . "现在还没有任何人向你求婚 ." . "\n------------------------");
                            return true;
                        }

                        if ($this->isMarry($nr)) {
                            $Pl->sendMessage("------------------------\n" . "来晚了一步 ，对方已和另外一人结婚 ." . "\n------------------------");
                            return true;
                        }

                        $name = $this->YAMLR_NeedRequest(strtolower($sender_name));
                        $this->YAMLW_NeedRequest(strtolower($sender_name), "");
                        $this->YAMLW_Marry(strtolower($sender_name), strtolower($name));
                        $this->YAMLW_Marry(strtolower($name), strtolower($sender_name));
                        $this->YAMLW_Request(strtolower($name), "");
                        //$sender->sendMessage("------------------------\n" . "好消息 ！\n 你已与 [ " . strtolower($Pl->getName()) . " ] 成功夫妻 ！" . "\n------------------------");
                        $this->getServer()->getInstance()->broadcastMessage("------------------------\n" . "恭喜 [ " . strtolower($sender_name) . " ] 与 [ " . strtolower($name) . " ] 成功夫妻 ！" . "\n------------------------");
                        return true;
                    case "拒绝":
                        $sender_name = $sender->getName();
                        if ($this->isMarry(strtolower($sender_name))) {
                            $sender->sendMessage("------------------------\n" . "你都已经结婚了 ，还拒绝什么 ？" . "\n------------------------");
                            return true;
                        }

                        $nr = $this->YAMLR_NeedRequest(strtolower($sender_name));
                        if ($nr == "") {
                            $sender->sendMessage("------------------------\n" . "现在还没有任何人向你求婚 ." . "\n------------------------");
                            return true;
                        }

                        if ($this->isMarry($nr)) {
                            $Pl->sendMessage("------------------------\n" . "来晚了一步 ，对方已和另外一人结婚 ." . "\n------------------------");
                            return true;
                        }

                        //寻找对方是否在线：
                        $find = false;
                        foreach ($this->getServer()->getOnlinePlayers() as $p) {
                            if ($nr == strtolower($p->getName()) && $find == false) {
                                $Pl = $p;
                                $find = true;
                            }
                        }
                        ///////////////////////////////
                        if ($find == true) {
                            $Pl->sendMessage("------------------------\n" . "糟糕 ！\n  [ " . strtolower($sender_name) . " ] 拒绝了你的求婚 ！" . "\n------------------------");
                        }
                        $this->YAMLW_NeedRequest(strtolower($sender_name), "");
                        $this->YAMLW_Request(strtolower($nr), "");
                        $sender->sendMessage("------------------------\n" . "成功拒绝 ." . "\n------------------------");
                        return true;
                    case "取消":
                        $sender_name = $sender->getName();
                        $nl = $this->YAMLR_NeedRequest($sender_name);
                        if ($nl !== "") {
                            $sender->sendMessage("------------------------\n" . "[ " . $nl . " ] 向你求婚啦 ！\n接受还是拒绝？\n* 接受 ：/结婚 接受\n* 拒绝 :/结婚 拒绝" . "\n------------------------");
                            return true;
                        }
                        if ($this->isMarry(strtolower($sender_name))) {
                            $sender->sendMessage("------------------------\n" . "你都已经结婚了 ，还取消什么 ？" . "\n------------------------");
                            return true;
                        }

                        $nr = $this->YAMLR_Request(strtolower($sender_name));
                        if ($nr == "") {
                            $sender->sendMessage("------------------------\n" . "你还没有向任何人求婚 ." . "\n------------------------");
                            return true;
                        }

                        $this->YAMLW_Request(strtolower($sender_name), "");
                        $sender->sendMessage("------------------------\n" . "取消成功 ." . "\n------------------------");
                        return true;
                    case "离婚":
                        $sender_name = $sender->getName();
                        /*
                        $nl = $this->YAMLR_NeedRequest($sender_name);
                        if ($nl !== ""){
                            $sender->sendMessage("------------------------\n" . "[ " . $nl . " ] 向你求婚啦 ！\n接受还是拒绝？\n* 接受 ：/接受\n* 拒绝 :/拒绝" . "\n------------------------");
                            return true;
                        }
                        */
                        if ($this->isMarry(strtolower($sender_name))) {
                            $name = $this->YAMLR_Marry(strtolower($sender_name));

                            //寻找对方是否在线：
                            $find = false;
                            foreach ($this->getServer()->getOnlinePlayers() as $p) {
                                if ($name == strtolower($p->getName()) && $find == false) {
                                    $Pl = $p;
                                    $find = true;
                                }
                            }
                            ///////////////////////////////
                            if ($find == true) {
                                $Pl->sendMessage("------------------------\n" . "你的情侣已经和你离婚了 你恢复了单身 ." . "\n------------------------");
                            }
                            $this->YAMLW_Marry($this->YAMLR_Marry(strtolower($sender_name)), "");
                            $this->YAMLW_Marry(strtolower($sender_name), "");
                            $sender->sendMessage("------------------------\n" . "成功离婚 你恢复了单身 ." . "\n------------------------");
                            return true;
                        } else {
                            $sender->sendMessage("------------------------\n" . "你还没有结婚 ." . "\n------------------------");
                            return true;
                        }
                    case "tp":
                        $sender_name = $sender->getName();
                        $nl = $this->YAMLR_NeedRequest($sender_name);
                        if ($nl !== "") {
                            $sender->sendMessage("------------------------\n" . "[ " . $nl . " ] 向你求婚啦 ！\n接受还是拒绝？\n* 接受 ：/结婚 接受\n* 拒绝 :/结婚 拒绝" . "\n------------------------");
                            return true;
                        }
                        if ($this->isMarry(strtolower($sender_name))) {
                            $marry = $this->YAMLR_Marry($sender_name);
                            //寻找对方是否在线：
                            $find = false;
                            foreach ($this->getServer()->getOnlinePlayers() as $p) {
                                if ($marry == strtolower($p->getName()) && $find == false) {
                                    $Pl = $p;
                                    $find = true;
                                }
                            }
                            ///////////////////////////////

                            if ($find == true) {
                                $sender->teleport($Pl->getPosition());
                                $Pl->sendMessage("------------------------\n" . "你的情侣传送到了你身边 . " . "\n------------------------");
                                $sender->sendMessage("------------------------\n" . "传送成功 . " . "\n------------------------");
                            } else {
                                $sender->sendMessage("------------------------\n" . "你的情侣不在线 ， 无法传送到Ta身边 . " . "\n------------------------");
                            }
                        } else {
                            $sender->sendMessage("------------------------\n" . "你还没有结婚 . " . "\n------------------------");

                        }
                        return true;
                    case "情侣":
                        $sender_name = $sender->getName();
                        $nl = $this->YAMLR_NeedRequest($sender_name);
                        if ($nl !== "") {
                            $sender->sendMessage("------------------------\n" . "[ " . $nl . " ] 向你求婚啦 ！\n接受还是拒绝？\n* 接受 ：/结婚 接受\n* 拒绝 :/结婚 拒绝" . "\n------------------------");
                            return true;
                        }

                        if ($this->isMarry(strtolower($sender_name))) {
                            $sender->sendMessage("------------------------\n" . "你的情侣是 [ " . $this->YAMLR_Marry($sender_name) . " ]" . "\n------------------------");
                        } else {
                            $sender->sendMessage("------------------------\n" . "你还没有结婚 ." . "\n------------------------");
                        }
                        return true;
                        return true;
                }
                return false;

        }

    }

    /**
     * @param PlayerRespawnEvent $event
     *
     * @priority        NORMAL
     * @ignoreCancelled false
     */
    public function onSpawn(PlayerRespawnEvent $event) {
        $nl = $this->YAMLR_NeedRequest($event->getPlayer()->getName());
        if ($nl !== "") {
            $event->getPlayer()->sendMessage("------------------------\n" . "[ " . $nl . " ] 向你求婚啦 ！\n接受还是拒绝？\n* 接受 ：/结婚 接受\n* 拒绝 :/结婚 拒绝" . "\n------------------------");
        }
        //Server::getInstance()->broadcastMessage($event->getPlayer()->getDisplayName() . " has just spawned!");
    }
}