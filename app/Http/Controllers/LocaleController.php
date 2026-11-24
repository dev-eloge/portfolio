<?php

namespace App\Http\Controllers;

use App\Support\Localize;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function __invoke(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, Localize::LOCALES, true), 404);

        $request->session()->put('locale', $locale);

        // Retour sur la même page (uniquement une URL du site)
        $previous = url()->previous();
        $target = str_starts_with($previous, url('/')) ? $previous : url('/');

        return redirect()->to($target);
    }
}
