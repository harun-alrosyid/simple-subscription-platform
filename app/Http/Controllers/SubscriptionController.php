<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\SubscribeRequest;
use App\Models\User;
use App\Models\Website;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function store(SubscribeRequest $req, Website $website)
    {
        $user = User::firstOrCreate(
            ['email' => strtolower($req->email)],
            ['name' => $req->name]
        );

        Subscription::firstOrCreate([
            'website_id' => $website->id,
            'user_id' => $user->id,
        ]);

        return response()->json(['message' => 'Subscribed'], 201);
    }
}
