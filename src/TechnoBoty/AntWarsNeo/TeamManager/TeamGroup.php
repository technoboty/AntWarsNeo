<?php

namespace TechnoBoty\AntWarsNeo\TeamManager;

use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use TechnoBoty\AntWarsNeo\Arenas\Arena;
use TechnoBoty\AntWarsNeo\Arenas\ArenaManager;
use TechnoBoty\AntWarsNeo\SettingsArenas\Settings;

class TeamGroup {

    private const TEAM_LIST = ["red", "blue", "green", "yellow"];

    private array $teams = [];

    public function __construct(private ?Settings $data){
        if(!$data == null){
            $maxPlayers = $data::MAX_PLAYERS;
            foreach($data::TEAM_LIST as $name){
                $this->teams[$name] = new Team($name,$maxPlayers / count($data::TEAM_LIST));
            }
        } else {
            foreach (self::TEAM_LIST as $name) {
                $this->teams[$name] = new Team($name, 16 / count(self::TEAM_LIST));
            }
        }
    }

    public function getTeams(): array {
        return $this->teams;
    }

    public function getTeam(string $name): ?Team {
        if(array_key_exists($name, $this->teams)) {
            return $this->teams[$name];
        }
        return NULL;
    }

    public function joinTeam(string $name, Player $player): void{
        $team = $this->getTeamByPlayer($player);
        $count = $this->teamCount();
        $countPlayers = count(ArenaManager::getInstance()->getArenaByPlayer($player)->getPlayers());
        if($team?->getTeamName() == $name) {
            $player->sendMessage(TextFormat::RED . "Вы итак состоите в этой команде!");
            return;
        }
        if(($count[$name]) < ($countPlayers / count($count))) {
            $team?->removePlayer($player);
            $needTeam = $this->getTeam($name);
            $needTeam?->addPlayer($player);
            $text = $this->data?->colorizeTeamName($name);
            $player->sendMessage(TextFormat::GRAY."Вы присоиденились к команде $text");
        } else {
            $player->sendMessage(TextFormat::RED . "Команда заполнена!");
        }
    }

    public function getTeamByPlayer(Player $player): ?Team {
        foreach($this->teams as $team) {
            /** @var Team $team */
            foreach($team->getPlayers() as $pl) {
                if($pl->getName() == $player->getName()) {
                    return $team;
                }
            }
        }
        return NULL;
    }

    public function quitTeam(Player $player): void {
        $team = $this->getTeamByPlayer($player);
        $team?->removePlayer($player);
    }

    private function teamCount(): array {
        $arr = [];
        foreach($this->teams as $team) {
            $arr[$team->getTeamName()] = count($team->getPlayers());
        }
        return $arr;
    }

    /**
     * @param array $players
     * @return void
     */
    public function equipTeams(array $players): void {
        $teamPlayers = [];
        foreach($this->teams as $team) {
            /** @var Team $team */
            foreach($team->getPlayers() as $player) {
                $teamPlayers[$player->getName()] = $player;
            }
        }
        $playersOutTeam = array_diff($players, $teamPlayers);
        foreach($playersOutTeam as $pl) {
            $count = $this->teamCount();
            uasort($count, function(int $a, int $b) { return $a <=> $b; });
            foreach($count as $team => $val){
                $text = $this->data?->colorizeTeamName($team);
                $pl->sendMessage(TextFormat::GRAY."Вас автоматически было определенно в команду $text");
                $this->getTeam($team)->addPlayer($pl);
                break;
            }
        }
    }
}