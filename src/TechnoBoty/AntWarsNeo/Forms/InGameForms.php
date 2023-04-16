<?php

namespace TechnoBoty\AntWarsNeo\Forms;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat as TF;
use TechnoBoty\AntWarsNeo\Arenas\ArenaManager;
use TechnoBoty\AntWarsNeo\Main;

class InGameForms{

    use SingletonTrait;

    public function __construct(){self::setInstance($this);}

    public function onSelectTeam(Player $player){
        $api = Server::getInstance()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $play, $data){
            if($data === null){
                return FALSE;
            }
            $arena = ArenaManager::getInstance()->getArenaByPlayer($play);
            $teams = $arena->getTeamGroup();
            switch($data){
                case 0:
                    $teams->joinTeam("red",$play);
                    break;
                case 1:
                    $teams->joinTeam("blue",$play);
                    break;
                case 2:
                    $teams->joinTeam("green",$play);
                    break;
                case 3:
                    $teams->joinTeam("yellow",$play);
                    break;
            }
        });
        $form->setTitle(TF::DARK_PURPLE."Выберите команду");
        $form->addButton(TF::RED."Красные");
        $form->addButton(TF::BLUE."Синие");
        $form->addButton(TF::DARK_GREEN."Зеленые");
        $form->addButton(TF::YELLOW."Желтые");
        $player->sendForm($form);
        return $form;
    }
}