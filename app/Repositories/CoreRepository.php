<?php


namespace App\Repositories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CoreRepository
 */
abstract class CoreRepository
{
    protected $model;

    /**
     * CoreRepository constructor.
     */
    public function __construct()
    {
        $this->model = app($this->getModelClass());
    }

    /**
     * @return Model|mixed
     */
    protected function startConditions(): Model
    {
        return clone $this->model;
    }

    /**
     * @return mixed
     */
    abstract protected function getModelClass();
}
