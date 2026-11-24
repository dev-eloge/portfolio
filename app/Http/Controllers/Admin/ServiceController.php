<?php

namespace App\Http\Controllers\Admin;

use App\Models\Service;
use Illuminate\Database\Eloquent\Model;

class ServiceController extends ResourceController
{
    protected string $route = 'services';

    protected string $title = 'Services';

    protected string $singular = 'Service';

    protected function model(): string
    {
        return Service::class;
    }

    protected function fields(): array
    {
        return [
            ['name' => 'title', 'type' => 'translatable', 'label' => 'Titre', 'required' => true],
            ['name' => 'description', 'type' => 'translatable_textarea', 'label' => 'Description', 'required' => true],
            ['name' => 'icon', 'type' => 'select', 'label' => 'Icône', 'required' => true, 'default' => 'code', 'options' => self::options([
                'code' => 'Code', 'brain' => 'Cerveau (IA)', 'layout' => 'Mise en page', 'database' => 'Base de données', 'api' => 'API', 'device-mobile' => 'Mobile',
            ])],
            ['name' => 'order', 'type' => 'number', 'label' => "Ordre d'affichage", 'default' => 0],
        ];
    }

    protected function columns(): array
    {
        return ['Titre'];
    }

    protected function row(Model $m): array
    {
        return [$m->getTranslation('title', 'fr')];
    }
}
