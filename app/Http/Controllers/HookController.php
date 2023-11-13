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
        Telegram::sendMessage(['chat_id' => 454775346, 'text' => "new hook call"]);

        try {
            $update = json_decode($request->getContent(), true);

            if (isset($update['channel_post'])) {
                $channel_post = $update['channel_post'];

                // Check if the message ID has been processed before
                $messageId = $channel_post['message_id'];
                if (MessageLog::where('message_id', $messageId)->exists()) {
                    // Skip processing the message
                    return;
                }

                // Save the message ID to the database
                MessageLog::create(['message_id' => $messageId]);

                if (isset($channel_post['sender_chat']['type']) && $channel_post['sender_chat']['type'] == "channel") {
                    $channel = Channel::query()->where('chat_id', $channel_post['sender_chat']['id'])->first();

                    if ($channel) {
                        // Handle text message
                        if (isset($channel_post['text'])) {
                            $channel->saveText($channel_post);
                            Telegram::sendMessage(['chat_id' => 454775346, 'text' => "A text saved"]);
                        }

                        // Handle photo message
                        if (isset($channel_post['photo'])) {
                            $channel->savePhoto($channel_post);
                            Telegram::sendMessage(['chat_id' => 454775346, 'text' => "A photo saved"]);
                        }

                        // Handle document and animation (gif) messages
                        if (isset($channel_post['document'])) {
                            if (isset($channel_post['animation'])) {
                                $channel->saveGif($channel_post);
                                Telegram::sendMessage(['chat_id' => 454775346, 'text' => "A gif saved"]);
                            } else {
                                $channel->saveDoc($channel_post);
                                Telegram::sendMessage(['chat_id' => 454775346, 'text' => "A doc saved"]);
                            }
                        }

                        // Handle sticker message
                        if (isset($channel_post['sticker'])) {
                            $channel->saveSticker($channel_post);
                            Telegram::sendMessage(['chat_id' => 454775346, 'text' => "A sticker saved"]);
                        }

                        // Handle video message
                        if (isset($channel_post['video'])) {
                            $channel->saveVideo($channel_post);
                            Telegram::sendMessage(['chat_id' => 454775346, 'text' => "A video saved"]);
                        }

                        // Handle audio message
                        if (isset($channel_post['audio'])) {
                            $channel->saveAudio($channel_post);
                            Telegram::sendMessage(['chat_id' => 454775346, 'text' => "An Audio saved"]);
                        }

                        // Handle voice message
                        if (isset($channel_post['voice'])) {
                            $channel->saveVoice($channel_post);
                            Telegram::sendMessage(['chat_id' => 454775346, 'text' => "A Voice saved"]);
                        }

                        // If none of the media types match
                        if (!isset($channel_post['text']) && !isset($channel_post['photo']) && !isset($channel_post['document']) &&
                            !isset($channel_post['sticker']) && !isset($channel_post['video']) && !isset($channel_post['audio']) && !isset($channel_post['voice'])) {
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
