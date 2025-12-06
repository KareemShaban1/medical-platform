<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ScanTranslations extends Command
{
    protected $signature = 'translations:scan
        {--path= : Path(s) to scan, comma separated (default: resources/views,app/Http/Controllers)}
        {--locales= : Comma-separated locales, e.g. en,ar}
        {--translate : Enable auto translation via Google API (experimental)}
        {--ignore= : Comma-separated paths to ignore (e.g. views,controllers)}
        {--overwrite : Overwrite existing translations with new ones}';

    protected $description = 'Scan Blade & PHP files recursively and update translation files';

    public function handle()
    {
        $paths = $this->option('path')
            ? explode(',', $this->option('path'))
            : ['resources/views', 'app/Http/Controllers'];

        $ignore = $this->option('ignore')
            ? explode(',', $this->option('ignore'))
            : [];

        $locales = $this->option('locales')
            ? explode(',', $this->option('locales'))
            : ['en'];

        foreach ($paths as $path) {
            $path = trim($path);

            foreach ($ignore as $skip) {
                if (str_contains($path, trim($skip))) {
                    $this->warn("⏭️ Skipping {$path} (ignored)");
                    continue 2;
                }
            }

            $this->info("🔎 Scanning files in: " . base_path($path));

            $keys = $this->extractKeys(base_path($path));
            $this->info("📂 Found " . count($keys) . " translation keys...");

            foreach ($locales as $locale) {
                $this->updateTranslations($keys, $locale);
            }
        }

        return Command::SUCCESS;
    }

    private function extractKeys(string $path): array
    {
        $keys = [];
        $files = File::allFiles($path);

        $this->info("📂 Found " . count($files) . " files to scan...");
        $bar = $this->output->createProgressBar(count($files));
        $bar->setFormat("  Scanning [<fg=cyan>%bar%</>] %current%/%max% files");

        foreach ($files as $file) {
            $content = $file->getContents();

            // Extract translation keys in various formats
            // Format 1: __('key') or __('key.subkey')
            preg_match_all("/__\\(['\"](.+?)['\"]\\)/", $content, $matches1);
            
            // Format 2: @lang('key') or @lang('key.subkey')
            preg_match_all("/@lang\\(['\"](.+?)['\"]\\)/", $content, $matches2);
            
            // Format 3: trans('key') or trans('key.subkey')
            preg_match_all("/trans\\(['\"](.+?)['\"]\\)/", $content, $matches3);
            
            // Format 4: Lang::get('key') or Lang::get('key.subkey')
            preg_match_all("/Lang::get\\(['\"](.+?)['\"]\\)/", $content, $matches4);
            
            // Format 5: {{ __('key') }} in blade templates
            preg_match_all("/\\{\\{\\s*__\\(['\"](.+?)['\"]\\)\\s*\\}\\}/", $content, $matches5);
            
            // Format 6: {{ @lang('key') }} in blade templates
            preg_match_all("/\\{\\{\\s*@lang\\(['\"](.+?)['\"]\\)\\s*\\}\\}/", $content, $matches6);
            
            // Format 7: {{ trans('key') }} in blade templates
            preg_match_all("/\\{\\{\\s*trans\\(['\"](.+?)['\"]\\)\\s*\\}\\}/", $content, $matches7);

            $matches = array_merge(
                $matches1[1] ?? [],
                $matches2[1] ?? [],
                $matches3[1] ?? [],
                $matches4[1] ?? [],
                $matches5[1] ?? [],
                $matches6[1] ?? [],
                $matches7[1] ?? []
            );

            foreach ($matches as $match) {
                // Clean up the key (remove whitespace)
                $cleanKey = trim($match);
                if (!empty($cleanKey)) {
                    $keys[] = $cleanKey;
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        return array_unique($keys);
    }

    private function updateTranslations(array $keys, string $locale): void
    {
        $phpGrouped = [];
        $jsonKeys   = [];
    
        foreach ($keys as $fullKey) {
            if (str_contains($fullKey, '.')) {
                [$file, $key] = explode('.', $fullKey, 2);
    
                if (trim($key) === '') {
                    $jsonKeys[] = $fullKey;
                    continue;
                }
    
                $phpGrouped[$file][] = $key;
            } else {
                $jsonKeys[] = $fullKey;
            }
        }
    
        // ---- Handle PHP files (file.key style) ----
        foreach ($phpGrouped as $file => $fileKeys) {
            $langPath = lang_path("{$locale}/{$file}.php");
    
            if (!File::exists(dirname($langPath))) {
                File::makeDirectory(dirname($langPath), 0755, true);
            }
    
            $translations = File::exists($langPath) ? include $langPath : [];
            if (!is_array($translations)) {
                $translations = [];
            }
    
            $new = 0;
            $bar = $this->output->createProgressBar(count($fileKeys));
            $bar->setFormat("  Translating {$file}.php [<fg=green>%bar%</>] %current%/%max% keys");
    
            foreach ($fileKeys as $key) {
                $safeKey = $this->makeSafeKey($key);
    
                $shouldTranslate = !isset($translations[$safeKey]) 
                    || $this->option('overwrite') 
                    || $this->needsTranslation($translations[$safeKey], $locale);
    
                if ($shouldTranslate) {
                    $translations[$safeKey] = $this->option('translate')
                        ? $this->translate($key, $locale)
                        : $this->cleanKey($key);
                    $new++;
                }
    
                $bar->advance();
            }
    
            $bar->finish();
            $this->newLine();
    
            ksort($translations);
    
            File::put(
                $langPath,
                "<?php\n\nreturn " . var_export($translations, true) . ";\n"
            );
    
            $this->info("✅ Updated {$langPath} with {$new} new/updated keys.");
        }
    
        // ---- Handle JSON ----
        if (!empty($jsonKeys)) {
            $jsonPath = lang_path("{$locale}.json");
    
            // Only update if JSON file exists, don't create new files
            if (!File::exists($jsonPath)) {
                $this->warn("⏭️ Skipping {$locale}.json (file does not exist, only updating existing files)");
            } else {
                $translations = json_decode(File::get($jsonPath), true);
        
                if (!is_array($translations)) {
                    $translations = [];
                }
        
                $new = 0;
                $bar = $this->output->createProgressBar(count($jsonKeys));
                $bar->setFormat("  Translating {$locale}.json [<fg=yellow>%bar%</>] %current%/%max% keys");
        
                foreach ($jsonKeys as $key) {
                    $shouldTranslate = !isset($translations[$key]) 
                        || $this->option('overwrite') 
                        || $this->needsTranslation($translations[$key], $locale);
        
                    if ($shouldTranslate) {
                        $translations[$key] = $this->option('translate')
                            ? $this->translate($key, $locale)
                            : $key;
                        $new++;
                    }
        
                    $bar->advance();
                }
        
                $bar->finish();
                $this->newLine();
        
                ksort($translations);
        
                File::put(
                    $jsonPath,
                    json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                );
        
                $this->info("✅ Updated {$jsonPath} with {$new} new/updated keys.");
            }
        }
    }
    




    private function makeSafeKey(string $key): string
    {
        // If it's a long sentence → slugify as safe key
        if (preg_match('/\s/', $key)) {
            return \Illuminate\Support\Str::slug($key, '_');
        }

        return $key;
    }




    private function cleanKey(string $key): string
    {
        $last = $key;

        if (str_contains($key, '.')) {
            $parts = explode('.', $key);
            $last = end($parts);
        }

        if (str_contains($last, '/')) {
            $parts = explode('/', $last);
            $last = end($parts);
        }

        $last = str_replace('_', ' ', $last);

        return trim(ucwords($last));
    }

    private function translate(string $key, string $locale): string
    {
        $text = $this->cleanKey($key);

        if ($locale === 'en') {
            return $text;
        }

        try {
            $url = "https://translate.googleapis.com/translate_a/single?client=gtx&sl=en&tl={$locale}&dt=t&q=" . urlencode($text);

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                curl_close($ch);
                return $text;
            }

            curl_close($ch);

            $result = json_decode($response, true);

            return $result[0][0][0] ?? $text;
        } catch (\Exception $e) {
            return $text;
        }
    }

    private function needsTranslation($value, string $locale): bool
{
    // Case 1: Empty or null
    if ($value === null || $value === '') {
        return true;
    }

    // Case 2: Not a string (bool, int, array, etc.)
    if (!is_string($value)) {
        return true;
    }

    // Case 3: Wrong language check
    // Example: If Arabic locale, but text looks like English letters
    if ($locale === 'ar' && preg_match('/^[a-zA-Z0-9\s]+$/', $value)) {
        return true;
    }

    if ($locale === 'en' && preg_match('/[\p{Arabic}]/u', $value)) {
        return true;
    }

    return false;
}

}