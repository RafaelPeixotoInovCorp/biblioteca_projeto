<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-base-content leading-tight">
            {{ __('Resultados da Pesquisa: ') }} "{{ $query }}"
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-2xl font-bold">{{ $total }} livros encontrados</h3>
                    <p class="text-base-content/70">Selecione os livros que deseja importar</p>
                </div>
                <div class="flex gap-2">
                    <button onclick="selecionarTodos()" class="btn btn-outline btn-sm">
                        Selecionar Todos
                    </button>
                    <button onclick="importarSelecionados()" class="btn btn-primary btn-sm" id="btnImportarMultiplos">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        Importar Selecionados
                    </button>
                    <a href="{{ route('admin.importar.index') }}" class="btn btn-ghost btn-sm">
                        Nova Pesquisa
                    </a>
                </div>
            </div>

            <!-- Tabela de Resultados -->
            <div class="bg-base-100 shadow-xl rounded-3xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table">
                        <thead>
                        <tr>
                            <th class="w-12">
                                <input type="checkbox" id="checkboxTodos" class="checkbox checkbox-primary">
                            </th>
                            <th>Capa</th>
                            <th>Título / Autor</th>
                            <th>ISBN</th>
                            <th>Editora</th>
                            <th>Ano</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($livros as $index => $livro)
                            <tr class="{{ $livro['ja_existe'] ? 'opacity-50' : '' }}" id="row-{{ $index }}">
                                <td>
                                    @if(!$livro['ja_existe'])
                                        <input type="checkbox" class="checkbox checkbox-primary livro-checkbox" value="{{ $livro['google_id'] }}">
                                    @endif
                                </td>
                                <td>
                                    @if($livro['imagem_capa_url'])
                                        <div class="avatar">
                                            <div class="w-12 h-16 rounded">
                                                <img src="{{ $livro['imagem_capa_url'] }}" alt="Capa">
                                            </div>
                                        </div>
                                    @else
                                        <div class="w-12 h-16 bg-base-200 rounded flex items-center justify-center">
                                            <span class="text-2xl">📚</span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="font-bold">{{ $livro['titulo'] }}</div>
                                    @if(!empty($livro['subtitulo']))
                                        <div class="text-sm opacity-70">{{ $livro['subtitulo'] }}</div>
                                    @endif
                                    <div class="text-sm text-primary">
                                        @foreach(array_slice($livro['autores'], 0, 2) as $autor)
                                            <span class="badge badge-primary badge-outline badge-sm mr-1">{{ $autor }}</span>
                                        @endforeach
                                        @if(count($livro['autores']) > 2)
                                            <span class="text-xs">+{{ count($livro['autores'])-2 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($livro['isbn'])
                                        <span class="font-mono text-sm">{{ $livro['isbn'] }}</span>
                                    @else
                                        <span class="text-sm opacity-50">N/A</span>
                                    @endif
                                </td>
                                <td>{{ Str::limit($livro['editora'], 20) }}</td>
                                <td>{{ $livro['data_publicacao'] ? date('Y', strtotime($livro['data_publicacao'])) : 'N/A' }}</td>
                                <td>
                                    @if($livro['ja_existe'])
                                        <span class="badge badge-ghost">Já existe</span>
                                    @else
                                        <span class="badge badge-success">Disponível</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$livro['ja_existe'])
                                        <button onclick="importarLivro('{{ $livro['google_id'] }}', '{{ addslashes($livro['titulo']) }}')"
                                                class="btn btn-sm btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Importar
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-ghost" disabled>Importado</button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Progresso -->
    <dialog id="progressModal" class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg mb-4">A importar livros...</h3>
            <div class="space-y-4">
                <progress class="progress progress-primary w-full" id="importProgress" value="0" max="100"></progress>
                <p id="importStatus">A preparar importação...</p>
                <div id="importResults" class="text-sm space-y-1"></div>
            </div>
        </div>
    </dialog>

    <script>
        let livrosSelecionados = [];

        // Checkbox "Selecionar Todos"
        document.getElementById('checkboxTodos').addEventListener('change', function(e) {
            const checkboxes = document.querySelectorAll('.livro-checkbox');
            checkboxes.forEach(cb => {
                cb.checked = e.target.checked;
            });
        });

        function selecionarTodos() {
            const checkboxes = document.querySelectorAll('.livro-checkbox');
            checkboxes.forEach(cb => cb.checked = true);
            document.getElementById('checkboxTodos').checked = true;
        }

        function importarLivro(googleId, titulo) {
            if (!confirm(`Deseja importar o livro "${titulo}"?`)) {
                return;
            }

            fetch('{{ route("admin.importar.importar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ google_id: googleId })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('✅ Livro importado com sucesso!');
                        location.reload();
                    } else {
                        alert('❌ Erro: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('❌ Erro ao importar livro');
                });
        }

        function importarSelecionados() {
            const checkboxes = document.querySelectorAll('.livro-checkbox:checked');

            if (checkboxes.length === 0) {
                alert('Selecione pelo menos um livro para importar');
                return;
            }

            const googleIds = Array.from(checkboxes).map(cb => cb.value);

            if (!confirm(`Deseja importar ${googleIds.length} livro(s)?`)) {
                return;
            }

            // Abrir modal de progresso
            const modal = document.getElementById('progressModal');
            const progress = document.getElementById('importProgress');
            const status = document.getElementById('importStatus');
            const results = document.getElementById('importResults');

            modal.showModal();
            results.innerHTML = '';
            progress.value = 0;

            fetch('{{ route("admin.importar.importar-multiplos") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ google_ids: googleIds })
            })
                .then(response => response.json())
                .then(data => {
                    progress.value = 100;

                    let html = '';
                    if (data.sucesso.length > 0) {
                        html += `<p class="text-success">✅ ${data.sucesso.length} livro(s) importados com sucesso:</p>`;
                        data.sucesso.forEach(titulo => {
                            html += `<p class="text-sm ml-4">• ${titulo}</p>`;
                        });
                    }
                    if (data.existentes.length > 0) {
                        html += `<p class="text-warning mt-2">⚠️ ${data.existentes.length} livro(s) já existiam:</p>`;
                        data.existentes.forEach(titulo => {
                            html += `<p class="text-sm ml-4">• ${titulo}</p>`;
                        });
                    }
                    if (data.erro.length > 0) {
                        html += `<p class="text-error mt-2">❌ ${data.erro.length} erro(s):</p>`;
                        data.erro.forEach(erro => {
                            html += `<p class="text-sm ml-4">• ${erro}</p>`;
                        });
                    }

                    results.innerHTML = html;
                    status.innerHTML = 'Importação concluída!';

                    // Recarregar página após 3 segundos
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                })
                .catch(error => {
                    progress.value = 100;
                    status.innerHTML = 'Erro na importação';
                    results.innerHTML = `<p class="text-error">${error.message}</p>`;
                });
        }
    </script>
</x-app-layout>
