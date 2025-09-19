<?php

namespace App\Transformers;

use App\Models\History;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user): array
    {
        return $user->toArray();
    }
}
