<?php

/*
 __PocketMine Plugin__
 name=Chat Pro
 description=Adds new commands for handling the chat
 version=5.0
 author=Glitchmaster_PE(汉化：zmdd）
 class=ChatPro
 apiversion=10
 */

/*
 This Plugin was made by me, Glitchmaster_PE, and any unauthorized
 distribution is not allowed, please contact me when using any of
 his code in other plugins. Thank you for choosing to use ChatPro
 to enhance your servers chatting!
 */

class ChatPro implements Plugin
{
    private $api, $lang, $prefix0, $prefix, $path, $config, $user, $secondsLeft, $empty;

    public function __construct(ServerAPI $api, $server = false)
    {
        $this -> api = $api;
        $this -> secondsLeft = 0;
        $this -> empty = false;
    }

    public function init()
    {
        $this -> api -> console -> register("stafflist", "给出一个权限组的名单 。", array(
            $this,
            "StaffList"
        ));
        $this -> api -> ban -> cmdwhitelist("stafflist");
        $this -> path = $this -> api -> plugin -> configPath($this);
        $this -> owner = new Config($this -> path . "owner.yml", CONFIG_YAML, array());
        $this -> admins = new Config($this -> path . "admins.yml", CONFIG_YAML, array());
        $this -> mods = new Config($this -> path . "mods.yml", CONFIG_YAML, array());
        $this -> trusted = new Config($this -> path . "trusted.yml", CONFIG_YAML, array());
        $this -> api -> console -> register("staffadd", "为权限组添加一个玩家", array(
            $this,
            "Staffadd"
        ));
        $this -> reload();
        $this -> api -> console -> register("prefix", "设置你的称号 . 用法 : /prefix < 你的名字 > [ 称号 ]", array(
            $this,
            "Prefix"
        ));
        $this -> api -> ban -> cmdwhitelist("stafflist");
        $this -> readConfig();
        $this -> api -> addHandler("player.join", array(
            $this,
            "handler"
        ), 5);
        $this -> api -> addHandler("player.chat", array(
            $this,
            "handler"
        ), 5);
        $this -> api -> console -> register("announce", "设置服务器的公告", array(
            $this,
            "Announce"
        ));
        $this -> api -> ban -> cmdwhitelist("announce");
        $this -> path = $this -> api -> plugin -> configPath($this);
        $this -> announcemsgs = new Config($this -> path . "announcements.yml", CONFIG_YAML, array("Announcements" => "你的公告在这里！"));
        $this -> announcemsgs = $this -> api -> plugin -> readYAML($this -> path . "announcements.yml");
        $this -> api -> console -> register("/cphelp", "显示本插件所有命令", array(
            $this,
            "Help"
        ));
        $this -> api -> ban -> cmdwhitelist("/cphelp");
        $this -> api -> console -> register("rules", "给出服务器的规则", array(
            $this,
            "Rules"
        ));
        $this -> api -> ban -> cmdwhitelist("rules");
        $this -> path = $this -> api -> plugin -> configPath($this);
        $this -> rulesmsgs = new Config($this -> path . "rules.yml", CONFIG_YAML, array(
            "Rule-1" => "Do not grief!",
            "Rule-2" => "Do not abuse OP!",
            "Rule-3" => "Do not swear!",
            "Rule-4" => "Do not troll!"
        ));
        $this -> rulesmsgs = $this -> api -> plugin -> readYAML($this -> path . "rules.yml");
        $this -> api -> console -> register("griefwarn", "警告滋事者", array(
            $this,
            "Grief"
        ));
        $this -> api -> console -> register("trollwarn", "警告控制器", array(
            $this,
            "Troll"
        ));
        $this -> api -> console -> register("swearwarn", "警告人说脏话", array(
            $this,
            "Swear"
        ));
        $this -> griefmsgs = new Config($this -> path . "griefwarn.yml", CONFIG_YAML, array("Grief Warning" => "Griefing is not allowed!"));
        $this -> griefmsgs = $this -> api -> plugin -> readYAML($this -> path . "griefwarn.yml");
        $this -> trollmsgs = new Config($this -> path . "trollwarn.yml", CONFIG_YAML, array("Troll Warning" => "Trolling is not allowed!"));
        $this -> trollmsgs = $this -> api -> plugin -> readYAML($this -> path . "trollwarn.yml");
        $this -> swearmsgs = new Config($this -> path . "swearwarn.yml", CONFIG_YAML, array("Swear Warning" => "Swearing is not allowed!"));
        $this -> swearmsgs = $this -> api -> plugin -> readYAML($this -> path . "swearwarn.yml");
        $this -> api -> console -> register("/opcphelp", "Gives list of ChatPro commands for OP", array(
            $this,
            "OPHelp"
        ));
        $this -> api -> console -> register("afk", "告诉大家你已离开", array(
            $this,
            "afk"
        ));
        $this -> api -> console -> register("afkoff", "告诉大家你不再离开", array(
            $this,
            "afk"
        ));
        $this -> api -> ban -> cmdwhitelist("afk");
        $this -> api -> ban -> cmdwhitelist("afkoff");
        $this -> api -> schedule(200, array(
            $this,
            'PostingTimer'
        ), array(), true, 'server.schedule');
        $this -> api -> console -> register('removepost', '删除一个自动发布公告', array(
            $this,
            'RemoveMessage'
        ));
        $this -> api -> console -> register('addpost', '添加一个自动发布的公告', array(
            $this,
            'AddMessage'
        ));
        $this -> autopost = new Config($this -> path . "autopost.yml", CONFIG_YAML, array(
            "Minutes for broadcast" => array("Minutes" => 10),
            "Messages" => array(
                "1" => "欢迎来到服务器 ，/spawn 回到出生点",
                "2" => "",
                "3" => "",
                "4" => "",
                "5" => "",
                "6" => "",
                "7" => "",
                "8" => "",
                "9" => "",
                "10" => ""
            )
        ));
    }

    public function StaffList($cmd, $args, $issuer)
    {
        if($issuer instanceof Player){
        $this -> reload();
        $username = $issuer -> username;
        $this -> api -> chat -> sendTo(false, $msg, $username);
        $msg1 = "[StaffList] Owners ： " . implode(", ", $this -> owner);
        $this -> api -> chat -> sendTo(false, $msg1, $username);
        $msg2 = "[StaffList] Admins ： " . implode(", ", $this -> admins);
        $this -> api -> chat -> sendTo(false, $msg2, $username);
        $msg3 = "[StaffList] Moderators ： " . implode(", ", $this -> mods);
        $this -> api -> chat -> sendTo(false, $msg3, $username);
        $msg4 = "[StaffList] Trusted ：" . implode(", ", $this -> trusted);
        $this -> api -> chat -> sendTo(false, $msg4, $username);
        }
        else{
            $output = "请在游戏里运行这个命令 。";
            return $output;
        }
    }

    public function Staffadd($cmd, $args, $issuer)
    {
        switch($args[0])
        {
            case "owner" :
            case "o" :
                $target = $args[1];
                array_push($this -> owner, $target);
                $this -> owner = $this -> api -> plugin -> writeYAML($this -> path . "owner.yml", $target);
                return "Added " . $target . " as an owner";
                break;
            case "admins" :
            case "admin" :
            case "a" :
                $target = $args[1];
                array_push($this -> admins, $target);
                $this -> admins = $this -> api -> plugin -> writeYAML($this -> path . "admins.yml", $target);
                return "大家 ，" . $target . " 是一名admin了";
                break;
            case "moderators" :
            case "mod" :
            case "moderator" :
            case "mods" :
            case "m" :
                $target = $args[1];
                array_push($this -> mods, $target);
                $this -> mods = $this -> api -> plugin -> writeYAML($this -> path . "mods.yml", $target);
                return "大家 ， " . $target . "是一名 moderator 了";
                break;
            case "trusted" :
            case "t" :
                $target = $args[1];
                array_push($this -> trusted, $target);
                $this -> trusted = $this -> api -> plugin -> writeYAML($this -> path . "trusted.yml", $target);
                return "大家 ， " . $target . " 是一名 trusted 了";
                break;
            default :
                $output = "用法：: /staffadd <o|a|m|t> <full username>";
                return $output;
        }
    }

    public function reload()
    {
        $this -> owner = $this -> api -> plugin -> readYAML($this -> path . "owner.yml");
        $this -> admins = $this -> api -> plugin -> readYAML($this -> path . "admins.yml");
        $this -> mods = $this -> api -> plugin -> readYAML($this -> path . "mods.yml");
        $this -> trusted = $this -> api -> plugin -> readYAML($this -> path . "trusted.yml");
    }

    public function Announce($cmd, $args, $issuer)
    {
        if (!$issuer instanceof Player)
        {
            $output = "[ChatPro] " . $this -> announcemsgs["Announcements"];
            return $output;
        }
        $username = $issuer -> username;
        $this -> api -> chat -> sendTo(false, "[ChatPro] " . $this -> announcemsgs["Announcements"], $username);
    }

    public function Help($cmd, $args, $issuer)
    {
        if (!$issuer instanceof Player)
        {
            $output = "[ChatPro] Commands: /rules, /announce, /stafflist, /afk, /afkoff";
            return $output;
        }
        $username = $issuer -> username;
        $this -> api -> chat -> sendTo(false, "[ChatPro] Commands: /rules, /announce, /stafflist, /afk, /afkoff", $username);
    }

    public function Rules($cmd, $args, $issuer)
    {
        if (!$issuer instanceof Player)
        {
            $output = "[ChatPro] Rules: 1. " . $this -> rulesmsgs["Rule-1"] . " 2." . $this -> rulesmsgs["Rule-2"] . " 3. " . $this -> rulesmsgs["Rule-3"] . " 4. " . $this -> rulesmsgs["Rule-4"];
            return $output;
        }
        $username = $issuer -> username;
        $this -> api -> chat -> sendTo(false, "[ChatPro] Rules: 1. " . $this -> rulesmsgs["Rule-1"] . " 2." . $this -> rulesmsgs["Rule-2"] . " 3. " . $this -> rulesmsgs["Rule-3"] . " 4. " . $this -> rulesmsgs["Rule-4"], $username);
    }

    public function Grief($cmd, $args, $issuer, $target)
    {
        $target = $args[0];
        foreach ($this->griefmsgs as $griefmsg)
        {
            $this -> api -> chat -> sendTo(false, "[ChatPro] " . $griefmsg, $target);
        }
    }

    public function Troll($cmd, $args, $issuer, $target)
    {
        $target = $args[0];
        foreach ($this->trollmsgs as $trollmsg)
        {
            $this -> api -> chat -> sendTo(false, "[ChatPro] " . $trollmsg, $target);
        }
    }

    public function Swear($cmd, $args, $issuer, $target)
    {
        $target = $args[0];
        foreach ($this->swearmsgs as $swearmsg)
        {
            $this -> api -> chat -> sendTo(false, "[ChatPro] " . $swearmsg, $target);
        }
    }

    public function OPHelp($cmd, $args, $issuer)
    {
        if (!$issuer instanceof Player)
        {
            $output = "[ChatPro] OP 可以用的命令 : /prefix, /griefwarn, /trollwarn, /swearwarn, /addpost, /removepost";
            return $output;
        }
        $username = $issuer -> username;
        $this -> api -> chat -> sendTo(false, "[ChatPro] OP 可以用的命令 : /prefix, /griefwarn, /trollwarn, /swearwarn, /addpost, /removepost", $username);
    }

    public function __destruct()
    {
    }

    public function readConfig()
    {
        $this -> path = $this -> api -> plugin -> createConfig($this, array("format" => "[{prefix}]<{DISPLAYNAME}> {MESSAGE}", ));
        $this -> config = $this -> api -> plugin -> readYAML($this -> path . "config.yml");
    }

    public function Prefix($cmd, $args)
    {
        switch($cmd)
        {
            case "prefix" :
                $player = $args[0];
                $pref = $args[1];

                $this -> config['player'][$player] = $pref;
                $this -> api -> plugin -> writeYAML($this -> path . "config.yml", $this -> config);
                $output .= "[ChatPro] Gave " . $pref . " to " . $player . ".";
                $this -> api -> chat -> sendTo(false, "[ChatPro] Your prefix is now " . $pref . " !", $player);
                break;

            default :
                $output .= 'ChatPro by Glitchmaster_PE';
                break;
        }
    }

    public function handler(&$data, $event)
    {
        switch($event)
        {
            case "player.join" :
                $user = $data -> username;
                if (!isset($this -> config['player'][$user]))
                {
                    $this -> config['player'][$user] = 'Player';
                    $this -> api -> plugin -> writeYAML($this -> path . "config.yml", $this -> config);
                }
                break;
            case "player.chat" :
                $prefix = $data["player"] -> username;
                $this -> config = $this -> api -> plugin -> readYAML($this -> path . "config.yml");
                $data = array(
                    "player" => $data["player"],
                    "message" => str_replace(array(
                        "{DISPLAYNAME}",
                        "{MESSAGE}",
                        "{prefix}"
                    ), array(
                        $data["player"] -> username,
                        $data["message"],
                        $this -> config["player"][$prefix]
                    ), $this -> config["format"])
                );
                if ($this -> api -> handle("ChatPro." . $event, $data) !== false)
                {
                    $this -> api -> chat -> broadcast($data["message"]);
                }
                return false;
                break;
        }
    }

    public function afk($cmd, $arg, $issuer)
    {
        switch($cmd)
        {
            case "afk" :
                $this -> api -> chat -> broadcast("[ChatPro] 玩家  " . $issuer -> username . " 现在已经离开");
                $this -> api -> chat -> sendTo(false, "[ChatPro] 用 /afkoff  来告诉大家 ，你回来了", $issuer -> username);
                break;
            case "afkoff" :
                $this -> api -> chat -> broadcast("[ChatPro] 玩家  " . $issuer -> username . " 已经回来了");
                break;
        }
    }

    public function AddMessage($cmd, $arg, $issuer)
    {
        $cfg = $this -> api -> plugin -> readYAML($this -> path . "autopost.yml");
        switch($cmd)
        {
            case "addpost" :
                $mgs = $arg;
                if ($this -> api -> ban -> isOp($issuer) === true)
                {
                    $empty = false;
                    $mNum = 11;
                    foreach ($cfg as $Messages => $elements)
                    {
                        if ($Messages == "Messages")
                        {
                            while ($empty == false)
                            {
                                if ($mNum != 0)
                                {
                                    --$mNum;
                                    if ($cfg['Messages']["$mNum"] === "")
                                    {
                                        $this -> arrayReplace($mNum, $mgs);
                                        $output .= "公告已经添加 ";
                                        $empty = true;
                                        break;
                                    }
                                }
                                else
                                {
                                    $output .= "[ChatPro] 没有可用的空间增添一个公告 。请删除多余的 ！";
                                }
                            }
                        }
                    }
                }
                else
                {
                    $output .= "[ChatPro] 你没权限使用这个命令 。";
                }
        }
        return $output;
    }

    public function RemoveMessage($cmd, $arg, $issuer)
    {
        switch($cmd)
        {
            case "removepost" :
                if ($this -> api -> ban -> isOp($issuer) === false)
                {
                    $num = $arg[0];
                    $this -> arrayReplace($num, "");
                    $output .= "[ChatPro] 公告已移除";
                }
                else
                {
                    $output .= "[ChatPro] 你没权限使用这个命令 。";
                }
        }
        return $output;
    }

    public function PostingTimer()
    {
        $cfg = $this -> api -> plugin -> readYAML($this -> path . "autopost.yml");
        $minutes = $cfg["Minutes for broadcast"]["Minutes"];
        $seconds = $minutes * 60;
        $checkSeconds = $seconds / 300;
        $this -> secondsLeft = $this -> secondsLeft - 10;
        if (!is_int($checkSeconds))
        {
            console("[ChatPro] Minutes must be at least 5!");
            console("[ChatPro] 没有消息被广播 ，如果不改变配置文件的设置");
        }
        else
        {
            if ($this -> secondsLeft <= 0)
            {
                $this -> secondsLeft = $seconds;
                $test = 11;
                while ($this -> empty === false)
                {
                    if ($test === 0)
                    {
                        break;
                        $this -> empty = true;
                    }
                    --$test;
                    if ($cfg["Messages"]["$test"] == "")
                    {
                        $this -> empty = false;
                        break;
                    }
                }
                $success = false;
                while ($success == false)
                {
                    $rand = rand(1, 10);
                    if ($cfg["Messages"]["$rand"] == "")
                    {
                        $success = false;
                        $rand = rand(1, 20);
                    }
                    else
                    {
                        $success = true;
                        $this -> api -> chat -> broadcast("[ChatPro] " . $cfg["Messages"]["$rand"]);
                    }
                }
            }
        }
    }

    private function findKey($num)
    {
        $cfg = $this -> api -> plugin -> readYAML($this -> path . "autopost.yml");
        $text = $cfg["Messages"][$num];
        return $text;
    }

    private function arrayReplace($lineToReplace, $message)
    {
        $array = array("Messages" => array());
        $done = false;
        $nums = 1;
        while ($done == false)
        {
            if ($nums == $lineToReplace)
            {
                $array["Messages"][$lineToReplace] = implode(' ', $message);
            }
            else
            {
                $array["Messages"]["$nums"] = $this -> findKey($nums);
            }
            ++$nums;
            if ($nums == 11)
            {
                break;
            }
        }
        safe_var_dump($array);
        $this -> overwriteConfig($array);
    }

    private function overwriteConfig($dat)
    {
        $cfg = array();
        $cfg = $this -> api -> plugin -> readYAML($this -> path . "autopost.yml");
        $result = array_merge($cfg, $dat);
        $this -> api -> plugin -> writeYAML($this -> path . "autopost.yml", $result);
    }

}
?>
