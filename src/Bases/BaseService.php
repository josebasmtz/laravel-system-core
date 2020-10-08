<?php


namespace Josebasmtz\SystemCore\Bases;

use Illuminate\Support\Collection;
use Illuminate\Support\MessageBag;
use Josebasmtz\SystemCore\Criterias\SearchCriteria;
use Josebasmtz\SystemCore\Exceptions\NotifiableException;
use Josebasmtz\SystemCore\Exceptions\ValidatorException;
use Josebasmtz\SystemCore\Interfaces\IRepository;
use Josebasmtz\SystemCore\Interfaces\IValidator;

abstract class BaseService
{
    /**
     * @var IRepository
     */
    protected $repository;

    /**
     * @var IValidator
     */
    protected $validator;

    /**
     * @var MessageBag
     */
    protected $errors;

    public function __construct()
    {
        $this->cleanErrors();
    }

    public function create($data)
    {
        $response = false;
        try {
            if ($this->validator->withData($data)->fail('create'))
            {
                throw new ValidatorException($this->validator->errors(true));
            }
            $response = $this->repository->create($data);
        }
        catch (\Throwable $e)
        {
            $this->treatException($e);
        }
        return $response;
    }

    public function update($key, $data)
    {
        $response = null;
        try {
            $model = $this->repository->find($key);
            if ($model === null)
            {
                throw new NotifiableException(_i("El registro no existe"));
            }
            if ($this->validator->withData($data)->fail('update'))
            {
                throw new ValidatorException($this->validator->errors(true));
            }
            $response = $this->repository->update($key, $data);
        }
        catch (\Throwable $e)
        {
            $this->treatException($e);
        }
        return $response;
    }

    public function delete($key)
    {
        $response = null;
        try {
            $model = $this->repository->find($key);
            if ($model === null)
            {
                throw new NotifiableException(_i("El registro no existe"));
            }
            $response = $this->repository->delete($key);
        }
        catch (\Throwable $e)
        {
            $this->treatException($e);
        }
        return $response;
    }

    public function find($key)
    {
        return $this->repository->find($key);
    }

    protected function treatException(\Throwable $e)
    {
        \Log::error($e);
        if ($e instanceof ValidatorException)
        {
            $this->addError(_i("La información no es válida"));
            return;
        }

        if ($e instanceof NotifiableException)
        {
            $this->addError($e->getMessage());
            return;
        }
    }

    protected function addError(string $message)
    {
        $this->errors->add(errors_key(), $message);
    }

    /**
     * @param array $terms
     * @param array|Collection|null $paginationAttrs
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection|null
     */
    public function search(array $terms, $paginationAttrs = null)
    {
        $this->repository->pushCriteria(new SearchCriteria($terms));
        if (is_array($paginationAttrs))
        {
            $collectionPagAttrs = collect($paginationAttrs);

            $pageName = $collectionPagAttrs->get('pageName') ?? 'page';
            return $this->repository->applyCriteria()->paginate(
                $collectionPagAttrs->get('perPage') ?? 15,
                $collectionPagAttrs->get('columns')??['*'],
                $pageName,
                $collectionPagAttrs->get($pageName) ?? request($pageName) ?? 1
            );
        }
        return $this->repository->all();
    }

    /**
     * @return MessageBag
     */
    public function errors()
    {
        return $this->errors ?? new MessageBag();
    }

    /**
     * @param IRepository $repository
     * @return $this;
     */
    public function setRepository(IRepository $repository)
    {
        $this->repository = $repository;
        return $this;
    }

    public function cleanErrors()
    {
        $this->errors = new MessageBag();
    }
}