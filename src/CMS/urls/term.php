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
        'regex' => '#^/terms/schema$#',
        'model' => 'Pluf_Views',
        'method' => 'getSchema',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'CMS_Term'
        )
    ),
    // --------------------------------------------------------------------
    // Term
    // --------------------------------------------------------------------
    array( // Create
        'regex' => '#^/terms$#',
        'model' => 'Pluf_Views',
        'method' => 'createObject',
        'http-method' => 'POST',
        'params' => array(
            'model' => 'CMS_Term'
        ),
        'precond' => array(
            'CMS_Precondition::authorRequired'
        )
    ),
    array( // Read
        'regex' => '#^/terms/(?P<modelId>\d+)$#',
        'model' => 'Pluf_Views',
        'method' => 'getObject',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'CMS_Term'
        )
    ),
    array( // Read (by slug)
        'regex' => '#^/terms/(?P<slug>[^/]+)$#',
        'model' => 'CMS_Views_Term',
        'method' => 'getBySlug',
        'http-method' => 'GET'
    ),
    array( // Read (list)
        'regex' => '#^/terms$#',
        'model' => 'Pluf_Views',
        'method' => 'findObject',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'CMS_Term'
        )
    ),
    array( // Update
        'regex' => '#^/terms/(?P<modelId>\d+)$#',
        'model' => 'Pluf_Views',
        'method' => 'updateObject',
        'http-method' => 'POST',
        'params' => array(
            'model' => 'CMS_Term'
        ),
        'precond' => array(
            'CMS_Precondition::editorRequired'
        )
    ),
    array( // Delete
        'regex' => '#^/terms/(?P<modelId>\d+)$#',
        'model' => 'Pluf_Views',
        'method' => 'deleteObject',
        'http-method' => 'DELETE',
        'params' => array(
            'model' => 'CMS_Term',
            'permanently' => true
        ),
        'precond' => array(
            'CMS_Precondition::editorRequired'
        )
    ),
    // --------------------------------------------------------------------
    // Term Metas
    // --------------------------------------------------------------------
    array( // Read (list)
        'regex' => '#^/terms/(?P<parentId>\d+)/metas$#',
        'model' => 'Pluf_Views',
        'method' => 'findManyToOne',
        'http-method' => 'GET',
        'params' => array(
            'parent' => 'CMS_Term',
            'parentKey' => 'term_id',
            'model' => 'CMS_TermMeta'
        )
    ),
    array( // Create (list)
        'regex' => '#^/terms/(?P<parentId>\d+)/metas$#',
        'model' => 'Pluf_Views',
        'method' => 'createManyToOne',
        'http-method' => 'POST',
        'precond' => array(
            'CMS_Precondition::authorRequired'
        ),
        'params' => array(
            'parent' => 'CMS_Term',
            'parentKey' => 'term_id',
            'model' => 'CMS_TermMeta'
            // 'precond' => function($request, $object, $parent) -> {false, true} | throw exception
        )
    ),

    array( // Get
        'regex' => '#^/terms/(?P<parentId>\d+)/metas/(?P<modelId>\d+)$#',
        'model' => 'Pluf_Views',
        'method' => 'getManyToOne',
        'http-method' => 'GET',
        'params' => array(
            'parent' => 'CMS_Term',
            'parentKey' => 'term_id',
            'model' => 'CMS_TermMeta'
        )
    ),
    array( // Update
        'regex' => '#^/terms/(?P<parentId>\d+)/metas/(?P<modelId>\d+)$#',
        'model' => 'Pluf_Views',
        'method' => 'updateManyToOne',
        'http-method' => array(
            'POST',
            'PUT'
        ),
        'precond' => array(
            'CMS_Precondition::authorRequired'
        ),
        'params' => array(
            'parent' => 'CMS_Term',
            'parentKey' => 'term_id',
            'model' => 'CMS_TermMeta'
            // 'precond' => function($request, $object, $parent) -> {false, true} | throw exception
        )
    ),
    array( // Delete
        'regex' => '#^/terms/(?P<parentId>\d+)/metas/(?P<modelId>\d+)$#',
        'model' => 'Pluf_Views',
        'method' => 'deleteManyToOne',
        'http-method' => 'DELETE',
        'precond' => array(
            'CMS_Precondition::authorRequired'
        ),
        'params' => array(
            'parent' => 'CMS_Term',
            'parentKey' => 'term_id',
            'model' => 'CMS_TermMeta'
            // 'precond' => function($request, $object, $parent) -> {false, true} | throw exception
        )
    )
);