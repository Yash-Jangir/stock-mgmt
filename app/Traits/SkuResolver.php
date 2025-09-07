<?php

namespace App\Traits;

use App\Models\{
    Product,
    Sku,
};

trait SkuResolver {
    private function resolveInstance($id)
    {
        $ids = explode("|", $id);
        if (count($ids) !== 2) return null;

        $id1 = explode(":", $ids[0]);
        $id2 = explode(":", $ids[1]);

        if (count($id1) !== 2 && count($id2) !== 2) return null;

        if ($id1[0] === $id2[0] && $id1[0] === 'p') {
            $model = Product::class;
            $id    = $id1[1];
        } else if ($id2[0] === 's') {
            $model = Sku::class;
            $id    = $id2[1];
        }

        $instance = $model::where('user_id', auth()->id())->where('is_active', 1)->find($id);
        if ($instance && $instance instanceof Product) {
            $instance->load('skus');
            $instance = $instance->skus->count() ? null : $instance;
        }

        return $instance;
    }
}