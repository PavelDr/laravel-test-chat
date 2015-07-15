<?php
namespace App\Chat;

use Ratchet\ConnectionInterface;

/**
 * Class User
 * @package App\Chat
 */
class User implements UserInterface
{
    protected $socket;
    protected $id;
    protected $name;

    /**
     * @return mixed
     */
    public function getSocket()
    {
        return $this->socket;
    }

    /**
     * @param ConnectionInterface $socket
     * @return $this
     */
    public function setSocket(ConnectionInterface $socket)
    {
        $this->socket = $socket;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}