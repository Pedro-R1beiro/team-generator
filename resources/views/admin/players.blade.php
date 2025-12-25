<x-app-layout>
    <div x-data="playersPagination({
        initialUsers: @js($users->items()),
        currentPage: {{ $users->currentPage() }},
        lastPage: {{ $users->lastPage() }},
    })" class="py-16 flex flex-col items-center gap-10 relative w-full">

        @if (session('success'))
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2000)" x-show="show" x-transition.opacity.duration.300ms
                class="mb-4 w-[90%] bg-green-600 text-white px-4 py-2 rounded-md absolute top-[10px] z-50">
                {{ session('success') }}
            </div>
        @endif


        @if ($errors->any())
            <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 2000)" x-show="show" x-transition.opacity.duration.300ms
                class="mb-4 w-[90%] bg-red-600 text-white px-4 py-2 rounded-md absolute top-[10px] z-50">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <!-- TOPO -->
        <div class="flex gap-8">
            <button @click="openCreate = !openCreate" @click.stop
                class="bg-green-600 w-[42px] h-[42px] p-[8px] rounded-md hover:scale-105">
                <x-icon name="user-plus" />
            </button>

            <div class="relative">
                <x-icon name="magnifying-glass" class="absolute left-[5px] top-[50%] -translate-y-1/2" />
                <x-text-input x-model="search" @input.debounce.400ms="searchUsers" class="pl-[30px] h-full"
                    placeholder="Search Player" />
            </div>
        </div>

        <!-- LISTA -->
        <div class="relative w-[90%] rounded-2xl h-[55vh]">
            <div class="p-6 overflow-y-auto grid gap-4 grid-cols-1 md:grid-cols-2 lg:grid-cols-3 max-h-full">

                <template x-for="user in users.filter(u => u.name.toLowerCase().includes(search.toLowerCase()))"
                    :key="user.id">
                    <div
                        class="flex p-2 gap-5 items-center rounded-md border-[3px]
                               border-slate-800/60 dark:border-slate-200/60 dark:text-white
                               relative h-fit">

                        <x-icon x-show="!user.is_admin" name="user" class="text-lg" />

                        <x-icon x-show="user.is_admin" name="user-gear" class="text-lg text-green-600" />

                        <p class="truncate pr-[30px] max-w-full" x-text="user.name"></p>

                        <!-- BOTÃO EDIT -->
                        <button x-show="authUserId !== user.id"
                            @click="
                                selectedUser = {
                                    ...JSON.parse(JSON.stringify(user)),
                                    is_admin: Boolean(user.is_admin)
                                };
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

            <!-- EFFECT -->
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
            <div x-show="openEdit" @click.outside="openEdit = false, openConfirmDelete = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 -translate-y-1/2"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
                       bg-stone-200 text-black p-1 rounded-lg shadow-xl z-50 min-w-[min(300px,80vw)]"
                style="display: none;">
                <div class="w-full">
                    <form method="POST" :action="`/players/${selectedUser?.id}`"
                        class="w-full h-full rounded-lg border-[3px] border-stone-600
                               p-3 flex flex-col items-center gap-7 relative">
                        @csrf
                        @method('PUT')

                        <x-icon name="close" class="absolute top-3 right-3 cursor-pointer hover:text-red-500"
                            @click="openEdit = false, openConfirmDelete = false" />
                        <h1>Edit Player</h1>

                        <div class="w-full">
                            <x-input-label for="edit_name" value="Name" />
                            <x-text-input id="edit_name" type="text" name="name" class="w-full"
                                x-model="selectedUser.name" />
                        </div>

                        <label for="is_admin" class="inline-flex items-center">
                            <input id="is_admin" type="checkbox" value="1"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                name="is_admin" :checked="selectedUser.is_admin">
                            <span class="ms-2 text-sm text-gray-600">{{ __('Admin permission') }}</span>
                        </label>

                        <div class="flex w-full justify-around">
                            <div @click="openConfirmDelete = true"
                                class='inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer'>
                                Delete
                            </div>

                            <x-primary-button>Update</x-primary-button>
                        </div>

                    </form>
                </div>
                <!-- CONFIRM DELETE -->
                <div x-show="openConfirmDelete"
                    class="w-full bg-green-600 absolute bottom-[-15px] left-[50%] translate-x-[-50%] translate-y-full bg-stone-200 text-black p-1 rounded-lg shadow-xl z-50"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                    <form method="POST" :action="`/players/${selectedUser?.id}`"
                        class="rounded-lg border-[3px] border-stone-600 p-3 flex flex-col gap-2 items-center w-full">
                        @csrf
                        @method('DELETE')
                        <div class="flex gap-1 items-center">
                            <x-icon name="circle-exclamation" class="text-red-800 text-xl" />
                            <p>Are you sure?</p>
                        </div>
                        <p>This process cannot be undone.</p>
                        <div class="flex w-full justify-around">
                            <div @click="openConfirmDelete = false"
                                class='inline-flex items-center px-4 py-2 bg-neutral-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-neutral-500 focus:bg-neutral-500 active:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer'>
                                Cancel
                            </div>
                            <button type="submit"
                                class='inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer'>
                                Delete
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
        <div class="flex items-center gap-4 mt-4">
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

    <script>
        function playersPagination({
            initialUsers,
            currentPage,
            lastPage
        }) {
            return {
                /* ====== DADOS ====== */
                users: initialUsers,
                page: currentPage,
                lastPage: lastPage,
                search: '',
                loading: false,

                /* ====== ESTADO DOS MODAIS ====== */
                openCreate: false,
                openEdit: false,
                openConfirmDelete: false,

                /* ====== CONTEXTO ====== */
                authUserId: {{ auth()->id() }},
                selectedUser: null,

                /* ====== MÉTODOS ====== */
                async fetchPage(page = 1) {
                    if (page < 1 || page > this.lastPage) return;

                    this.loading = true;

                    const params = new URLSearchParams({
                        page,
                        search: this.search
                    });

                    const response = await fetch(`/players?${params.toString()}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    this.users = data.data;
                    this.page = data.current_page;
                    this.lastPage = data.last_page;

                    this.loading = false;
                },

                searchUsers() {
                    this.fetchPage(1);
                }
            }
        }
    </script>

</x-app-layout>
