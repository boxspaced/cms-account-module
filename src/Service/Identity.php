<?php
namespace Boxspaced\CmsAccountModule\Service;

class Identity
{

    /**
     *
     * @var int
     */
    public $id;

    /**
     *
     * @var string
     */
    public $username;

    /**
     *
     * @var boolean
     */
    public $changePassword;

    /**
     *
     * @var string[]
     */
    public $roles = [];

}
