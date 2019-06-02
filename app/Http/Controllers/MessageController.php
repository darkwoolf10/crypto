<?php

namespace App\Http\Controllers;

use App\Message;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MessageController extends Controller
{
    /**
     * @param Request $request
     * @return View
     */
    public function crypt(Request $request): View
    {
        $validated = $request->validate([
            'message' => 'required',
            'encrypt_method' => 'required',
        ]);

        $start = microtime(true);
        $encrypt = hash($request->get('encrypt_method'), $request->get('message'));
        $time = round(microtime(true) - $start,  20);

        $message = new Message();
        $message->message = $request->get('message');
        $message->encode = $encrypt;
        $message->type = $request->get('encrypt_method');
        $message->time = $time;
        auth()->user()->messages()->save($message);
        $message->save();

        return view('message', ['message' => $message]);
    }
}
