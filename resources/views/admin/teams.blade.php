<x-app-layout>
    <div x-data="teamsBuilder({
        initialUsers: @js($users->items()),
        currentPage: {{ $users->currentPage() }},
        lastPage: {{ $users->lastPage() }},
    })" class="flex h-[calc(100vh-4rem)]" x-init="init()">
        {{-- SIDEBAR --}}
        <aside class="w-80 dark:bg-zinc-900 text-white border-r border-zinc-700 flex flex-col bg-stone-400">

            {{-- Header da sidebar --}}
            <div class="p-4 border-b border-zinc-700">
                <h2 class="text-lg font-semibold text-black dark:text-white">Players</h2>
            </div>
            <div class="flex gap-2 p-2">
                <button @click="sidebarTab = 'players'"
                    :class="sidebarTab === 'players' ? 'bg-zinc-200 text-black' : 'bg-zinc-800 text-white'"
                    class="flex-1 py-1 rounded text-sm font-medium">
                    All Players
                </button>

                <button @click="sidebarTab = 'auto'"
                    :class="sidebarTab === 'auto' ? 'bg-zinc-200 text-black' : 'bg-zinc-800 text-white'"
                    class="flex-1 py-1 rounded text-sm font-medium">
                    Player Auto
                </button>
            </div>


            <div class="flex gap-3 justify-center my-4" x-show="sidebarTab === 'players'" x-transition>
                <button @click="openCreate = !openCreate" @click.stop
                    class="bg-green-600 w-[42px] h-[42px] p-[8px] rounded-md hover:scale-105">
                    <x-icon name="user-plus" />
                </button>
                <div class="relative text-black">
                    <x-text-input x-model="search" @input.debounce.400ms="searchUsers" class="pl-[30px] h-full"
                        placeholder="Search Player" />
                    <x-icon name="magnifying-glass" class="absolute left-[5px] top-[50%] -translate-y-1/2" />
                </div>
            </div>

            {{-- Lista com scroll --}}
            <div class="flex-1 overflow-y-auto mx-1 py-5 px-3 space-y-2 relative" x-show="sidebarTab === 'players'"
                x-transition>
                <template x-for="user in filteredUsers()" :key="user.id">
                    <div draggable="true" @dragstart="startDrag(user, 'sidebar')"
                        class="flex justify-between items-center p-2 rounded-md border-[3px]
           border-slate-300 dark:border-slate-200/60
           text-black dark:text-white cursor-grab">

                        <div class="flex items-center gap-3">
                            <x-icon name="user" class="text-lg" />
                            <p class="truncate max-w-[120px]" x-text="user.name"></p>
                        </div>

                        <span class="text-xs font-semibold rounded w-[40px]">
                            <x-icon name="trophy" class="inline text-yellow-500 mr-1" />
                            <span x-text="user.score ?? 0"></span>
                        </span>
                    </div>

                </template>

                <h1 x-show="search && filteredUsers().length === 0 && searchInAllUsers().length === 0"
                    class="col-span-full text-center text-zinc-400 text-3xl pt-8">
                    No result
                    <x-icon name="face-frown" />
                </h1>

                <h1 x-show="search && filteredUsers().length === 0 && searchAssignedInfo()"
                    class="col-span-full text-center text-yellow-500 text-xl pt-8">
                    <span x-text="searchAssignedInfo()"></span>
                </h1>

                <div
                    class=" pointer-events-none absolute inset-0 rounded-2xl border border-transparent /* luz da borda para dentro */ bg-[radial-gradient(ellipse_at_top,rgba(0,0,0,0.3),transparent_0%)] /* dissolve laterais e bottom */ [mask-image:linear-gradient(to_bottom,black_65%,transparent_100%)] /* glow suave */ shadow-[inset_0_1px_12px_rgba(0,0,0,0.6)] ">
                </div>
            </div>

            <div class="flex justify-center p-3" x-show="sidebarTab === 'players'" x-transition>
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

            <div x-show="sidebarTab === 'auto'" x-transition class="p-3 space-y-4">

                <div>
                    <label class="block text-sm font-medium text-black dark:text-white mb-1">
                        Lista de jogadores
                    </label>

                    <textarea x-model="autoPlayersText" placeholder="Um nome por linha"
                        class="w-full h-[60vh] rounded border border-zinc-300 p-2 text-black resize-none"></textarea>
                </div>

                <button type="button" @click="sendAutoPlayers" class="w-full py-2 bg-zinc-800 text-white rounded">
                    Enviar
                </button>

                <p x-show="autoError" class="text-sm text-red-500" x-text="autoError"></p>
            </div>
        </aside>

        {{-- CONTEÚDO PRINCIPAL --}}
        <section class="flex-1 p-6 overflow-y-auto relative">
            <div class="flex justify-between mb-3">
                <div class="flex gap-3">
                    <x-text-input type="date" x-model="playedAt" class="w-[150px]" />
                    <x-text-input type="text" x-model="teamSetName" placeholder="Name" class="pl-[10px] w-[150px]" />
                    <button @click="saveTeams" :disabled="!canSave()"
                        :class="!canSave() ?
                            'opacity-40 cursor-not-allowed' :
                            'hover:scale-105'"
                        class="bg-green-600 w-[42px] h-[42px] p-[8px] rounded-md text-white transition">
                        <x-icon name="floppy-disk" />
                    </button>
                    <button @click="openMatches = true" :disabled="!currentTeamSetId"
                        :class="!currentTeamSetId ? 'opacity-40 cursor-not-allowed' : 'hover:scale-105'"
                        class="bg-yellow-500 w-[42px] h-[42px] p-[8px] rounded-md text-white transition">
                        <x-icon name="trophy" />
                    </button>
                    <button @click="openConfirmDelete = true" :disabled="!currentTeamSetId"
                        :class="!currentTeamSetId
                            ?
                            'opacity-40 cursor-not-allowed' :
                            'hover:scale-105'"
                        class="bg-red-600 w-[42px] h-[42px] p-[8px] rounded-md text-white transition">
                        <x-icon name="trash" />
                    </button>
                    <button @click="toggleStats()"
                        class="bg-blue-700 w-[42px] h-[42px] p-[8px] rounded-md text-white transition">
                        <template x-if="showStats">
                            <x-icon name="eye" />
                        </template>
                        <template x-if="!showStats">
                            <x-icon name="eye-slash" />
                        </template>
                    </button>
                    <button @click="sortByAlphabet = !sortByAlphabet; sortTeams()"
                        class="bg-fuchsia-700 w-[42px] h-[42px] p-[8px] rounded-md text-white transition">
                        <template x-if="sortByAlphabet">
                            <x-icon name="arrow-down-a-z" />
                        </template>
                        <template x-if="!sortByAlphabet">
                            <x-icon name="star" />
                        </template>
                    </button>
                </div>
                <select x-model="currentTeamSetId" @change="loadTeamSet(currentTeamSetId)"
                    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-[200px]">

                    <option value="">New team</option>

                    <template x-for="set in teamSets" :key="set.id">
                        <option :value="set.id"
                            x-text="`(${set.played_at ?? set.created_at.slice(0,10)}) - ${set.name}`">
                        </option>
                    </template>
                </select>
            </div>

            <div id="teams-area" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <template x-for="team in ['A','B','C','D']" :key="team">
                    <div @dragover.prevent @drop="drop(team)"
                        class="min-h-[calc(100vh/2.4)] p-4 pb-2 border-2 border-dashed
                   border-black dark:border-white rounded-lg
                   bg-stone-200 dark:bg-zinc-900 dark:text-white">

                        <h2 class="font-semibold mb-3 flex items-center justify-between">
                            <span>
                                Team <span x-text="team"></span>
                            </span>

                            <span x-show="showStats" x-transition class="text-sm text-zinc-600 dark:text-zinc-400">
                                <x-icon name="users" /> <span x-text="teamStats(team).players"></span>
                                |
                                <x-icon name="gamepad" /> <span x-text="teamStats(team).games"></span>
                                |
                                <x-icon name="trophy" /> <span x-text="teamStats(team).wins"></span>
                                |
                                <x-icon name="xmark" /> <span x-text="teamStats(team).losses"></span>
                                |
                                <x-icon name="star" /> <span x-text="teamStats(team).score"></span>
                            </span>
                        </h2>

                        <!-- jogadores do time -->
                        <template x-for="player in teams[team]" :key="player.id">
                            <div draggable="true" @dragstart="startDrag(player, team)"
                                class="flex justify-between items-center p-2 mb-2
                           bg-zinc-100 dark:bg-zinc-800 rounded cursor-grab">

                                <span x-text="player.name"></span>
                                <div x-show="showStats" x-transition class="flex gap-1 items-center">
                                    <span class="text-xs font-semibold rounded w-[40px]">
                                        <x-icon name="trophy" class="inline text-yellow-500 mr-1" />
                                        <span x-text="player.score ?? 0"></span>
                                    </span>

                                    <button @click="removeFromTeam(team, player)" class="text-red-500 text-sm">
                                        <x-icon name="user-minus" />
                                    </button>
                                </div>
                            </div>
                        </template>

                        <p x-show="teams[team].length === 0" class="text-sm dark:text-zinc-400 text-zinc-600">
                            Drag players here
                        </p>
                    </div>
                </template>
            </div>

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
                <form @submit.prevent="createPlayer"
                    class="w-full h-full rounded-lg border-[3px] border-stone-600
             p-3 flex flex-col items-center gap-7 relative">
                    @csrf

                    <x-icon name="close" class="absolute top-3 right-3 cursor-pointer hover:text-red-500"
                        @click="openCreate = false" />

                    <h1>Add Player</h1>

                    <div class="w-full">
                        <x-input-label for="create_name" value="Name" />
                        <x-text-input x-model="createName" id="create_name" type="text" class="w-full" />
                    </div>

                    <x-primary-button>Register</x-primary-button>
                    <p x-show="createSuccess" class="text-green-600 text-sm" x-text="createSuccess"></p>

                    <p x-show="createError" class="text-red-600 text-sm" x-text="createError"></p>
                </form>
            </div>

            <!-- CONFIRM DELETE -->
            <div x-show="openConfirmDelete" @click.outside="openConfirmDelete = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                x-transition:enter-end="opacity-100 scale-100 -translate-y-1/2"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-2"
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
           bg-stone-200 text-black p-1 rounded-lg shadow-xl z-50 w-[300px]"
                style="display: none;">
                <!-- CONTAINER INTERNO (ANTES ERA FORM) -->
                <div
                    class="w-full h-full rounded-lg border-[3px] border-stone-600
               p-3 flex flex-col gap-2 items-center relative">
                    <!-- BOTÃO FECHAR -->
                    <x-icon name="close" class="absolute top-3 right-3 cursor-pointer hover:text-red-500"
                        @click="openConfirmDelete = false" />

                    <div class="flex gap-1 items-center">
                        <x-icon name="circle-exclamation" class="text-red-800 text-xl" />
                        <p>Are you sure?</p>
                    </div>
                    <p>This process cannot be undone.</p>

                    <!-- AÇÕES -->
                    <div class="flex gap-3 justify-center pt-2">
                        <button type="button" @click="openConfirmDelete = false"
                            class='inline-flex items-center px-4 py-2 bg-neutral-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-neutral-500 focus:bg-neutral-500 active:bg-neutral-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer'>
                            Cancel
                        </button>

                        <button type="button" @click="deleteTeamSet()"
                            class='inline-flex items-center px-4 py-2 bg-red-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer'>
                            Confirm
                        </button>
                    </div>
                </div>
            </div>

            <div x-show="openMatches" @click.outside="openMatches = false"
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2
            bg-stone-200 text-black p-1 rounded-lg shadow-xl z-50 w-[360px]">

                <div class="rounded-lg border-[3px] border-stone-600 p-3 space-y-3 relative">

                    <x-icon name="close" class="absolute top-3 right-3 cursor-pointer hover:text-red-500"
                        @click="openMatches = false" />

                    <h2 class="text-center font-semibold">Partidas</h2>
                    <!-- NOVA PARTIDA -->
                    <div class="border-t pt-3 space-y-2">
                        <label class="flex items-center gap-2 text-sm">
                            <input type="checkbox" x-model="useMatchSequence">
                            Usar sequência automática de jogos
                        </label>
                        <h3 class="text-sm font-semibold">Nova partida</h3>

                        <div class="flex gap-2 items-center">
                            <select x-model="newMatch.team_1" class="flex-1 rounded">
                                <template x-for="t in ['A','B','C','D']" :key="t">
                                    <option :value="t" x-text="t"></option>
                                </template>
                            </select>

                            <input type="number" min="0" x-model.number="newMatch.score_1"
                                placeholder="Score" class="w-1/2 rounded text-xs h-[-webkit-fill-available]" />

                            <p>X</p>

                            <select x-model="newMatch.team_2" :key="newMatch.team_1" class="flex-1 rounded">
                                <template x-for="t in ['B','A','C','D']" :key="t">
                                    <option :value="t" :disabled="t === newMatch.team_1" x-text="t">
                                    </option>
                                </template>
                            </select>

                            <input type="number" min="0" x-model.number="newMatch.score_2"
                                placeholder="Score" class="w-1/2 rounded text-xs h-[-webkit-fill-available]" />

                        </div>

                        <select x-model="newMatch.winner" class="w-full rounded">
                            <option :value="newMatch.team_1" x-text="`Time ${newMatch.team_1}`"></option>
                            <option :value="newMatch.team_2" x-text="`Time ${newMatch.team_2}`"></option>
                        </select>

                        <button @click="addMatch" class="w-full bg-green-600 text-white py-1 rounded">
                            Adicionar partida
                        </button>
                    </div>
                    <!-- LISTA -->
                    <div class="overflow-y-auto max-h-[40vh] flex flex-col gap-2">
                        <template x-for="match in matches" :key="match.id">
                            <div class="flex items-center justify-between bg-white p-2 rounded">
                                <span>
                                    <b x-text="match.team_1"></b>
                                    (<span x-text="match.score_1 ?? '-'"></span>)
                                    x
                                    (<span x-text="match.score_2 ?? '-'"></span>)
                                    <b x-text="match.team_2"></b>
                                    -
                                    <span x-text="match.winner ? 'Vencedor: ' + match.winner : 'Sem vencedor'"></span>
                                </span>

                                <button @click="deleteMatch(match)" class="text-red-600 hover:text-red-800">
                                    <x-icon name="trash" />
                                </button>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </section>

        <aside class="w-80 dark:bg-zinc-900 text-white border-r border-zinc-700 flex flex-col bg-stone-400">

            <div class="p-4 border-b border-zinc-700">
                <h2 class="text-lg font-semibold text-black dark:text-white">
                    Selected Players
                    <span class="text-sm text-zinc-500 ml-1" x-text="`(${teams.selected.length})`"></span>
                </h2>
            </div>



            <div class="flex-1 overflow-y-auto mx-1 py-5 px-3 space-y-2 relative" @dragover.prevent
                @drop="drop('selected')">
                <div
                    class=" pointer-events-none absolute inset-0 rounded-2xl border border-transparent /* luz da borda para dentro */ bg-[radial-gradient(ellipse_at_top,rgba(0,0,0,0.3),transparent_0%)] /* dissolve laterais e bottom */ [mask-image:linear-gradient(to_bottom,black_65%,transparent_100%)] /* glow suave */ shadow-[inset_0_1px_12px_rgba(0,0,0,0.6)] ">
                </div>

                <template x-for="player in teams.selected" :key="player.id">
                    <div draggable="true" @dragstart="startDrag(player, 'selected')"
                        class="flex justify-between items-center p-2
                bg-zinc-100 dark:bg-zinc-800 rounded cursor-grab">

                        <span x-text="player.name"></span>
                        <div class="flex gap-1 items-center">
                            <span class="text-xs font-semibold rounded w-[40px]">
                                <x-icon name="trophy" class="inline text-yellow-500 mr-1" />
                                <span x-text="player.score ?? 0"></span>
                            </span>

                            <button @click="removeFromTeam('selected', player)" class="text-red-500 text-sm">
                                <x-icon name="user-minus" />
                            </button>
                        </div>
                    </div>
                </template>

                <p x-show="teams.selected.length === 0" class="text-sm dark:text-zinc-400 text-zinc-600">
                    Arraste jogadores aqui
                </p>
            </div>
            <div x-show="autoNotFound.length" class="border-t border-zinc-700 px-4 py-3">

                <h3 class="text-sm font-semibold text-black dark:text-white mb-2">
                    Não encontrados
                    <span class="text-xs text-red-500 ml-1" x-text="`(${autoNotFound.length})`"></span>
                </h3>

                <ul class="text-sm text-stone-700 dark:text-stone-300 space-y-1 max-h-40 overflow-y-auto">
                    <template x-for="name in autoNotFound" :key="name">
                        <li class="list-disc list-inside">
                            <span x-text="name"></span>
                        </li>
                    </template>
                </ul>
            </div>


        </aside>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>
        function teamsBuilder({
            initialUsers,
            currentPage,
            lastPage
        }) {
            return {
                /* ================== STATE ================== */
                showStats: true,
                toggleStats() {
                    this.showStats = !this.showStats;
                },

                openMatches: false,
                matches: [],

                teams: {
                    A: [],
                    B: [],
                    C: [],
                    D: [],
                    selected: [],
                },

                useMatchSequence: true,

                matchSequence: [
                    ['A', 'B'],
                    ['C', 'D'],
                    ['C', 'B'],
                    ['A', 'D'],
                    ['A', 'C'],
                    ['B', 'D'],
                ],

                applyMatchSequence() {
                    if (!this.useMatchSequence) return;

                    const index = this.matches.length % this.matchSequence.length;
                    const [team1, team2] = this.matchSequence[index];

                    this.newMatch.team_1 = team1;
                    this.newMatch.team_2 = team2;
                    this.newMatch.winner = null;
                },

                users: initialUsers, // usuários visíveis (sidebar)
                allUsersPage: initialUsers, // página crua vinda do backend
                page: currentPage,
                lastPage: lastPage,
                search: '',
                loading: false,

                loadingTeamSet: false,

                currentTeamSetId: null,
                isDirty: false,
                teamSets: [],

                dragSource: null,

                openCreate: false,
                createName: '',
                createSuccess: null,
                createError: null,

                openConfirmDelete: false,
                deleting: false,

                playedAt: null,

                sidebarTab: 'players',

                autoPlayersText: '',
                autoLoading: false,
                autoError: null,
                autoNotFound: [],

                teamSetName: '',

                draggingUser: null,

                // IDs já alocados em algum time
                assignedIds: new Set(),

                newMatch: {
                    team_1: 'A',
                    team_2: 'B',
                    score_1: null,
                    score_2: null,
                    winner: null,
                },

                async loadMatches(teamSetId) {
                    if (!teamSetId) {
                        this.matches = [];
                        return;
                    }

                    const res = await fetch(`/team-sets/${teamSetId}`);
                    const data = await res.json();

                    this.matches = data.games || [];

                    // 🔥 aplica sequência baseada no BD
                    this.applyMatchSequence();
                },

                canSave() {
                    const hasPlayers =
                        this.teams.A.length ||
                        this.teams.B.length ||
                        this.teams.C.length ||
                        this.teams.D.length;

                    const hasName = this.teamSetName && this.teamSetName.trim().length > 0;
                    const hasDate = !!this.playedAt;

                    return this.isDirty && hasPlayers && hasName && hasDate;
                },

                filteredUsers() {
                    return this.users.filter(u =>
                        u.name.toLowerCase().includes(this.search.toLowerCase())
                    );
                },

                searchInAllUsers() {
                    return this.allUsersPage.filter(u =>
                        u.name.toLowerCase().includes(this.search.toLowerCase())
                    );
                },

                normalizeName(name) {
                    return name
                        .toLowerCase()
                        .normalize('NFD')
                        .replace(/[\u0300-\u036f]/g, '')
                        .trim();
                },

                searchAssignedInfo() {
                    const search = this.search.toLowerCase();
                    if (!search) return null;

                    // está em algum time?
                    for (const team of ['A', 'B', 'C', 'D']) {
                        const found = this.teams[team].find(p =>
                            p.name.toLowerCase().includes(search)
                        );
                        if (found) {
                            return `Player already in team ${team}`;
                        }
                    }

                    // está em selected?
                    const selected = this.teams.selected.find(p =>
                        p.name.toLowerCase().includes(search)
                    );

                    if (selected) {
                        return 'Player already selected';
                    }

                    return null;
                },

                today() {
                    return new Date().toISOString().slice(0, 10);
                },

                teamScore(team) {
                    return this.teams[team]
                        .reduce((total, player) => total + (player.score ?? 0), 0);
                },

                teamStats(team) {
                    const players = this.teams[team];

                    const gamesPlayed = this.matches.filter(
                        m => m.team_1 === team || m.team_2 === team
                    );

                    const wins = gamesPlayed.filter(
                        m => m.winner === team
                    ).length;

                    return {
                        players: players.length,
                        games: gamesPlayed.length,
                        wins,
                        losses: gamesPlayed.length - wins,
                        score: players.reduce(
                            (sum, p) => sum + (p.score ?? 0),
                            0
                        ),
                    };
                },

                init() {
                    this.playedAt = this.today();
                    this.loadTeamSets();

                    this.$watch('teams', () => {
                        if (this.loadingTeamSet) return;
                        this.isDirty = true;
                        this.sortTeams();
                    }, {
                        deep: true
                    });

                    this.$watch('playedAt', () => {
                        if (this.loadingTeamSet) return;
                        this.isDirty = true;
                    });

                    this.$watch('teamSetName', () => {
                        if (this.loadingTeamSet) return;
                        this.isDirty = true;
                    });

                    this.$watch('openMatches', async (open) => {
                        if (!open) return;
                        if (!this.currentTeamSetId) return;

                        await this.loadMatches(this.currentTeamSetId);

                        this.applyMatchSequence();
                    });

                    this.$watch('useMatchSequence', (val) => {
                        if (val) this.applyMatchSequence();
                    });

                    this.$watch('newMatch.team_1', (value) => {
                        if (this.newMatch.team_2 === value) {
                            this.newMatch.team_2 = ['A', 'B', 'C', 'D'].find(t => t !== value);
                        }
                        this.newMatch.winner = null;
                    });

                    this.$watch('newMatch.team_2', () => {
                        this.newMatch.winner = null;
                    });
                },

                async addMatch() {
                    if (!this.newMatch.team_1 || !this.newMatch.team_2) return;

                    if (!this.newMatch.winner) {
                        this.newMatch.winner = this.newMatch.team_1;
                    }

                    const res = await fetch(
                        `/team-sets/${this.currentTeamSetId}/games`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            },
                            body: JSON.stringify(this.newMatch),
                        }
                    );

                    const match = await res.json();
                    this.matches.unshift(match);

                    // reaplica sequência considerando o novo total
                    this.applyMatchSequence();
                },

                async deleteMatch(match) {
                    await fetch(`/games/${match.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        }
                    });

                    this.matches = this.matches.filter(m => m.id !== match.id);
                },

                async loadTeamSets() {
                    const res = await fetch('/team-sets');
                    this.teamSets = await res.json();
                },

                async createPlayer() {
                    this.createError = null;
                    this.createSuccess = null;

                    try {
                        const response = await fetch('/players', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                            },
                            body: JSON.stringify({
                                name: this.createName,
                            }),
                        });

                        if (!response.ok) {
                            const err = await response.json();
                            throw new Error(err.message || 'Erro ao criar jogador');
                        }

                        const data = await response.json();

                        /** 1️⃣ mostra mensagem */
                        this.createSuccess = data.message;

                        /** 🔥 REMOVE DOS NÃO ENCONTRADOS SE EXISTIR */
                        const createdName = this.normalizeName(data.user.name);

                        this.autoNotFound = this.autoNotFound.filter(
                            name => this.normalizeName(name) !== createdName
                        );

                        /** 2️⃣ adiciona na lista se não estiver alocado */
                        if (!this.assignedIds.has(data.user.id)) {
                            this.users.unshift(data.user);
                            this.allUsersPage.unshift(data.user);
                        }

                        /** 3️⃣ limpa */
                        this.createName = '';

                        /** 4️⃣ fecha modal após leve delay */
                        setTimeout(() => {
                            this.openCreate = false;
                            this.createSuccess = null;
                        }, 800);

                    } catch (e) {
                        this.createError = e.message;
                    }
                },

                async sendAutoPlayers() {
                    this.autoError = null;
                    this.autoNotFound = [];
                    this.autoLoading = true;

                    try {
                        const response = await fetch('/players/auto', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document
                                    .querySelector('meta[name="csrf-token"]')
                                    .getAttribute('content'),
                            },
                            body: JSON.stringify({
                                players: this.autoPlayersText,
                            }),
                        });

                        if (!response.ok) {
                            throw new Error('Erro na requisição');
                        }

                        const data = await response.json();

                        /**
                         * 1️⃣ ADICIONA ENCONTRADOS NA SIDEBAR DIREITA
                         */
                        data.found.forEach(player => {
                            if (!this.assignedIds.has(player.id)) {
                                this.teams.selected.push(player);
                                this.assignedIds.add(player.id);
                            }
                        });

                        this.sortTeams();

                        /**
                         * 2️⃣ REMOVE DA SIDEBAR DE BUSCA
                         */
                        this.users = this.users.filter(
                            u => !this.assignedIds.has(u.id)
                        );

                        /**
                         * 3️⃣ GUARDA NÃO ENCONTRADOS
                         */
                        this.autoNotFound = data.not_found || [];

                        /**
                         * 4️⃣ LIMPA TEXTAREA E VOLTA PRA LISTA
                         */
                        this.autoPlayersText = '';
                        this.sidebarTab = 'players';

                    } catch (e) {
                        this.autoError = e.message;
                    } finally {
                        this.autoLoading = false;
                    }
                },

                /* ================== DRAG ================== */
                startDrag(user, source) {
                    this.draggingUser = user;
                    this.dragSource = source;
                },

                drop(targetTeam) {
                    if (!this.draggingUser || !this.dragSource) return;

                    const user = this.draggingUser;
                    const source = this.dragSource;

                    // ❌ mesmo lugar → ignora completamente
                    if (source === targetTeam) {
                        this.resetDrag();
                        return;
                    }

                    this.isDirty = true; // ✅ AGORA SIM

                    if (source === 'sidebar') {
                        this.users = this.users.filter(u => u.id !== user.id);
                    } else {
                        this.teams[source] = this.teams[source].filter(u => u.id !== user.id);
                    }

                    this.teams[targetTeam].push(user);
                    this.assignedIds.add(user.id);

                    this.resetDrag();
                    this.sortTeams();
                },

                sortByAlphabet: true,

                sortTeams() {
                    if (this.sortByAlphabet) {
                        const sortByName = (a, b) =>
                            a.name.localeCompare(b.name, 'pt-BR', {
                                sensitivity: 'base'
                            });

                        // ordena todos os times
                        ['A', 'B', 'C', 'D', 'selected'].forEach(team => {
                            this.teams[team].sort(sortByName);
                        });
                    } else {
                        const sortByScore = (a, b) => {
                            const scoreA = a.score ?? 0;
                            const scoreB = b.score ?? 0;

                            // 1️⃣ maior pontuação primeiro
                            if (scoreA !== scoreB) {
                                return scoreB - scoreA;
                            }

                            // 2️⃣ desempate por nome (opcional, mas recomendado)
                            return a.name.localeCompare(b.name, 'pt-BR', {
                                sensitivity: 'base'
                            });
                        };

                        ['A', 'B', 'C', 'D', 'selected'].forEach(team => {
                            this.teams[team].sort(sortByScore);
                        });
                    }
                },

                resetDrag() {
                    this.draggingUser = null;
                    this.dragSource = null;
                },

                removeFromTeam(team, player) {
                    const exists = this.teams[team].some(u => u.id === player.id);
                    if (!exists) return;

                    this.isDirty = true;

                    this.teams[team] = this.teams[team].filter(u => u.id !== player.id);
                    this.assignedIds.delete(player.id);

                    if (this.allUsersPage.some(u => u.id === player.id)) {
                        this.insertUserAtOriginalPosition(player);
                    }

                    this.sortTeams();
                },

                async saveTeams() {
                    const payload = {
                        name: this.teamSetName ?? null,
                        played_at: this.playedAt ?? null,
                        teams: {
                            A: this.teams.A.map(u => u.id),
                            B: this.teams.B.map(u => u.id),
                            C: this.teams.C.map(u => u.id),
                            D: this.teams.D.map(u => u.id),
                        }
                    };

                    const isUpdate = !!this.currentTeamSetId;
                    const method = isUpdate ? 'PUT' : 'POST';
                    const url = isUpdate ?
                        `/team-sets/${this.currentTeamSetId}` :
                        `/team-sets`;

                    const response = await fetch(url, {
                        method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        console.error(data);
                        alert('Erro ao salvar');
                        return;
                    }

                    /* ================== 🔥 SINCRONIZA SELECT ================== */

                    if (isUpdate) {
                        // 🔁 atualiza item existente
                        const index = this.teamSets.findIndex(s => s.id === data.id);
                        if (index !== -1) {
                            this.teamSets[index] = {
                                ...this.teamSets[index],
                                name: data.name,
                                played_at: data.played_at,
                            };
                        }
                    } else {
                        // 🆕 adiciona novo no topo
                        this.teamSets.unshift({
                            id: data.id,
                            name: data.name,
                            played_at: data.played_at,
                            created_at: new Date().toISOString().slice(0, 10),
                        });

                        // seleciona automaticamente
                        this.currentTeamSetId = data.id;
                    }

                    this.isDirty = false;

                    this.$dispatch('notify', {
                        type: 'success',
                        message: data.message ?? 'Times salvos com sucesso!'
                    });
                },

                async loadTeamSet(id) {
                    this.loadingTeamSet = true;

                    if (!id) {
                        this.resetAll();

                        this.matches = [];

                        this.playedAt = this.today();
                        this.teamSetName = '';

                        this.loadingTeamSet = false;
                        return;
                    }

                    const res = await fetch(`/team-sets/${id}`);
                    const data = await res.json();

                    this.resetAll();

                    data.players.forEach(row => {
                        this.teams[row.team].push(row.user);
                        this.assignedIds.add(row.user.id);
                    });

                    this.users = this.users.filter(
                        u => !this.assignedIds.has(u.id)
                    );

                    this.currentTeamSetId = id;

                    await this.loadMatches(id);

                    this.playedAt = data.played_at ?
                        data.played_at.slice(0, 10) :
                        this.today();

                    this.teamSetName = data.name ?? '';

                    this.sortTeams();
                    this.isDirty = false; // 🔥 estado limpo
                    this.loadingTeamSet = false;
                },

                resetAll() {
                    this.teams = {
                        A: [],
                        B: [],
                        C: [],
                        D: [],
                        selected: [],
                    };

                    this.matches = []; // 🔥 GARANTIA TOTAL

                    this.autoNotFound = [];
                    this.assignedIds.clear();

                    this.isDirty = false;

                    this.users = this.allUsersPage.filter(
                        u => !this.assignedIds.has(u.id)
                    );
                },

                async deleteTeamSet() {
                    if (!this.currentTeamSetId) return;

                    this.deleting = true;
                    const deletingId = this.currentTeamSetId;

                    try {
                        const response = await fetch(`/team-sets/${deletingId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json',
                            }
                        });

                        if (!response.ok) throw new Error('Erro ao excluir');

                        this.teamSets = this.teamSets.filter(s => s.id !== deletingId);

                        // 🔥 estado base
                        this.currentTeamSetId = '';
                        this.playedAt = this.today();
                        this.teamSetName = '';

                        this.resetAll();

                        this.$dispatch('notify', {
                            type: 'success',
                            message: 'Time excluído com sucesso!'
                        });

                    } catch (e) {
                        alert(e.message);
                    } finally {
                        this.deleting = false;
                        this.openConfirmDelete = false;
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
