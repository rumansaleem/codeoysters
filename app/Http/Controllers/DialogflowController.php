<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;

class DialogflowController extends Controller
{
    public function index()
    {
        $client = new SessionsClient([
            'credentials' => base_path('prKey.json')
        ]);

        $session = $client->sessionName("farmer-582ff", uniqid());

        $textInput = new TextInput();
        $textInput->setText(request('text'));
        $textInput->setLanguageCode('en-US');

        $queryInput = new QueryInput();
        $queryInput->setText($textInput);

        $response = $client->detectIntent($session, $queryInput);

        return $response->getQueryResult()->getFulfillmentText();
    }
}
