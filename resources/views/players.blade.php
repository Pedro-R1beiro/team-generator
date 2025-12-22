<x-app-layout>

    <div x-data="{
        openCreate: false,
        openEdit: false,
        search: '',
        users: @js($users),
        selectedUser: null
    }" class="py-12 flex flex-col items-center gap-10">

        <!-- TOPO -->
        <div class="flex gap-8">
            <button @click="openCreate = !openCreate" @click.stop
                class="bg-green-600 w-[42px] h-[42px] p-[8px] rounded-md hover:scale-105">
                <x-icon name="user-plus" />
            </button>

            <div class="relative">
                <x-icon name="magnifying-glass" class="absolute left-[5px] top-[50%] -translate-y-1/2" />
                <x-text-input x-model="search" class="pl-[30px] h-full" placeholder="Search Player" />
            </div>
        </div>

        <!-- LISTA -->
        <div class="relative w-[90%] rounded-2xl h-[50vh]">

            <div class="p-6 overflow-y-auto grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 max-h-full">

                <template x-for="user in users.filter(u => u.name.toLowerCase().includes(search.toLowerCase()))"
                    :key="user.id">
                    <div
                        class="flex p-2 gap-5 items-center rounded-md border-[3px]
                               border-slate-800/60 dark:border-slate-200/60 dark:text-white
                               relative h-fit">
                        <x-icon name="user" />

                        <p class="truncate pr-[30px] max-w-full" x-text="user.name"></p>

                        <!-- BOTÃO EDIT -->
                        <button
                            @click="
                                selectedUser = JSON.parse(JSON.stringify(user));
                                openEdit = true;
                            "
                            class="absolute top-1/2 right-[5px] -translate-y-1/2
                                   bg-blue-600 hover:bg-blue-500 p-[8px] rounded-sm
                                   w-[32px] h-[32px]">
                            <x-icon name="user-pen" class="text-slate-200" />
                        </button>
                    </div>
                </template>

                <!-- SEM RESULTADO -->
                <h1 x-show="users.filter(u => u.name.toLowerCase().includes(search.toLowerCase())).length === 0"
                    class="col-span-full text-center text-zinc-400 text-3xl pt-8">
                    No result
                    <x-icon name="face-frown" />
                </h1>
            </div>

            <div
                class=" pointer-events-none absolute inset-0 rounded-2xl border border-transparent /* luz da borda para dentro */ bg-[radial-gradient(ellipse_at_top,rgba(0,0,0,0.3),transparent_0%)] /* dissolve laterais e bottom */ [mask-image:linear-gradient(to_bottom,black_65%,transparent_100%)] /* glow suave */ shadow-[inset_0_1px_12px_rgba(0,0,0,0.6)] ">
            </div>

            <!-- CREATE MODAL -->
            <div x-show="openCreate" @click.outside="openCreate = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 -translate-y-1/2"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                       bg-stone-200 text-black p-1 rounded-lg shadow-xl z-50 w-[300px]"
                style="display: none;">
                <form method="POST" action="{{ route('players') }}"
                    class="w-full h-full rounded-lg border-[3px] border-stone-600
                           p-3 flex flex-col items-center gap-7 relative">
                    @csrf

                    <x-icon name="close" class="absolute top-3 right-3 cursor-pointer hover:text-red-500"
                        @click="openCreate = false" />

                    <h1>Add Player</h1>

                    <div class="w-full">
                        <x-input-label for="create_name" value="Name" />
                        <x-text-input id="create_name" type="text" name="name" class="w-full" />
                    </div>

                    <x-primary-button>Register</x-primary-button>
                </form>
            </div>

            <!-- EDIT MODAL -->
            <div x-show="openEdit" @click.outside="openEdit = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 -translate-y-1/2"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                       bg-stone-200 text-black p-1 rounded-lg shadow-xl z-50 w-[300px]"
                style="display: none;">
                <form method="POST" :action="`/players/${selectedUser?.id}`"
                    class="w-full h-full rounded-lg border-[3px] border-stone-600
                           p-3 flex flex-col items-center gap-7 relative">
                    @csrf
                    @method('PUT')

                    <x-icon name="close" class="absolute top-3 right-3 cursor-pointer hover:text-red-500"
                        @click="openEdit = false" />

                    <h1>Edit Player</h1>

                    <div class="w-full">
                        <x-input-label for="edit_name" value="Name" />
                        <x-text-input id="edit_name" type="text" name="name" class="w-full"
                            x-model="selectedUser.name" />
                    </div>

                    <x-primary-button>Update</x-primary-button>
                </form>
            </div>

        </div>
    </div>

</x-app-layout>
