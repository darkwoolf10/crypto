<?php

namespace App\Http\Controllers;

use App\Message;
use App\Sevices\CryptoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MessageController extends Controller
{
    /**
     * @param Request $request
     * @return RedirectResponse
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

            $path = 'encrypt/' . hash('sha256', random_int(0, 100)) . '.txt';
            Storage::disk('local')->put($path, $encrypt);
            $message->encrypt_parh = $path;
            /**
             * Если текс отправлен в поле
             */
        } elseif ($request->get('message') !== null) {
            /**
             * Если текст не большой то записываем в БД иначе в файл
             */
            if (strlen($request->get('message')) < 1000) {
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

                $message->encode = $encrypt;
                $message->message_type = 'text';
                $message->encode = $encrypt;
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

                $path = 'encrypt/' . hash('sha256', random_int(0, 100)) . '.txt';
                Storage::disk('local')->put($path, $encrypt);
                $message->encrypt_parh = $path;
            }
        } else {
            throw new \InvalidArgumentException('Ошибка при отправке данных =(');
        }

        $message->message = $request->get('message');
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
        /**
         * Находим запись
         */
        $message = Message::find($request->get('id'));

        /**
         * Если это файл скачиваем содержимое
         */
        if ($message->message_type === 'file') {
            $message->encode = Storage::get($message->encrypt_parh);
        }

        /**
         * Расшифровуем
         */
        $start = microtime(true);
        $decrypted = CryptoService::encrypt_decrypt(
            'decrypt',
            $message->encode,
            $message->type
        );
        $time = round(microtime(true) - $start,  20);

        return view('decode', [
            'decrypted' => $decrypted,
            'time' => $time
        ]);
    }

    /**
     * @param Message $message
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(Message $message): JsonResponse
    {
        if (!$message) {
            throw new \InvalidArgumentException('Message not found!');
        }

        Storage::delete($message->path);
        Storage::delete($message->encrypt_parh);
        $message->delete();

        return response()->json(['message' => 'Сообщение удалено'], 200);
    }

}
