{{-- Em resources/views/csv_preview.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pré-visualização dos Dados CSV') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    {{-- Exibição de mensagens de sucesso ou erro --}}
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
                    {{-- Título da pré-visualização --}}
                    <h1 class="text-lg font-bold mb-4">Pré-visualização dos Dados CSV</h1>
                    <div class="mt-4">
                        <form action="{{ route('import') }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Confirmar Importação</button>
                        </form>
                        <a href="{{ route('cancel-import') }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-block ml-2">Cancelar Importação</a>
                    </div>
                    <br>
                    {{-- Exibição dos dados do CSV em uma tabela --}}
                    @if (!empty($data) && is_array($data))
                        <div class="overflow-x-auto">
                            <table class="min-w-full table-auto text-sm bg-white rounded-lg shadow overflow-hidden">
                                <thead class="bg-gray-200 text-gray-600">
                                    <tr>
                                        {{-- Cabeçalhos da tabela --}}
                                        @if (!empty($data[0]))
                                            @foreach (array_keys($data[0]) as $key)
                                                <th class="px-4 py-2 font-semibold">{{ $key }}</th>
                                            @endforeach
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="text-gray-700">
                                    {{-- Linhas da tabela --}}
                                    @foreach ($data as $row)
                                        <tr class="bg-gray-100 border-b">
                                            {{-- Colunas da tabela --}}
                                            @foreach ($row as $value)
                                                <td class="px-4 py-2">{{ $value }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        {{-- Mensagem de ausência de dados --}}
                        <p>Não há dados para exibir ou o arquivo CSV estava vazio.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
