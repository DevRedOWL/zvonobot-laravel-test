@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Добро пожаловать</div>

                <div class="panel-body">

                    <table >
                        <tr style="display: grid;">
                            <td>POST /getDialogs - получить диалоги со всеми пользовтелями с сортировкой по последнему сообщению</td>
                            <td>POST /getMessages - получить все сообщения от заданного пользователя с сортировкой по убыванию времени отправки</td>
                            <td>POST /sendMessage - Отправить сообщение</td>
                            <td>Авторизация реализована встроенными в фреймворк средствами, изначально планировал прикрутить Passport, но в этой версии слишком больно (https://laravel.com/docs/5.2/passport)</td>
                        </tr>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection