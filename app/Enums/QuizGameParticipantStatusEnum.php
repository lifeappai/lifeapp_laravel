<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class QuizGameParticipantStatusEnum extends Enum
{
    const PENDING = 1;
    const ACCEPT = 2;
    const REJECT = 3;
}
