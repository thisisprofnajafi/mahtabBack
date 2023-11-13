<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;

class MessageController extends Controller
{
    public function sendMessage($id, $message)
    {
        Telegram::sendMessage(['chat_id' => 454775346, 'text' => "message: " . $message . " channel:" . $id]);
        $channelId = $id;
        $messageId = $message;
        $channel = Channel::find($channelId);

        if (!$channel) {
            return response()->json([
                "status" => false,
                "message" => "Channel Not Found"
            ]);
        }

        $message = $channel->messages->where("id", $messageId)->first();

        if (!$message) {
            return response()->json([
                "status" => false,
                "message" => "Message Not Found"
            ]);
        }

        $response = $this->sendTelegramMessage($channel, $message);

        if ($response->text) {
            $channel->saveText($response);
        }
        if ($response->photo) {
            $channel->savePhoto($response);
        }
        if ($response->document) {
            if ($response->animation) {
                $channel->saveGif($response);
            } else {
                $channel->saveDocument($response);
            }
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
    }

    private function sendTelegramMessage($channel, $message)
    {
        $params = [
            'chat_id' => '@' . $channel->channel_id,
            'caption' => $message->caption,
        ];
        Telegram::sendMessage(['chat_id' => 454775346, 'text' => "chat id: " . $channel->chat_id . " channel id: " . $channel->channel_id]);


        switch ($message->type) {
            case "text":
                $params['text'] = $message->text;
                return Telegram::sendMessage($params);

            case "photo":
                $params['photo'] = InputFile::create($message->path);
                return Telegram::sendPhoto($params);

            case "document":
                $params['document'] = InputFile::create($message->path);
                return Telegram::sendDocument($params);
            case "gif":
                $params['animation'] = InputFile::create($message->path);
                return Telegram::sendAnimation($params);

            case "video":
                $params['video'] = InputFile::create($message->path);
                return Telegram::sendVideo($params);

            case "audio":
                $params['audio'] = InputFile::create($message->path);
                return Telegram::sendAudio($params);

            case "voice":
                $params['voice'] = InputFile::create($message->path);
                return Telegram::sendVoice($params);

            case "sticker":
                $params['document'] = $this->stickerCleaner($message->path);
                return Telegram::sendDocument($params);

            default:
                return null;
        }
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
                $channel->saveDocument($response);
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


    public function stickerCleaner($path)
    {

        $pattern = '/\/([^\/]+)\.webm$/';

        if (preg_match($pattern, $path, $matches)) {
            // $matches[0] contains the entire matched string
            // $matches[1] contains the desired portion
            $result = $matches[1];

            // Output the result
            return $result;
        } else {
            // No match found
            echo "No match found";
        }
    }
}
