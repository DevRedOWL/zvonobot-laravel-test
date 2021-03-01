<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\User;

class MessagesController extends Controller
{
    /**
     * Show dialogs with all users
     */
    public function GetDialogs(Request $req)
    {
        return response()->json(['complete' => 'true', 'data' => ['dialogs_list' => [$req->user()->AllDialogs()]]]);
    }

    /**
     * Show all messages with user
     * @uses $_POST['user_id'] as second person in dialog
     * This method can be paginated using ?page=n request query param
     */
    public function GetMessages(Request $req)
    {
        // Validation
        $validator = Validator::make($req->all(), ['user_id' => 'required|integer',]);
        if ($validator->fails())
            return response()->json(['complete' => 'false', 'message' => $validator->errors()]);

        $body = $req->all();
        return response()->json(['complete' => 'true', 'data' => $req->user()->DialogMessages($body['user_id'])->paginate(25)]);
        // Интересный факт, здесь часто используется 302 redirect back, который может создавать прикольный цикл ошибок,
        // если последний запрос ведет на текущий контроллер, поэтому нам не подходит стандартный вариант из документации

    }

    /**
     * Send message to user
     * @uses $_POST['user_id'] message reciever
     * @uses $_POST['message'] text
     */
    public function SendMessage(Request $req)
    {
        // Validation
        $validator = Validator::make(
            $req->all(),
            [
                'user_id' => 'required|integer',
                'message' => 'required|string|max:255'
            ]
        );
        if ($validator->fails())
            return response()->json(['complete' => 'false', 'message' => $validator->errors()]);
        if ($req->user()->id == $req->input('user_id'))
            return response()->json(['complete' => 'false', 'message' => "You can not send messages to yourself"]);
        // TODO: В целом - хорошо было бы впилить проверку на существование такого ИДа, а так же блокировку аккаунта

        // Отправляем сообщение
        if ($req->user()->SendMessage($req->input('user_id'), $req->input('message')) == true)
            return response()->json(['complete' => 'true', 'message' => 'Message sent']);
    }
}
