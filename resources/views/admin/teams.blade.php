<x-app-layout>
    <div class="flex h-[calc(100vh-4rem)]">
        {{-- SIDEBAR --}}
        <aside
            class="w-80 bg-zinc-900 text-white border-r border-zinc-700 flex flex-col">

            {{-- Header da sidebar --}}
            <div class="p-4 border-b border-zinc-700">
                <h2 class="text-lg font-semibold">Players</h2>
            </div>

            <div class="relative">
                <x-icon name="magnifying-glass" class="absolute left-[5px] top-[50%] -translate-y-1/2" />
                <x-text-input x-model="search" @input.debounce.400ms="searchUsers" class="pl-[30px] h-full"
                    placeholder="Search Player" />
            </div>

            {{-- Lista com scroll --}}
            <div class="flex-1 overflow-y-auto p-3 space-y-2">
                <div
                    class="flex p-2 gap-5 items-center rounded-md border-[3px]
                                   border-slate-800/60 dark:border-slate-200/60 dark:text-white
                                   relative h-fit w-full">

                    <x-icon name="user" class="text-lg text-green-600" />

                    <p class="truncate pr-[30px] max-w-full">Pedro Ribeiro</p>
                </div> 
            </div>
        </aside>

        {{-- CONTEÚDO PRINCIPAL --}}
        <section class="flex-1 bg-stone-200 dark:bg-zinc-800 p-6 overflow-y-auto">
            <h1 class="text-2xl font-bold mb-4">
                Detalhes / Conteúdo
            </h1>

            <div class="bg-white dark:bg-zinc-900 rounded-lg p-6 shadow">
                <p class="text-zinc-700 dark:text-zinc-300">
                    Aqui vai o conteúdo principal da página.
                </p>
            </div>
        </section>
    </div>
</x-app-layout>