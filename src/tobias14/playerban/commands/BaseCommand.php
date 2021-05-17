<?php

namespace tobias14\playerban\commands;

use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat as C;
use tobias14\playerban\database\DataManager;
use tobias14\playerban\PlayerBan;

/**
 * Parent class of all commands.
 *
 * @package tobias14\playerban\commands
 */
abstract class BaseCommand extends PluginCommand {

    /**
     * Checks if the player has the required permission for the command.
     *
     * @param CommandSender $sender
     * @return bool
     */
    abstract function canUse(CommandSender $sender) : bool;

    /**
     * Checks if the plugin is enabled/disabled.
     *
     * @param Plugin $plugin
     * @param CommandSender $sender
     * @return bool
     */
    public function checkPluginState(Plugin $plugin, CommandSender $sender) : bool {
        if($plugin->isDisabled()) {
            $sender->sendMessage(C::RED . $this->translate("plugin.disabled"));
            return false;
        }
        return true;
    }

    /**
     * @param string $str
     * @param array $params
     * @return string
     */
    public function translate(string $str, array $params = []) : string {
        return PlayerBan::getInstance()->getLang()->translateString($str, $params);
    }

    /**
     * @return DataManager
     */
    public function getDataMgr() : DataManager {
        return PlayerBan::getInstance()->getDataManager();
    }

}
