<?php


namespace Josebasmtz\SystemCore\Interfaces;


use Illuminate\Database\Query\Builder;

interface ICriteria
{
    /**
     * @param Builder|mixed $model
     * @return Builder|mixed
     */
    public function apply($model);
}