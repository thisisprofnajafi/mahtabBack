<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Telegram\Bot\Laravel\Facades\Telegram;

class Channel extends Model
{
    use HasFactory;


    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function updateChannel()
    {
        $channel = Telegram::getChat(['chat_id' => '@' . $this->channel_id]);
        if ($channel) {
            $channelName = $channel['title'];
            $channelId = $channel['id'];
            $profilePhoto = $channel->getPhoto();
            $this->title = $channelName;
            $this->chat_id = $channelId;
            $members = Telegram::getChatMemberCount(['chat_id' => '@' . $this->channel_id]);
            $this->members_count = $members;
            if ($profilePhoto) {
                $profilePhotoFile = Telegram::getFile(['file_id' => $profilePhoto['big_file_id']]);
                $profilePhotoUrl = 'https://api.telegram.org/file/bot' . env('TELEGRAM_BOT_TOKEN') . '/' . $profilePhotoFile->getFilePath();
                $localFilePath = public_path('media/channels/' . $this->channel_id . '.jpg');
                file_put_contents($localFilePath, file_get_contents($profilePhotoUrl));
                $this->profile_path = $localFilePath;
            } else {
                $this->profile_path = null;
            }
            $this->save();
        }
    }

    public function saveMessage($post, $type)
    {
        $message = new Message();
        $message->message_id = $post['message_id'];
        $message->type = $type;

        if (isset($post['caption'])) {
            $message->caption = $post['caption'];
        }

        // Handle different types of media
        switch ($type) {
            case 'text':
                $message->text = $post['text'];
                break;
            case 'photo':
            case 'doc':
            case 'gif':
            case 'video':
            case 'audio':
            case 'voice':
            case 'sticker':
                $fileId = '';

                // Determine the file ID based on the media type
                switch ($type) {
                    case 'photo':
                        $fileId = $post['photo'][count($post['photo']) - 1]['file_id'];
                        break;
                    case 'doc':
                        $fileId = $post['document']['file_id'];
                        break;
                    case 'video':
                        $fileId = $post['video']['file_id'];
                        break;
                    case 'audio':
                        $fileId = $post['audio']['file_id'];
                        break;
                    case 'voice':
                        $fileId = $post['voice']['file_id'];
                        break;
                    case 'gif':
                    case 'sticker':
                        $fileId = $post['document']['file_id'];
                        break;
                }

                // Download and save the file
                $message->path = $this->downloadGetFile($fileId);
                break;
        }

        $this->messages()->save($message);
    }

    public function saveText($post)
    {
        $this->saveMessage($post, 'text');
    }

    public function savePhoto($post)
    {
        $this->saveMessage($post, 'photo');
    }

    public function saveDocument($post)
    {
        $this->saveMessage($post, 'doc');
    }

    public function saveVideo($post)
    {
        $this->saveMessage($post, 'video');
    }

    public function saveAudio($post)
    {
        $this->saveMessage($post, 'audio');
    }

    public function saveVoice($post)
    {
        $this->saveMessage($post, 'voice');
    }

    public function saveGif($post)
    {
        $this->saveMessage($post, 'gif');
    }

    public function saveSticker($post)
    {
        $this->saveMessage($post, 'sticker');
    }

    private function downloadGetFile($param): string
    {
        $profilePhotoFile = Telegram::getFile(['file_id' => $param]);
        $profilePhotoUrl = 'https://api.telegram.org/file/bot' . env('TELEGRAM_BOT_TOKEN') . '/' . $profilePhotoFile->getFilePath();
        $localFilePath = public_path('media/messages/' .$param. '.'.explode('.',$profilePhotoFile->getFilePath())[1]);
        file_put_contents($localFilePath, file_get_contents($profilePhotoUrl));
        return 'media/messages/' .$param. '.'.explode('.',$profilePhotoFile->getFilePath())[1];
    }
}
