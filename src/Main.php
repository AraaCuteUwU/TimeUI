<?php

namespace FiraAja\TimeUI;

use jojoe77777\FormAPI\CustomForm;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;

class Main extends PluginBase
{

    /**
     * @param CommandSender $sender
     * @param Command $command
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($command->getName() === "timeui") {
            if (!$sender instanceof Player) return false;
            if (!$sender->hasPermission("timeui.command")) return false;
            $this->sendForm($sender);
            return true;
        }
        return false;
    }

    /**
     * @param Player $player
     * @return void
     */
    private function sendForm(Player $player): void {
        $worlds = ["Global"];
        foreach ($this->getServer()->getWorldManager()->getWorlds() as $world) {
            $worlds[] = $world->getFolderName();
        }
        $form = new CustomForm(function (Player $player, $data) use ($worlds){
            if ($data === null) return;
            $selectedWorld = $worlds[$data[1]];
            if ($selectedWorld === "Global") {
                foreach ($this->getServer()->getWorldManager()->getWorlds() as $world) {
                    $world->setTime($data[0]);
                }
                return;
            }
            $world = $this->getServer()->getWorldManager()->getWorldByName($selectedWorld);
            $world->setTime($data[0]);
        });
        $form->setTitle("TimeUI");
        $form->addSlider("Select time", floor(World::TIME_DAY), floor(World::TIME_FULL), -1, $player->getWorld()->getTime());
        $form->addDropdown("World: ", $worlds, 0);
        $player->sendForm($form);
    }
}
