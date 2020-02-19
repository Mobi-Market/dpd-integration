<?php

declare(strict_types=1);

namespace MobiMarket\DpdShipping\Entities;

class ApiAuth
{
    /**
     * @var string
     */
    public $username;
    /**
     * @var string
     */
    public $password;
    /**
     * @var string
     */
    public $account_id;

    /**
     * @var string
     */
    public $token = null;

    /**
     * Create entity instance from array.
     */
    public static function fromArray(array $auth): self
    {
        $entity = new self();

        $entity->username   = $auth['username'];
        $entity->password   = $auth['password'];
        $entity->account_id = $auth['account_id'];

        return $entity;
    }
}
