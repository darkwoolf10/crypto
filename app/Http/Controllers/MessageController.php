<?php

namespace App\Http\Controllers;

use App\Message;
use App\Sevices\CryptoService;
use Illuminate\Http\JsonResponse;
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
//        $validated = $request->validate([
//            'message' => 'required',
//        ]);

        $cryptoService = new CryptoService();

        $start = microtime(true);

        $encrypt = $cryptoService->encrypt_decrypt(
            'encrypt',
            $request->get('message'),
            $request->get('encrypt_method')
        );

        $time = round(microtime(true) - $start,  20);

        $message = new Message();
        $message->type = $request->get('encrypt_method');
        $message->time = $time;
        if (strlen($message->message) < 1000) {
            /**
             * Data for front
             */
            $message->message = $request->get('message');
            $message->encode = $encrypt;
        }
        auth()->user()->messages()->save($message);
        $message->save();

        if (strlen($message->message) >= 1000) {
            /**
             * Data for front
             */
            $message->message = $request->get('message');
            $message->encode = $encrypt;
        }


        return view('message', ['message' => $message]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function decrypt(Request $request): JsonResponse
    {
        $message = Message::find($request->get('id'));
        $cryptoService = new CryptoService();

        $start = microtime(true);
        $decrypted = $cryptoService->encrypt_decrypt('decrypt', $message->encode, $message->type);
        $time = round(microtime(true) - $start,  20);

        return response()->json([
            'decrypted' => $decrypted,
            'time' => $time
        ], 200);
    }

}
