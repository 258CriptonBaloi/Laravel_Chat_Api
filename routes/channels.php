<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Aqui você pode registrar todos os canais de broadcast que sua aplicação
| suporta. As callbacks de autorização dos canais são usadas para verificar
| se um usuário autenticado pode ouvir o canal.
|
*/

Broadcast::channel('chat', function ($user) {
    return Auth::check();
});


