<?php

namespace App\Constants;

final class NotificationTemplate
{
    public const FRIEND_REQUEST = [
        'title' => 'Life App',
        'body' => 'You have new friend request.'
    ];

    public const FRIEND_REQUEST_APPROVE = [
        'title' => 'Life App',
        'body' => '%s have accepted your friend request.'
    ];

    public const CAMPAIGN_INVITATION = [
        'title' => 'Life App',
        'body' => '%s have invited you to his campaign.'
    ];

    public const CAMPAIGN_INVITATION_SUPPORT = [
        'title' => 'Life App',
        'body' => '%s have supported your campaign with %s coins.',
    ];

    public const MISSION_APPROVED = [
        'title' => 'Life App',
        'body' => 'Your mission have been approved.',
    ];

    public const MISSION_REJECTED = [
        'title' => 'Life App',
        'body' => 'Your mission have been rejected.',
    ];

    public const NEW_MISSION = [
        'title' => 'Life App',
        'body' => 'New mission is live to explore.',
    ];

    public const Quiz_GAME_INVITE = [
        'title' => 'Life App',
        'body' => '%s invited you to participate in quiz game.'
    ];

    public const QUIZ_GAME_INVITE_ACCEPT = [
        'title' => 'Life App',
        'body' => '%s accepted the invite request'
    ];

    public const QUIZ_GAME_INVITE_REJECT = [
        'title' => 'Life App',
        'body' => '%s rejected the invite request'
    ];

    public const QUIZ_GAME_START = [
        'title' => 'Life App',
        'body' => 'Quiz game has been started.'
    ];

    public const QUERY_FOR_MENTOR = [
        'title' => 'Life App',
        'body' => 'You have a question from %s'
    ];

    public const REPLY_FROM_MENTOR = [
        'title' => 'Life App',
        'body' => 'Your mentor has sent you an answer.'
    ];

    public const CLOSE_QUERY = [
        'title' => 'Life App',
        'body' => 'Your Mentor has requested to close the questions'
    ];

    public const QUERY_FEEDBACK = [
        'title' => 'Life App',
        'body' => '%s has closed the questions with %s star ratings'
    ];

    public const ADMIN_OPEN_QUERY = [
        'title' => 'Life App',
        'body' => '%s has opened the questions.'
    ];

    public const ADMIN_CLOSE_QUERY = [
        'title' => 'Life App',
        'body' => '%s has opened the questions.'
    ];

    public const ADMIN_QUERY_MESSAGE = [
        'title' => 'Life App',
        'body' => 'New message from %s'
    ];

    public const NEW_MISSION_ASSIGNED = [
        'title' => 'Life App',
        'body' => 'Your teacher has assigned you a Mission.',
    ];

    public const NEW_SESSION = [
        'title' => 'Life App',
        'body' => 'New mentor session is available.',
    ];

    public const NEW_VISION_ASSIGNED = [
        'title' => 'Life App',
        'body' => 'A new vision has been assigned to you. Check it out now!',
    ];

    public const VISION_APPROVED = [
        'title' => 'Life App',
        'body' => 'Your vision has been approved.',
    ];

    public const VISION_REJECTED = [
        'title' => 'Life App',
        'body' => 'Your vision has been rejected.',
    ];

    public const ADMIN_CUSTOM_MESSAGE = [
        'title' => '%s', 
        'body'  => '%s', 
    ];

    public const JIGYASA_APPROVED = [
        'title' => 'Life App',
        'body'  => 'Your Jigyasa has been approved!',
    ];

    public const JIGYASA_REJECTED = [
        'title' => 'Life App',
        'body'  => 'Your Jigyasa has been rejected.',
    ];

    public const PRAGYA_APPROVED = [
        'title' => 'Life App',
        'body'  => 'Your Pragya has been approved!',
    ];

    public const PRAGYA_REJECTED = [
        'title' => 'Life App',
        'body'  => 'Your Pragya has been rejected.',
    ];

    public const NEW_JIGYASA_ASSIGNED = [
        'title' => 'Life App',
        'body'  => 'Your teacher has assigned you a Jigyasa.',
    ];

    public const NEW_PRAGYA_ASSIGNED = [
        'title' => 'Life App',
        'body'  => 'Your teacher has assigned you a Pragya.',
    ];

}
