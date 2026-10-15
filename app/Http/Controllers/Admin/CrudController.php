<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

abstract class CrudController extends Controller
{
    abstract protected function model(): string;

    abstract protected function title(): string;

    abstract protected function routeName(): string;

    abstract protected function fields(): array;

    public function index(): View
    {
        return view('admin.resources.index', [
            'title' => $this->title(),
            'fields' => $this->resolvedFields(),
            'records' => $this->query()->latest()->paginate(15),
            'routeName' => $this->routeName(),
        ]);
    }

    public function create(): View
    {
        return $this->formView(new ($this->model())());
    }

    public function store(Request $request): RedirectResponse
    {
        $record = new ($this->model())();
        $record->fill($this->validatedData($request, $record));
        $record->save();

        return redirect()->route($this->routeName().'.show', $record)
            ->with('status', $this->title().' créé avec succès.');
    }

    public function show(string $id): View
    {
        return view('admin.resources.show', [
            'title' => $this->title(),
            'fields' => $this->resolvedFields(),
            'record' => $this->query()->findOrFail($id),
            'routeName' => $this->routeName(),
        ]);
    }

    public function edit(string $id): View
    {
        return $this->formView($this->query()->findOrFail($id));
    }

    public function update(Request $request, string $id): RedirectResponse
    {
        $record = $this->query()->findOrFail($id);
        $record->fill($this->validatedData($request, $record));
        $record->save();

        return redirect()->route($this->routeName().'.show', $record)
            ->with('status', $this->title().' modifié avec succès.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->query()->findOrFail($id)->delete();

        return redirect()->route($this->routeName().'.index')
            ->with('status', $this->title().' supprimé avec succès.');
    }

    protected function query()
    {
        $model = $this->model();

        return $model::query();
    }

    protected function formView(Model $record): View
    {
        return view('admin.resources.form', [
            'title' => $this->title(),
            'fields' => $this->resolvedFields(),
            'record' => $record,
            'routeName' => $this->routeName(),
        ]);
    }

    protected function resolvedFields(): array
    {
        return array_map(function (array $field): array {
            if (isset($field['options_model'])) {
                $model = $field['options_model'];
                $field['options'] = $model::query()
                    ->orderBy($field['option_label'])
                    ->pluck($field['option_label'], $field['option_value'] ?? 'id')
                    ->all();
            }

            return $field;
        }, $this->fields());
    }

    protected function validatedData(Request $request, Model $record): array
    {
        $rules = [];
        foreach ($this->fields() as $name => $field) {
            $rules[$name] = $record->exists
                ? ($field['update_rules'] ?? $field['rules'] ?? ['nullable'])
                : ($field['create_rules'] ?? $field['rules'] ?? ['nullable']);
            if (isset($field['unique'])) {
                $rules[$name][] = Rule::unique($record->getTable(), $field['unique'])
                    ->ignore($record->exists ? $record->getKey() : null, $record->getKeyName());
            }
            if (isset($field['parent_field'], $field['parent_column'], $field['options_model'])) {
                $relatedModel = new $field['options_model'];
                $rules[$name][] = Rule::exists($relatedModel->getTable(), 'id')
                    ->where($field['parent_column'], $request->input($field['parent_field']));
            }
        }

        $data = $request->validate($rules);

        foreach ($this->fields() as $name => $field) {
            if (($field['type'] ?? null) === 'password' && empty($data[$name])) {
                unset($data[$name]);
            }
            if (($field['type'] ?? null) === 'checkbox') {
                $data[$name] = $request->boolean($name);
            }
            if (($field['type'] ?? null) === 'json' && isset($data[$name])) {
                $data[$name] = json_decode($data[$name], true);
            }
        }

        return $data;
    }
}
