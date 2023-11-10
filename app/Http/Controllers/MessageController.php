<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class MessageController extends Controller
{
    public function sendMessage($message, $id)
    {
        $channel = Channel::query()->where("chat_id", $id)->first();
        if ($channel) {
            $message = $channel->messages->where("message_id", $message)->first();
            if ($message) {
                if ($message->type == "text") {
                    $response = Telegram::sendMessage([
                        'chat_id' => $channel->chat_id,
                        'text' => $message->text,
                    ]);
                }
                if ($message->type == "photo") {
                    $response = Telegram::sendPhoto([
                        'chat_id' => $channel->chat_id,
                        'photo' => InputFile::create($message->path),
                        'caption' => $message->caption,
                    ]);
                }
                if ($message->type == "doc" || $message->type == "gif") {
                    $response = Telegram::sendDocument([
                        'chat_id' => $channel->chat_id,
                        'document' => InputFile::create($message->path),
                        'caption' => $message->caption,
                    ]);
                }
                if ($message->type == "video") {
                    $response = Telegram::sendVideo([
                        'chat_id' => $channel->chat_id,
                        'video' => InputFile::create($message->path),
                        'caption' => $message->caption,
                    ]);
                }
                if ($message->type == "audio") {
                    $response = Telegram::sendAudio([
                        'chat_id' => $channel->chat_id,
                        'audio' => InputFile::create($message->path),
                        'caption' => $message->caption,
                    ]);
                }
                if ($message->type == "voice") {
                    $response = Telegram::sendVoice([
                        'chat_id' => $channel->chat_id,
                        'voice' => InputFile::create($message->path),
                        'caption' => $message->caption,
                    ]);
                }
                if ($message->type == "sticker") {
                    $response = Telegram::sendSticker([
                        'chat_id' => $channel->chat_id,
                        'sticker' => InputFile::create($message->path),
                        'caption' => $message->caption,
                    ]);
                }

                if ($response->text) {
                    $channel->saveText($response);
                }
                if ($response->photo) {
                    $channel->savePhoto($response);
                }
                if ($response->document) {

                    if ($response->animation)
                        $channel->saveGif($response);
                    else
                        $channel->saveDoc($response);
                }

                if ($response->sticker) {
                    $channel->saveSticker($response);
                }

                if ($response->video) {
                    $channel->saveVideo($response);
                }

                if ($response->audio) {
                    $channel->saveAudio($response);
                }

                if ($response->voice) {
                    $channel->saveVoice($response);
                }

                return response()->json([
                    "status" => true,
                    "message" => "Message Sent"
                ]);
            } else {
                return response()->json([
                    "status" => false,
                    "message" => "Message Not Found"
                ]);
            }
        }
        return response()->json([
            "status" => false,
            "message" => "Channel Not Found"
        ]);
    }

    public function send($id, Request $request)
    {

        $channel = Auth::user()->channels->where("id", $id)->first();
        if ($channel) {
            if ($request->text) {
                $response = Telegram::sendMessage([
                    'chat_id' => $channel->chat_id,
                    'text' => $message->text,
                ]);
            }
            if ($request->file('photo')) {
                $file = $request->file('photo');
                $filepath = $file->store('media/messages/send', 'public');
                $response = Telegram::sendPhoto([
                    'chat_id' => $channel->chat_id,
                    'photo' => InputFile::create($filepath),
                    'caption' => ($request->text) ? $request->text : null,
                ]);
            }
            if ($request->file('document')) {
                $file = $request->file('document');
                $filepath = $file->store('media/messages/send', 'public');

                $response = Telegram::sendDocument([
                    'chat_id' => $channel->chat_id,
                    'document' => InputFile::create($filepath),
                    'caption' => ($request->text) ? $request->text : null,
                ]);
            }
            if ($request->file('video')) {
                $file = $request->file('video');
                $filepath = $file->store('media/messages/send', 'public');
                $response = Telegram::sendVideo([
                    'chat_id' => $channel->chat_id,
                    'video' => InputFile::create($filepath),
                    'caption' => ($request->text) ? $request->text : null,
                ]);
            }
            if ($request->file('video')) {
                $file = $request->file('video');
                $filepath = $file->store('media/messages/send', 'public');
                $response = Telegram::sendAudio([
                    'chat_id' => $channel->chat_id,
                    'audio' => InputFile::create($filepath),
                    'caption' => ($request->text) ? $request->text : null,
                ]);
            }
            if ($response->text) {
                $channel->saveText($response);
            }
            if ($response->photo) {
                $channel->savePhoto($response);
            }
            if ($response->document) {
                    $channel->saveDoc($response);
            }
            if ($response->video) {
                $channel->saveVideo($response);
            }

            if ($response->audio) {
                $channel->saveAudio($response);
            }
            return response()->json([
                "status" => true,
                "message" => "Message Sent"
            ]);
        }
    }
}
