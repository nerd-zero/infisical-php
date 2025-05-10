<?php

namespace Infisical\Admin\TokenStorages;

class RunTimeTokenStorage implements TokenStorage
{
    /**
     * @var ?array
     */
    private $token = null;

    public function getToken()
    {
        return $this->token;
    }

    public function saveToken(array $token)
    {
        $this->token = $token;
    }
}
