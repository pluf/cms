<?php
/*
 * This file is part of Pluf Framework, a simple PHP Application Framework.
 * Copyright (C) 2010-2020 Phoinex Scholars Co. (http://dpq.co.ir)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
return array(
    // ************************************************************* Schema
    array(
        'regex' => '#^/contents/schema$#',
        'model' => 'Pluf_Views',
        'method' => 'getSchema',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'CMS_Content'
        )
    ),
    /*
     * Content
     */
    array( // Create
        'regex' => '#^/contents$#',
        'model' => 'CMS_Views',
        'method' => 'create',
        'http-method' => 'POST',
        'precond' => array(
            'CMS_Precondition::authorRequired'
        )
    ),
    array( // Read (list)
        'regex' => '#^/contents$#',
        'model' => 'CMS_Views',
        'method' => 'find',
        'http-method' => 'GET'
    ),
    array( // Read
        'regex' => '#^/contents/(?P<modelId>\d+)$#',
        'model' => 'Pluf_Views',
        'method' => 'getObject',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'CMS_Content'
        )
    ),
    array( // Update
        'regex' => '#^/contents/(?P<modelId>\d+)$#',
        'model' => 'CMS_Views',
        'method' => 'update',
        'http-method' => 'POST',
        'precond' => array(
            'CMS_Precondition::authorRequired'
        )
    ),
    array( // Delete
        'regex' => '#^/contents/(?P<modelId>\d+)$#',
        'model' => 'CMS_Views',
        'method' => 'delete',
        'http-method' => 'DELETE',
        'precond' => array(
            'CMS_Precondition::authorRequired'
        )
    ),
    /*
     * Term-Taxonomies of content 
     */
    // /cms/contents/{commentId}/term-taxonomies:
    
    /*
     * Binary content of content
     */
    array( // Read
        'regex' => '#^/contents/(?P<modelId>\d+)/content$#',
        'model' => 'CMS_Views',
        'method' => 'download',
        'http-method' => 'GET',
        // Cache apram
        'cacheable' => true,
        'revalidate' => true,
        'intermediate_cache' => true,
        'max_age' => 25000
    ),
    array( // Update
        'regex' => '#^/contents/(?P<modelId>\d+)/content$#',
        'model' => 'CMS_Views',
        'method' => 'updateFile',
        'http-method' => 'POST',
        'precond' => array(
            'CMS_Precondition::authorRequired'
        )
    ),
    array( // Read
        'regex' => '#^/contents/(?P<name>[^/]+)/content$#',
        'model' => 'CMS_Views',
        'method' => 'download',
        'http-method' => 'GET',
        // Cache apram
        'cacheable' => true,
        'revalidate' => true,
        'intermediate_cache' => true,
        'max_age' => 25000
    ),
    
    /*
     * Thumbnail of content
     */
    // TODO: maso, 2018: implement thumbnail generator
    array( // Read
        'regex' => '#^/contents/(?P<modelId>\d+)/thumbnail$#',
        'model' => 'CMS_Views',
        'method' => 'download',
        'http-method' => 'GET',
        // Cache apram
        'cacheable' => true,
        'revalidate' => true,
        'intermediate_cache' => true,
        'max_age' => 25000
    ),
    array( // Update
        'regex' => '#^/contents/(?P<modelId>\d+)/thumbnail$#',
        'model' => 'CMS_Views',
        'method' => 'updateThumbnail',
        'http-method' => 'POST',
        'precond' => array(
            'CMS_Precondition::authorRequired'
        )
    ),

    array( // Read (by name)
        'regex' => '#^/contents/(?P<name>.+)$#',
        'model' => 'CMS_Views',
        'method' => 'get',
        'http-method' => 'GET'
    )
);

