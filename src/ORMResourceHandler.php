<?php
/**
 * Created by PhpStorm.
 * User: ts
 * Date: 20.09.18
 * Time: 15:10
 */

namespace TS\Web\Resource;


class ORMResourceHandler extends AbstractORMResourceHandler
{

    /** @var HashStorage */
    private $storage;


    public function __construct(HashStorage $storage)
    {
        parent::__construct();
        $this->storage = $storage;
    }


    /**
     * @return HashStorage
     */
    protected function getStorage(): HashStorage
    {
        return $this->storage;
    }


}
