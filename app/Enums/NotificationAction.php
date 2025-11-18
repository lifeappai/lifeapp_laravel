<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static Campaign()
 * @method static static Friendship()
 * @method static static Mission()
 * @method static static QuizGame()
 * @method static static Query()
 * @method static static AdminCampaign()
 * @method static static Session()
 */
final class NotificationAction extends Enum
{
    const Campaign =   0;
    const Friendship =   1;
    const Mission = 2;
    const QuizGame = 3;
    const Query = 4;

    const AdminCampaign = 5;
    const Session = 6;
    const Vision = 7;
    const AdminMessage = 8;
    const Jigyasa = 9;
    const Pragya = 10;

}
