<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Players') }}
        </h2>
    </x-slot>

    <div x-data="{ open: false }" class="py-12 flex flex-col items-center gap-10">
        <div class="flex gap-8">
            <button @click="open = ! open" @click.stop
                class="bg-green-500 w-[42px] h-[42px] p-[8px] rounded-md hover:scale-105">
                <x-icon name="user-plus" />
            </button>
            <div class="relative">
                <x-icon name="magnifying-glass" class="absolute left-[5px] top-[50%] translate-y-[-50%]" />
                <x-text-input class="pl-[30px] h-full" placeholder="Search Player"></x-text-input>
            </div>
        </div>
        <div class="relative w-[90%] rounded-2xl h-[50vh]">

            <div class="p-6 overflow-y-auto grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 max-h-full transition ease-in duration-150"
                :class="{ 'blur-sm': open }">
                @foreach ($users as $user)
                    <div
                        class="flex p-2 gap-5 items-center rounded-md border-solid border-[3px] border-slate-600/60 relative h-fit">
                        <x-icon name="user" />
                        <p class="truncate pr-[30px] max-w-full">{{ $user->name }}</p>
                        <a href=""
                            class="hover:bg-blue-500 absolute top-[50%] right-[5px] translate-y-[-50%] bg-blue-700 p-[5px] rounded-sm">
                            <x-icon name="user-pen" class="text-slate-200" />
                        </a>
                    </div>
                @endforeach
            </div>

            <div
                class="
                    pointer-events-none
                    absolute inset-0 rounded-2xl
                    border border-transparent

                    /* luz da borda para dentro */
                    bg-[radial-gradient(ellipse_at_top,rgba(0,0,0,0.45),transparent_0%)]

                    /* dissolve laterais e bottom */
                    [mask-image:linear-gradient(to_bottom,black_65%,transparent_100%)]

                    /* glow suave */
                    shadow-[inset_0_1px_12px_rgba(0,0,0,0.5)]
                    ">
            </div>

            <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
               bg-stone-200 text-black p-1 rounded-lg shadow-xl z-50 w-[300px]"
                style="display: none;">
                <form method="POST"
                    class="w-full h-full rounded-lg border-solid border-stone-600 border-[3px] p-3 flex flex-col items-center gap-7">
                    <h1>Add Player</h1>
                    <div class="w-full">
                        <x-input-label for="name" :value="__('Name')" />
                        <x-text-input id="name" type="text" name="name" class="w-full" />
                    </div>
                    <x-primary-button>{{ __('Register') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
