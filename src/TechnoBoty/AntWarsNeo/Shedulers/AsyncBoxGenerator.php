<?php

namespace TechnoBoty\AntWarsNeo\Shedulers;

use pocketmine\block\VanillaBlocks;
use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\world\SimpleChunkManager;
use pocketmine\world\World;
use TechnoBoty\AntWarsNeo\MapManager\MapConstant;
use TechnoBoty\AntWarsNeo\Utils\ChunkSerializer;

class AsyncBoxGenerator extends AsyncTask{
    private string $chunks;
    private int $size;

    public function __construct(World $world,array $chunks,int $size){
        $this->chunks = ChunkSerializer::EncodeChunks($chunks);
        $this->storeLocal("world",$world->getFolderName());
        $this->size = $size;
    }

    public function onRun(): void {
        $chunks = ChunkSerializer::DecodeChunks($this->chunks);
        $chunks = $this->setBlocks($chunks);
        $this->setResult(ChunkSerializer::EncodeChunks($chunks));
    }
    public function setBlocks(array $chunks) : array{
        $manager = new SimpleChunkManager(World::Y_MIN,World::Y_MAX);
        foreach($chunks as $hash => $chunk){
            World::getXZ($hash, $x, $z);
            $manager->setChunk($x, $z, $chunk);
        }
        $XZ_min = -(floor($this->size/2));
        $XZ_max = floor($this->size/2);
        $Y_min = MapConstant::CENTRAL_BLOCK_Y - $this->size;
        $Y_max = MapConstant::CENTRAL_BLOCK_Y + $this->size;
        for($i = $XZ_min;$i <= $XZ_max;$i++){
            for($k = $XZ_min;$k = $XZ_max;$k++){
                for($j = $Y_min;$j <= $Y_max;$j++){
                    $block = VanillaBlocks::GOLD();
                    $manager->setBlockAt($i,$j,$k,$block);
                }
            }
        }
        return $chunks;
    }
    public function onCompletion() : void{
        $chunks = ChunkSerializer::DecodeChunks($this->getResult());
        /** @var string $worldName */
        $worldName = $this->fetchLocal("world");
        $world = $this->loadworld($worldName);
        $this->setChunks($world, $chunks);
        Server::getInstance()->broadcastMessage("gen\n");
    }
    private function setChunks(World $world, array $chunks){
        foreach($chunks as $hash => $chunk){
            World::getXZ($hash, $x, $z);
            $world->setChunk($x, $z, $chunk);
        }
    }
    public function loadworld(string $worldName) : World{
        $worldManager = Server::getInstance()->getWorldManager();
        $worldManager->loadWorld($worldName);
        return $worldManager->getWorldByName($worldName);
    }
}