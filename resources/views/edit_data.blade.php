{{-- Em resources/views/edit_data.blade.php --}}
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
                    <form action="{{ route('update-data', $row->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        @foreach ($row as $key => $value)
                            @if ($key !== 'id' && $key !== 'created_at' && $key !== 'updated_at')
                                <label for="{{ $key }}">{{ $key }}</label>
                                <input type="text" id="{{ $key }}" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach
                        <button type="submit">Salvar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
