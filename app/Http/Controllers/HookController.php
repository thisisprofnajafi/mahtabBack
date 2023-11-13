<?php

namespace App\Http\Controllers;
use Exception;
use App\Models\Channel;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class HookController extends Controller
{
   public function getMessage(Request $request)
{

    Telegram::sendMessage([
        'chat_id' => 454775346,
        'text' => "new hook call",
    ]);
    Telegram::sendMessage([
        'chat_id' => 454775346,
        'text' => "request " . $request->getContent(),
    ]);
    try {
        $update = json_decode($request->getContent(), true);
        if (isset($update['channel_post'])) {
            $channel_post = $update['channel_post'];

            if (isset($channel_post['sender_chat']['type']) && $channel_post['sender_chat']['type'] == "channel") {
                $channel = Channel::query()->where('channel_id', $channel_post['sender_chat']['username'])->first();
                if ($channel) {
                    if (isset($channel_post['text'])) {
                        $channel->saveText($channel_post);
                        Telegram::sendMessage([
                            'chat_id' => 454775346,
                            'text' => "A text saved",
                        ]);
                    }
                    if (isset($channel_post['photo'])) {
                        $channel->savePhoto($channel_post);
                        Telegram::sendMessage([
                            'chat_id' => 454775346,
                            'text' => "A photo saved",
                        ]);
                    }
                    if (isset($channel_post['document'])) {
                        if (isset($channel_post['animation'])) {
                            $channel->saveGif($channel_post);
                            Telegram::sendMessage([
                                'chat_id' => 454775346,
                                'text' => "A gif saved",
                            ]);
                        } else {
                            $channel->saveDoc($channel_post);
                            Telegram::sendMessage([
                                'chat_id' => 454775346,
                                'text' => "A doc saved",
                            ]);
                        }
                    }
                    if (isset($channel_post['sticker'])) {
                        $channel->saveSticker($channel_post);
                        Telegram::sendMessage([
                            'chat_id' => 454775346,
                            'text' => "A sticker saved",
                        ]);
                    }
                    if (isset($channel_post['video'])) {
                        $channel->saveVideo($channel_post);
                        Telegram::sendMessage([
                            'chat_id' => 454775346,
                            'text' => "A video saved",
                        ]);
                    }
                    if (isset($channel_post['audio'])) {
                        $channel->saveAudio($channel_post);
                        Telegram::sendMessage([
                            'chat_id' => 454775346,
                            'text' => "An Audio saved",
                        ]);
                    }
                    if (isset($channel_post['voice'])) {
                        $channel->saveVoice($channel_post);
                        Telegram::sendMessage([
                            'chat_id' => 454775346,
                            'text' => "A Voice saved",
                        ]);
                    }
                }
            }else{
                Telegram::sendMessage([
                    'chat_id' => 454775346,
                    'text' => "did not detect",
                ]);
            }
        }else{
            Telegram::sendMessage([
                'chat_id' => 454775346,
                'text' => "channel post is not defined",
            ]);
        }
    } catch (Exception $e) {
        $errorMessage = $e->getMessage();
        Telegram::sendMessage([
            'chat_id' => 454775346,
            'text' => "error  " . $errorMessage,
        ]);
    }
}

}
