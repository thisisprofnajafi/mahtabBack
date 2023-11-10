<?php

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Telegram\Bot\Laravel\Facades\Telegram;

class ChannelController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);

        $user = Auth::user();
        $ch = $user->channels()->where('channel_id', $request->id)->first();

        if (!$ch) {
            try {
                $channel = Telegram::getChat(['chat_id' => '@' . $request->id]);
            } catch (\Exception $s){
                return redirect()->back()->with('error', 'An Error With Telegram');
            }


            if ($channel) {
                $channelName = $channel['title'];
                $channelId = $channel['id'];
                $profilePhoto = $channel->getPhoto();
                $ch = new Channel();
                $ch->title = $channelName;
                $ch->channel_id = $request->id;
                $ch->chat_id = $channelId;
                $members = Telegram::getChatMemberCount(['chat_id' => '@' . $request->id]);
                $ch->members_count = $members;

                if ($profilePhoto) {
                    $profilePhotoFile = Telegram::getFile(['file_id' => $profilePhoto['big_file_id']]);
                    $profilePhotoUrl = 'https://api.telegram.org/file/bot' . env('TELEGRAM_BOT_TOKEN') . '/' . $profilePhotoFile->getFilePath();
                    $localFilePath = public_path('media/channels/' . $request->id . '.jpg');
                    file_put_contents($localFilePath, file_get_contents($profilePhotoUrl));
                    $ch->profile_path = $localFilePath;
                }

                $user->channels()->save($ch);

                return redirect(route('index'));
            }

            return redirect()->back()->with('error', 'Channel Not Found!');
        }

        return redirect()->back()->with('error', 'Channel already exists.');
    }

    public function getMy()
    {
        return view('channels')->with(['channels'=>Auth::user()->channels]);
    }

    public function getMessages($id, Request $request)
    {

        $user = Auth::user();
        $channel = $user->channels()->where('id', $id)->first();
        $count = $channel->messages->count();


        $messages = $channel->messages()->orderBy('created_at', 'desc')->get();

        return response()->json([

            'messages' => $messages,
            "count" => $count

        ]);
    }

    public function delete($id, $message)
    {
        $channel = Auth::user()->channels->where("id", $id)->first();
        if ($channel) {
            $message = $channel->messages->where("message_id", $message)->first();
            if ($message) {
                try {
                    Telegram::deleteMessage([
                        'chat_id' => $channel->chat_id,
                        'message_id' => $message->message_id,
                    ]);
                    return response()->json([
                        "status" => true,
                        "message" => "Deleted"
                    ]);
                }catch (\Exception $e){
                    return response()->json([
                        "status" => false,
                        "message" => $e->getMessage()
                    ]);
                }

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
}
