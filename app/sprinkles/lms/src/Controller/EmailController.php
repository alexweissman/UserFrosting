<?php

namespace UserFrosting\Sprinkle\Lms\Controller;

use Carbon\Carbon;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Support\Exception\ForbiddenException;

use UserFrosting\Sprinkle\Core\Mail\EmailRecipient;
use UserFrosting\Sprinkle\Core\Mail\TwigMailMessage;

use UserFrosting\Sprinkle\Lms\Database\Models\RoundUser;
use UserFrosting\Sprinkle\Lms\Database\Models\Gameweek;
use UserFrosting\Sprinkle\Lms\Database\Models\Pick;
use UserFrosting\Sprinkle\Lms\Database\Models\Round;
use UserFrosting\Sprinkle\Account\Database\Models\User;

class EmailController extends SimpleController
{

    public function pickReminder(Request $request, Response $response, $args)
    { 
        $message = new TwigMailMessage($this->ci->view, "mail/pickReminderEmail.html.twig");

        $message->from([
            'email' => 'info@football-knockout.co.uk',
            'name' => 'Football Knocout.'
        ]);
        $gameweek = Gameweek::where('id', $args['gameweek_id'])->first();

        $rounds_np = Round::where('current_gameweek', $gameweek->id)
            ->select('id')
            ->get();
        // get all round users for the current gameweek
        $round_users_np = RoundUser::whereIn('round_id', $rounds_np)
            ->select('id', 'user_id')
            ->get();
        
        
        
        //cut down to one email per user
        $sentUsers = [];
        
        foreach ($round_users_np as $r) {
            $p = Pick::where('gameweek_id', $gameweek->id)
                ->where('user_id', $r->user_id)
                ->first();
            // if they don't have a pick
            if (!$p) {
                // if we haven't emailed the user already (because they can have multiple leagues with no pick)
                if(!in_array($r->user_id, $sentUsers)){   
                    array_push($sentUsers, $r->user_id);
                    $user = User::where('id', $r->user_id)->first();
                    $message->addEmailRecipient(
                        new EmailRecipient($user->email, $user->full_name, [
                            'deadline' => $gameweek->deadline
                        ])
                    );
                    $this->ci->mailer->sendDistinct($message);
                }
            }
        }
    }
}