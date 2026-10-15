<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-xl font-semibold text-gray-800">{{ $record->exists ? 'Modifier' : 'Créer' }} : {{ $title }}</h2>
            <a href="{{ route($routeName.'.index') }}" class="text-sm font-medium text-gray-700 hover:underline">Retour à la liste</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <form class="space-y-6 rounded-md bg-white p-6 shadow-sm" method="POST" action="{{ $record->exists ? route($routeName.'.update', $record) : route($routeName.'.store') }}">
                @csrf
                @if ($record->exists)
                    @method('PUT')
                @endif

                @foreach ($fields as $name => $field)
                    @php
                        $value = old($name, $record->getAttribute($name));
                        $rules = implode('|', $record->exists ? ($field['update_rules'] ?? $field['rules'] ?? []) : ($field['create_rules'] ?? $field['rules'] ?? []));
                        $required = str_contains($rules, 'required');
                        if (($field['type'] ?? '') === 'json' && is_array($value)) {
                            $value = json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                        }
                        if (in_array($field['type'] ?? '', ['date', 'datetime-local'], true) && $value) {
                            $value = \Illuminate\Support\Carbon::parse($value)->format($field['type'] === 'date' ? 'Y-m-d' : 'Y-m-d\TH:i');
                        }
                    @endphp

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700" for="{{ $name }}">{{ $field['label'] }}@if ($required) <span class="text-red-600">*</span>@endif</label>
                        @if (($field['type'] ?? '') === 'textarea' || ($field['type'] ?? '') === 'json')
                            <textarea class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" id="{{ $name }}" name="{{ $name }}" rows="{{ ($field['type'] ?? '') === 'json' ? 5 : 3 }}" @required($required)>{{ $value }}</textarea>
                        @elseif (($field['type'] ?? '') === 'select')
                            <select class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" id="{{ $name }}" name="{{ $name }}" @required($required)>
                                @if (!$required)<option value="">Aucun</option>@endif
                                @foreach (($field['options'] ?? []) as $optionValue => $optionLabel)
                                    <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                                @endforeach
                            </select>
                        @elseif (($field['type'] ?? '') === 'checkbox')
                            <input type="hidden" name="{{ $name }}" value="0">
                            <label class="flex items-center gap-2 text-sm text-gray-700"><input class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" id="{{ $name }}" type="checkbox" name="{{ $name }}" value="1" @checked((bool) $value)> Oui</label>
                        @else
                            <input class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" id="{{ $name }}" name="{{ $name }}" type="{{ $field['type'] ?? 'text' }}" value="{{ ($field['type'] ?? '') === 'password' ? '' : $value }}" @required($required) @if (($field['type'] ?? '') === 'number') step="any" @endif>
                            @if (($field['type'] ?? '') === 'password' && $record->exists)
                                <p class="mt-1 text-xs text-gray-500">Laisser vide pour conserver le mot de passe actuel.</p>
                            @endif
                        @endif
                        @error($name)<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach

                <div class="flex items-center gap-3 border-t border-gray-100 pt-5">
                    <button class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700" type="submit">Enregistrer</button>
                    <a class="text-sm text-gray-700 hover:underline" href="{{ route($routeName.'.index') }}">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>