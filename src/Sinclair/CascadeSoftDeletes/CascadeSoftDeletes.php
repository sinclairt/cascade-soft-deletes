<?php

namespace Sinclair\CascadeSoftDeletes;

use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException;

trait CascadeSoftDeletes
{
    public static function bootCascadeSoftDeletes()
    {
        // soft delete the children before deleting the parent
        static::deleting(function ( $model )
        {
            foreach ( $model->getChildren() as $child )
                $model->$child()
                      ->delete();

        });

        // we need th parent to be restored before it can be attached
        static::restored(function ( $model )
        {
            foreach ( $model->getChildren() as $child )
                $model->$child()
                      ->onlyTrashed()
                      ->restore();
        });
    }

    public function addChild( $value )
    {
        $this->getChildren();

        $this->children[] = $value;
    }

    private function getChildren()
    {
        if ( isset( $this->children ) )
            return $this->children;

        throw new NotAcceptableHttpException('The variable "$children" needs to be defined!');
    }
}