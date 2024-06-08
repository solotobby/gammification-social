<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function handle(Request $request){

        $event = $request["event"];

        Webhook::create(['name' => $event, 'content' => $request]);


    }
}
