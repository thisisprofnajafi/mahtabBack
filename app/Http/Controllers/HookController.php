<?php

namespace App\Http\Controllers;

use App\Models\MessageLog;
use Exception;
use App\Models\Channel;
use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class HookController extends Controller
{
    public function getMessage(Request $request)
    {
        Telegram::sendMessage(['chat_id' => 454775346, 'text' => "new hook call " . $request->getContent()]);

        try {
            $update = json_decode($request->getContent(), true);

            if (isset($update['channel_post'])) {
                $channel_post = $update['channel_post'];

                if (isset($channel_post['message_id'])){
                    $messageId = $channel_post['message_id'];
                }else{
                    $messageId = $channel_post[0]['message_id'];
                }

                if (MessageLog::where('message_id', $messageId)->exists()) {
                    // Skip processing the message
                    return;
                }

                // Save the message ID to the database
                MessageLog::create(['message_id' => $messageId]);

                if (isset($channel_post['sender_chat']['type']) && $channel_post['sender_chat']['type'] == "channel") {
                    $channel = Channel::query()->where('chat_id', $channel_post['sender_chat']['id'])->first();

                    if ($channel) {
                        $mediaTypes = ['text', 'photo', 'document', 'sticker', 'video', 'audio', 'voice'];

                        foreach ($mediaTypes as $type) {
                            if (isset($channel_post[$type])) {
                                $methodName = 'save' . ucfirst($type);
                                if (isset($channel_post['animation'])) {
                                    $channel->saveGif($channel_post);
                                }else{
                                    $channel->$methodName($channel_post);
                                }
                                Telegram::sendMessage(['chat_id' => 454775346, 'text' => "A " . $type . " saved"]);
                            }
                        }

                        if (empty($mediaTypes)) {
                            Telegram::sendMessage(['chat_id' => 454775346, 'text' => "No media type detected"]);
                        }
                    } else {
                        \Log::warning('Channel not detected for ID: ' . $channel_post['sender_chat']['id']);
                        Telegram::sendMessage(['chat_id' => 454775346, 'text' => "Channel Not Detected"]);
                    }
                } else {
                    Telegram::sendMessage(['chat_id' => 454775346, 'text' => "Did not detect channel"]);
                }
            } else {
                Telegram::sendMessage(['chat_id' => 454775346, 'text' => "Channel post is not defined"]);
            }
        } catch (Exception $e) {
            \Log::error('Error processing Telegram message: ' . $e->getMessage());
            Telegram::sendMessage(['chat_id' => 454775346, 'text' => "An error occurred " . $e->getMessage()]);
        }
    }
}
