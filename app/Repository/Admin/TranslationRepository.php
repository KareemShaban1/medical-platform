<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\TranslationRepositoryInterface;
use Illuminate\Support\Facades\File;

class TranslationRepository implements TranslationRepositoryInterface
{
    public function getAllTranslations($locale = 'en', $search = null)
    {
        $translations = [];
        $langPath = lang_path($locale);

        // Get PHP translation files
        if (File::exists($langPath) && File::isDirectory($langPath)) {
            $phpFiles = File::files($langPath);
            foreach ($phpFiles as $file) {
                if ($file->getExtension() === 'php') {
                    $fileName = $file->getFilenameWithoutExtension();
                    $fileTranslations = include $file->getPathname();

                    if (is_array($fileTranslations)) {
                        $flattened = $this->flattenArray($fileTranslations);
                        $filePath = str_replace(base_path(), '', $file->getPathname());
                        foreach ($flattened as $key => $value) {
                            $fullKey = $fileName.'.'.$key;
                            if ($this->matchesSearch($fullKey, $value, $search, $filePath)) {
                                $translations[] = [
                                    'key' => $fullKey,
                                    'value' => $value,
                                    'file' => $fileName,
                                    'file_path' => $filePath,
                                    'type' => 'php',
                                ];
                            }
                        }
                    }
                }
            }
        }

        // Get JSON translation file
        $jsonPath = lang_path("{$locale}.json");
        if (File::exists($jsonPath)) {
            $jsonTranslations = json_decode(File::get($jsonPath), true);
            $jsonFilePath = str_replace(base_path(), '', $jsonPath);
            if (is_array($jsonTranslations)) {
                foreach ($jsonTranslations as $key => $value) {
                    if ($this->matchesSearch($key, $value, $search, $jsonFilePath)) {
                        $translations[] = [
                            'key' => $key,
                            'value' => $value,
                            'file' => null,
                            'file_path' => $jsonFilePath,
                            'type' => 'json',
                        ];
                    }
                }
            }
        }

        return $translations;
    }

    public function getTranslationFiles()
    {
        $files = [];
        $locales = ['en', 'ar']; // Add more locales as needed

        foreach ($locales as $locale) {
            $langPath = lang_path($locale);
            if (File::exists($langPath) && File::isDirectory($langPath)) {
                $phpFiles = File::files($langPath);
                foreach ($phpFiles as $file) {
                    if ($file->getExtension() === 'php') {
                        $files[] = [
                            'locale' => $locale,
                            'file' => $file->getFilenameWithoutExtension(),
                            'path' => $file->getPathname(),
                        ];
                    }
                }
            }

            // Check for JSON file
            $jsonPath = lang_path("{$locale}.json");
            if (File::exists($jsonPath)) {
                $files[] = [
                    'locale' => $locale,
                    'file' => null,
                    'path' => $jsonPath,
                    'type' => 'json',
                ];
            }
        }

        return $files;
    }

    public function updateTranslation($locale, $key, $value, $file = null)
    {
        if ($file) {
            // PHP file translation
            $langPath = lang_path("{$locale}/{$file}.php");
            if (! File::exists($langPath)) {
                throw new \Exception("Translation file not found: {$langPath}");
            }

            $translations = include $langPath;
            if (! is_array($translations)) {
                $translations = [];
            }

            // Handle nested keys - remove file prefix if present
            $keyParts = explode('.', $key);
            if ($keyParts[0] === $file) {
                array_shift($keyParts);
                $key = implode('.', $keyParts);
            }

            // Handle nested keys (e.g., "about.title")
            $keys = explode('.', $key);
            $lastKey = array_pop($keys);
            $current = &$translations;

            foreach ($keys as $k) {
                if (! isset($current[$k]) || ! is_array($current[$k])) {
                    $current[$k] = [];
                }
                $current = &$current[$k];
            }

            $current[$lastKey] = $value;

            // Format and save the file
            $this->savePhpFile($langPath, $translations);
        } else {
            // JSON file translation
            $jsonPath = lang_path("{$locale}.json");
            if (! File::exists($jsonPath)) {
                File::put($jsonPath, json_encode(new \stdClass, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }

            $translations = json_decode(File::get($jsonPath), true);
            if (! is_array($translations)) {
                $translations = [];
            }

            $translations[$key] = $value;
            ksort($translations);

            File::put(
                $jsonPath,
                json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
            );
        }

        return true;
    }

    public function addTranslation($locale, $key, $value, $file = null)
    {
        if ($file) {
            // PHP file translation
            $langPath = lang_path("{$locale}/{$file}.php");

            if (! File::exists(dirname($langPath))) {
                File::makeDirectory(dirname($langPath), 0755, true);
            }

            $translations = File::exists($langPath) ? include $langPath : [];
            if (! is_array($translations)) {
                $translations = [];
            }

            // Handle nested keys - remove file prefix if present
            $keyParts = explode('.', $key);
            if ($keyParts[0] === $file) {
                array_shift($keyParts);
                $key = implode('.', $keyParts);
            }

            // Handle nested keys
            $keys = explode('.', $key);
            $lastKey = array_pop($keys);
            $current = &$translations;

            foreach ($keys as $k) {
                if (! isset($current[$k]) || ! is_array($current[$k])) {
                    $current[$k] = [];
                }
                $current = &$current[$k];
            }

            if (! isset($current[$lastKey])) {
                $current[$lastKey] = $value;
                $this->savePhpFile($langPath, $translations);
            }
        } else {
            // JSON file translation
            $jsonPath = lang_path("{$locale}.json");

            if (! File::exists(lang_path())) {
                File::makeDirectory(lang_path(), 0755, true);
            }

            $translations = File::exists($jsonPath)
                ? json_decode(File::get($jsonPath), true)
                : [];

            if (! is_array($translations)) {
                $translations = [];
            }

            if (! isset($translations[$key])) {
                $translations[$key] = $value;
                ksort($translations);
                File::put(
                    $jsonPath,
                    json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                );
            }
        }

        return true;
    }

    public function deleteTranslation($locale, $key, $file = null)
    {
        if ($file) {
            // PHP file translation
            $langPath = lang_path("{$locale}/{$file}.php");
            if (! File::exists($langPath)) {
                return false;
            }

            $translations = include $langPath;
            if (! is_array($translations)) {
                return false;
            }

            // Handle nested keys - remove file prefix if present
            $keyParts = explode('.', $key);
            if ($keyParts[0] === $file) {
                array_shift($keyParts);
                $key = implode('.', $keyParts);
            }

            // Handle nested keys
            $keys = explode('.', $key);
            $lastKey = array_pop($keys);
            $current = &$translations;

            foreach ($keys as $k) {
                if (! isset($current[$k]) || ! is_array($current[$k])) {
                    return false;
                }
                $current = &$current[$k];
            }

            if (isset($current[$lastKey])) {
                unset($current[$lastKey]);
                $this->savePhpFile($langPath, $translations);

                return true;
            }
        } else {
            // JSON file translation
            $jsonPath = lang_path("{$locale}.json");
            if (! File::exists($jsonPath)) {
                return false;
            }

            $translations = json_decode(File::get($jsonPath), true);
            if (! is_array($translations)) {
                return false;
            }

            if (isset($translations[$key])) {
                unset($translations[$key]);
                File::put(
                    $jsonPath,
                    json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                );

                return true;
            }
        }

        return false;
    }

    public function getTranslationByKey($locale, $key, $file = null)
    {
        if ($file) {
            // PHP file translation
            $langPath = lang_path("{$locale}/{$file}.php");
            if (! File::exists($langPath)) {
                return null;
            }

            $translations = include $langPath;
            if (! is_array($translations)) {
                return null;
            }

            // Handle nested keys
            $keys = explode('.', $key);
            $value = $translations;

            foreach ($keys as $k) {
                if (! isset($value[$k])) {
                    return null;
                }
                $value = $value[$k];
            }

            return $value;
        } else {
            // JSON file translation
            $jsonPath = lang_path("{$locale}.json");
            if (! File::exists($jsonPath)) {
                return null;
            }

            $translations = json_decode(File::get($jsonPath), true);
            if (! is_array($translations)) {
                return null;
            }

            return $translations[$key] ?? null;
        }
    }

    /**
     * Flatten nested array to dot notation
     */
    private function flattenArray($array, $prefix = '')
    {
        $result = [];
        foreach ($array as $key => $value) {
            $newKey = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    /**
     * Check if translation matches search query
     */
    private function matchesSearch($key, $value, $search = null, $filePath = null)
    {
        if (empty($search)) {
            return true;
        }

        $search = strtolower($search);
        $keyMatch = str_contains(strtolower($key), $search);
        $valueMatch = is_string($value) && str_contains(strtolower($value), $search);
        $filePathMatch = $filePath && str_contains(strtolower($filePath), $search);

        return $keyMatch || $valueMatch || $filePathMatch;
    }

    /**
     * Save PHP translation file with proper formatting
     */
    private function savePhpFile($path, $array)
    {
        $content = "<?php\n\nreturn ".$this->varExport($array).";\n";
        File::put($path, $content);
    }

    /**
     * Export array with proper formatting
     */
    private function varExport($var, $indent = '')
    {
        switch (gettype($var)) {
            case 'string':
                return "'".addcslashes($var, "\\\$\"\r\n\t\v\f")."'";
            case 'array':
                $indexed = array_keys($var) === range(0, count($var) - 1);
                $r = [];
                foreach ($var as $key => $value) {
                    $r[] = $indent.'    '
                        .($indexed ? '' : $this->varExport($key).' => ')
                        .$this->varExport($value, $indent.'    ');
                }

                return "[\n".implode(",\n", $r)."\n".$indent.']';
            case 'boolean':
                return $var ? 'true' : 'false';
            case 'NULL':
                return 'null';
            default:
                return var_export($var, true);
        }
    }
}
