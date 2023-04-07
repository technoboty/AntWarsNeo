<?php

namespace TechnoBoty\AntWarsNeo\MapManager;

use pocketmine\Server;
use pocketmine\world\World;
use TechnoBoty\AntWarsNeo\Shedulers\AsyncBoxGenerator;
use TechnoBoty\AntWarsNeo\Utils\ChunkSerializer;

class Map {

    private string $world;
    private bool $flag = FALSE;

    public function __construct(string $world){
        $this->world = $world;
    }
    public function getLoadedWorldByName(string $name): ?World {
        if(!Server::getInstance()->getWorldManager()->isWorldLoaded($name)){
            Server::getInstance()->getWorldManager()->loadWorld($name, true);
        }
        return Server::getInstance()->getWorldManager()->getWorldByName($name);
    }
}