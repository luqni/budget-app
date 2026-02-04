<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class YearMonthFormat implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // Validate format: YYYY-MM (e.g., 2026-01, 2026-12)
        return preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Format bulan tidak valid. Gunakan format YYYY-MM (contoh: 2026-01).';
    }
}
