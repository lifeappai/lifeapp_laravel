<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class QuizGameStatusEnum extends Enum
{
    const PENDING = 1;
    const INPROGRESS = 2;
    const COMPLETE = 3;
    const EXPIRED = 4;
}
