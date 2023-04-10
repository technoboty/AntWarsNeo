<?php

namespace TechnoBoty\AntWarsNeo\Arenas;

use pocketmine\utils\SingletonTrait;

class ArenaManager{

    private array $arenas = [];

    use SingletonTrait;
    public function __construct(){
        self::setInstance($this);
    }
    public function getArena() : Arena{
        if(count($this->arenas) >= 1) {
            foreach($this->arenas as $arena) {
                if($arena->alreadyJoin()) {
                    return $arena;
                }
            }
        }
        $arena = new Arena();
        $this->arenas[] = $arena;
        return $arena;
    }
}