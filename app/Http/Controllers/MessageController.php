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

        $cryptoService = new CryptoService($request->get('encrypt_method'));

        switch ($request->get('encrypt_method')) {
            case 'BF-OFB': $length = 8; break;
            case 'AES-256-CBC': $length = 16; break;
            default: $length = 0;
        }

        $secret_key = hash('sha256', config('app.crypt.key'));
        $secret_iv = substr(hash('sha256', config('app.crypt.iv')), 0, $length);

        $start = microtime(true);
        $encrypt = $cryptoService->encrypt($request->get('message'), $secret_key, $secret_iv);
        $time = round(microtime(true) - $start,  20);

        $message = new Message();
        $message->type = $request->get('encrypt_method');
        $message->time = $time;
        $message->key = $secret_key;
        $message->iv = $secret_iv;
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

    public function decrypt(int $id): JsonResponse
    {
        $message = Message::find($id);
        $cryptoService = new CryptoService($message->type);

        $start = microtime(true);
        $decrypted = $cryptoService->decrypt($message->encode, $message->key, $message->iv);
        $time = round(microtime(true) - $start,  20);

        return response()->json([
            'decrypted' => $decrypted,
            'time' => $time
        ], 200);
    }
}
