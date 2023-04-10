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
        if(count($this->arenas) < 1){
            $arena = new Arena();
            $this->arenas[] = $arena;
            return $arena;
        } else {
            return $this->arenas[0];
        }
    }
}