<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Chats;
use App\Models\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\EmailTemplate;
use App\Http\Controllers\Controller;
use App\Mail\GeneralMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class MessagesController extends Controller
{

    public function create(Request $request)
    {

            $validator = Validator::make($request->all(), [
                'message' => 'required',
            ],[
                'message.required' => 'Message is Required',
            ]);

            if ($validator->fails())
            {
                return response()->json([
                    'status' => 400,
                    'messages' => $validator->getMessageBag()
                ]);
            }

            $chat = new Chats();
            $chat->sender_id = Auth::user()->id;
            $chat->receiver_id = $request->user_id;
            if ($chat->save()) {

                $message = new Messages();
                $message->chat_id = $chat->id;
                $message->sender_id = Auth::user()->id;
                $message->message = $request->message;
                $message->file_ext = 'Text';
                $message->seen = '2';
                $message->save();


                if (get_setting('enable_emails') == 'Yes') {
                    $chatt = Chats::where('id', $chat->id)->first();
                    if ($chatt->sender_id == Auth::user()->id) {
                        $rec = $chatt->receiver_id;
                    } elseif($chatt->receiver_id == Auth::user()->id) {
                        $rec = $chatt->sender_id;
                    }

                    $re = User::where('id',$rec)->first();
                    if ($re->email_message == '1') {

                        //Replace paremeter
                        $replace = array(
                            '{url}'              => env('APP_URL'),
                            '{logo}'              => get_setting('logo_dark'),
                            '{sitename}'          => get_setting('site_name'),
                            '{year}'              => date('Y'),
                            '{name}'              => Auth::user()->name,
                            '{image}'              => Auth::user()->image,
                            '{chat_id}'              => $chatt->id,
                        );

                        //Send email
                        $template = EmailTemplate::where('name','message')->first();
                        $template->body = process_string($replace, $template->body);

                        Mail::to($re->email)->send(new GeneralMail($template));
                    }
                }


                return response()->json([
                    'status' => 200,
                    'messages' => 'Message Conversation started Successfully'
                ]);

            }else{

                return response()->json([
                    'status' => 401,
                    'messages' => 'Error, Something went wrong'
                ]);

            }

    }

    public function new(Request $request)
    {
        $user_id = $request->user_id;

        if (Chats::where('sender_id', Auth::user()->id)->where('receiver_id', $user_id)->orWhere('sender_id', $user_id)->where('receiver_id', Auth::user()->id)->exists()) {
            //return redirect()->route('user.chats');

            return response()->json([
                'status' => 402,
                'messages' => 'Conversation already started'
            ]);
        }

        $chat = new Chats();
        $chat->sender_id = Auth::user()->id;
        $chat->receiver_id = $request->user_id;
        if ($chat->save()) {

            $message = new Messages();
            $message->chat_id = $chat->id;
            $message->sender_id = Auth::user()->id;
            $message->message = 'Hi 👍';
            $message->file_ext = 'Text';
            $message->seen = '2';
            $message->save();


            if (get_setting('enable_emails') == 'Yes') {
                $chatt = Chats::where('id', $chat->id)->first();
                if ($chatt->sender_id == Auth::user()->id) {
                    $rec = $chatt->receiver_id;
                } elseif($chatt->receiver_id == Auth::user()->id) {
                    $rec = $chatt->sender_id;
                }

                $re = User::where('id',$rec)->first();
                if ($re->email_message == '1') {

                    //Replace paremeter
                    $replace = array(
                        '{url}'              => env('APP_URL'),
                        '{logo}'              => get_setting('logo_dark'),
                        '{sitename}'          => get_setting('site_name'),
                        '{year}'              => date('Y'),
                        '{name}'              => Auth::user()->name,
                        '{image}'              => Auth::user()->image,
                        '{chat_id}'              => $chatt->id,
                    );

                    //Send email
                    $template = EmailTemplate::where('name','message')->first();
                    $template->body = process_string($replace, $template->body);

                    Mail::to($re->email)->send(new GeneralMail($template));
                }
            }

            return response()->json([
                'status' => 200,
                'messages' => 'Conversation started Successfully',
                'chat_id' => $chat->id
            ]);

        }else{

            return response()->json([
                'status' => 401,
                'messages' => 'Error, Something went wrong'
            ]);

        }

    }

    public function chats()
    {
        $chats = Chats::where('sender_id', Auth::user()->id)->orWhere('receiver_id', Auth::user()->id)->orderByDesc('created_at')->get();
        return view('user.messages.index', ['chats' => $chats]);
    }

    public function get(Request $request)
    {

        $search_term = $request->get('search_term');
        if ($search_term != '') {
            $user = User::where('name', 'like', '%'.$search_term.'%')->first();
            $chats = Chats::where('sender_id', $user->id)->orWhere('receiver_id', $user->id)->orderByDesc('created_at')->get();
        }else{
            $chats = Chats::where('sender_id', Auth::user()->id)->orWhere('receiver_id', Auth::user()->id)->orderByDesc('created_at')->get();
        }

        return view('user.messages.chats', ['chats' => $chats]);
    }

    public function user(Request $request)
    {
        $search_term = $request->get('search_term');
        if ($search_term != '') {
            $users = User::where('name', 'like', '%'.$search_term.'%')->orderByDesc('created_at')->get();
        }else{
            $users = User::orderByDesc('created_at')->get();
        }

        return view('user.messages.user', ['users' => $users]);
    }

    public function messages(Request $request)
    {
        $chat_id = $request->chat_id;

        if (!Chats::where('id', $chat_id)->where('sender_id', Auth::user()->id)->orWhere('id', $chat_id)->where('receiver_id', Auth::user()->id)->exists()) {
            return redirect()->route('user.chats');
        }

        $chats = Chats::where('sender_id', Auth::user()->id)->orWhere('receiver_id', Auth::user()->id)->orderByDesc('created_at')->get();
        $messages = Messages::where('chat_id', $chat_id)->orderBy("created_at", "asc")->get();
        //Update Seen
        Messages::where('chat_id', $chat_id)->where('sender_id', '!=', Auth::user()->id)->update(['seen' => 1]);
        $current_chat = Chats::where('id', $chat_id)->first();
        return view('user.messages.messages', ['chats' => $chats, 'messages' => $messages, 'chat_id' => $chat_id, 'current_chat' => $current_chat]);
    }

    public function messages_send(Request $request)
    {

        $message = new Messages();
        $message->chat_id = $request->chat_id;
        $message->sender_id = Auth::user()->id;
        $message->message = $request->messageTxt;
        $message->file_ext = $request->fileType;
        $message->seen = '2';
        $message->save();


        if (get_setting('enable_emails') == 'Yes') {
            $chat = Chats::where('id', $request->chat_id)->first();
            if ($chat->sender_id == Auth::user()->id) {
                $rec = $chat->receiver_id;
            } elseif($chat->receiver_id == Auth::user()->id) {
                $rec = $chat->sender_id;
            }

            $re = User::where('id',$rec)->first();
            if ($re->email_message == '1') {

                //Replace paremeter
                $replace = array(
                    '{url}'              => env('APP_URL'),
                    '{logo}'              => get_setting('logo_dark'),
                    '{sitename}'          => get_setting('site_name'),
                    '{year}'              => date('Y'),
                    '{name}'              => Auth::user()->name,
                    '{image}'              => Auth::user()->image,
                    '{chat_id}'              => $request->chat_id,
                );

                //Send email
                $template = EmailTemplate::where('name','message')->first();
                $template->body = process_string($replace, $template->body);

                Mail::to($re->email)->send(new GeneralMail($template));
            }
        }

        return response()->json([
            'status' => 200,
            'messages' => 'Message sent Successfully'
        ]);

    }

    public function messages_upload(Request $request)
    {

        if($request->hasFile('attachmentfile'))
        {
            $file = $request->file('attachmentfile');
            $ext = $file->getClientOriginalExtension();
            $mime_type = $file->getClientMimeType();
            $mime = explode('/',$mime_type);

            if ($mime[0] === 'image') {
                $file_ext = 'Image';
            } elseif($mime[0] === 'video') {
                $file_ext = 'Video';
            } elseif($mime[0] === 'audio') {
                $file_ext = 'Audio';
            }
            $size = $file->getSize()/1000;
            $filename = md5(microtime()).'.'.$ext;

        if ($size <= '20000') {
            $file->move('public/uploads/attachments/',$filename);
        } else {

            return response()->json([
                'status' => 400,
                'messages' => $size
            ]);

        }

            $message = new Messages();
            $message->chat_id = $request->chat_id;
            $message->sender_id = Auth::user()->id;
            $message->attachment_name = $filename;

            if ($mime[0] === 'image') {
                $message->file_ext = 'Image';
            } elseif($mime[0] === 'video') {
                $message->file_ext = 'Video';
            } elseif($mime[0] === 'audio') {
                $message->file_ext = 'Audio';
            }

            $message->mime_type = $mime_type;
            $message->seen = '2';
            $message->save();


            if (get_setting('enable_emails') == 'Yes') {
                $chat = Chats::where('id', $request->chat_id)->first();
                if ($chat->sender_id == Auth::user()->id) {
                    $rec = $chat->receiver_id;
                } elseif($chat->receiver_id == Auth::user()->id) {
                    $rec = $chat->sender_id;
                }

                $re = User::where('id',$rec)->first();
                if ($re->email_message == '1') {

                    //Replace paremeter
                    $replace = array(
                        '{url}'              => env('APP_URL'),
                        '{logo}'              => get_setting('logo_dark'),
                        '{sitename}'          => get_setting('site_name'),
                        '{year}'              => date('Y'),
                        '{name}'              => Auth::user()->name,
                        '{image}'              => Auth::user()->image,
                        '{chat_id}'              => $request->chat_id,
                    );

                    //Send email
                    $template = EmailTemplate::where('name','message')->first();
                    $template->body = process_string($replace, $template->body);

                    Mail::to($re->email)->send(new GeneralMail($template));
                }
            }


            return response()->json([
                'status' => 200,
                'file_name' => $filename,
                'type' => $file_ext,
            ]);

        }else {

            return response()->json([
                'status' => 400,
                'messages' => 'Please Upload an Image/Video/Audio'
            ]);
        }

    }

    public function messages_zip(Request $request)
    {

        if($request->hasFile('attachmentfile'))
        {
            $file = $request->file('attachmentfile');
            $ext = $file->getClientOriginalExtension();
            $mime_type = $file->getClientMimeType();
            $mime = explode('/',$mime_type);
            $file_ext = 'Zip';
            $size = $file->getSize()/1000;
            $filename = md5(microtime()).'.'.$ext;

            if ($size <= '20000') {
                $file->move('public/uploads/attachments/',$filename);
            } else {

                return response()->json([
                    'status' => 400,
                    'messages' => 'File size Limit Exceeded'
                ]);

            }

            $message = new Messages();
            $message->chat_id = $request->chat_id;
            $message->sender_id = Auth::user()->id;
            $message->attachment_name = $filename;
            $message->file_ext = $file_ext;
            $message->mime_type = $mime_type;
            $message->seen = '2';
            $message->save();


            if (get_setting('enable_emails') == 'Yes') {
                $chat = Chats::where('id', $request->chat_id)->first();
                if ($chat->sender_id == Auth::user()->id) {
                    $rec = $chat->receiver_id;
                } elseif($chat->receiver_id == Auth::user()->id) {
                    $rec = $chat->sender_id;
                }

                $re = User::where('id',$rec)->first();
                if ($re->email_message == '1') {

                    //Replace paremeter
                    $replace = array(
                        '{url}'              => env('APP_URL'),
                        '{logo}'              => get_setting('logo_dark'),
                        '{sitename}'          => get_setting('site_name'),
                        '{year}'              => date('Y'),
                        '{name}'              => Auth::user()->name,
                        '{image}'              => Auth::user()->image,
                        '{chat_id}'              => $request->chat_id,
                    );

                    //Send email
                    $template = EmailTemplate::where('name','message')->first();
                    $template->body = process_string($replace, $template->body);

                    Mail::to($re->email)->send(new GeneralMail($template));
                }
            }


            return response()->json([
                'status' => 200,
                'file_name' => $filename,
                'type' => $file_ext,
            ]);

        }else {

            return response()->json([
                'status' => 400,
                'messages' => 'Please Upload a Zip File'
            ]);
        }

    }

	public function messages_delete(Request $request){

        $id = $request->id;
        $message = Messages::find($id);

        if ($message->file_ext === 'Image' || $message->file_ext === 'Video' || $message->file_ext === 'Audio' || $message->file_ext === 'Zip') {

            $path = 'public/uploads/attachments/'.$message->attachment_name;
            if(File::exists($path)){
                File::delete($path);
            }

        }

        $message->delete();
        return response()->json(['status' => 'Message Deleted Successfully']);
	}

	public function mute(Request $request){

        $chat_id = $request->chat_id;
        $type = $request->type;

        $chat = Chats::find($chat_id);
        if($type == 'Mute') {
            $chat->status = '2';
        } elseif ($type == 'Unmute') {
            $chat->status = '1';
        }

        if ($chat->update()) {

            if($type == 'Mute') {

                return response()->json([
                    'status' => 200,
                    'messages' => 'Chat Muted Successfully'
                ]);

            } elseif ($type == 'Unmute') {

                return response()->json([
                    'status' => 200,
                    'messages' => 'Chat Unmuted Successfully'
                ]);
            }

        }else{

            return response()->json([
                'status' => 401,
                'messages' => 'Error, Something went wrong'
            ]);

        }
	}

	public function delete(Request $request){

        $id = $request->id;
        $item = Chats::find($id);

        $messages = Messages::where('chat_id', $item->id)->get();
        if ($messages != null) {
            foreach ($messages as $message) {

                if ($message->file_ext === 'Image' || $message->file_ext === 'Video' || $message->file_ext === 'Audio' || $message->file_ext === 'Zip') {

                    $path = 'public/uploads/attachments/'.$message->attachment_name;
                    if(File::exists($path)){
                        File::delete($path);
                    }

                }
                $message->delete();
            }
        }

        $item->delete();

        return response()->json(['status' => 'Chat Deleted Successfully']);
	}


}
