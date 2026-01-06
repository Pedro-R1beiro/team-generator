<div class="flex justify-center p-3">
    <div class="relative text-black">
        <x-text-input x-model="search" @input.debounce.400ms="searchUsers" class="pl-[30px] h-full"
            placeholder="Search Player" />
        <x-icon name="magnifying-glass" class="absolute left-[5px] top-[50%] -translate-y-1/2" />
    </div>
</div>

{{-- Lista com scroll --}}
<div class="flex-1 overflow-y-auto mx-1 py-5 px-3 space-y-2 relative">
    <template x-for="user in users.filter(u => u.name.toLowerCase().includes(search.toLowerCase()))"
        :key="user.id">
        <div draggable="true" @dragstart="startDrag(user)"
            class="flex p-2 gap-5 items-center rounded-md border-[3px] text-black
                                   border-slate-300 dark:border-slate-200/60 dark:text-white
                                   relative h-fit w-full cursor-grab">

            <x-icon name="user" class="text-lg dark:text-white" />

            <p class="truncate pr-[30px] max-w-full" x-text="user.name"></p>
        </div>
    </template>

    <h1 x-show="users.filter(u => u.name.toLowerCase().includes(search.toLowerCase())).length === 0"
        class="col-span-full text-center text-zinc-400 text-3xl pt-8">
        No result
        <x-icon name="face-frown" />
    </h1>

    <div
        class=" pointer-events-none absolute inset-0 rounded-2xl border border-transparent /* luz da borda para dentro */ bg-[radial-gradient(ellipse_at_top,rgba(0,0,0,0.3),transparent_0%)] /* dissolve laterais e bottom */ [mask-image:linear-gradient(to_bottom,black_65%,transparent_100%)] /* glow suave */ shadow-[inset_0_1px_12px_rgba(0,0,0,0.6)] ">
    </div>
</div>

<div class="flex justify-center p-3">
    <div class="flex items-center gap-4">
        <button @click="fetchPage(page - 1)" :disabled="page === 1 || loading"
            class="px-4 py-2 bg-zinc-700 text-white rounded disabled:opacity-40">
            <x-icon name="backward" />
        </button>

        <span class="text-sm text-zinc-400">
            Page <span x-text="page"></span> of <span x-text="lastPage"></span>
        </span>

        <button @click="fetchPage(page + 1)" :disabled="page === lastPage || loading"
            class="px-4 py-2 bg-zinc-700 text-white rounded disabled:opacity-40">
            <x-icon name="forward" />
        </button>
    </div>
</div>