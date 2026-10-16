<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ateliê</title>
    <!-- Fonte Inter Obrigatória -->
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text x=%2250%25%22 y=%22.9em%22 font-size=%2290%22 text-anchor=%22middle%22 font-family=%22Arial,sans-serif%22 fill=%22%23abe2ff%22>&#x269B;</text></svg>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style> 
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#f7f2ff] text-[#02000f] min-h-screen flex flex-col justify-between" x-data="galleryApp()" x-init="">

    <!-- 1. Cabeçalho da Aplicação -->
    <header class="flex justify-between items-center px-6 py-4 border-b border-gray-200">
        <!-- 1.1 Seção Usuário -->
        <div class="flex items-center gap-3 w-1/3">
            <template x-if="user">
                <div class="flex items-center gap-3">
                    <img :src="user.profile_img ? '/storage/profile' + user.profile_img : 'storage/images/default-profile_img.png'" class="w-10 h-10 rounded-full border border-[#02000f] object-cover">
                    <span class="font-semibold" x-text="'@' + user.name"></span>
                    <button 
                        @click="openProfileModal = true" 
                        :disabled="user.role !== 'fotografo'"
                        class="bg-[#02000f] text-white px-3 py-1 text-sm font-semibold rounded disabled:opacity-50 disabled:cursor-not-allowed">
                        Ver Perfil
                    </button>
                </div>
            </template>
        </div>

        <!-- 1.2 Seção Empresa -->
        <div class="w-1/3 text-center">
            <h1 class="text-2xl font-bold tracking-tight text-[#02000f]"><span class="text-3xl text-violet-400 transition-transform duration-300 hover:rotate-12">
                    ⚛
                </span> Ateliê <span class="text-violet-400">Sonhador</span></h1>
        </div>

        <!-- 1.3 Seção de Login / Logout -->
        <div class="w-1/3 flex justify-end">
            <template x-if="!user">
                <button @click="showLoginModal = true; authMode = 'login';" class="rounded bg-[#02000f] text-white px-5 py-2 rounded font-semibold hover:bg-gray-800 transition">
                    Login
                </button>
            </template>
            <template x-if="user">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="bg-[#02000f] text-white px-5 py-2 rounded font-semibold hover:bg-red transition">
                        Sair
                    </button>
                </form>
            </template>
        </div>
    </header>

    <!-- Menu Lateral (Perfil do Fotógrafo) -->
    <div x-show="openProfileModal" class="fixed inset-y-0 left-0 w-80 bg-gray-100 shadow-2xl p-6 z-40 border-r border-gray-300">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold">Perfil do Fotógrafo</h2>
            <button @click="openProfileModal = false" class="text-[#02000f] font-bold text-xl">&times;</button>
        </div>
        <div class="space-y-4">
            <p class="text-lg"><span class="font-bold" x-text="stats.likes"></span> Likes</p>
            <p class="text-lg border-t border-b border-[#02000f] py-2"><span class="font-bold" x-text="stats.publications"></span> Publicações</p>
        </div>
    </div>

    <!-- 2. Corpo da Aplicação -->
    <main class="max-w-6xl w-full mx-auto py-8 px-4 flex-growl">

        <!-- Seção Exclusiva para Fotógrafos: Formulário de Nova Foto -->
        <template x-if="user && user.role === 'fotografo'">
            <div class="mb-8 p-6 bg-gray-50 border border-[#02000f] rounded-lg max-w-xl mx-auto shadow-sm">
                <h2 class="text-lg font-bold mb-4">Nova Publicação</h2>

                <form @submit.prevent="uploadPhoto()" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="block text-sm font-semibold mb-1">Título da Foto</label>
                        <input type="text" x-model="photoForm.title" placeholder="Ex: Nuvêns sobre o Mar" required class="w-full border border-[#02000f] rounded p-2 text-sm">
                    </div>

                    <div class="mb-3">
                        <label class="block text-sm font-semibold mb-1">Localização</label>
                        <input type="text" x-model="photoForm.location" placeholder="Ex: Xique Xique, BH" required class="w-full border border-[#02000f] rounded p-2 text-sm">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-1">Imagem</label>
                        <input type="file" @change="photoForm.image = $event.target.files[0]" accept="image/*" required class="w-full border border-[#02000f] rounded p-2 text-sm bg-white">
                    </div>

                    <button type="submit" class="w-full bg-[#02000f] text-white font-semibold py-2 rounded hover:bg-gray-800 transition">
                        Publicar Foto
                    </button>
                </form>
            </div>
        </template>

        <!-- 2.1 Campo de Busca -->
        
    <div class="relative mx-auto mb-8 w-full max-w-xl">
        <div class="relative flex items-center">
            <input
                type="text"
                x-model="searchQuery"
                @input.debounce.300ms="fetchPhotos()"
                placeholder="Pesquisar fotógrafo..."
                class="w-full rounded-lg border border-gray-300 bg-white
                    py-3 pl-4 pr-12 text-sm text-gray-800
                    placeholder:text-gray-400
                    transition duration-200
                    focus:border-[#02000f] focus:outline-none
                    focus:ring-2 focus:ring-[#02000f]/10"
            >

            <img
                src="/icons/lupa.svg"
                alt="Pesquisar"
                class="pointer-events-none absolute right-4
                    h-5 w-5 opacity-50"
            >
        </div>
    </div>


        <!-- Mensagem de Erro na Busca -->
        <template x-if="searchError">
            <p class="text-center text-red font-semibold mb-6" x-text="searchError"></p>
        </template>

        <!-- 2.2 atelie de Fotos (3 colunas por linha) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <template x-for="photo in photos" :key="photo.id">
                <div class="relative group border border-gray-300 rounded overflow-hidden shadow-sm hover:shadow-md transition">
                    <!-- Tooltip no Hover com Informações da Foto -->
                    <div class="absolute inset-0 bg-[#02000f]/75 opacity-0 group-hover:opacity-100 transition-opacity p-4 text-white flex flex-col justify-end pointer-events-none">
                        <p class="text-sm font-semibold" x-text="'Foto tirada por: ' + photo.photographer"></p>
                        <p class="text-sm" x-text="'Local: ' + photo.location"></p>
                    </div>

                    <!-- Imagem -->
                    <img :src="photo.image_url" class="w-full h-64 object-cover">

                    <!-- Informações e Curtidas abaixo da foto -->
                    <div class="p-4 flex justify-between items-center bg-[#fcf7f0] border-t border-gray-100">
                        <span class="font-semibold text-gray-800" x-text="photo.title"></span>
                        <div class="flex items-center gap-1.5">
                            <button @click="toggleLike(photo)" class="focus:outline-none">
                                <svg class="w-6 h-6 transition-colors" :class="photo.is_liked ? 'text-red fill-current' : 'text-[#02000f] fill-none'" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </button>
                            <span class="font-bold text-sm" x-text="photo.likes_count"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </main>

    <!-- Modal de Autenticação (Login / Registro) com Breeze -->
    <div 
        x-show="showLoginModal" 
        x-cloak 
        class="fixed inset-0 z-50 flex items-center justify-center bg-[#02000f]/60 backdrop-blur-sm"
    >
        <div class="bg-[#fcf7f0] rounded-lg shadow-xl w-full max-w-md p-6 relative border border-[#02000f]">
            
            <!-- Botão Fechar (X) -->
            <button 
                @click="showLoginModal = false" 
                class="absolute top-4 right-4 text-[#02000f] font-bold text-xl hover:opacity-70"
            >
                &times;
            </button>

            <!-- Abas do Modal -->
            <div class="flex border-b border-gray-300 mb-6">
                <button 
                    type="button"
                    @click="authMode = 'login'" 
                    :class="authMode === 'login' ? 'border-b-2 border-[#02000f] font-bold text-[#02000f]' : 'text-gray-500'" 
                    class="flex-1 py-2 text-center text-lg"
                >
                    Login
                </button>
                <button 
                    type="button"
                    @click="authMode = 'register'" 
                    :class="authMode === 'register' ? 'border-b-2 border-[#02000f] font-bold text-[#02000f]' : 'text-gray-500'" 
                    class="flex-1 py-2 text-center text-lg"
                >
                    Cadastre-se
                </button>
            </div>

            <!-- FORMULÁRIO DE LOGIN -->
            <form
    x-show="authMode === 'login'"
    method="POST"
    action="{{ route('login') }}"
    class="space-y-4"
>
    @csrf

    <h2 class="mb-5 text-xl font-bold text-[#02000f]">
        Entrar na conta
    </h2>

    <!-- E-mail -->
    <div>
        <label for="email" class="mb-1 block text-sm font-medium text-gray-700">
            E-mail
        </label>

        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="Digite seu e-mail"
            autocomplete="email"
            required
            autofocus
            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm outline-none transition focus:border-violet-500 focus:bg-white focus:ring-2 focus:ring-violet-500/10"
        />

        @if ($errors->has('email'))
            <span class="mt-1 block text-xs text-red-600">
                {{ $errors->first('email') }}
            </span>
        @endif
    </div>

    <!-- Senha -->
    <div>
        <label for="password" class="mb-1 block text-sm font-medium text-gray-700">
            Senha
        </label>

        <input
            id="password"
            type="password"
            name="password"
            placeholder="Digite sua senha"
            autocomplete="current-password"
            required
            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm outline-none transition focus:border-violet-500 focus:bg-white focus:ring-2 focus:ring-violet-500/10"
        />

        @if ($errors->has('password'))
            <span class="mt-1 block text-xs text-red-600">
                {{ $errors->first('password') }}
            </span>
        @endif
    </div>

    <!-- Botões -->
    <div class="flex justify-end gap-2 pt-2">
        <button
            type="button"
            @click="showLoginModal = false"
            class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-100"
        >
            Cancelar
        </button>

        <button
            type="submit"
            class="rounded-lg bg-[#02000f] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-violet-700 focus:outline-none focus:ring-2 focus:ring-violet-500 focus:ring-offset-2"
        >
            Entrar
        </button>
    </div>
</form>


            <!-- FORMULÁRIO DE REGISTRO -->
            <form
    x-show="authMode === 'register'"
    method="POST"
    action="{{ route('register') }}"
    enctype="multipart/form-data"
    class="space-y-3"
>
    @csrf

    <h2 class="mb-5 text-xl font-bold text-[#02000f]">
        Criar conta
    </h2>

    <!-- Nome -->
    <div>
        <label for="register_name" class="mb-1 block text-sm font-medium text-gray-700">
            Nome completo
        </label>
        <input
            type="text"
            name="name"
            id="register_name"
            value="{{ old('name') }}"
            placeholder="João Silva"
            required
            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm outline-none transition focus:border-violet-500 focus:bg-white focus:ring-2 focus:ring-violet-500/10"
        />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>

    <!-- Usuário -->
    <div>
        <label for="register_username" class="mb-1 block text-sm font-medium text-gray-700">
            Nome de usuário
        </label>
        <input
            type="text"
            name="username"
            id="register_username"
            value="{{ old('username') }}"
            placeholder="joaosilva"
            required
            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm outline-none transition focus:border-violet-500 focus:bg-white focus:ring-2 focus:ring-violet-500/10"
        />
        <x-input-error :messages="$errors->get('username')" class="mt-1" />
    </div>

    <!-- E-mail -->
    <div>
        <label for="register_email" class="mb-1 block text-sm font-medium text-gray-700">
            E-mail
        </label>
        <input
            type="email"
            name="email"
            id="register_email"
            value="{{ old('email') }}"
            placeholder="exemplo@email.com"
            required
            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm outline-none transition focus:border-violet-500 focus:bg-white focus:ring-2 focus:ring-violet-500/10"
        />
        <x-input-error :messages="$errors->get('email')" class="mt-1" />
    </div>

    <!-- Perfil -->
    <div>
        <label for="register_role" class="mb-1 block text-sm font-medium text-gray-700">
            Tipo de perfil
        </label>
        <select
            name="role"
            id="register_role"
            required
            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm outline-none transition focus:border-violet-500 focus:ring-2 focus:ring-violet-500/10"
        >
            <option value="usuario" @selected(old('role', 'usuario') === 'usuario')>Usuário comum</option>
            <option value="fotografo" @selected(old('role') === 'fotografo')>Fotógrafo</option>
        </select>
        <x-input-error :messages="$errors->get('role')" class="mt-1" />
    </div>

    <!-- Senha -->
    <div>
        <label for="register_password" class="mb-1 block text-sm font-medium text-gray-700">
            Senha
        </label>
        <input
            type="password"
            name="password"
            id="register_password"
            placeholder="Mínimo de 6 caracteres"
            minlength="6"
            required
            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm outline-none transition focus:border-violet-500 focus:bg-white focus:ring-2 focus:ring-violet-500/10"
        />
        <x-input-error :messages="$errors->get('password')" class="mt-1" />
    </div>

    <!-- Confirmar senha -->
    <div>
        <label for="register_password_confirmation" class="mb-1 block text-sm font-medium text-gray-700">
            Confirmar senha
        </label>
        <input
            type="password"
            name="password_confirmation"
            id="register_password_confirmation"
            placeholder="Repita sua senha"
            minlength="6"
            required
            class="w-full rounded-lg border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm outline-none transition focus:border-violet-500 focus:bg-white focus:ring-2 focus:ring-violet-500/10"
        />
    </div>

    <!-- Foto -->
    <div>
        <label for="profile_img" class="mb-1 block text-sm font-medium text-gray-700">
            Foto de perfil <span class="font-normal text-gray-400">(opcional)</span>
        </label>
        <input
            type="file"
            name="profile_img"
            id="profile_img"
            accept="image/png, image/jpeg, image/jpg, image/webp"
            class="block w-full text-sm text-gray-500 file:mr-3 file:rounded-lg file:border-0 file:bg-violet-100 file:px-3 file:py-2 file:font-medium file:text-violet-700 hover:file:bg-violet-200"
        />
        <x-input-error :messages="$errors->get('profile_img')" class="mt-1" />
    </div>

    <!-- Ações -->
    <div class="flex justify-end gap-2 pt-3">
        <button
            type="button"
            @click="showLoginModal = false"
            class="rounded-lg border border-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-600 transition hover:bg-gray-100"
        >
            Cancelar
        </button>

        <button
            type="submit"
            class="rounded-lg bg-[#02000f] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-violet-700 focus:ring-2 focus:ring-violet-500 focus:ring-offset-2"
        >
            Cadastrar
        </button>
    </div>
</form>


        </div>
    </div>

    <!-- 3. Rodapé da Aplicação -->
        <footer class="mt-16 border-t border-white/10 bg-[#02000f] px-6 py-4 text-white sm:px-10">
        <div class="mx-auto grid max-w-7xl grid-cols-1 items-center gap-6 sm:grid-cols-3">

            <a href="/" class="flex items-center justify-center gap-2 sm:justify-self-start"
            aria-label="Ateliê Sonhador - Início">
                <span class="text-3xl text-violet-400 transition-transform duration-300 hover:rotate-12">
                    ⚛
                </span>
                <span class="text-sm font-semibold tracking-wide">
                    Ateliê <span class="text-violet-400">Sonhador</span>
                </span>
            </a>

            <nav aria-label="Redes sociais"
                class="flex items-center justify-center gap-3">
                <a href="#" aria-label="TikTok"
                class="group flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 transition-all duration-300 hover:-translate-y-1 hover:border-violet-400/50 hover:bg-violet-500/10">
                    <img src="/icons/tiktok.svg" alt=""
                        class="h-5 w-5 invert transition-opacity group-hover:opacity-70">
                </a>

                <a href="#" aria-label="YouTube"
                class="group flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 transition-all duration-300 hover:-translate-y-1 hover:border-violet-400/50 hover:bg-violet-500/10">
                    <img src="/icons/youtube.svg" alt=""
                        class="h-5 w-5 invert transition-opacity group-hover:opacity-70">
                </a>

                <a href="#" aria-label="Instagram"
                class="group flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 transition-all duration-300 hover:-translate-y-1 hover:border-violet-400/50 hover:bg-violet-500/10">
                    <img src="/icons/instagram.svg" alt=""
                        class="h-5 w-5 invert transition-opacity group-hover:opacity-70">
                </a>

                <a href="#" aria-label="LinkedIn"
                class="group flex h-10 w-10 items-center justify-center rounded-full border border-white/10 bg-white/5 transition-all duration-300 hover:-translate-y-1 hover:border-violet-400/50 hover:bg-violet-500/10">
                    <img src="/icons/linkedIn.svg" alt=""
                        class="h-5 w-5 invert transition-opacity group-hover:opacity-70">
                </a>
            </nav>

            <div class="text-center text-xs leading-relaxed text-white/50 sm:justify-self-end sm:text-right">
                <p>
                    &copy; 2026 <span class="text-white/80">Ateliê Sonhador</span>
                </p>
            </div>

        </div>
    </footer>

    <!-- Controle de Estado Reativo com Alpine.js -->
    <script>
        function galleryApp() {
            return {
                photos: [],
                searchQuery: '',
                searchError: '',
                user: @json(auth()->user()),
                
                showLoginModal: {{ $errors->any() ? 'true' : 'false' }},
                authMode: '{{ old("name") || old("username") ? "register" : "login" }}',
                openProfileModal: false,
                stats: { likes: 0, publications: 0 },

                photoForm: {
                    title: '',
                    location: '',
                    image: null
                },

                async uploadPhoto() {
                    if (!this.photoForm.image) return;

                    let formData = new FormData();
                    formData.append('title', this.photoForm.title);
                    formData.append('location', this.photoForm.location);
                    formData.append('image', this.photoForm.image);

                    let res = await fetch('/photos', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    if (res.ok) {
                        this.photoForm = { title: '', location: '', image: null };
                        this.fetchPhotos();
                    } else {
                        let data = await res.json();
                        alert(data.message || 'Erro ao publicar foto.');
                    }
                }
            }
        }
    </script>
</body>
</html>