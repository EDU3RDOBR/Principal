{{-- Em resources/views/show_table.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Tabela de Dados') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($data->isEmpty())
                        <p>Nenhum dado encontrado para exibir.</p>
                    @else
                        <div class="flex justify-end mb-4">
                            <label for="perPage" class="mr-2">Exibir por página:</label>
                            <select id="perPage" name="perPage" class="border border-gray-300 rounded px-2 py-1" onchange="changePerPage()">
                                @foreach($perPageOptions as $option)
                                    <option value="{{ $option }}" {{ $option == $perPage ? 'selected' : '' }}>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        ID
                                    </th>
                                    @foreach (array_keys((array)$data->first()) as $key)
                                        @if (!in_array($key, ['id', 'created_at', 'updated_at']))
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                                            </th>
                                        @endif
                                    @endforeach
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
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
                                            <a href="{{ route('edit-data', ['modelName' => $modelName, 'id' => $row->id]) }}" class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                            <form action="{{ route('delete-data', ['id' => $row->id]) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 ml-4">Excluir</button>
                                            </form>
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
</x-app-layout>

<script>
    function changePerPage() {
        const selectedValue = document.getElementById('perPage').value;
        const currentUrl = window.location.href;
        const urlWithoutParams = currentUrl.split('?')[0];
        const newUrl = urlWithoutParams + '?perPage=' + selectedValue;
        window.location.href = newUrl;
    }
</script>
