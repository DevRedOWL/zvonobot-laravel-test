<?php

namespace App;

use Error;
use Exception;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /** Relations with messages */
    public function SentMessages()
    {
        return $this->hasMany('App\Message', 'from');
    }
    public function RecievedMessages()
    {
        return $this->hasMany('App\Message', 'to');
    }

    /**
     * Get all available users
     * (Запрос довольно хитрый, потому, что возвращает диалоги в сортировке в зависимости от последнего сообщения (как в вк))
     * @return {name,dialog,last_message}[]
     */
    public function AllDialogs()
    {
        $thid = $this->id;
        // Выбираем всех пользователей, кроме себя (Чтобы поместить пустые диалоги в конец, ставим дату в 0)
        $usersQuery = DB::table('users')
            ->where('id', '!=', $thid)
            ->select('name', 'id as dialog', DB::raw("date '1970-01-01' as last_message_t"));
        // Выбираем все сообщения, в которых текущий юзер отправитель
        $fromQuery = DB::table('messages')
            ->where('from', $thid)
            ->join('users', 'users.id', '=', 'messages.to')
            ->select('name', 'to as dialog', 'messages.created_at as last_message_t');
        // Выбираем все сообщения, в которых текущий юзер получатель
        $toQuery = DB::table('messages')
            ->where('to', $thid)
            ->join('users', 'users.id', '=', 'messages.from')
            ->select('name', 'from as dialog', 'messages.created_at as last_message_t');
        // Т.к. дистинкт тут работает упорото (либо я не понял как) - обработаем запрос средствами коллекций
        $result = new Collection($usersQuery->union($toQuery->union($fromQuery))->orderBy('last_message_t', 'desc')->get());
        return $result->unique('dialog');
    }

    /**
     * Get all messages that were sended from/by current user to/from specified user
     * @param int $dialog_id Id of user, that we have dialog with
     * @return Query(Message) 
     */
    public function DialogMessages($dialog_id)
    {
        return DB::table('messages')
            ->where(function ($query) use ($dialog_id) {
                // Where current user is sender and coversator is reciever
                $query->where('from', $this->id)->where('to', $dialog_id);
            })
            ->orWhere(function ($query) use ($dialog_id) {
                // Where current user is reciever and coversator is sender
                $query->where('from', $dialog_id)->where('to', $this->id);
            })
            ->orderBy('created_at', 'desc'); // ->get()
    }

    /**
     * Send message to a specified user
     * @param int $dialog_id Id of user, that we have dialog with
     * @return Message
     */
    public function SendMessage($dialog_id, $text)
    {
        $message = new Message();
        $message->text = $text;
        $message->to = $dialog_id;
        $message->from = $this->id;
        $message->save();
        return true;
    }
}
