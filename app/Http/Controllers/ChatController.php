<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Message;

/**
 * Class ChatController
 * @package App\Http\Controllers
 */
class ChatController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function getChat(Request $request)
    {
        if(!Auth::check()){
            return redirect('/');
        }

        $id = $request->input('id');

        $messages = Message::whereRaw('to_user_id = ?', array($id))->get();

        $messagesArray = [];
        foreach ($messages as $msg){
            $messagesArray[] = [
                "id" => $msg->id,
                "from_user_id" => $msg->from_user_id,
                "user" => $msg->fromUser->username,
                "message" => $msg->message,
            ];
        }

        return view('chat/chat', [ 'user' => Auth::user(), 'toUser' => $id, 'messages' => json_encode($messagesArray)]);
    }

    /**
     * Get list with all users
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getList()
    {
        if(!Auth::check()){
            return redirect('/');
        }

        // Show the page.
        $users = User::whereRaw('role != ?', array('admin'))->get();

        return view('chat/list', ['users' => $users, 'user' => Auth::user()]);
    }

    /**
     * Show all messages
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function getMessages()
    {
        if(!Auth::check()){
            return redirect('/');
        }

        if(Auth::user()->role !== 'admin'){
            return redirect('/');
        }

        // Get all messages
        $messages = Message::get();

        return view('chat/messages', ['messages' => $messages, 'user' => Auth::user()]);
    }

    /**
     * Remove message
     * @param Request $request
     * @return string
     */
    public function getRemove(Request $request)
    {
        $id = $request->input('id');
        $message = Message::find($id);
        $message->delete();

        return json_encode(['success' => 'true']);
    }
}
