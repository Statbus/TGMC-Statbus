<?php

namespace App\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Tool\ArrayAccessorTrait;

class TGForumResourceOwner implements ResourceOwnerInterface
{
    use ArrayAccessorTrait;

    public function __construct(private array $response = [])
    {

    }

    public function toArray()
    {
        return $this->response;
    }

    public function getId()
    {
        return $this->getValueByKey($this->response, 'byond_ckey');
    }

}
