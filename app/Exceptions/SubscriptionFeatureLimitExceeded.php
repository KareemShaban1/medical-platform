<?php

namespace App\Exceptions;

use Exception;

class SubscriptionFeatureLimitExceeded extends Exception
{
    protected $featureCode;

    public function __construct(string $message = "", string $featureCode = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->featureCode = $featureCode;
    }

    public function getFeatureCode(): string
    {
        return $this->featureCode;
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => $this->getMessage(),
                'error_type' => 'feature_limit_exceeded',
                'feature_code' => $this->featureCode,
            ], 403);
        }

        return redirect()->back()
            ->with('error', $this->getMessage())
            ->with('upgrade_required', true)
            ->with('feature_code', $this->featureCode);
    }
}

