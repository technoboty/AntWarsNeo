<?php

namespace TechnoBoty\AntWarsNeo\SettingsArenas;

use pocketmine\utils\TextFormat;

final class SquadSettings implements Settings{

    public const TEAM_LIST = ["red","blue","green","yellow"];

    public const TEAM_SPAWN_LOCATION = [
        "red" => [25,57,25],
        "blue" => [-25,57,-25],
        "green" => [-25,4,25],
        "yellow" => [25,4,-25]
    ];
    public const MIN_PLAYERS = 4;

    public const MAX_PLAYERS = 16;

    public const TIME_GLOWING = 43;

    public const TIME_DEATH_MATH = 12;

    public function colorizeTeamName(string $name){
        switch($name){
            case "red":
                return TextFormat::RED."Красные";
            case "blue":
                return TextFormat::BLUE."Синие";
            case "green":
                return TextFormat::GREEN."Зеленые";
            case "yellow":
                return TextFormat::YELLOW."Желтые";
        }
    }
}