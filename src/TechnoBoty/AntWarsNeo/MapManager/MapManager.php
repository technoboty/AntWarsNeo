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

    private string $UpPattern = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    private string $DownPattern = "abcdefghijklmnopqrstuvwxyz";

    use SingletonTrait;

    public function __construct(){
        self::setInstance($this);
    }
    public function addNewArena() : Map{
        $name = $this->generateWorldName();
        $manager = Server::getInstance()->getWorldManager();
        $manager->generateWorld($name,(new WorldCreationOptions())->setGeneratorClass(VoidGenerator::class));
        $manager->loadWorld($name);
        $this->name = $name;
        $map = new Map(Server::getInstance()->getWorldManager()->getWorldByName($name));
        $this->maps[] = $map;
        return $map;
    }
    public function generateWorldName() : string{
        $list = str_split($this->pattern,1);
        $UpList = str_split($this->UpPattern,1);
        $DownList = str_split($this->DownPattern,1);
        $name = "";
        do{
            $name .= $UpList[mt_rand(0,count($UpList) - 1)];
            $name .= mt_rand(11111,99999);
            $name .= $DownList[mt_rand(0,count($DownList) - 1)];
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