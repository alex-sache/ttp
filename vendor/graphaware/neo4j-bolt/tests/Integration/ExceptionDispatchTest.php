<?php

namespace GraphAware\Bolt\Tests\Integration;

use GraphAware\Bolt\GraphDatabase;
use GraphAware\Bolt\Exception\MessageFailureException;
use GraphAware\Common\Type\Node;

/**
 * Class ExceptionDispatchTest
 * @package GraphAware\Bolt\Tests\Integration
 *
 * @group integration
 * @group exception-dispatch
 */
class ExceptionDispatchTest extends \PHPUnit_Framework_TestCase
{
    public function testExceptionsAreThrown()
    {
        $driver = GraphDatabase::driver('bolt://localhost');
        $session = $driver->session();

        $this->setExpectedException(MessageFailureException::class);
        $session->run("CREATE (n:)");

        try {
            $session->run("CR");
        } catch (MessageFailureException $e) {
            $this->assertEquals('Neo.ClientError.Statement.SyntaxError', $e->getStatusCode());
        }
    }

    public function testNeo4jStatusCodeIsAvalailble()
    {
        $driver = GraphDatabase::driver('bolt://localhost');
        $session = $driver->session();

        try {
            $session->run("CR");
        } catch (MessageFailureException $e) {
            $this->assertEquals('Neo.ClientError.Statement.SyntaxError', $e->getStatusCode());
        }
    }

    public function testMessageFailuresAreHandled()
    {
        $driver = GraphDatabase::driver('bolt://localhost');
        $session = $driver->session();
        try {
            $session->run('CR');
        } catch (MessageFailureException $e) {
            //
        }
        $result = $session->run('CREATE (n) RETURN n');
        $this->assertTrue($result->firstRecord()->get('n') instanceof Node);
    }
}