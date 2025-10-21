<?php
namespace Application\Repository;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Crypt\Password\Bcrypt;

class CompressRepository
{
    private $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }
}