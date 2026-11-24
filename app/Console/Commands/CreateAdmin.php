<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

/** Crée (ou met à jour) le compte de l'admin. Le mot de passe est saisi de façon masquée. */
class CreateAdmin extends Command
{
    protected $signature = 'portfolio:admin {email : Adresse e-mail de connexion}';

    protected $description = "Crée ou met à jour le compte d'accès à l'admin du portfolio";

    public function handle(): int
    {
        $email = $this->argument('email');
        $password = $this->secret('Mot de passe (12 caractères minimum)');
        $confirm = $this->secret('Confirmez le mot de passe');

        $validator = Validator::make(
            ['email' => $email, 'password' => $password, 'password_confirmation' => $confirm],
            ['email' => ['required', 'email'], 'password' => ['required', 'string', 'min:12', 'confirmed']],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::updateOrCreate(['email' => $email], ['name' => 'Admin', 'password' => Hash::make($password)]);
        $this->info("Compte admin prêt : {$email}. Connexion sur /".config('portfolio.admin_path'));

        return self::SUCCESS;
    }
}
