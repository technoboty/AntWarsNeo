<?php

namespace TechnoBoty\AntWarsNeo\TeamManager;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use TechnoBoty\AntWarsNeo\Arenas\Arena;
use TechnoBoty\AntWarsNeo\Arenas\ArenaManager;

class TeamGroup{

    private const TEAM_LIST = ["red","blue","green","yellow"];

    private array $teams = [];

    public function __construct(){
        foreach(self::TEAM_LIST as $name){
            $this->teams[$name] = new Team($name,Arena::MAX_PLAYERS / count(self::TEAM_LIST));
        }
    }
    public function getTeams() : array{
        return $this->teams;
    }
    public function getTeam(string $name) : ?Team{
        if(array_key_exists($name,$this->teams)){
            return $this->teams[$name];
        }
        return null;
    }
    public function joinTeam(string $name,Player $player) : void{
        $team = $this->getTeamByPlayer($player);
        $count = $this->teamCount();
        $countPlayers = count(ArenaManager::getInstance()->getArenaByPlayer($player)->getPlayers());
        if($team?->getTeamName() == $name){
            $player->sendMessage(TextFormat::RED."Вы итак состоите в этой команде!");
            return;
        }
        if(($count[$name]) < ($countPlayers / count($count))){
            $team?->removePlayer($player);
            $needTeam = $this->getTeam($name);
            $needTeam?->addPlayer($player);
            $player->sendMessage("Вы присоиденились к команде $name");
        } else {
            $player->sendMessage(TextFormat::RED."Команда заполнена!");
        }
    }
    public function getTeamByPlayer(Player $player) : ?Team{
        foreach($this->teams as $team){
            /** @var Team $team*/
            foreach($team->getPlayers() as $pl){
                if($pl->getName() == $player->getName()){return $team;}
            }
        }
        return NULL;
    }
    public function quitTeam(Player $player) : void{
        $team = $this->getTeamByPlayer($player);
        $team?->removePlayer($player);
    }
    private function teamCount() : array{
        $arr = [];
        foreach($this->teams as $team){
            $arr[$team->getTeamName()] = count($team->getPlayers());
        }
        return $arr;
    }
}