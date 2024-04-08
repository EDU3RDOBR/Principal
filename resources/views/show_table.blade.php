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
                            <select id="perPage" name="perPage" class="border border-gray-300 rounded px-2 py-1">
                                @isset($perPageOptions)
                                    @foreach($perPageOptions as $option)
                                        <option value="{{ $option }}" @if($option == $perPage) selected @endif>{{ $option }}</option>
                                    @endforeach
                                @endisset
                            </select>
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th>Selecionar</th>
                                    @foreach ($data->first() as $key => $value)
                                        <th>{{ $key }}</th>
                                    @endforeach
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $row)
                                    <tr>
                                        <td><input type="checkbox" name="selected[]" value="{{ $row->id }}"></td>
                                        @foreach ($row as $value)
                                            <td>{{ $value }}</td>
                                        @endforeach
                                        <td>
                                            <a href="{{ route('edit-data', $row->id) }}">Editar</a>
                                            <a href="{{ route('delete-data', $row->id) }}">Excluir</a>
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
    document.getElementById('perPage').addEventListener('change', function() {
        var selectedValue = this.value;
        var currentUrl = window.location.href;
        var urlWithoutParams = currentUrl.split('?')[0];
        var newUrl = urlWithoutParams + '?perPage=' + selectedValue;
        window.location.href = newUrl;
    });
</script>
