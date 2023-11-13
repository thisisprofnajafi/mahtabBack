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
        $ch = Channel::where('channel_id', $request->id)->first();

        if ($ch) {
            return redirect()->back()->with('error', 'Channel already exists and is owned by another user.');
        }

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
                    $ch->profile_path = 'media/channels/' . $request->id . '.jpg';
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

        if (!$channel) {
            // Handle the case where the channel is not found, e.g., redirect or show an error message.
            // You can customize this based on your application's needs.
            abort(404, 'Channel not found');
        }

        $messages = $channel->messages()->orderBy('created_at', 'desc')->get();

        return view('messages', compact('channel', 'messages'));
    }

    public function delete($id, $messageId)
    {
        $user = Auth::user();
        $channel = $user->channels->where("id", $id)->first();

        if (!$channel) {
            return response()->json([
                "status" => false,
                "message" => "Channel not found"
            ]);
        }

        $message = $channel->messages->where("message_id", $messageId)->first();

        if (!$message) {
            return response()->json([
                "status" => false,
                "message" => "Message not found"
            ]);
        }

        try {
            Telegram::deleteMessage([
                'chat_id' => $channel->chat_id,
                'message_id' => $message->message_id,
            ]);

            // Assuming you also want to delete the message from the database
            $message->delete();

            return response()->json([
                "status" => true,
                "message" => "Deleted"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => "Error deleting message: " . $e->getMessage()
            ]);
        }
    }

    public function restore($id, $message){

    }
}
