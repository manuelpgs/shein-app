<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shein App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        <nav>
            <router-link to="/orders">Orders</router-link>
            <router-link to="/clients">Clients</router-link>
            <router-link to="/2fa">2FA</router-link>
        </nav>        
        <router-view></router-view>
    </div>
    @vite('resources/js/app.js')
</body>
</html>