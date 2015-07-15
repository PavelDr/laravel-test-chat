<?php

namespace App\Chat;

use Evenement\EventEmitterInterface;
use Exception;
use Ratchet\ConnectionInterface;
use SplObjectStorage;
use App\Message;

use Ratchet\Wamp\WampServerInterface;

/**
 * Class Chat
 * @package App\Chat
 */
class Chat implements ChatInterface, WampServerInterface
{
    protected $users;
    protected $emitter;
    protected $id = 1;
    protected $channels;

    /**
     * @param ConnectionInterface $socket
     * @return mixed|null|object
     */
    public function getUserBySocket(ConnectionInterface $socket)
    {
        foreach ($this->users as $next) {
            if ($next->getSocket() === $socket) {
                return $next;
            }
        }
        return null;
    }

    /**
     * @return EventEmitterInterface
     */
    public function getEmitter()
    {
        return $this->emitter;
    }

    /**
     * @param EventEmitterInterface $emitter
     */
    public function setEmitter(EventEmitterInterface $emitter)
    {
        $this->emitter = $emitter;
    }

    /**
     * @return SplObjectStorage
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param EventEmitterInterface $emitter
     */
    public function __construct(EventEmitterInterface $emitter)
    {
        $this->emitter = $emitter;
        $this->users = new SplObjectStorage();
        $this->channels = [];
    }

    /**
     * @param ConnectionInterface $socket
     */
    public function onOpen(ConnectionInterface $socket)
    {
        $user = new User();
        $user->setId($this->id++);
        $user->setSocket($socket);
        $this->users->attach($user);
        $this->emitter->emit("open", [$user]);
    }

    /**
     * @param ConnectionInterface $socket
     * @param string $message
     */
    public function onMessage(ConnectionInterface $socket, $message)
    {
        /**
         * Message author
         */
        $user = $this->getUserBySocket($socket);
        $message = json_decode($message);

        switch ($message->type) {
            case "name": {
                $user->setName($message->data);
                $this->emitter->emit("name", [$user, $message->data]);
                break;
            }
            case "message": {
                if (!isset($this->channels[$message->to_user_id])) {
                    $this->channels[$message->to_user_id] = new SplObjectStorage();
                }
                if (!$this->channels[$message->to_user_id]->contains($user)) {
                    $this->channels[$message->to_user_id]->attach($user);
                }

                if ($message->data !== 'ping') {
                    $newMessage = Message::create([
                        'from_user_id' => $message->from_user_id,
                        'to_user_id' => $message->to_user_id,
                        'message' => $message->data
                    ]);

                    $this->emitter->emit("message", [$user, $message->data]);
                }

                break;
            }
        }

        if ($message->data !== 'ping') {
            foreach ($this->channels[$message->to_user_id] as $user) {
                $user->getSocket()->send(json_encode([
                    "user" => [
                        "id" => $message->from_user_id,
                        "name" => $message->from_name,
                    ],
                    "message" => $message
                ]));
            }
        }
    }

    /**
     * @param ConnectionInterface $socket
     */
    public function onClose(ConnectionInterface $socket)
    {
        $user = $this->getUserBySocket($socket);
        if ($user) {
            $this->users->detach($user);
            $this->emitter->emit("close", [$user]);
        }
    }

    /**
     * @param ConnectionInterface $socket
     * @param Exception $exception
     */
    public function onError(ConnectionInterface $socket, Exception $exception)
    {
        $user = $this->getUserBySocket($socket);
        if ($user) {
            $user->getSocket()->close();
            $this->emitter->emit("error", [$user, $exception]);
        }
    }

    protected $subscribedTopics = array();

    public function onSubscribe(ConnectionInterface $conn, $topic) {
        $this->subscribedTopics[$topic->getId()] = $topic;

        $this->emitter->emit("open", [$topic]);
    }

    /**
     * @param string JSON'ified string we'll receive from ZeroMQ
     */
    public function onBlogEntry($entry) {
        $entryData = json_decode($entry, true);

        // If the lookup topic object isn't set there is no one to publish to
        if (!array_key_exists($entryData['category'], $this->subscribedTopics)) {
            return;
        }

        $topic = $this->subscribedTopics[$entryData['category']];

        // re-send the data to all the clients subscribed to that category
        $topic->broadcast($entryData);
    }

    public function onCall(ConnectionInterface $conn, $id, $topic, array $params) {
        // In this application if clients send data it's because the user hacked around in console
        $conn->callError($id, $topic, 'You are not allowed to make calls')->close();
    }
    public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
        // In this application if clients send data it's because the user hacked around in console
        $conn->close();
    }

    public function onUnSubscribe(ConnectionInterface $conn, $topic) {
    }
}