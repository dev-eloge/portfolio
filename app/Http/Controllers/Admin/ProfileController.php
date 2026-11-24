<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/** Profil : textes, liens, photos et CV (enregistrés dans la table settings). */
class ProfileController extends Controller
{
    private function fields(): array
    {
        return [
            ['name' => 'name', 'type' => 'text', 'label' => 'Nom affiché', 'required' => true],
            ['name' => 'role', 'type' => 'translatable', 'label' => 'Rôle', 'required' => true],
            ['name' => 'intro', 'type' => 'translatable_textarea', 'label' => "Présentation de l'accueil", 'help' => '2 phrases, affichées en effet machine à écrire.'],
            ['name' => 'about', 'type' => 'translatable_textarea', 'label' => 'Texte de la page À propos', 'help' => 'Séparez les paragraphes par une ligne vide.'],
            ['name' => 'location', 'type' => 'text', 'label' => 'Localisation'],
            ['name' => 'work_mode', 'type' => 'translatable', 'label' => 'Mode de travail', 'help' => 'Ex. Remote / hybride'],
            ['name' => 'email', 'type' => 'email', 'label' => 'E-mail public'],
            ['name' => 'github', 'type' => 'url', 'label' => 'Lien GitHub'],
            ['name' => 'linkedin', 'type' => 'url', 'label' => 'Lien LinkedIn'],
            ['name' => 'cv', 'type' => 'file', 'label' => 'CV (PDF)', 'accept' => 'application/pdf', 'help' => 'PDF, 5 Mo max.'],
            ['name' => 'face', 'type' => 'image', 'label' => "Photo ronde de l'accueil", 'help' => 'Visage centré, format carré.'],
            ['name' => 'face_alt', 'type' => 'translatable', 'label' => 'Description de la photo de l’accueil'],
            ['name' => 'about_photo', 'type' => 'image', 'label' => 'Photo de la page À propos', 'help' => 'Format portrait.'],
            ['name' => 'about_alt', 'type' => 'translatable', 'label' => 'Description de la photo À propos'],
        ];
    }

    public function edit(): Response
    {
        $p = Profile::all();
        $tr = fn ($v) => is_array($v) && array_key_exists('fr', $v) ? ['fr' => $v['fr'] ?? '', 'en' => $v['en'] ?? ''] : ['fr' => (string) $v, 'en' => ''];
        $paragraphs = fn ($v) => is_array($v) ? implode("\n\n", $v) : (string) $v;
        $about = $p['about'] ?? [];

        return Inertia::render('Admin/Resource/Form', [
            'title' => 'Profil',
            'back' => route('admin.dashboard'),
            'action' => route('admin.profile.update'),
            'method' => 'put',
            'fields' => $this->fields(),
            'values' => [
                'name' => $p['name'] ?? '',
                'role' => $tr($p['role'] ?? ''),
                'intro' => $tr($p['intro'] ?? ''),
                'about' => ['fr' => $paragraphs($about['fr'] ?? ''), 'en' => $paragraphs($about['en'] ?? '')],
                'location' => $p['location'] ?? '',
                'work_mode' => $tr($p['work_mode'] ?? ''),
                'email' => $p['email'] ?? '',
                'github' => $p['github'] ?? '',
                'linkedin' => $p['linkedin'] ?? '',
                'cv' => $p['cv'] ?? null,
                'face' => $p['images']['face'] ?? null,
                'face_alt' => $tr($p['images']['face_alt'] ?? ''),
                'about_photo' => $p['images']['about'] ?? null,
                'about_alt' => $tr($p['images']['about_alt'] ?? ''),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $t = fn (bool $required, int $max = 255) => [
            '' => ['array'], '.fr' => [$required ? 'required' : 'nullable', 'string', "max:{$max}"], '.en' => ['nullable', 'string', "max:{$max}"],
        ];
        $rules = ['name' => ['required', 'string', 'max:100'], 'location' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email'], 'github' => ['nullable', 'url'], 'linkedin' => ['nullable', 'url'],
            'cv' => ['nullable', 'file', 'mimes:pdf', 'max:5120'], 'face' => ['nullable', 'image', 'max:4096'], 'about_photo' => ['nullable', 'image', 'max:4096']];
        foreach (['role' => [true, 255], 'intro' => [false, 1000], 'about' => [false, 10000], 'work_mode' => [false, 255], 'face_alt' => [false, 255], 'about_alt' => [false, 255]] as $name => [$req, $max]) {
            foreach ($t($req, $max) as $suffix => $r) {
                $rules[$name.$suffix] = $r;
            }
        }
        $data = $request->validate($rules);

        $clean = fn ($v) => array_filter(['fr' => $v['fr'] ?? null, 'en' => $v['en'] ?? null], fn ($x) => filled($x));
        $split = fn ($text) => array_values(array_filter(array_map('trim', preg_split("/\R\s*\R/", (string) $text))));

        $images = Profile::get('images', []);
        if ($request->hasFile('face')) {
            $images['face'] = ResourceController::storeUpload($request->file('face'), 'profil');
        }
        if ($request->hasFile('about_photo')) {
            $images['about'] = ResourceController::storeUpload($request->file('about_photo'), 'profil');
        }
        $images['face_alt'] = $clean($data['face_alt'] ?? []);
        $images['about_alt'] = $clean($data['about_alt'] ?? []);

        Profile::save([
            'name' => $data['name'],
            'role' => $clean($data['role']),
            'intro' => $clean($data['intro'] ?? []),
            'about' => array_filter(['fr' => $split($data['about']['fr'] ?? ''), 'en' => $split($data['about']['en'] ?? '')]),
            'location' => $data['location'] ?? null,
            'work_mode' => $clean($data['work_mode'] ?? []),
            'email' => $data['email'] ?? null,
            'github' => $data['github'] ?? null,
            'linkedin' => $data['linkedin'] ?? null,
            'cv' => $request->hasFile('cv') ? ResourceController::storeUpload($request->file('cv'), 'cv') : Profile::get('cv'),
            'images' => $images,
        ]);

        return back()->with('success', 'Profil enregistré.');
    }
}
