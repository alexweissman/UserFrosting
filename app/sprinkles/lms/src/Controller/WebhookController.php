<?php

namespace UserFrosting\Sprinkle\Lms\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use UserFrosting\Sprinkle\Core\Controller\SimpleController;
use UserFrosting\Sprinkle\Account\Database\Models\User;
use UserFrosting\Sprinkle\Lms\Database\Models\Subscription;

class WebhookController extends SimpleController
{

    public function ReceiveWebhook(Request $request, Response $response, $args)
    {  
        $payload = $request->getParsedBody();

        // Handle the event
        switch ($payload["type"]) {
        case 'checkout.session.completed':
            if($payload["data"]["object"]["payment_status"] != 'paid'){
                // they haven't paid and we don't continue
                http_response_code(200);
                exit();
            }
            // check if we have a subscription record for the given email address
            $sub = Subscription::where('stripe_cus_id', $payload["data"]["object"]["customer_details"]["email"])->first();

            if($sub){
                //record exists, update the details
                $sub->term = '2324';
                $sub->status = 'active';
                $sub->save();   
            }else{
                //record doesn't exist make them one
                $user = User::where('email', $payload["data"]["object"]["customer_details"]["email"])->first();
                $subscription = new Subscription([
                    'user_id' => $user->id,
                    'term' => '2324',
                    'status' => 'active'
                ]);
                $subscription->save();
            }
            http_response_code(200);
            exit();
        // ... handle other event types
        default:
            echo 'Received unknown event type ' . $payload["type"];
        }

        http_response_code(200);
    }
}
