<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Interfaces\Admin\TranslationRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TranslationController extends Controller
{
    protected $translationRepo;

    public function __construct(TranslationRepositoryInterface $translationRepo)
    {
        $this->translationRepo = $translationRepo;
    }

    public function index(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('view translations'), 403, __('You are not authorized to view translations'));
        $locale = $request->get('locale', 'en');
        $search = $request->get('search');
        $translations = $this->translationRepo->getAllTranslations($locale, $search);
        $locales = ['en', 'ar']; // Add more locales as needed

        return view('backend.dashboards.admin.pages.translations.index', compact('translations', 'locale', 'locales', 'search'));
    }

    public function data(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('view translations'), 403, __('You are not authorized to view translations'));
        $locale = $request->get('locale', 'en');
        $search = $request->get('search');
        $translations = $this->translationRepo->getAllTranslations($locale, $search);

        // Format for DataTables
        $data = collect($translations)->map(function ($translation) {
            return [
                'key' => $translation['key'],
                'value' => $translation['value'],
                'file' => $translation['file'],
                'file_path' => $translation['file_path'] ?? null,
                'type' => $translation['type'],
                'actions' => $this->getActionsHtml($translation),
            ];
        });

        return response()->json([
            'data' => $data->values()->all(),
        ]);
    }

    private function getActionsHtml($translation)
    {
        $keyJson = htmlspecialchars(json_encode($translation['key']), ENT_QUOTES, 'UTF-8');
        $valueJson = htmlspecialchars(json_encode($translation['value']), ENT_QUOTES, 'UTF-8');
        $fileJson = htmlspecialchars(json_encode($translation['file']), ENT_QUOTES, 'UTF-8');
        $typeJson = htmlspecialchars(json_encode($translation['type']), ENT_QUOTES, 'UTF-8');

        $html = '<div class="d-flex gap-2">';
        $html .= '<button onclick="editTranslation('.$keyJson.', '.$valueJson.', '.$fileJson.', '.$typeJson.')" class="btn btn-sm btn-info" title="Edit">';
        $html .= '<i class="mdi mdi-pencil"></i>';
        $html .= '</button>';
        $html .= '<button onclick="deleteTranslation('.$keyJson.', '.$fileJson.')" class="btn btn-sm btn-danger" title="Delete">';
        $html .= '<i class="mdi mdi-delete"></i>';
        $html .= '</button>';
        $html .= '</div>';

        return $html;
    }

    public function scan(Request $request)
    {
        try {
            // Run the scan command
            Artisan::call('translations:scan', [
                '--locales' => 'en,ar',
                '--path' => 'resources/views,app/Http/Controllers',
            ]);

            $output = Artisan::output();

            return response()->json([
                'success' => true,
                'message' => __('Translations scanned successfully. Check the output for details.'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to scan translations: ').$e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('update translations'), 403, __('You are not authorized to update translations'));
        $request->validate([
            'locale' => 'required|string',
            'key' => 'required|string',
            'value' => 'required|string',
            'file' => 'nullable|string',
        ]);

        try {
            $this->translationRepo->updateTranslation(
                $request->locale,
                $request->key,
                $request->value,
                $request->file
            );

            return response()->json([
                'success' => true,
                'message' => __('Translation updated successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to update translation: ').$e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('create translations'), 403, __('You are not authorized to add translations'));
        $request->validate([
            'locale' => 'required|string',
            'key' => 'required|string',
            'value' => 'required|string',
            'file' => 'nullable|string',
        ]);

        try {
            $this->translationRepo->addTranslation(
                $request->locale,
                $request->key,
                $request->value,
                $request->file
            );

            return response()->json([
                'success' => true,
                'message' => __('Translation added successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to add translation: ').$e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('delete translations'), 403, __('You are not authorized to delete translations'));
        $request->validate([
            'locale' => 'required|string',
            'key' => 'required|string',
            'file' => 'nullable|string',
        ]);

        try {
            $deleted = $this->translationRepo->deleteTranslation(
                $request->locale,
                $request->key,
                $request->file
            );

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => __('Translation deleted successfully'),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('Translation not found'),
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to delete translation: ').$e->getMessage(),
            ], 500);
        }
    }
}