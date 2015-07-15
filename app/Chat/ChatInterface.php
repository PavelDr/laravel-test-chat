<?php
namespace App\Chat;

use Evenement\EventEmitterInterface;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

/**
 * Interface ChatInterface
 * @package App\Chat
 */
interface ChatInterface extends MessageComponentInterface
{
    /**
     * @param ConnectionInterface $socket
     * @return mixed
     */
    public function getUserBySocket(ConnectionInterface $socket);

    /**
     * @return mixed
     */
    public function getEmitter();

    /**
     * @param EventEmitterInterface $emitter
     * @return mixed
     */
    public function setEmitter(EventEmitterInterface $emitter);

    /**
     * @return mixed
     */
    public function getUsers();
}