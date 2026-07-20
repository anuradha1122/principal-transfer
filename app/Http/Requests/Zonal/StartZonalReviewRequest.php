<?php

namespace App\Http\Requests\Zonal;

use App\Models\TransferApplication;
use Illuminate\Foundation\Http\FormRequest;

class StartZonalReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        $transferApplication = $this->route('transferApplication');

        return $transferApplication instanceof TransferApplication
            && $this->user()?->can(
                'startZonalReview',
                $transferApplication
            );
    }

    public function rules(): array
    {
        return [];
    }
}
