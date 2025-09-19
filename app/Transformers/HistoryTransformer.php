<?php

namespace App\Transformers;

use App\Models\History;
use League\Fractal\TransformerAbstract;

class HistoryTransformer extends TransformerAbstract
{
    public function transform(History $history): array
    {
        return [
            'id' => $history->id,
            'slave_id' => $history->slave_id,
            'com_port' => $history->com_port,
            'kw' => $history->kw ? $history->kw : $history->kw_draft,
            'kw_draft' => $history->kw_draft,
            'created_at' => $history->created_at?->format('Y-m-d H:i:s'),
            'created_at_formated' => $history->created_at?->format('H:i d/m/Y'),
            'datetime' => $history->created_at?->format('Y-m-d H:i:s'),
            'datetime_formated' => $history->created_at?->format('H:i d/m/Y'),
        ];
    }
}
