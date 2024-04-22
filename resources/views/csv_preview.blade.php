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
                    <h1 class="text-lg font-bold mb-4">Pré-visualização dos Dados CSV</h1>
                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto text-sm bg-white rounded-lg shadow overflow-hidden">
                            <thead class="bg-gray-200 text-gray-600">
                                <tr>
                                    @foreach ($data[0] as $key => $value)
                                        <th class="px-4 py-2 font-semibold">{{ $key }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="text-gray-700">
                                @foreach ($data as $row)
                                    <tr class="bg-gray-100 border-b">
                                        @foreach ($row as $value)
                                            <td class="px-4 py-2">{{ $value }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        <form action="{{ route('import') }}" method="POST" class="inline-block">
                            @csrf
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Confirmar Importação</button>
                        </form>
                        <a href="{{ route('cancel-import') }}" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded inline-block ml-2">Cancelar Importação</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
