<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800">Gestion des {{ strtolower($title) }}s</h2>
            <a href="{{ route($routeName.'.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Créer</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif

            <div class="overflow-x-auto rounded-md bg-white shadow-sm">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            @foreach (array_slice($fields, 0, 4, true) as $name => $field)
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-600">{{ $field['label'] }}</th>
                            @endforeach
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-600">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($records as $record)
                            <tr>
                                @foreach (array_slice($fields, 0, 4, true) as $name => $field)
                                    @php($value = data_get($record, $name))
                                    <td class="max-w-xs truncate px-4 py-3 text-sm text-gray-700">
                                        @if (is_array($value))
                                            {{ json_encode($value, JSON_UNESCAPED_UNICODE) }}
                                        @elseif (is_bool($value))
                                            {{ $value ? 'Oui' : 'Non' }}
                                        @else
                                            {{ $value ?? '—' }}
                                        @endif
                                    </td>
                                @endforeach
                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm">
                                    <a class="font-medium text-indigo-700 hover:underline" href="{{ route($routeName.'.show', $record) }}">Ouvrir</a>
                                    <a class="ml-3 font-medium text-gray-700 hover:underline" href="{{ route($routeName.'.edit', $record) }}">Modifier</a>
                                    <form class="ml-3 inline" method="POST" action="{{ route($routeName.'.destroy', $record) }}" onsubmit="return confirm('Supprimer cet élément ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="font-medium text-red-700 hover:underline" type="submit">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td class="px-4 py-8 text-center text-sm text-gray-500" colspan="{{ min(count($fields), 4) + 1 }}">Aucun élément.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $records->links() }}
        </div>
    </div>
</x-app-layout>