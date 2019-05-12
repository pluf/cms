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
        'regex' => '#^/term-taxonomies/schema$#',
        'model' => 'Pluf_Views',
        'method' => 'getSchema',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'CMS_TermTaxonomy'
        )
    ),
    /*
     * Term-Taxonomy
     */
    array( // Create
        'regex' => '#^/term-taxonomies$#',
        'model' => 'Pluf_Views',
        'method' => 'createObject',
        'http-method' => 'POST',
        'params' => array(
            'model' => 'CMS_TermTaxonomy'
        ),
        'precond' => array(
            'CMS_Precondition::authorRequired'
        )
    ),
    array( // Read
        'regex' => '#^/term-taxonomies/(?P<modelId>\d+)$#',
        'model' => 'Pluf_Views',
        'method' => 'getObject',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'CMS_TermTaxonomy'
        )
    ),
    array( // Read (list)
        'regex' => '#^/term-taxonomies$#',
        'model' => 'Pluf_Views',
        'method' => 'findObject',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'CMS_TermTaxonomy'
        )
    ),
    array( // Update
        'regex' => '#^/term-taxonomies/(?P<modelId>\d+)$#',
        'model' => 'Pluf_Views',
        'method' => 'updateObject',
        'http-method' => 'POST',
        'params' => array(
            'model' => 'CMS_TermTaxonomy'
        ),
        'precond' => array(
            'CMS_Precondition::editorRequired'
        )
    ),
    array( // Delete
        'regex' => '#^/term-taxonomies/(?P<modelId>\d+)$#',
        'model' => 'Pluf_Views',
        'method' => 'deleteObject',
        'http-method' => 'DELETE',
        'params' => array(
            'model' => 'CMS_TermTaxonomy',
            'permanently' => true
        ),
        'precond' => array(
            'CMS_Precondition::editorRequired'
        )
    ),
    /*
     * TermTaxonomy Children
     */
    array( // Read (list)
        'regex' => '#^/term-taxonomies/(?P<parentId>\d+)/children$#',
        'model' => 'Pluf_Views',
        'method' => 'findManyToOne',
        'http-method' => 'GET',
        'params' => array(
            'model' => 'CMS_TermTaxonomy',
            'parentKey' => 'parent_id'
        )
    ),
    /*
     * Contents of TermTaxonomy
     */
    array( // Create
        'regex' => '#^/term-taxonomies/(?P<parentId>\d+)/contents$#',
        'model' => 'CMS_Views_TermTaxonomy',
        'method' => 'addContent',
        'http-method' => 'POST',
        'precond' => array(
            'CMS_Precondition::authorRequired'
        )
    ),
    array( // Create
        'regex' => '#^/term-taxonomies/(?P<parentId>\d+)/contents/(?P<modelId>\d+)$#',
        'model' => 'CMS_Views_TermTaxonomy',
        'method' => 'addContent',
        'http-method' => 'POST',
        'precond' => array(
            'CMS_Precondition::authorRequired'
        )
    ),
    array( // Read (list)
        'regex' => '#^/term-taxonomies/(?P<parentId>\d+)/contents$#',
        'model' => 'CMS_Views_TermTaxonomy',
        'method' => 'findContents',
        'http-method' => 'GET'
    ),
    array( // Delete
        'regex' => '#^/term-taxonomies/(?P<parentId>\d+)/contents/(?P<modelId>\d+)$#',
        'model' => 'CMS_Views_TermTaxonomy',
        'method' => 'removeContent',
        'http-method' => 'DELETE',
        'precond' => array(
            'CMS_Precondition::authorRequired'
        )
    ),
    /*
     * Taxonomy
     */
    array( // Read (list)
        'regex' => '#^/taxonomies$#',
        'model' => 'CMS_Views_TermTaxonomy',
        'method' => 'taxonomies',
        'http-method' => 'GET'
    )
);

