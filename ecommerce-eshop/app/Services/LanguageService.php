<?php

namespace App\Services;

use App\Models\Language;

class LanguageService
{
    public function getAll()
    {
        return Language::orderBy('is_default', 'desc')->get()->map(fn($l) => (object) $l->toArray());
    }

    public function find($id)
    {
        $l = Language::find($id);
        return $l ? (object) $l->toArray() : null;
    }

    public function findByCode($code)
    {
        $l = Language::where('code', $code)->first();
        return $l ? (object) $l->toArray() : null;
    }

    public function getDefault()
    {
        $l = Language::where('is_default', true)->first() ?? Language::first();
        return $l ? (object) $l->toArray() : null;
    }

    public function getActive()
    {
        return Language::where('status', 1)->get()->map(fn($l) => (object) $l->toArray());
    }
}
