<?php

namespace TechnoBoty\AntWarsNeo;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\Position;
use pocketmine\world\WorldCreationOptions;
use TechnoBoty\AntWarsNeo\Arenas\ArenaManager;
use TechnoBoty\AntWarsNeo\MapManager\MapManager;
use TechnoBoty\AntWarsNeo\WorldGenerator\VoidGenerator;

class Main extends PluginBase{

    private Config $config;

    private ArenaManager $manager;

    use SingletonTrait;

    public function onEnable(): void{
        self::setInstance($this);
        @mkdir($this->getDataFolder()."settings");
        @mkdir($this->getDataFolder()."statistic");
        $this->config = new Config($this->getDataFolder()."settings/config.json",Config::JSON);
        Server::getInstance()->getPluginManager()->registerEvents(new EventsListener(),$this);
        GeneratorManager::getInstance()->addGenerator(VoidGenerator::class,"antwars",fn() => NULL,TRUE);
        $this->manager = ArenaManager::getInstance();
        parent::onEnable();
    }
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if($command->getName() == "gen"){
            $arena  = $this->manager->getArena();
            $arena->join($sender);
        }elseif($command->getName() == "tpa"){
            $this->manager->getArena()->quit($sender);
        }
        return parent::onCommand($sender, $command, $label, $args);
    }
}