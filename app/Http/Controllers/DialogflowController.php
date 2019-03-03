<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Google\Cloud\Dialogflow\V2\QueryParameters;
use Session;

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
        
        $queryParams = new QueryParameters();

        $queryParams->setContexts(Session::get('dialogflow_contexts') ?? []);
        
        $response = $client->detectIntent($session, $queryInput, [$queryParams]);

        Session::put('dialogflow_contexts', $response->getQueryResult()->getOutputContexts());
        
        return $response->getQueryResult()->getFulfillmentText();
    }
}
