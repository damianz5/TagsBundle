<?php

namespace EzSystems\TagsBundle\Core\Persistence\Legacy\Tags\Gateway;

use EzSystems\TagsBundle\Core\Persistence\Legacy\Tags\Gateway;
use ezcDbException;
use PDOException;
use RuntimeException;

class ExceptionConversion extends Gateway
{
    /**
     * The wrapped gateway
     *
     * @var \EzSystems\TagsBundle\Core\Persistence\Legacy\Tags\Gateway
     */
    protected $innerGateway;

    /**
     * Creates a new exception conversion gateway around $innerGateway
     *
     * @param \EzSystems\TagsBundle\Core\Persistence\Legacy\Tags\Gateway $innerGateway
     */
    public function __construct( Gateway $innerGateway )
    {
        $this->innerGateway = $innerGateway;
    }

    /**
     * Returns an array with basic tag data
     *
     * @throws \RuntimeException
     *
     * @param mixed $tagId
     *
     * @return array
     */
    public function getBasicTagData( $tagId )
    {
        try
        {
            return $this->innerGateway->getBasicTagData( $tagId );
        }
        catch ( ezcDbException $e )
        {
            throw new RuntimeException( 'Database error', 0, $e );
        }
        catch ( PDOException $e )
        {
            throw new RuntimeException( 'Database error', 0, $e );
        }
    }

    /**
     * Returns an array with basic tag data for the tag with $remoteId
     *
     * @throws \RuntimeException
     *
     * @param string $remoteId
     *
     * @return array
     */
    public function getBasicTagDataByRemoteId( $remoteId )
    {
        try
        {
            return $this->innerGateway->getBasicTagDataByRemoteId( $remoteId );
        }
        catch ( ezcDbException $e )
        {
            throw new RuntimeException( 'Database error', 0, $e );
        }
        catch ( PDOException $e )
        {
            throw new RuntimeException( 'Database error', 0, $e );
        }
    }
}