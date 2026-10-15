<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800">{{ $title }} #{{ $record->getKey() }}</h2>
            <div class="flex gap-4">
                <a href="{{ route($routeName.'.index') }}" class="text-sm font-medium text-gray-700 hover:underline">Liste</a>
                <a href="{{ route($routeName.'.edit', $record) }}" class="text-sm font-medium text-indigo-700 hover:underline">Modifier</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-4 px-4 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-800">{{ session('status') }}</div>
            @endif
            <dl class="divide-y divide-gray-200 rounded-md bg-white px-6 shadow-sm">
                @foreach ($fields as $name => $field)
                    @php($value = data_get($record, $name))
                    <div class="grid gap-1 py-4 sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-600">{{ $field['label'] }}</dt>
                        <dd class="break-words text-sm text-gray-900 sm:col-span-2">
                            @if (is_array($value))
                                <pre class="whitespace-pre-wrap">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            @elseif (is_bool($value))
                                {{ $value ? 'Oui' : 'Non' }}
                            @else
                                {{ $value ?? '—' }}
                            @endif
                        </dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </div>
</x-app-layout>