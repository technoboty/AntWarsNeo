<?php

namespace TechnoBoty\AntWarsNeo;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\scheduler\Task;
use TechnoBoty\AntWarsNeo\Arenas\ArenaManager;
use TechnoBoty\AntWarsNeo\Scoreboard\InGameScoreboards;
use TechnoBoty\AntWarsNeo\WorldGenerator\VoidGenerator;

class Main extends PluginBase{

    public Config $config;

    private ArenaManager $manager;

    use SingletonTrait;

    public function onEnable(): void{
        self::setInstance($this);
        @mkdir($this->getDataFolder()."settings");
        @mkdir($this->getDataFolder()."statistic");
        $this->config = new Config($this->getDataFolder()."settings/config.json",Config::JSON,[]);
        Server::getInstance()->getPluginManager()->registerEvents(new EventsListener(),$this);
        GeneratorManager::getInstance()->addGenerator(VoidGenerator::class,"antwars",fn() => NULL,TRUE);
        $this->manager = ArenaManager::getInstance();
        $this->resetSideBar();
        parent::onEnable();
    }
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if($command->getName() == "gen"){
            $arena  = ArenaManager::getInstance()->getArenaByPlayer($sender);
            if($arena != null){
                $arena->getSession()->onStart();
                $arena->incrementStage();
            }
        }elseif($command->getName() == "tpa"){
            $this->manager->getArena()->quit($sender);
        }
        return parent::onCommand($sender, $command, $label, $args);
    }
    private function resetSideBar() : void{
        $this->getScheduler()->scheduleRepeatingTask(new class($this) extends Task{

            public function __construct(private Main $main){}

            public function onRun(): void {
                $players = Server::getInstance()->getWorldManager()->getDefaultWorld()->getPlayers();
                foreach($players as $player){
                    $this->main->reSendDefaultScoreBoard($player);
                }
            }
        },20);
    }
    public function sendDefaultScoreBoard(Player $player) : void{
        $online = count(Server::getInstance()->getOnlinePlayers());
        $lines = [
            1=> "Текущий онлайн: $online",
            2 => "Ваша привилегия: ".TextFormat::BLUE."Player   ",
            3 => "Донат: ".TextFormat::GOLD."summer-world.me "
        ];
        InGameScoreboards::getInstance()->setInHubScoreBoard([$player],$lines);
    }
    public function reSendDefaultScoreBoard(Player $player) : void{
        $player->getNetworkSession()->sendDataPacket(RemoveObjectivePacket::create("inhub"));
        $online = count(Server::getInstance()->getOnlinePlayers());
        $lines = [
            1=> "Текущий онлайн: $online",
            2 => "Ваша привилегия: ".TextFormat::BLUE."Player   ",
            3 => "Донат: ".TextFormat::GOLD."summer-world.me "
        ];
        InGameScoreboards::getInstance()->setInHubScoreBoard([$player],$lines);
    }
}