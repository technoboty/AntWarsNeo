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
        return new Arena();
    }
}