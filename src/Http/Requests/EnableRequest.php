<?php

namespace Anfragen\TwoFactor\Http\Requests;

use Anfragen\TwoFactor\Enum\TwoFactorType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\{Rule, ValidationException};

class EnableRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $types = collect(TwoFactorType::cases())->map(fn ($type) => $type->value)->toArray();

        return [
            'type' => [
                'required',
                'string',
                Rule::in($types),
            ]
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    public function passedValidation(): void
    {
        if ($this->type === TwoFactorType::SMS->value && is_null($this->user()->phone)) {
            throw ValidationException::withMessages([
                'type' => trans('anfragen::two-factor.error-phone'),
            ]);
        }
    }
}
