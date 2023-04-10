<?php

namespace TechnoBoty\AntWarsNeo\MapManager;

use pocketmine\Server;
use pocketmine\world\World;
use TechnoBoty\AntWarsNeo\Shedulers\AsyncBoxGenerator;
use TechnoBoty\AntWarsNeo\Utils\ChunkSerializer;

class Map {

    private World $world;
    private bool $flag = FALSE;

    public function __construct(World $world){
        $this->world = $world;
    }
    public function getWorld() : World{
        return $this->world;
    }
}