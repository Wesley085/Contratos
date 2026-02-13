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
    
    {{-- Mantendo o Vite padrão do Laravel 12 --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    {{-- Scripts utilitários --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/imask"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    {{-- ESTILOS ORIGINAIS MANTIDOS --}}
    <style>
        :root {
            --gradient-start: #115e59;
            --gradient-end: #0f292b;
            --primary: #2DC197;
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
            background: linear-gradient(170deg, var(--gradient-start) 0%, var(--gradient-end) 90%);
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
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        /* --- NAV ITEMS --- */
        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.9rem 1.2rem;
            border-radius: var(--border-radius);
            margin-bottom: 0.35rem;
            transition: var(--transition);
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .nav-item:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(3px);
            color: white;
        }

        .nav-item.active {
            background: white !important;
            color: #0d3532 !important;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            font-weight: 700;
        }

        .nav-icon {
            width: 1.35rem;
            height: 1.35rem;
            margin-right: 0.85rem;
            text-align: center;
            filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1));
        }

        .nav-item.active .nav-icon { color: var(--gradient-start); }

        .nav-section-title {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0 1.2rem;
            margin-top: 2rem;
            margin-bottom: 0.75rem;
            color: rgba(255, 255, 255, 0.5);
        }

        /* --- SUBMENU (DROPDOWN) --- */
        .submenu {
            padding: 0.5rem 0.5rem 0.5rem 0;
            overflow: hidden;
            background: rgba(0, 0, 0, 0.25);
            border-bottom-left-radius: var(--border-radius);
            border-bottom-right-radius: var(--border-radius);
            margin-bottom: 0.8rem;
            position: relative;
        }

        .submenu::before {
            content: '';
            position: absolute;
            left: 1.9rem;
            top: 0;
            bottom: 15px;
            width: 2px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            z-index: 1;
        }

        .submenu .nav-item {
            font-size: 0.9rem;
            margin-left: 1.9rem;
            margin-right: 0.5rem;
            margin-bottom: 0.2rem;
            padding: 0.6rem 1rem;
            width: auto;
            line-height: 1.2;
        }

        .toggle-submenu {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.9rem 1.2rem;
            border-radius: var(--border-radius);
            margin-bottom: 0.35rem;
            transition: all 0.2s ease-in-out;
            color: rgba(255, 255, 255, 0.85);
            cursor: pointer;
            font-weight: 500;
            position: relative;
            z-index: 2;
        }

        .toggle-submenu:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .toggle-submenu.rotate {
            background: rgba(0, 0, 0, 0.25);
            color: white;
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            margin-bottom: 0;
            box-shadow: inset 0 -1px 0 rgba(255,255,255,0.05);
        }

        .toggle-submenu.rotate i:last-child { transform: rotate(180deg); }

        /* --- FOOTER & CONTENT --- */
        .sidebar-footer { padding-top: 2rem; }

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
            background: rgba(255,255,255,0.95);
            color: #0f292b;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .btn-logout:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); }

        .btn-profile {
            background: transparent;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .btn-profile:hover { background: rgba(255, 255, 255, 0.1); }

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

        .welcome-banner {
            background: linear-gradient(120deg, var(--gradient-start) 0%, var(--primary) 100%);
            color: white;
            border-radius: var(--border-radius-lg);
            padding: 2.5rem;
            margin-bottom: 2.5rem;
            box-shadow: var(--shadow-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
        }

        .welcome-text h2 { font-size: 1.8rem; margin-bottom: 0.5rem; font-weight: 700; }
        .welcome-text p { opacity: 0.95; max-width: 600px; color: #f0fdf4; }
        .welcome-icon { font-size: 3.5rem; opacity: 0.8; }

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

        .fade-in { animation: fadeIn 0.5s ease-in-out; }
        .slide-in { animation: slideIn 0.4s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideIn { from { transform: translateX(-20px); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>
</head>

<body x-data="{
    sidebarOpen: false,
    openSubmenuExample: false // Adicionei essa variável para o exemplo de submenu
}">

    <div class="app-container">
        <aside class="sidebar" id="sidebar" :class="{ 'active': sidebarOpen }">
            <div>
                <div class="sidebar-logo">
                    {{-- Rota genérica para dashboard --}}
                    <a href="{{ route('dashboard') }}">
                        <img src="{{ asset('logo/logo_gestcloud.png') }}"
                             onerror="this.src='https://placehold.co/180x70?text=LOGO&bg=115e59&color=ffffff'; this.onerror=null;"
                             alt="Logo">
                    </a>
                </div>

                <nav>
                    {{-- DASHBOARD (Padrão Breeze) --}}
                    <a href="{{ route('dashboard') }}"
                        class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>

                    <div class="nav-section-title">Menu Principal</div>

                    {{-- EXEMPLO: Link Simples --}}
                    <a href="#" class="nav-item">
                        <i class="fas fa-box nav-icon"></i>
                        <span>Prefeituras</span>
                    </a>

                    {{-- EXEMPLO: Submenu (Utilizando as classes CSS que já existiam) --}}
                    <div class="toggle-submenu" 
                         @click="openSubmenuExample = !openSubmenuExample"
                         :class="{ 'rotate': openSubmenuExample }">
                        <div class="flex items-center">
                            <i class="fas fa-layer-group nav-icon"></i>
                            <span>Emissão de Ordens</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200"></i>
                    </div>

                    {{-- Container do Submenu --}}
                    <div class="submenu" x-show="openSubmenuExample" x-collapse>
                        <a href="#" class="nav-item">
                            <i class="fas fa-minus text-[10px]"></i>
                            <span>Opção A</span>
                        </a>
                        <a href="#" class="nav-item">
                            <i class="fas fa-minus text-[10px]"></i>
                            <span>Opção B</span>
                        </a>
                    </div>
                    
                    {{-- 
                        ÁREA COMENTADA: Lógica antiga. 
                        Descomente e ajuste as rotas/permissões conforme for criando no Laravel 12.
                    --}}
                    
                    {{-- 
                    @if(auth()->user()->can('modulo.minha_prefeitura'))
                        <div class="nav-section-title">Administração</div>
                        <a href="#" class="nav-item">
                            <i class="fas fa-building nav-icon"></i> <span>Prefeituras</span>
                        </a>
                    @endif 
                    --}}

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
                        {{-- Usando fallback se a section não for definida na view filha --}}
                        <h2>@yield('header', 'Painel de Controle')</h2> 
                        <p>Bem-vindo ao sistema.</p>
                    </div>
                    <div class="welcome-icon">
                        <i class="fas fa-building-circle-check"></i>
                    </div>
                </div>

                {{-- O Breeze usa $slot por padrão, mas mantive @yield para compatibilidade com seu código antigo --}}
                @yield('content')
                
                {{-- Caso queira usar componentes do blade (Breeze padrão), descomente abaixo: --}}
                {{-- {{ $slot }} --}}
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
        // Fecha sidebar ao clicar fora (Mobile)
        document.addEventListener('click', (e) => {
            const sidebar = document.getElementById('sidebar');
            const menuButton = document.getElementById('menu-button');
            if (window.innerWidth < 1024 && sidebar && !sidebar.contains(e.target) && !menuButton.contains(e.target)) {
                // Acessa o escopo do Alpine
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