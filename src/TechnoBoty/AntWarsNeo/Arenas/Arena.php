<?php

namespace TechnoBoty\AntWarsNeo\Arenas;

use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\Position;
use TechnoBoty\AntWarsNeo\MapManager\Map;
use TechnoBoty\AntWarsNeo\MapManager\MapManager;

class Arena{

    //Const
    private const MAX_PLAYERS = 16;

    private const WAIT_STAGE = 0;
    private const I_STAGE = 1;
    private const II_STAGE = 2;
    private const III_STAGE = 3;
    private const IV_STAGE = 4;

    private array $players = [];
    private int $stage;

    private bool $lockedFlag = FALSE;

    private Map $arena;

    public function __construct(){
        $this->arena = MapManager::getInstance()->addNewArena();
        $this->stage = self::WAIT_STAGE;
    }
    public function join(Player $player) : void{
        if(!array_key_exists($player->getName(),$this->players)){
            $this->players[$player->getName()] = $player;
            $player->teleport(new Position(0,100,0,$this->arena->getWorld()));
        }
    }
    public function quit(Player $player) : void{
        if(array_key_exists($player->getName(),$this->players)){
            unset($this->players[$player->getName()]);
        }
        $player->teleport(new Position(0,100,0,Server::getInstance()->getWorldManager()->getDefaultWorld()));
        $this->arena->deleteWorld();
    }
}