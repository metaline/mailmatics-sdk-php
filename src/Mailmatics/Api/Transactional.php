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
 * Transactional
 */
class Transactional extends AbstractApi
{
    /**
     * @return array[]
     */
    public function all()
    {
        return $this->client->request('transactional');
    }

    /**
     * @param int $id
     * @return array
     */
    public function get($id)
    {
        return $this->client->request('transactional/' . $id);
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
     * @param int $id
     * @return string
     */
    public function htmlPreview($id)
    {
        return $this->client->request('transactional/' . $id . '/preview', 'GET', [], true);
    }

    /**
     * @param int $id
     * @return string
     */
    public function textPreview($id)
    {
        return $this->client->request('transactional/' . $id . '/text', 'GET', []);
    }

    /**
     * @param int       $uuid
     * @param string    $email
     * @param array     $data
     * @param \DateTime $schedule
     * @return array
     */
    public function send($uuid, $email, $data, \DateTime $schedule = null)
    {
        $params = [
            'recipient' => $email,
            'data'      => $data,
        ];

        if ($schedule) {
            $params['schedule'] = $schedule->getTimestamp();
        }

        return $this->client->request('transactional/' . $uuid . '/send', 'POST', $params);
    }

    /**
     * @param int $id
     * @return Transacted
     */
    public function getTransacted($id)
    {
        return new Transacted($this->client, $id);
    }

    /**
     * @param int $id
     * @return array
     */
    public function reports($id)
    {
        return $this->client->request('transactional/' . $id . '/reports', 'GET', []);
    }
}
