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

use Mailmatics\Client;

/**
 * Transacted
 */
class Transacted extends AbstractApi
{
    /**
     * @var int
     */
    protected $transactionalId;

    /**
     * Transacted constructor.
     *
     * @param Client $client
     * @param int    $transactionalId
     */
    public function __construct(Client $client, $transactionalId)
    {
        parent::__construct($client);

        if (!is_int($transactionalId)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Expected an integer greater than zero. "%s" given instead.',
                    gettype($transactionalId)
                )
            );
        }

        if ($transactionalId <= 0) {
            throw new \InvalidArgumentException('Expected an integer greater than zero.');
        }

        $this->transactionalId = $transactionalId;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->client->request('transactional/' . $this->transactionalId . '/transacted');
    }

    /**
     * @param int $id
     * @return array
     */
    public function get($id)
    {
        return $this->client->request('transactional/' . $this->transactionalId . '/transacted/' . $id);
    }
}
