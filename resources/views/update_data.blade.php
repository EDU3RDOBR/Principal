<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Dados') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
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

                    <h1>Editar Dados</h1>
                    @if(isset($table) && isset($row) && $row)
                        <form action="{{ route('edit-data.put', ['table' => $table, 'id' => $row->id]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="_method" value="PUT">
                            @foreach (get_object_vars($row) as $key => $value)
                                @if (!in_array($key, ['id', 'created_at', 'updated_at']))
                                    <div class="mb-4">
                                        <label for="{{ $key }}" class="block text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}</label>
                                        <input type="text" id="{{ $key }}" name="{{ $key }}" value="{{ $value }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                    </div>
                                @endif
                            @endforeach
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700">Salvar</button>
                        </form>
                    @else
                        <p>Erro: Modelo ou dados não encontrados.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
