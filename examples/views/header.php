<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Unnits/BankIdClient</title>

    <style>
        body {
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-size: 14px;
            line-height: 165%;
            box-sizing: border-box;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
        }

        .login-button {
            background: black;
            border-radius: 8px;
            color: white;
            padding: 9px 12px;
            text-decoration: none;
            display: inline-flex;
        }

        .login-button span {
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }

        .login-button-logo {
            width: 70px;
            padding-right: 10px;
            margin-right: 10px;
            border-right: 1px solid white;
        }

        .logout-button {
            border: 0;
            background: none;
            padding: 0;
            text-decoration: underline;
            color: blue;
            cursor: pointer;
        }

        pre {
            background: #eee;
            padding: 20px;
            overflow: scroll;
        }
    </style>
</head>

<body><div class="container">

    <h1>Bank iD PHP client example</h1>
