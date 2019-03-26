<?php

use Illuminate\Http\Request;

Route::get('/', function () {
    $query = http_build_query([
        'client_id' => 3, // Replace with Client ID
        'redirect_uri' => 'http://127.0.0.1:8001/callback',
        'response_type' => 'code',
        'scope' => ''
    ]);

    return redirect('http://127.0.0.1:8000/oauth/authorize?'.$query);
});

Route::get('/callback', function (Request $request) {
    $response = (new GuzzleHttp\Client)->post('http://127.0.0.1:8000/oauth/token', [
        'form_params' => [
            'grant_type' => 'authorization_code',
            'client_id' => 3, // Replace with Client ID
            'client_secret' => 'lziu2jbVO2D6sncz7viuw4ur9qKx9muBgF7G5Qs3', // Replace with client secret
            'redirect_uri' => 'http://127.0.0.1:8001/callback',
            'code' => $request->code,
        ]
    ]);

    session()->put('token', json_decode((string) $response->getBody(), true));

    return redirect('/todos');
});

Route::get('/todos', function () {
    $response = (new GuzzleHttp\Client)->get('http://127.0.0.1:8000/api/todos', [
        'headers' => [
            'Authorization' => 'Bearer '.session()->get('token.access_token')
        ]
    ]);

    return json_decode((string) $response->getBody(), true);
});