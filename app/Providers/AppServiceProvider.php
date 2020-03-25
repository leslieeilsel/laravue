<?php

namespace App\Providers;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 给 Eloquent 的 whereHas 加个 where in 的优化
        // 原文链接：https://learnku.com/articles/13708/optimization-of-eloquents-wherehas-plus-where-in
        Builder::macro('whereHasIn', function ($builder, $relationName, callable $callable) {
            $relationNames = explode('.', $relationName);
            $nextRelation = implode('.', array_slice($relationNames, 1));

            $method = $relationNames[0];
            /** @var Relations\BelongsTo|Relations\HasOne $relation */
            $relation = Relations\Relation::noConstraints(function () use ($method) {
                return $this->$method();
            });

            /** @var Builder $in */
            $in = $relation->getQuery()->whereHasIn($nextRelation, $callable);

            if ($relation instanceof Relations\BelongsTo) {
                return $builder->whereIn($relation->getForeignKey(), $in->select($relation->getOwnerKey()));
            } elseif ($relation instanceof Relations\HasOne) {
                return $builder->whereIn($this->getKeyName(), $in->select($relation->getForeignKeyName()));
            }

            throw new Exception(__METHOD__." 不支持 ".get_class($relation));
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
