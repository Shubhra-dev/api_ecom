<?php

namespace App\Traits;

use App\Models\Version;
use App\Models\Volume;
use Illuminate\Database\Eloquent\Relations\MorphTo;

trait WithProductRelations
{
    //
    public function scopeNameRelations($query)
    {
        return $query
            ->with(['productable' => function (MorphTo $morphTo) {
                $morphTo->constrain([
                    Volume::class => function ($query) {
                        $query->with([
                            'version.production',
                            // 'version.volumes:id,version_id',
                        ]);
                    },
                    Version::class => function ($query) {
                        $query->with([
                            'volumes',
                            'production'
                        ]);
                    },
                ]);
            }]);
    }

    public function scopeWithMorphTo($query, $relation, array $constrains)

    {

        // $constrains = [
        //     Volume::class => [
        //         'version.production.publisher:id,name',
        //         'version.volumes:id,version_id',
        //         'version.moderators:id,author_id,moderator_type,version_id',
        //         'version.moderators.moderators_type:id,name',
        //         'version.moderators.author:id,name'
        //     ],
        //     Version::class => [
        //         'moderators:id,author_id,moderator_type,version_id',
        //         'moderators.moderators_type:id,name',
        //         'moderators.author:id,name',
        //         'volumes',
        //         'production.publisher:id,name'
        //     ]
        // ];


        $query->with($relation, function (MorphTo $morphTo) use ($constrains) {

            array_walk($constrains, function (&$constrain) {
                $constrain = function ($query) use ($constrain) {
                    $query->with($constrain);
                };
            });

            $morphTo->constrain($constrains);
        });
    }
}
