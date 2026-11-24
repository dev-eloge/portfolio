<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Rend les contenus traduisibles (FR/EN) : chaque champ devient un JSON
 * {"fr": "...", "en": "..."}. Les textes existants sont conservés en "fr".
 */
return new class extends Migration
{
    private array $fields = [
        'projects' => ['title', 'category', 'description', 'context', 'problem', 'solution', 'result', 'image_alt'],
        'skills' => ['name', 'category'],
        'experiences' => ['title', 'type', 'description'],
    ];

    public function up(): void
    {
        foreach ($this->fields as $table => $columns) {
            // 1. Texte libre (MariaDB refuse un JSON invalide dans une colonne json)
            $this->changeColumns($table, $columns, 'text');

            // 2. Conversion des valeurs existantes en {"fr": "..."}
            DB::table($table)->orderBy('id')->each(function ($row) use ($table, $columns) {
                $update = [];
                foreach ($columns as $column) {
                    if ($row->{$column} !== null && ! $this->isTranslationJson($row->{$column})) {
                        $update[$column] = json_encode(['fr' => $row->{$column}], JSON_UNESCAPED_UNICODE);
                    }
                }
                if ($update) {
                    DB::table($table)->where('id', $row->id)->update($update);
                }
            });

            // 3. Type json
            $this->changeColumns($table, $columns, 'json');
        }
    }

    public function down(): void
    {
        foreach ($this->fields as $table => $columns) {
            $this->changeColumns($table, $columns, 'text');

            DB::table($table)->orderBy('id')->each(function ($row) use ($table, $columns) {
                $update = [];
                foreach ($columns as $column) {
                    if ($row->{$column} !== null && $this->isTranslationJson($row->{$column})) {
                        $update[$column] = json_decode($row->{$column}, true)['fr'] ?? null;
                    }
                }
                if ($update) {
                    DB::table($table)->where('id', $row->id)->update($update);
                }
            });
        }
    }

    private function changeColumns(string $table, array $columns, string $type): void
    {
        Schema::table($table, function (Blueprint $t) use ($columns, $type) {
            foreach ($columns as $column) {
                $t->{$type}($column)->nullable()->change();
            }
        });
    }

    private function isTranslationJson(string $value): bool
    {
        $decoded = json_decode($value, true);

        return is_array($decoded) && array_key_exists('fr', $decoded);
    }
};
