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

use Mailmatics\Resource\TransactionalEmailResource;

/**
 * Transactional
 */
class Transactional extends AbstractApi
{
    /**
     * @return TransactionalEmailResource[]
     */
    public function all()
    {
        return $this->client->request('transactional');
    }

    /**
     * @param string $id
     * @return TransactionalEmailResource
     */
    public function get($id)
    {
        return $this->client->request('transactional/' . $id);
    }

    /**
     * @param string $id
     * @param array  $data
     *
     * @codeCoverageIgnore
     */
    public function edit($id, array $data)
    {
        // TODO
    }

    /**
     * @param string $id
     * @return mixed
     */
    public function preview($id)
    {
        return $this->client->request('transactional/' . $id . '/preview', 'GET', [], true);
    }

    /**
     * @param string $id
     * @return mixed
     *
     * @codeCoverageIgnore
     */
    public function text($id)
    {
        // TODO
    }

    /**
     * @param string $id
     *
     * @codeCoverageIgnore
     */
    public function transacted($id)
    {
        // TODO
    }

    /**
     * @param string $id
     *
     * @codeCoverageIgnore
     */
    public function reports($id)
    {
        // TODO
    }

    /**
     * @param string $id
     *
     * @codeCoverageIgnore
     */
    public function send($id)
    {
        // TODO
    }
}
