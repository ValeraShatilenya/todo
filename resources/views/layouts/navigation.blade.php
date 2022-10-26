<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between md:h-16">
            <div class="flex">
                <div class="space-x-8 sm:-my-px sm:flex">
                    <x-nav-link :href="route('main')" :active="request()->routeIs('main')">
                        {{ __('Главная') }}
                    </x-nav-link>
                    <x-nav-link :href="route('group')" :active="request()->routeIs('group')">
                        {{ __('Группы') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="flex items-center tracking-widest font-medium">
                TODO
            </div>

            <div class="sm:flex sm:items-center sm:ml-6">
                @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Выйти') }}
                    </x-nav-link>
                </form>
                @else
                <!-- <form>
                    <x-nav-link :href="route('login')">
                        {{ __('Войти') }}
                    </x-nav-link>
                </form> -->
                @endauth
            </div>

        </div>
    </div>
</nav>
