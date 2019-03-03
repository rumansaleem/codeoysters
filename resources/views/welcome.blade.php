<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Codeoysters</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
        <link rel="stylesheet" href="/css/app.css">
        
    </head>
    <body class="font-sans">
        <div class="flex justify-center relative min-h-screen">
            <div class="content">
                <h2 class="text-3xl text-center my-4">
                    Codeoysters
                </h2>
                <div id="app">
                    <chatbot-component api-key="{{ config('services.google-cloud.key') }}"></chatbot-component>
                </div>
            </div>
        </div>
        <script src="/js/app.js"></script>
    </body>
</html>
