<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Gestão</title>
    
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Scripts utilitários --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/imask"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        :root {            
            --gradient-start: #234c8c; 
            --gradient-end: #163057;   
            
            --primary: #184ea4;        
            --primary-hover: #133b7a;  
            
            --light-blue: #84a4cd;     
            
            --background: #f8fafc;     
            
            --sidebar-width: 280px;
            --border-radius: 12px;
            --border-radius-lg: 16px;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        [x-cloak] { display: none !important; }

        body {
            font-family: 'Figtree', sans-serif;
            background-color: var(--background);
            color: #1e2a32;
            overflow-x: hidden;
            line-height: 1.6;
        }

        .app-container {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.8rem 1.2rem;
            position: fixed;
            height: 100vh;
            z-index: 100;
            box-shadow: var(--shadow-lg);
            transition: var(--transition);
            overflow-y: auto;
            border-right: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar::-webkit-scrollbar { display: none; }
        .sidebar { -ms-overflow-style: none; scrollbar-width: none; }

        .sidebar-logo {
            padding: 0.5rem;
            margin-bottom: 2.5rem;
            display: flex;
            justify-content: center;
        }

        .sidebar-logo img {
            max-width: 170px;
            height: auto;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
            border-radius: 15px;
        }

        /* --- NAV ITEMS --- */
        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.9rem 1.2rem;
            border-radius: var(--border-radius);
            margin-bottom: 0.35rem;
            transition: var(--transition);
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-weight: 500;
            position: relative;
            overflow: hidden;
            border: 1px solid transparent;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-color: rgba(255, 255, 255, 0.1);
        }

        .nav-item.active {
            background: rgba(255, 255, 255, 0.15) !important;
            color: white !important;
            font-weight: 700;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 10%;
            bottom: 10%;
            width: 4px;
            background-color: var(--light-blue); 
            border-radius: 0 4px 4px 0;
        }

        .nav-icon {
            width: 1.35rem;
            height: 1.35rem;
            margin-right: 0.85rem;
            text-align: center;
            color: #fff;
            transition: var(--transition);
        }

        .nav-item:hover .nav-icon,
        .nav-item.active .nav-icon { 
            color: var(--light-blue); 
            filter: drop-shadow(0 0 5px rgba(132, 164, 205, 0.4));
        }

        .nav-section-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0 1.2rem;
            margin-top: 2rem;
            margin-bottom: 0.75rem;
            color: rgba(255, 255, 255, 0.4);
        }

        /* --- FOOTER & CONTENT --- */
        .sidebar-footer { padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.05); }

        .sidebar-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.75rem;
            border-radius: var(--border-radius);
            margin-bottom: 0.75rem;
            font-weight: 600;
            transition: var(--transition);
            cursor: pointer;
            gap: 0.5rem;
        }

        .btn-logout {
            background: #fff; 
            color: #133b7a; 
            border: 1px solid rgba(255, 173, 173, 0.1);
        }

        .btn-profile {
            background: transparent;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .btn-profile:hover { background: rgba(255, 255, 255, 0.1); border-color: var(--light-blue); }

        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: var(--transition);
            display: flex;
            flex-direction: column;
        }

        .page-content {
            padding: 2.5rem;
            flex: 1;
            max-width: 1400px;
            width: 100%;
            margin: 0 auto;
        }

        /* Banner de Boas-vindas */
        .welcome-banner {
            background: linear-gradient(135deg, var(--gradient-start) 0%, var(--primary) 100%);
            color: white;
            border-radius: var(--border-radius-lg);
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            box-shadow: var(--shadow-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .welcome-text h2 { font-size: 1.8rem; margin-bottom: 0.5rem; font-weight: 700; }
        .welcome-text p { opacity: 0.9; max-width: 600px; color: #eef2ff; }
        
        .welcome-icon { 
            font-size: 3.5rem; 
            color: white; 
            opacity: 0.15; 
        }

        .mobile-menu-btn {
            display: none;
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            width: 40px; height: 40px;
            border-radius: 50%;
            align-items: center; justify-content: center;
            transition: var(--transition);
            margin-right: 15px;
        }

        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .mobile-menu-btn { display: flex; position: absolute; top: 1.5rem; left: 1.5rem; }
            .welcome-banner { padding-top: 5rem; text-align: center; flex-direction: column; align-items: center; }
            .welcome-icon { display: none; }
        }

        .bg-primary { background-color: var(--primary) !important; }
        .text-primary { color: var(--primary) !important; }
        .hover-primary:hover { background-color: var(--primary-hover) !important; }

        .fade-in { animation: fadeIn 0.5s ease-in-out; }
        .slide-in { animation: slideIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideIn { from { transform: translateX(-20px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>
</head>

<body x-data="{ sidebarOpen: false }">

    <div class="app-container">
        <aside class="sidebar" id="sidebar" :class="{ 'active': sidebarOpen }">
            <div>
                <div class="sidebar-logo">
                    {{-- Logo --}}
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('logo/Screenshot_1.png') }}"
                             onerror="this.src='https://placehold.co/180x70?text=LOGO&bg=115e59&color=ffffff'; this.onerror=null;"
                             alt="Logo">
                    </a>
                </div>

                {{-- MENU PRINCIPAL --}}
                <nav>
                    <a href="{{ route('dashboard') }}"
                        class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>

                    <div class="nav-section-title">Gestão</div>

                    <a href="{{ route('empresas.index') }}"
                        class="nav-item {{ request()->routeIs('empresas.*') ? 'active' : '' }}">
                        <i class="fas fa-building nav-icon"></i>
                        <span>Minha Empresa</span>
                    </a>

                    <a href="{{ route('prefeituras.index') }}"
                        class="nav-item {{ request()->routeIs('prefeituras.*', 'contratos.*', 'lotes.*', 'itens.*') ? 'active' : '' }}">
                        <i class="fas fa-landmark nav-icon"></i>
                        <span>Prefeituras / Contratos</span>
                    </a>

                    <a href="{{ route('entregas.index') }}"
                        class="nav-item {{ request()->routeIs('entregas.*') ? 'active' : '' }}">
                        <i class="fas fa-truck-loading nav-icon"></i>
                        <span>Recibos de Entrega</span>
                    </a>
                </nav>
            </div>

            <div class="sidebar-footer">
                <a href="{{ route('profile.edit') }}" class="sidebar-btn btn-profile">
                    <i class="fas fa-user-circle"></i>
                    <span>Meu Perfil</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-btn btn-logout">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Sair</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        <div class="main-content">
            <main class="page-content fade-in">
                <div class="welcome-banner slide-in">
                    <button id="menu-button" class="mobile-menu-btn" @click="sidebarOpen = !sidebarOpen" aria-label="Abrir menu">
                        <i class="fas fa-bars"></i>
                    </button>

                    <div class="welcome-text">
                        {{-- Título Dinâmico --}}
                        <h2>@yield('page-title', 'Painel de Controle')</h2> 
                        <p>@yield('page-subtitle', 'Bem-vindo ao sistema de gestão.')</p>
                    </div>
                    <div class="welcome-icon">
                        <i class="fas fa-building-circle-check"></i>
                    </div>
                </div>

                @yield('content')
            </main>
        </div>

        {{-- OVERLAY MOBILE --}}
        <div x-show="sidebarOpen"
             @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/50 z-40 lg:hidden"
             style="display: none;"
             x-transition.opacity>
        </div>
    </div>

    <script>
        document.addEventListener('click', (e) => {
            const sidebar = document.getElementById('sidebar');
            const menuButton = document.getElementById('menu-button');
            if (window.innerWidth < 1024 && sidebar && !sidebar.contains(e.target) && !menuButton.contains(e.target)) {
                const sidebarElement = document.querySelector('[x-data]');
                if (sidebarElement && sidebarElement.__x) {
                    sidebarElement.__x.$data.sidebarOpen = false;
                }
            }
        });
    </script>
    @stack('scripts')
</body>
</html>