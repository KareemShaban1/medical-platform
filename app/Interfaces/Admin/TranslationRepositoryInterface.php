<?php

namespace App\Interfaces\Admin;

interface TranslationRepositoryInterface
{
    public function getAllTranslations($locale = 'en', $search = null);
    public function getTranslationFiles();
    public function updateTranslation($locale, $key, $value, $file = null);
    public function addTranslation($locale, $key, $value, $file = null);
    public function deleteTranslation($locale, $key, $file = null);
    public function getTranslationByKey($locale, $key, $file = null);
}


