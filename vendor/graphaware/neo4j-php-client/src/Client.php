<?php

/**
 * This file is part of the GraphAware Neo4j Client package.
 *
 * (c) GraphAware Limited <http://graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace GraphAware\Neo4j\Client;

use GraphAware\Common\Cypher\Statement;
use GraphAware\Common\Result\AbstractRecordCursor;
use GraphAware\Common\Result\Record;
use GraphAware\Neo4j\Client\Connection\ConnectionManager;
use GraphAware\Neo4j\Client\Event\FailureEvent;
use GraphAware\Neo4j\Client\Event\PostRunEvent;
use GraphAware\Neo4j\Client\Event\PreRunEvent;
use GraphAware\Neo4j\Client\Exception\Neo4jException;
use GraphAware\Neo4j\Client\Result\ResultCollection;
use GraphAware\Neo4j\Client\Schema\Label;
use GraphAware\Neo4j\Client\Transaction\Transaction;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Client
{
    const NEOCLIENT_VERSION = '4.0';

    /**
     * @var ConnectionManager
     */
    protected $connectionManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    public function __construct(ConnectionManager $connectionManager, EventDispatcherInterface $ev = null)
    {
        $this->connectionManager = $connectionManager;
        $this->eventDispatcher = null !== $ev ? $ev : new EventDispatcher();
    }

    /**
     * Run a Cypher statement against the default database or the database specified.
     *
     * @param $query
     * @param null|array  $parameters
     * @param null|string $tag
     * @param null|string $connectionAlias
     *
     * @return \GraphAware\Common\Result\Result
     *
     * @throws \GraphAware\Neo4j\Client\Exception\Neo4jExceptionInterface
     */
    public function run($query, $parameters = null, $tag = null, $connectionAlias = null)
    {
        $connection = $this->connectionManager->getConnection($connectionAlias);
        $params = null !== $parameters ? $parameters : array();
        $statement = Statement::create($query, $params, $tag);
        $this->eventDispatcher->dispatch(Neo4jClientEvents::NEO4J_PRE_RUN, new PreRunEvent(array($statement)));

        try {
            $result = $connection->run($query, $parameters, $tag);
            $this->eventDispatcher->dispatch(Neo4jClientEvents::NEO4J_POST_RUN, new PostRunEvent(ResultCollection::withResult($result)));
        } catch (Neo4jException $e) {
            $event = new FailureEvent($e);
            $this->eventDispatcher->dispatch(Neo4jClientEvents::NEO4J_ON_FAILURE, $event);

            if ($event->shouldThrowException()) {
                throw $e;
            }

            return;
        }

        return $result;
    }

    /**
     * @param string      $query
     * @param null|array  $parameters
     * @param null|string $tag
     *
     * @return AbstractRecordCursor
     *
     * @throws Neo4jException
     */
    public function runWrite($query, $parameters = null, $tag = null)
    {
        $connection = $this->connectionManager->getMasterConnection();

        return $connection->run($query, $parameters, $tag);
    }

    /**
     * @deprecated since 4.0 - will be removed in 5.0 - use <code>$client->runWrite()</code> instead.
     *
     * @param string      $query
     * @param null|array  $parameters
     * @param null|string $tag
     *
     * @return AbstractRecordCursor
     *
     * @throws Neo4jException
     */
    public function sendWriteQuery($query, $parameters = null, $tag = null)
    {
        return $this->runWrite($query, $parameters, $tag);
    }

    /**
     * @param string|null $tag
     * @param string|null $connectionAlias
     *
     * @return Stack
     */
    public function stack($tag = null, $connectionAlias = null)
    {
        return Stack::create($tag, $connectionAlias);
    }

    /**
     * @param Stack $stack
     *
     * @return ResultCollection|null
     *
     * @throws Neo4jException
     */
    public function runStack(Stack $stack)
    {
        $pipeline = $this->pipeline(null, null, $stack->getTag(), $stack->getConnectionAlias());

        foreach ($stack->statements() as $statement) {
            $pipeline->push($statement->text(), $statement->parameters(), $statement->getTag());
        }

        $this->eventDispatcher->dispatch(Neo4jClientEvents::NEO4J_PRE_RUN, new PreRunEvent($stack->statements()));

        try {
            $results = $pipeline->run();
            $this->eventDispatcher->dispatch(Neo4jClientEvents::NEO4J_POST_RUN, new PostRunEvent($results));
        } catch (Neo4jException $e) {
            $event = new FailureEvent($e);
            $this->eventDispatcher->dispatch(Neo4jClientEvents::NEO4J_ON_FAILURE, $event);

            if ($event->shouldThrowException()) {
                throw $e;
            }

            return;
        }

        return $results;
    }

    /**
     * @param null|string $connectionAlias
     *
     * @return Transaction
     */
    public function transaction($connectionAlias = null)
    {
        $connection = $this->connectionManager->getConnection($connectionAlias);
        $driverTransaction = $connection->getTransaction();

        return new Transaction($driverTransaction);
    }

    /**
     * @param null|string $query
     * @param null|array  $parameters
     * @param null|string $tag
     * @param null|string $connectionAlias
     *
     * @return \GraphAware\Neo4j\Client\HttpDriver\Pipeline|\GraphAware\Bolt\Protocol\Pipeline
     */
    private function pipeline($query = null, $parameters = null, $tag = null, $connectionAlias = null)
    {
        $connection = $this->connectionManager->getConnection($connectionAlias);

        return $connection->createPipeline($query, $parameters, $tag);
    }

    /**
     * @param string|null $conn
     * @return Label[]
     */
    public function getLabels($conn = null)
    {
        $connection = $this->connectionManager->getConnection($conn);
        $result = $connection->getSession()->run("CALL db.labels()");

        return array_map(function(Record $record) {
            return new Label($record->get('label'));
        }, $result->records());
    }

    /**
     * @deprecated since 4.0 - will be removed in 5.0 - use <code>$client->run()</code> instead.
     *
     * @param string      $query
     * @param null|array  $parameters
     * @param null|string $tag
     * @param null|string $connectionAlias
     *
     * @return AbstractRecordCursor
     */
    public function sendCypherQuery($query, $parameters = null, $tag = null, $connectionAlias = null)
    {
        $connection = $this->connectionManager->getConnection($connectionAlias);

        return $connection->run($query, $parameters, $tag);
    }

    /**
     * @return ConnectionManager
     */
    public function getConnectionManager()
    {
        return $this->connectionManager;
    }

    /**
     * @return EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }
}
