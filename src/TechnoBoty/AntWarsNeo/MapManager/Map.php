<?php

namespace TechnoBoty\AntWarsNeo\MapManager;

use pocketmine\Server;
use pocketmine\world\World;
use TechnoBoty\AntWarsNeo\Shedulers\AsyncBoxGenerator;
use TechnoBoty\AntWarsNeo\Utils\ChunkSerializer;

class Map {

    private string $world;
    private int $size;
    private bool $flag = FALSE;

    public function __construct(int $size,string $world){
        $this->size = $size;
        $this->world = $world;
    }
    private function generateBox() : void{
        $chunks = ChunkSerializer::getChunks($this->getLoadedWorldByName($this->world),-(floor($this->size/2)),-(floor($this->size/2)),floor($this->size/2),floor($this->size/2));
        //Server::getInstance()->getAsyncPool()->submitTask(new AsyncBoxGenerator($this->getLoadedWorldByName($this->world),$chunks,$this->size));
    }
    public function unlockFlag() : void{
        $this->flag = TRUE;
        $this->generateBox();
    }
    public function getLoadedWorldByName(string $name): ?World {
        if(!Server::getInstance()->getWorldManager()->isWorldLoaded($name)){
            Server::getInstance()->getWorldManager()->loadWorld($name, true);
        }
        return Server::getInstance()->getWorldManager()->getWorldByName($name);
    }
}