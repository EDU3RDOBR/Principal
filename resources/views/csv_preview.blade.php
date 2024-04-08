{{-- Em resources/views/csv_preview.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Preview CSV Data') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1>Preview CSV Data</h1>
                    <table>
                        <thead>
                            <tr>
                                @foreach ($data[0] as $key => $value)
                                    <th>{{ $key }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $row)
                                <tr>
                                    @foreach ($row as $value)
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <form action="{{ route('import') }}" method="POST">
                        @csrf
                        <button type="submit">Confirm Import</button>
                    </form>
                    <a href="{{ route('cancel-import') }}" class="btn btn-danger">Cancelar Importação</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
