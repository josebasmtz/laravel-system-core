<?php


namespace Josebasmtz\SystemCore\Bases;


use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class BaseValidator
{
    protected $data = [];
    protected $rules = [];
    protected $messages = [];

    /**
     * @var \Illuminate\Support\MessageBag
     */
    private $errors;

    /**
     * @param string $key
     * @return \Illuminate\Contracts\Validation\Validator
     * @throws \Exception
     */
    protected function getValidator(string $key)
    {
        if (!Arr::has($this->rules, $key))
        {
            throw new \Exception("The key \"$key\" doesn't exists");
        }

        $rules = Arr::get($this->rules, $key);
        if (!is_array($rules))
        {
            throw new \Exception("The rules to key \"$key\" doesn't a array");
        }

        return \Validator::make($this->data, $rules, $this->messages);
    }

    /**
     * @param array $data
     * @return $this
     */
    public function withData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param bool $single
     * @return \Illuminate\Support\MessageBag|string|null
     */
    public function errors($single = false)
    {
        if ($single && $this->errors !== null)
        {
            $messages = new Collection($this->errors->messages());
            return $messages->map( static function ($item, $key) {
                $return = $key;

                if (count($item) > 0)
                {
                    $errors = implode(', ', $item);
                    $return = "$return ($errors)";
                }

                return $return;
            })->join(',');
        }
        return $this->errors;
    }

    /**
     * @param string $key
     * @return bool|null
     */
    public function success(string $key)
    {
        $response = null;
        try {
            $validator = $this->getValidator($key);
            $valid = $validator->passes();

            if (!$valid)
            {
                $this->errors = $validator->errors();
            }
            $response = $valid;
        }
        catch (\Throwable $e)
        {
            \Log::error($e);
        }

        return $response;
    }

    /**
     * @param string $key
     * @return bool|null
     */
    public function fail(string $key)
    {
        $response = null;

        $valid = $this->success($key);
        if ($valid !== null)
        {
            $response = !$valid;
        }

        return $response;
    }
}