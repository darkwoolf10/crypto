<?php

namespace App\Http\Controllers;

use App\Message;
use App\Sevices\CryptoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MessageController extends Controller
{
    /**
     * @param Request $request
     * @return View
     * @throws \Exception
     */
    public function crypt(Request $request): RedirectResponse
    {
        $message = new Message();

        /**
         * Если текст отправлен файлом
         */
        if ($request->has('file')) {
            $file = $request->file('file');
            $path = $file->store('encode');
            $encodeContent = Storage::get($path);

            $start = microtime(true);

            $encrypt = CryptoService::encrypt_decrypt(
                'encrypt',
                $encodeContent,
                $request->get('encrypt_method')
            );

            $time = round(microtime(true) - $start,  20);

            $message->path = $path;
            $message->message_type = 'file';
            $message->message = $encodeContent;
        } elseif ($request->get('message') !== null) { // Если текс отправлен в поле
            /**
             * Берём время шифрования
             */
            $start = microtime(true);

            $encrypt = CryptoService::encrypt_decrypt(
                'encrypt',
                $request->get('message'),
                $request->get('encrypt_method')
            );

            $time = round(microtime(true) - $start,  20);

            if (strlen($request->get('message')) < 1000) {
                $message->message = $request->get('message');
                $message->encode = $encrypt;
                $message->message_type = 'text';
            } else {
                $file = $request->get('message');
                $path = 'encode/' . hash('sha256', random_int(0, 40)) . '.txt';
                Storage::disk('local')->put($path, $file);
                $encodeContent = Storage::get($path);

                $start = microtime(true);

                $encrypt = CryptoService::encrypt_decrypt(
                    'encrypt',
                    $encodeContent,
                    $request->get('encrypt_method')
                );

                $time = round(microtime(true) - $start,  20);

                $message->path = $path;
                $message->message_type = 'file';
            }
        } else {
            throw new \InvalidArgumentException('Ошибка при отправке данных =(');
        }

        $message->encode = $encrypt;
        $message->type = $request->get('encrypt_method');
        $message->time = $time;
        auth()->user()->messages()->save($message);
        $message->save();


        return redirect()->back();
    }

    /**
     * @param Request $request
     * @return View
     */
    public function decrypt(Request $request): View
    {
        $message = Message::find($request->get('id'));

        $start = microtime(true);
        $decrypted = CryptoService::encrypt_decrypt('decrypt', $message->encode, $message->type);
        $time = round(microtime(true) - $start,  20);

        return view('decode', [
            'decrypted' => $decrypted,
            'time' => $time
        ]);
    }

}
