<?php

namespace TechnoBoty\AntWarsNeo\SettingsArenas;

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
}