<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Base des sections de l'admin : liste, création, modification, suppression.
 * Une section déclare seulement ses champs (fields) et ses colonnes de liste (columns).
 *
 * Types de champ : text, textarea, translatable (FR obligatoire si required, EN facultatif),
 * translatable_textarea, url, email, number, checkbox, select, date, year, tags (liste séparée par des virgules), image.
 * Option 'virtual' => true : le champ n'est pas une colonne du modèle (traité dans prepare()).
 */
abstract class ResourceController extends Controller
{
    /** Nom de route (ex. 'projects' → admin.projects.index). */
    protected string $route;

    protected string $title;

    protected string $singular;

    abstract protected function model(): string;

    abstract protected function fields(): array;

    /** En-têtes et cellules de la liste. */
    abstract protected function columns(): array;

    abstract protected function row(Model $model): array;

    protected function query(): Builder
    {
        return ($this->model())::query()->orderBy('order')->orderByDesc('id');
    }

    /** Ajustements propres à la section, avant l'enregistrement. */
    protected function prepare(Model $model, array $data, Request $request): void {}

    public function index(): Response
    {
        return Inertia::render('Admin/Resource/Index', [
            'title' => $this->title,
            'singular' => $this->singular,
            'route' => $this->route,
            'columns' => $this->columns(),
            'rows' => $this->query()->get()->map(fn (Model $m) => ['id' => $m->getKey(), 'cells' => $this->row($m)]),
        ]);
    }

    public function create(): Response
    {
        return $this->form(new ($this->model())());
    }

    public function edit(int $id): Response
    {
        return $this->form(($this->model())::findOrFail($id));
    }

    public function store(Request $request): RedirectResponse
    {
        $model = new ($this->model())();
        $this->save($model, $request);

        return redirect()->route("admin.{$this->route}.index")->with('success', "{$this->singular} ajouté(e).");
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $this->save(($this->model())::findOrFail($id), $request);

        return redirect()->route("admin.{$this->route}.index")->with('success', "{$this->singular} enregistré(e).");
    }

    public function destroy(int $id): RedirectResponse
    {
        ($this->model())::findOrFail($id)->delete();

        return back()->with('success', "{$this->singular} supprimé(e).");
    }

    protected function form(Model $model): Response
    {
        return Inertia::render('Admin/Resource/Form', [
            'title' => $model->exists ? "Modifier : {$this->singular}" : "Ajouter : {$this->singular}",
            'back' => route("admin.{$this->route}.index"),
            'action' => $model->exists ? route("admin.{$this->route}.update", $model->getKey()) : route("admin.{$this->route}.store"),
            'method' => $model->exists ? 'put' : 'post',
            'fields' => $this->fields(),
            'values' => $this->values($model),
        ]);
    }

    /** Valeurs du formulaire (traductions complètes FR + EN). */
    protected function values(Model $model): array
    {
        $values = [];
        foreach ($this->fields() as $field) {
            $name = $field['name'];
            if ($field['virtual'] ?? false) {
                continue;
            }
            $raw = $model->exists ? $model->getAttribute($name) : null;
            $values[$name] = match ($field['type']) {
                'translatable', 'translatable_textarea' => [
                    'fr' => $model->exists ? $model->getTranslation($name, 'fr', false) : '',
                    'en' => $model->exists ? $model->getTranslation($name, 'en', false) : '',
                ],
                'tags' => implode(', ', $raw ?? []),
                'date' => $raw?->format('Y-m-d') ?? '',
                'checkbox' => (bool) $raw,
                'image' => $raw ?? null,
                default => $raw ?? ($field['default'] ?? ''),
            };
        }

        return [...$values, ...$this->extraValues($model)];
    }

    /** Valeurs des champs virtuels. */
    protected function extraValues(Model $model): array
    {
        return [];
    }

    protected function rules(): array
    {
        $rules = [];
        foreach ($this->fields() as $field) {
            $name = $field['name'];
            $req = ($field['required'] ?? false) ? 'required' : 'nullable';
            switch ($field['type']) {
                case 'translatable':
                case 'translatable_textarea':
                    $max = $field['type'] === 'translatable' ? 255 : 10000;
                    $rules[$name] = ['array'];
                    $rules["{$name}.fr"] = [$req, 'string', "max:{$max}"];
                    $rules["{$name}.en"] = ['nullable', 'string', "max:{$max}"];
                    break;
                case 'url':
                    $rules[$name] = [$req, 'url', 'max:255'];
                    break;
                case 'email':
                    $rules[$name] = [$req, 'email', 'max:255'];
                    break;
                case 'number':
                    $rules[$name] = [$req, 'integer', 'min:0', 'max:100000'];
                    break;
                case 'year':
                    $rules[$name] = [$req, 'integer', 'min:1950', 'max:2100'];
                    break;
                case 'checkbox':
                    $rules[$name] = ['boolean'];
                    break;
                case 'select':
                    $rules[$name] = [$req, Rule::in(array_column($field['options'], 'value'))];
                    break;
                case 'date':
                    $rules[$name] = [$req, 'date'];
                    break;
                case 'image':
                    $rules[$name] = ['nullable', 'image', 'max:4096'];
                    break;
                case 'textarea':
                    $rules[$name] = [$req, 'string', 'max:10000'];
                    break;
                default:
                    $rules[$name] = [$req, 'string', 'max:255'];
            }
        }

        return $rules;
    }

    protected function save(Model $model, Request $request): void
    {
        $data = $request->validate($this->rules(), [], $this->attributeNames());

        foreach ($this->fields() as $field) {
            $name = $field['name'];
            if ($field['virtual'] ?? false) {
                continue;
            }
            $value = $data[$name] ?? null;
            match ($field['type']) {
                'translatable', 'translatable_textarea' => $model->setTranslations($name, array_filter(
                    ['fr' => $value['fr'] ?? null, 'en' => $value['en'] ?? null],
                    fn ($v) => filled($v),
                )),
                'tags' => $model->setAttribute($name, array_values(array_filter(array_map('trim', explode(',', (string) $value))))),
                'checkbox' => $model->setAttribute($name, (bool) $value),
                'number' => $model->setAttribute($name, (int) $value),
                'image' => $request->hasFile($name)
                    ? $model->setAttribute($name, self::storeUpload($request->file($name), $this->route))
                    : null,
                default => $model->setAttribute($name, filled($value) ? $value : null),
            };
        }

        $this->prepare($model, $data, $request);
        $model->save();
    }

    public static function storeUpload($file, string $folder): string
    {
        return '/storage/'.$file->store("uploads/{$folder}", 'public');
    }

    /** Libellés des champs dans les messages d'erreur. */
    protected function attributeNames(): array
    {
        $names = [];
        foreach ($this->fields() as $field) {
            $names[$field['name']] = $field['label'];
            $names["{$field['name']}.fr"] = $field['label'].' (FR)';
            $names["{$field['name']}.en"] = $field['label'].' (EN)';
        }

        return $names;
    }

    protected static function options(array $map): array
    {
        return collect($map)->map(fn ($label, $value) => ['value' => $value, 'label' => $label])->values()->all();
    }
}
