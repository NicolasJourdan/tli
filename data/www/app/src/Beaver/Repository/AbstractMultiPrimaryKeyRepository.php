<?php

namespace Beaver\Repository;

class AbstractMultiPrimaryKeyRepository extends AbstractRepository
{
    /**
     * This method is not allowed for this repository due to the multi primary key
     *
     * @param $id
     *
     * @throws \Exception
     */
    public function getById($id)
    {
        throw new \Exception('Please use one of the next methods : getByArrayIds, getByIdK, getByIdS');
    }
}
