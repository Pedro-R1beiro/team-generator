<x-app-layout>
    <div x-data="teamsBuilder({
        initialUsers: @js($users->items()),
        currentPage: {{ $users->currentPage() }},
        lastPage: {{ $users->lastPage() }},
    })" class="flex h-[calc(100vh-4rem)]">
        {{-- SIDEBAR --}}
        <aside class="w-80 dark:bg-zinc-900 text-white border-r border-zinc-700 flex flex-col bg-stone-400">

            {{-- Header da sidebar --}}
            <div class="p-4 border-b border-zinc-700">
                <h2 class="text-lg font-semibold text-black dark:text-white">Players</h2>
            </div>

            <x-side-bar-players/>
        </aside>

        {{-- CONTEÚDO PRINCIPAL --}}
        <section class="flex-1 p-6 overflow-y-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <template x-for="team in ['A','B','C','D']" :key="team">
                    <div @dragover.prevent @drop="drop(team)"
                        class="min-h-[calc(100vh/2.5)] p-4 border-2 border-dashed border-black dark:border-white rounded-lg
                               bg-stone-200 dark:bg-zinc-900 dark:text-white">
                        <h2 class="font-semibold mb-3">
                            Time <span x-text="team"></span>
                        </h2>

                        <template x-for="player in teams[team]" :key="player.id">
                            <div
                                class="flex justify-between items-center p-2 mb-2
                                       bg-zinc-100 dark:bg-zinc-800 rounded">
                                <span x-text="player.name"></span>

                                <button @click="removeFromTeam(team, player)" class="text-red-500 text-sm">
                                    <x-icon name="user-minus" />
                                </button>
                            </div>
                        </template>

                        <p x-show="teams[team].length === 0" class="text-sm dark:text-zinc-400 text-zinc-600">
                            Arraste jogadores aqui
                        </p>
                    </div>
                </template>

            </div>
        </section>
    </div>

    <script>
        function teamsBuilder({
            initialUsers,
            currentPage,
            lastPage
        }) {
            return {
                /* ================== STATE ================== */
                users: initialUsers, // usuários visíveis (sidebar)
                allUsersPage: initialUsers, // página crua vinda do backend
                page: currentPage,
                lastPage: lastPage,
                search: '',
                loading: false,

                draggingUser: null,

                // IDs já alocados em algum time
                assignedIds: new Set(),

                teams: {
                    A: [],
                    B: [],
                    C: [],
                    D: [],
                },

                /* ================== DRAG ================== */
                startDrag(user) {
                    this.draggingUser = user;
                },

                drop(team) {
                    if (!this.draggingUser) return;

                    const user = this.draggingUser;

                    // evita duplicação
                    if (this.assignedIds.has(user.id)) {
                        this.draggingUser = null;
                        return;
                    }

                    this.teams[team].push(user);
                    this.assignedIds.add(user.id);

                    // remove da sidebar
                    this.users = this.users.filter(u => u.id !== user.id);

                    this.draggingUser = null;
                },

                removeFromTeam(team, player) {
                    this.teams[team] = this.teams[team].filter(u => u.id !== player.id);
                    this.assignedIds.delete(player.id);

                    if (this.allUsersPage.some(u => u.id === player.id)) {
                        this.insertUserAtOriginalPosition(player);
                    }
                },

                insertUserAtOriginalPosition(player) {
                    const originalIndex = this.allUsersPage.findIndex(
                        u => u.id === player.id
                    );

                    if (originalIndex === -1) return;

                    const nextUser = this.allUsersPage
                        .slice(originalIndex + 1)
                        .find(u => !this.assignedIds.has(u.id));

                    if (!nextUser) {
                        this.users.push(player);
                        return;
                    }

                    const insertIndex = this.users.findIndex(
                        u => u.id === nextUser.id
                    );

                    if (insertIndex === -1) {
                        this.users.push(player);
                    } else {
                        this.users.splice(insertIndex, 0, player);
                    }
                },

                /* ================== PAGINAÇÃO ================== */
                async fetchPage(page = 1) {
                    if (page < 1 || page > this.lastPage) return;

                    this.loading = true;

                    const params = new URLSearchParams({
                        page,
                        search: this.search
                    });

                    const response = await fetch(`/players?${params}`, {
                        headers: {
                            Accept: 'application/json'
                        }
                    });

                    const data = await response.json();

                    this.allUsersPage = data.data;

                    // sidebar = página atual - jogadores já alocados
                    this.users = data.data.filter(
                        u => !this.assignedIds.has(u.id)
                    );

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
