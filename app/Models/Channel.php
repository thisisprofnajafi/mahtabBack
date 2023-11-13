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

    public function saveText($post)
    {
        $message = new Message();
        $message->message_id = $post['message_id'];
        $message->type = "text";
        $message->text = $post['text'];
        $this->messages()->save($message);

    }

    public function savePhoto($post)
    {
        $message = new Message();
        $message->message_id = $post['message_id'];
        $message->type = "photo";

        $message->path = $this->downloadGetFile($post->photo[count(json_decode($post['photo']))-1]['file_id']);
        if ($post['caption'])
            $message->caption = $post['caption'];

        $this->messages()->save($message);
    }

    public function saveDoc($post)
    {
        $message = new Message();
        $message->message_id = $post['message_id'];
        $message->type = "doc";

        $message->path = $this->downloadGetFile($post->document->file_id);
        if ($post['caption'])
            $message->caption = $post['caption'];

        $this->messages()->save($message);

    }

    public function saveVideo($post)
    {
        $message = new Message();
        $message->type = "video";
        $message->message_id = $post['message_id'];
        $message->path = $this->downloadGetFile($post['video']['file_id']);
        if ($post['caption'])
            $message->caption = $post['caption'];

        $this->messages()->save($message);

    }

    public function saveAudio($post)
    {
        $message = new Message();
        $message->type = "audio";
        $message->message_id = $post['message_id'];
        $message->path = $this->downloadGetFile($post['audio']['file_id']);
        if ($post['caption'])
            $message->caption = $post['caption'];
        $this->messages()->save($message);

    }

    public function saveVoice($post)
    {
        $message = new Message();
        $message->type = "voice";
        $message->message_id = $post['message_id'];
        $message->path = $this->downloadGetFile($post['voice']['file_id']);
        if ($post['caption'])
            $message->caption = $post['caption'];
        $this->messages()->save($message);

    }

    public function saveGif($post){
        $message = new Message();
        $message->type = "gif";
        $message->message_id = $post['message_id'];
        $message->path = $this->downloadGetFile($post['document']['file_id']);
        if ($post['caption'])
            $message->caption = $post['caption'];
        $this->messages()->save($message);
    }
    public function saveSticker($post){
        $message = new Message();
        $message->type = "sticker";
        $message->message_id = $post['message_id'];
        if($post['is_video'])
            $message->path = $this->downloadGetFile($post['sticker']['file_id']);
        else
            $message->path = $this->downloadGetFile($post['sticker']['file_id']);
        if ($post['caption'])
            $message->caption = $post['caption'];
        $this->messages()->save($message);
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
