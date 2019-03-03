<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Codeoysters</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">

        
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="title m-b-md">
                    Codeoysters
                </div>
                <div id="app">
                    <chatbot-component api-key="{{ config('services.google-cloud.key') }}"></chatbot-component>
                </div>
            </div>
        </div>
        <script src="/js/app.js"></script>
    </body>
</html>
