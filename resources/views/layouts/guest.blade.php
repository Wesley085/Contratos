<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GestCloud - Login</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background:
                linear-gradient(135deg, #f8fafc 0%, #f1f5f9 25%, #e2e8f0 100%),
                repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(5, 35, 35, 0.03) 10px, rgba(5, 35, 35, 0.03) 20px),
                repeating-linear-gradient(-45deg, transparent, transparent 10px, rgba(45, 193, 151, 0.03) 10px, rgba(45, 193, 151, 0.03) 20px);

            background-image:
                radial-gradient(circle at 10% 20%, rgba(45, 193, 151, 0.08) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(5, 35, 35, 0.08) 0%, transparent 20%),
                radial-gradient(circle at 50% 50%, rgba(5, 50, 42, 0.05) 0%, transparent 30%);

            background-attachment: fixed;
            background-size: cover;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                linear-gradient(45deg, transparent 49.5%, rgba(5, 35, 35, 0.02) 49.5%, rgba(5, 35, 35, 0.02) 50.5%, transparent 50.5%),
                linear-gradient(-45deg, transparent 49.5%, rgba(45, 193, 151, 0.02) 49.5%, rgba(45, 193, 151, 0.02) 50.5%, transparent 50.5%);
            background-size: 60px 60px;
            pointer-events: none;
            z-index: -1;
        }

        body::after {
            content: '';
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80vw;
            height: 80vh;
            background: radial-gradient(circle, rgba(45, 193, 151, 0.04) 0%, transparent 70%);
            filter: blur(40px);
            pointer-events: none;
            z-index: -1;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) rotate(0deg); }
            33% { transform: translate(10px, -10px) rotate(1deg); }
            66% { transform: translate(-5px, 5px) rotate(-1deg); }
        }

        .bg-float-element {
            position: fixed;
            border: 1px solid rgba(5, 35, 35, 0.1);
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(5px);
            animation: float 20s ease-in-out infinite;
            z-index: -1;
        }

        .shadow-xl {
            box-shadow:
                0 20px 60px rgba(5, 35, 35, 0.15),
                0 0 0 1px rgba(255, 255, 255, 0.9);
        }
    </style>
</head>

<body class="min-h-[100dvh] flex items-center justify-center p-4 sm:p-8">

    <div class="bg-float-element w-[10rem] h-[10rem] top-[25%] left-[2.5rem] opacity-20" style="animation-delay: -5s;"></div>
    <div class="bg-float-element w-[16rem] h-[16rem] bottom-[25%] right-[2.5rem] opacity-10" style="animation-delay: -10s;"></div>
    <div class="bg-float-element w-[8rem] h-[8rem] top-[2.5rem] right-[25%] opacity-15" style="animation-delay: -15s;"></div>
    <div class="bg-float-element w-[12rem] h-[12rem] bottom-[2.5rem] left-[25%] opacity-10" style="animation-delay: -7s;"></div>

    <div class="w-full max-w-[64rem] min-h-[35rem] bg-white shadow-2xl grid grid-cols-1 lg:grid-cols-2 rounded-[1.5rem] overflow-hidden relative">

        <div class="hidden lg:flex flex-col items-center justify-center relative p-[2.5rem] bg-[#052323]"
            style="background-image: url('{{ asset('logo/Pattern-Login-GestCloud.png') }}');
                    background-size: cover;
                    background-position: center;
                    background-repeat: no-repeat;">

            <img src="{{ url('logo/logo_gestcloud_login.png') }}"
                class="h-[6rem] mb-[2rem] select-none drop-shadow-lg"
                alt="GestCloud">

            <div class="bg-[#003333] text-white text-[0.65rem] leading-relaxed px-[1rem] py-[0.75rem] rounded-md text-center w-full max-w-[16rem] shadow-md border border-[#05322A]/30">
                DESENVOLVEMOS SISTEMAS QUE ORGANIZAM,
                MODERNIZAM E TORNAM A GESTÃO PÚBLICA
                MAIS EFICIENTE.
            </div>
        </div>

        <div class="flex flex-col items-center justify-center relative w-full h-full p-6 bg-[rgb(45,193,151)]">
            <div class="block lg:hidden mt-6 mb-[1.5rem] bg-[#052323] px-10 py-8 rounded-2xl shadow-lg border border-[#05322A]/30">
                <img src="{{ url('logo/logo_gestcloud.png') }}"
                    class="h-[1.5rem] sm:h-[2.5rem] select-none filter drop-shadow-md"
                    alt="GestCloud">
            </div>

            <div class="flex flex-col items-center mb-3">

                <span class="bg-[#052323] text-white text-[0.65rem] sm:text-xl font-bold tracking-widest uppercase px-4 py-1.5 rounded-full mb-3 shadow-md border border-[#2DC197]/30">
                    Distribuidora
                </span>

                <h2 class="text-lg sm:text-xl text-[#05322A] tracking-wider uppercase" style="font-weight: 800">
                    Entrar
                </h2>
            </div>

            <div class="w-full max-w-[22rem] z-10">
                {{ $slot }}
            </div>
        </div>

    </div>

        <a href="https://wa.me/5589981007240"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="Falar no WhatsApp"
    class="group fixed bottom-6 right-6 z-50 flex items-center justify-center w-16 h-16 bg-[#25D366] rounded-full shadow-xl hover:scale-110 transition-all duration-300 animate-pulse">

        <!-- Ícone oficial WhatsApp -->
        <svg xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 32 32"
            class="w-9 h-9 fill-white">

            <path d="M19.11 17.57c-.27-.13-1.58-.78-1.83-.87-.25-.09-.43-.13-.61.13s-.7.87-.86 1.05c-.16.18-.31.2-.58.07-.27-.13-1.13-.42-2.16-1.35-.8-.71-1.35-1.58-1.5-1.85-.16-.27-.02-.42.11-.55.12-.12.27-.31.4-.47.13-.16.18-.27.27-.45.09-.18.04-.34-.02-.47-.07-.13-.61-1.47-.83-2.02-.21-.51-.43-.44-.61-.45-.16-.01-.34-.01-.52-.01-.18 0-.47.07-.71.34-.25.27-.95.93-.95 2.27s.98 2.63 1.11 2.82c.13.18 1.92 2.93 4.65 4.11.65.28 1.16.45 1.56.58.66.21 1.27.18 1.75.11.53-.08 1.58-.65 1.8-1.28.22-.63.22-1.17.15-1.28-.07-.11-.25-.18-.52-.31z"/>

            <path d="M16 3C8.82 3 3 8.82 3 16c0 2.82.92 5.43 2.47 7.56L4 29l5.6-1.45A12.9 12.9 0 0016 29c7.18 0 13-5.82 13-13S23.18 3 16 3zm0 23.5c-2.22 0-4.29-.64-6.05-1.74l-.43-.26-3.32.86.88-3.23-.28-.44A10.43 10.43 0 015.5 16C5.5 10.2 10.2 5.5 16 5.5S26.5 10.2 26.5 16 21.8 26.5 16 26.5z"/>

        </svg>

        <!-- Tooltip -->
        <span class="absolute right-20 bg-white text-gray-800 text-sm font-semibold px-3 py-1.5 rounded-lg shadow-md whitespace-nowrap transition-all duration-300 group-hover:scale-105">
            Dificuldade no acesso? Fale conosco.
        </span>

    </a>
</body>
</html>
