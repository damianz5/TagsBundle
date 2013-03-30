<?php

namespace EzSystems\TagsBundle\Core\Persistence\Legacy\Tags\Gateway;

use EzSystems\TagsBundle\Core\Persistence\Legacy\Tags\Gateway;
use eZ\Publish\Core\Persistence\Legacy\EzcDbHandler;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use PDO;

class EzcDatabase extends Gateway
{
    /**
     * Database handler
     *
     * @var \eZ\Publish\Core\Persistence\Legacy\EzcDbHandler
     */
    protected $handler;

    /**
     * Construct from database handler
     *
     * @param \eZ\Publish\Core\Persistence\Legacy\EzcDbHandler $handler
     */
    public function __construct( EzcDbHandler $handler )
    {
        $this->handler = $handler;
    }

    /**
     * Returns an array with basic tag data
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\NotFoundException
     *
     * @param mixed $tagId
     *
     * @return array
     */
    public function getBasicTagData( $tagId )
    {
        $query = $this->handler->createSelectQuery();
        $query
            ->select( "*" )
            ->from( $this->handler->quoteTable( "eztags" ) )
            ->where(
                $query->expr->eq(
                    $this->handler->quoteColumn( "id" ),
                    $query->bindValue( $tagId, null, PDO::PARAM_INT )
                )
            )
        ;

        $statement = $query->prepare();
        $statement->execute();

        if ( $row = $statement->fetch( PDO::FETCH_ASSOC ) )
        {
            return $row;
        }

        throw new NotFoundException( "tag", $tagId );
    }

    /**
     * Returns an array with basic tag data for the tag with $remoteId
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\NotFoundException
     *
     * @param string $remoteId
     *
     * @return array
     */
    public function getBasicTagDataByRemoteId( $remoteId )
    {
        $query = $this->handler->createSelectQuery();
        $query
            ->select( "*" )
            ->from( $this->handler->quoteTable( "eztags" ) )
            ->where(
                $query->expr->eq(
                    $this->handler->quoteColumn( "remote_id" ),
                    $query->bindValue( $remoteId, null, PDO::PARAM_STR )
                )
            )
        ;

        $statement = $query->prepare();
        $statement->execute();

        if ( $row = $statement->fetch( PDO::FETCH_ASSOC ) )
        {
            return $row;
        }

        throw new NotFoundException( "tag", $remoteId );
    }

    /**
     * Deletes tag identified by $tagId, including its synonyms and all tags under it
     *
     * If $tagId is a synonym, only the synonym is deleted
     *
     * @param mixed $tagId
     */
    public function deleteTag( $tagId )
    {
        $query = $this->handler->createSelectQuery();
        $query
            ->select( "id" )
            ->from( $this->handler->quoteTable( "eztags" ) )
            ->where(
                $query->expr->lOr(
                    $query->expr->like(
                        $this->handler->quoteColumn( "path_string" ),
                        $query->bindValue( "%/" . (int) $tagId . "/%", null, PDO::PARAM_STR )
                    ),
                    $query->expr->eq(
                        $this->handler->quoteColumn( "main_tag_id" ),
                        $query->bindValue( $tagId, null, PDO::PARAM_INT )
                    )
                )
            )
        ;

        $statement = $query->prepare();
        $statement->execute();

        $tagIds = array();
        while ( $row = $statement->fetch( PDO::FETCH_ASSOC ) )
        {
            $tagIds[] = (int) $row["id"];
        }

        if ( empty( $tagIds ) )
        {
            return;
        }

        $query = $this->handler->createDeleteQuery();
        $query
            ->deleteFrom( $this->handler->quoteTable( "eztags_attribute_link" ) )
            ->where(
                $query->expr->in(
                    $this->handler->quoteColumn( "keyword_id" ),
                    $tagIds
                )
            )
        ;

        $query->prepare()->execute();

        $query = $this->handler->createDeleteQuery();
        $query
            ->deleteFrom( $this->handler->quoteTable( "eztags" ) )
            ->where(
                $query->expr->in(
                    $this->handler->quoteColumn( "id" ),
                    $tagIds
                )
            )
        ;

        $query->prepare()->execute();
    }
}
