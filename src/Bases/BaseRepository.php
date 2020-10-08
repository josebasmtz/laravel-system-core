<?php


namespace Josebasmtz\SystemCore\Bases;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Josebasmtz\SystemCore\Interfaces\ICriteria;
use Josebasmtz\SystemCore\Interfaces\IRepository;

abstract class BaseRepository implements IRepository
{
    /**
     * @var string
     */
    protected $modelClassName;
    /**
     * @var Collection
     */
    protected $criterias;

    public function __construct()
    {
        $this->criterias = new Collection();
    }

    public function create(array $data)
    {
        $response = false;
        try {
            $response = $this->table()->create($data);
        }
        catch (\Throwable $e){
            \Log::error($e);
        }
        return $response;
    }

    public function first(array $columns = ['*'])
    {
        $response = null;
        try {
            $model = $this->applyCriteria();
            $response = $model->first($columns);
        }
        catch (\Throwable $e) {
            \Log::error($e);
        }
        return $response;
    }

    public function find($key, array $columns = ['*'])
    {
        $response = null;
        try {
            $model = $this->applyCriteria();
            $response = $model->find($key, $columns);
        }
        catch (\Throwable $e) {
            \Log::error($e);
        }
        return $response;
    }

    /**
     * @param array $columns
     * @return Collection|null
     */
    public function all(array $columns = ['*'])
    {
        $response = null;
        try {
            $model = $this->applyCriteria();
            $response = $model->get($columns);
        }
        catch (\Throwable $e) {
            \Log::error($e);
        }
        return $response;
    }

    /**
     * @param $key
     * @param array $data
     * @return bool|null
     */
    public function update($key, array $data)
    {
        $response = null;
        try {
            /**
             * @var Model $model
             */
            $model = $this->find($key);
            if ($model === null) {
                throw new \Exception("The model doesn't exists");
            }
            $response = $model->update($data);
        }
        catch (\Throwable $e)
        {
            \Log::error($e);
        }
        return $response;
    }

    /**
     * @param null $key
     * @return bool|null
     */
    public function delete($key = null)
    {
        $response = null;
        if ($key === null)
        {
            try {
                $model = $this->applyCriteria();
                $deleted = $model->delete();
                $response = $deleted > 0;
            }
            catch (\Throwable $e)
            {
                \Log::error($e);
            }
            return $response;
        }

        try {
            /**
             * @var Model $model
             */
            $model = $this->find($key);
            if ($model === null) {
                throw new \Exception("The model doesn't exists");
            }
            $response = $model->delete();
        }
        catch (\Throwable $e)
        {
            \Log::error($e);
        }
        return $response;
    }

    /**
     * @return Model|Builder
     */
    protected function table()
    {
        return new $this->modelClassName();
    }

    /**
     * @return Builder|mixed
     */
    public function applyCriteria()
    {
        $model = $this->table();
        foreach ($this->criterias as $criteria)
        {
            if ($criteria instanceof ICriteria)
            {
                $model = $criteria->apply($model);
                continue;
            }

            if ($criteria instanceof \Closure)
            {
                $model = $criteria($model);
                continue;
            }
        }
        return $model;
    }

    /**
     * @param ICriteria|\Closure $criteria
     */
    public function pushCriteria($criteria)
    {
        if ($criteria instanceof ICriteria || $criteria instanceof \Closure)
        {
            $this->criterias->add($criteria);
        }
    }

    public function clearCriteria()
    {
        $this->criterias = new Collection();
    }
}