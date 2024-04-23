{{-- Em resources/views/show_table.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Visualização de Dados da Tabela') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Upload CSV Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('upload.csv.process') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <label for="csv_file" class="block text-sm font-medium text-gray-700">Selecione o arquivo CSV:</label>
                            <input type="file" id="csv_file" name="csv_file" accept=".csv" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        </div>
                        <br>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Importar CSV
                        </button>
                    </form>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-200 border-l-4 border-green-600 text-green-800 p-4 mb-4" role="alert">
                    <p class="font-bold">Sucesso!</p>
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-200 border-l-4 border-red-600 text-red-800 p-4 mb-4" role="alert">
                    <p class="font-bold">Erro!</p>
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($data->isEmpty())
                        <p>Nenhum dado encontrado para exibir.</p>
                    @else
                        <div class="flex justify-end mb-4">
                            <label for="perPage" class="mr-2">Exibir por página:</label>
                            <select id="perPage" name="perPage" class="border border-gray-300 rounded px-10 py-2" onchange="changePerPage()">
                                @foreach($perPageOptions as $option)
                                    <option value="{{ $option }}" {{ $option == $perPage ? 'selected' : '' }}>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    @foreach (array_keys((array)$data->first()) as $key)
                                        @if (!in_array($key, ['id', 'created_at', 'updated_at']))
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                                            </th>
                                        @endif
                                    @endforeach
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ações
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($data as $row)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $row->id }}
                                        </td>
                                        @foreach ((array)$row as $key => $value)
                                            @if (!in_array($key, ['id', 'created_at', 'updated_at']))
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $value }}
                                                </td>
                                            @endif
                                        @endforeach
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('edit-data', ['table' => $tableName, 'id' => $row->id]) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                            <button onclick="confirmDelete(event, '{{ $tableName }}', {{ $row->id }})" class="text-red-600 hover:text-red-900 ml-4">Excluir</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{ $data->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Confirmação de Exclusão</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Tem certeza que deseja excluir este registro? Esta ação é irreversível.</p>
                </div>
                <div class="items-center px-4 py-3">
                    <button id="cancelButton" class="px-4 py-2 bg-gray-200 text-gray-900 rounded hover:bg-gray-300 mr-2">Cancelar</button>
                    <button id="confirmButton" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Excluir</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Fim do Modal -->

</x-app-layout>

<script>
    function changePerPage() {
        const selectedValue = document.getElementById('perPage').value;
        const currentUrl = window.location.href;
        const urlWithoutParams = currentUrl.split('?')[0];
        const newUrl = urlWithoutParams + '?perPage=' + selectedValue;
        window.location.href = newUrl;
    }

    function confirmDelete(event, table, id) {
        event.preventDefault();
        document.getElementById('deleteModal').classList.remove('hidden');
        document.getElementById('confirmButton').onclick = function() {
            deleteData(table, id);
        };
    }

    document.getElementById('cancelButton').addEventListener('click', function() {
        document.getElementById('deleteModal').classList.add('hidden');
    });

    function deleteData(table, id) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/delete-data/${table}/${id}`;
        form.style.display = 'none';

        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';

        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';

        form.appendChild(methodInput);
        form.appendChild(csrfInput);
        document.body.appendChild(form);
        form.submit();
    }
</script>
