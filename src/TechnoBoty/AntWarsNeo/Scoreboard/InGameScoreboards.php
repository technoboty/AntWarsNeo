<?php

namespace TechnoBoty\AntWarsNeo\Scoreboard;

use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;
use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\player\Player;
use pocketmine\utils\SingletonTrait;
use pocketmine\utils\TextFormat;
use TechnoBoty\AntWarsNeo\Arenas\Arena;

class InGameScoreboards{

    use SingletonTrait;

    private string $name = TextFormat::RED."____Ant".TextFormat::BLUE."Wars".TextFormat::YELLOW."Neo____";
    private string $hubName = TextFormat::BLUE."____Summer".TextFormat::YELLOW."World____";

    public function __construct(){
        self::setInstance($this);
    }
    public function setInGameScoreboard(array $players, array $lines,string $name) : void{
        /** @var Player[] $players $pk */
        $pk = SetDisplayObjectivePacket::create("sidebar",$name,$this->name,"dummy",0);
        foreach($players as $player){
            $player->getNetworkSession()->sendDataPacket(RemoveObjectivePacket::create("inhub"));
            $player->getNetworkSession()->sendDataPacket($pk);
            foreach($lines as $score => $line){
                $this->setScoreboardLine($score,$line,$name,$player);
            }
        }
    }
    public function removeInLobbyScoreboard(array $players) : void{
        foreach($players as $player){
            $player->getNetworkSession()->sendDataPacket(RemoveObjectivePacket::create("inlobby"));
        }
    }
    public function setScoreboardLine(int $score, string $line,string $name,Player $player): void{
        $entry = new ScorePacketEntry();
        $entry->objectiveName = $name;
        $entry->type = $entry::TYPE_FAKE_PLAYER;
        $entry->customName = $line;
        $entry->score = $score;
        $entry->scoreboardId = $score;
        if(isset($this->scoreboardLines[$score])){
            $pk = new SetScorePacket();
            $pk->type = $pk::TYPE_REMOVE;
            $pk->entries[] = $entry;
            $player->getNetworkSession()->sendDataPacket($pk);
        }
        $pk = new SetScorePacket();
        $pk->type = $pk::TYPE_CHANGE;
        $pk->entries[] = $entry;
        $player->getNetworkSession()->sendDataPacket($pk);
    }
    public function setInHubScoreBoard(array $players,array $lines) : void{
        /** @var Player[] $players $pk */
        $pk = SetDisplayObjectivePacket::create("sidebar","inhub",$this->hubName,"dummy",0);
        foreach($players as $player){
            $player->getNetworkSession()->sendDataPacket($pk);
            foreach($lines as $score => $line){
                $this->setScoreboardLine($score,$line,"inhub",$player);
            }
        }
    }
}