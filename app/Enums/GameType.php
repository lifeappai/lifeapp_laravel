<?php

namespace App\Enums;

use BenSampo\Enum\Enum;


final class GameType extends Enum
{
    const MISSION = 1;
    const QUIZ = 2;
    const RIDDLE = 3;
    const PUZZLE = 4;
    const JIGYASA = 5;
    const PRAGYA = 6;
    const Vision = 7;
    const MENTOR_SESSION = 8;

    const QUESTION_TYPE = [
        "TEXT" => 1,
        "IMAGE" => 2,
    ];

    const ALLOW_FOR = [
        "ALL" => 1,
        "BY_TEACHER" => 2,
        "BY_STUDENT" => 3,
    ];
}
