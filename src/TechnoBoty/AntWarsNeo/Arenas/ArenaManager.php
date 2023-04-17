<?php

namespace TechnoBoty\AntWarsNeo\Arenas;

use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\SingletonTrait;
use TechnoBoty\AntWarsNeo\Main;

class ArenaManager{

    /** @var Arena[] $arenas  */
    private array $arenas = [];

    use SingletonTrait;
    public function __construct(){
        self::setInstance($this);
    }
    public function getArena() : Arena{
        if(count($this->arenas) >= 1) {
            foreach($this->arenas as $arena){
                if($arena->alreadyJoin()){
                    return $arena;
                }
            }
        }
        $arena = new Arena();
        $this->arenas[] = $arena;
        return $arena;
    }
    public function _unset(int $key) : void{
        if(array_key_exists($key,$this->arenas)){
            unset($this->arenas[$key]);
        }
    }
    public function unsetArena(Arena $arena) : void{
        $key =  array_search($arena,$this->arenas);
        if(!is_bool($key)){
            Main::getInstance()->getScheduler()->scheduleDelayedTask(new class($key,$this) extends Task{

                public function __construct(private int $key,private ArenaManager $manager){}
                public function onRun(): void{
                    $this->manager->_unset($this->key);
                }
            },20);
        }
    }
    public function getArenaByPlayer(Player $player) : ?Arena{
        foreach($this->arenas as $arena){
            foreach($arena->getPlayers() as $name => $player){
                if($player->getName() == $name){
                    return $arena;
                }
            }
        }
        return null;
    }
    public function getArenaByWorldName(string $name) : ?Arena{
        foreach($this->arenas as $arena){
            if($arena->getMap()->getWorld()->getFolderName() == $name){
                return $arena;
            }
        }
        return null;
    }
}