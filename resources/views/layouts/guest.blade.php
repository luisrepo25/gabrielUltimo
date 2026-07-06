<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Ferretería Guisella') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Estilos Premium -->
        <style>
            :root {
                --primary: #0f766e;
                --accent: #14b8a6;
                --bg-grad: linear-gradient(135deg, #f0fdfa 0%, #e0f2f1 100%);
            }

            body {
                font-family: 'Plus Jakarta Sans', sans-serif;
                background: var(--bg-grad);
                min-height: 100vh;
                margin: 0;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .auth-card {
                background: rgba(255, 255, 255, 0.8);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.5);
                border-radius: 30px;
                box-shadow: 0 25px 50px -12px rgba(15, 118, 110, 0.1);
                padding: 40px;
                width: 100%;
                max-width: 450px;
                animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
            }

            .auth-logo {
                text-align: center;
                margin-bottom: 30px;
            }

            .auth-logo svg {
                width: 60px;
                height: 60px;
                color: var(--primary);
                filter: drop-shadow(0 4px 6px rgba(15, 118, 110, 0.2));
            }

            .auth-title {
                text-align: center;
                font-size: 1.8rem;
                font-weight: 800;
                color: #0f172a;
                margin-bottom: 10px;
            }

            .auth-subtitle {
                text-align: center;
                color: #64748b;
                font-size: 0.95rem;
                margin-bottom: 30px;
            }

            /* Estilos para inputs */
            input, select {
                width: 100%;
                padding: 12px 18px;
                border-radius: 14px;
                border: 1px solid #e2e8f0;
                background: white;
                font-family: inherit;
                font-size: 0.95rem;
                transition: all 0.3s ease;
                box-sizing: border-box;
            }

            input:focus {
                outline: none;
                border-color: var(--accent);
                box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.1);
                transform: translateY(-1px);
            }

            label {
                display: block;
                font-size: 0.8rem;
                font-weight: 700;
                color: #475569;
                text-transform: uppercase;
                margin-bottom: 8px;
                margin-left: 5px;
            }

            .btn-auth {
                width: 100%;
                background: linear-gradient(135deg, var(--primary), var(--accent));
                color: white;
                padding: 14px;
                border-radius: 14px;
                border: none;
                font-weight: 700;
                font-size: 1rem;
                cursor: pointer;
                transition: all 0.3s ease;
                box-shadow: 0 10px 15px -3px rgba(15, 118, 110, 0.3);
            }

            .btn-auth:hover {
                transform: translateY(-2px);
                box-shadow: 0 20px 25px -5px rgba(15, 118, 110, 0.4);
            }

            @keyframes slideUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
        </style>
    </head>
    <body>
        <div class="auth-card">
            <div class="auth-logo">
                <a href="/">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </a>
            </div>
            {{ $slot }}
        </div>
    </body>
</html>
