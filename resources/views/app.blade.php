<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" prefix="og: http://ogp.me/ns#">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#008000" >
         <link rel="icon" href="{{asset('storage/files/green-logo.jpg')}}">
        <title>Ethusiastgh.com</title>
        <!-- Fonts -->
        <link
        href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200"
        rel="stylesheet"
      />
     <link
      href="https://use.fontawesome.com/releases/v5.7.1/css/all.css"
     rel="stylesheet">    
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link rel="stylesheet" href="{{asset('css/w3.css')}}">
    
    </head>
    <body>
    <div id="app" assets="{{asset('/storage/files')}}">
    </div>
    <script src="{{asset('js/app.js')}}"></script>
    
    </body>
</html>
