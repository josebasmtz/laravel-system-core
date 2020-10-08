<?php


namespace Josebasmtz\SystemCore\Interfaces;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface IRepository
{
    public function create(array $data);
    public function first(array $columns = ['*']);
    public function find($key, array $columns = ['*']);

    /**
     * @param array $columns
     * @return Collection|null
     */
    public function all(array $columns = ['*']);

    /**
     * @param $key
     * @param array $data
     * @return bool|null
     */
    public function update($key, array $data);

    /**
     * @param null $key
     * @return bool|null
     */
    public function delete($key = null);

    /**
     * @return Builder
     */
    public function applyCriteria();

    /**
     * @param ICriteria|\Closure $criteria
     */
    public function pushCriteria($criteria);
    public function clearCriteria();
}