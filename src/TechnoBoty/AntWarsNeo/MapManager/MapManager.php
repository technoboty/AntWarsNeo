<?php

namespace TechnoBoty\AntWarsNeo\MapManager;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\WorldCreationOptions;
use pocketmine\world\WorldManager;
use TechnoBoty\AntWarsNeo\Main;
use TechnoBoty\AntWarsNeo\WorldGenerator\VoidGenerator;

class MapManager{

    private array $maps;

    public string $name;
    private string $pattern = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_-+=.";

    use SingletonTrait;

    public function __construct(){
        self::setInstance($this);
    }
    public function addNewMap(int $size) : void{
        $name = $this->generateWorldName();
        $manager = Server::getInstance()->getWorldManager();
        $manager->generateWorld($name,(new WorldCreationOptions())->setGeneratorClass(VoidGenerator::class));
        $manager->loadWorld($name);
        $map = $this->maps[] = new Map($size,$name);
        Main::getInstance()->getScheduler()->scheduleDelayedTask(new class($map) extends Task{

            public function __construct(private Map $map){}

            public function onRun(): void {
                $this->map->unlockFlag();
            }
        },200);
        $this->name = $name;
    }
    public function generateWorldName() : string{
        $list = str_split($this->pattern,1);
        $name = "";
        do{
            $name = "";
            for($i=0;$i <= 10;$i++){
                $name .= $list[mt_rand(0,count($list) - 1)];
            }
        }while(!$this->validateWorldName($name));
        return $name;
    }
    public function validateWorldName(string $name) : bool{
        $dir = @scandir(Server::getInstance()->getDataPath()."worlds");
        foreach($dir as $worldName){
            if($worldName == "." || $worldName == ".."){continue;}
            if($worldName == $name){return FALSE;}
        }
        return TRUE;
    }
}