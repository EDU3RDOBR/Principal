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
                    <h1>Editar Dados</h1>
                    @if(isset($model) && isset($row) && $row)
                        <form action="{{ url('/update-data/' . $model . '/' . $row->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="_method" value="PUT">
                            @foreach ($row->getAttributes() as $key => $value)
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
