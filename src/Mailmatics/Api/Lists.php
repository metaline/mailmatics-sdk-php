<?php

/*
 * This file is part of the Mailmatics SDK package.
 *
 * (c) Web Agency Meta Line S.r.l.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mailmatics\Api;

/**
 * Lists
 */
class Lists extends AbstractApi
{
    /**
     * @return array[]
     */
    public function all()
    {
        return $this->client->request('lists');
    }

    /**
     * @param int $id
     * @return array
     */
    public function get($id)
    {
        return $this->client->request('lists/' . $id);
    }

    /**
     * @codeCoverageIgnore
     */
    public function create()
    {
        // TODO
    }

    /**
     * @codeCoverageIgnore
     */
    public function edit()
    {
        // TODO
    }

    /**
     * @codeCoverageIgnore
     */
    public function delete()
    {
        // TODO
    }

    /**
     * @codeCoverageIgnore
     */
    public function getSubscribers()
    {
        // TODO
    }

    /**
     * @codeCoverageIgnore
     */
    public function getSubscriber()
    {
        // TODO
    }

    /**
     * @codeCoverageIgnore
     */
    public function getSubscriberInfo()
    {
        // TODO
    }

    /**
     * Adds a subscriber to a list.
     *
     * @param int    $listId
     * @param string $email
     * @param array  $data
     * @return array
     */
    public function addSubscriber($listId, $email, array $data = [])
    {
        $data['email'] = $email;

        if (!isset($data['name'])) {
            $data['name'] = $email;
        }

        return $this->client->request('lists/' . $listId . '/subscribe', 'POST', $data);
    }

    /**
     * @codeCoverageIgnore
     */
    public function editSubscriber()
    {
        // TODO
    }

    /**
     * @codeCoverageIgnore
     */
    public function removeSubscriber()
    {
        // TODO
    }

    /**
     * @param int $listId
     * @param int $subscriberId
     * @return bool
     */
    public function unsubscribe($listId, $subscriberId)
    {
        $data = [
            'action'      => 'unsubscribe',
            'subscribers' => [$subscriberId],
        ];

        $this->client->request('lists/' . $listId . '/subscribers/batch', 'POST', $data);

        return true;
    }
}
