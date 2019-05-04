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

/**
 * TermTaxonomy data model.
 *
 * A term is not a category or tag on its own. It must be given context via the TermTaxonomy entity.
 * The TermTaxonomy places a term within a taxonomy.
 * This is what makes a term a category, a tag or part of a custom taxonomy (or in a combination of taxonomies).
 *
 * <li>taxonomy: designates the taxonomy in which the term resides. Some of examples for taxonomy are category, tag or link.</li>
 * <li>term_id: is the ID of a term in the terms table</li>
 *
 * The rest of the fields provide information about the term in the context of the taxonomy.
 *
 * <li>parent_id: it keeps track of hierarchical relationships between terms in the taxonomy.</li>
 * <li>description: provides a taxonomy specific description of the term.</li>
 * <li>count: tracks how many objects are associated with the term+taxonomy pair.</li>
 *
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 *        
 */
class CMS_TermTaxonomy extends Pluf_Model
{

    /**
     * مدل داده‌ای را بارگذاری می‌کند.
     *
     * @see Pluf_Model::init()
     */
    function init()
    {
        $this->_a['table'] = 'cms_term_taxonomy';
        $this->_a['verbose'] = 'CMS_TermTaxonomy';
        $this->_a['cols'] = array(
            // ID
            'id' => array(
                'type' => 'Pluf_DB_Field_Sequence',
                'blank' => false,
                'editable' => false
            ),
            // Fields
            'taxonomy' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => true,
                'size' => 128,
                'default' => '',
                'editable' => true
            ),
            'description' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => true,
                'size' => 2048,
                'default' => '',
                'editable' => true
            ),
            'count' => array(
                'type' => 'Pluf_DB_Field_Integer',
                'blank' => false,
                'default' => 0,
                'editable' => false
            ),
            /*
             * Foreign keys
             */
            'term_id' => array(
                'type' => 'Pluf_DB_Field_Foreignkey',
                'model' => 'CMS_Term',
                'name' => 'term',
                'graphql_name' => 'term',
                'relate_name' => 'term_taxonomies',
                'is_null' => true,
                'editable' => true
            ),
            'parent_id' => array(
                'type' => 'Pluf_DB_Field_Foreignkey',
                'model' => 'CMS_TermTaxonomy',
                'name' => 'parent',
                'graphql_name' => 'parent',
                'relate_name' => 'children',
                'is_null' => true,
                'editable' => true
            ),
            'contents_ids' => array(
                'type' => 'Pluf_DB_Field_Manytomany',
                'model' => 'CMS_Content',
                'is_null' => true,
                'editable' => false,
                'name' => 'contents',
                'graphql_name' => 'contents',
                'relate_name' => 'term_taxonomies'
            )
        );

        $this->_a['idx'] = array(
            'term_taxonomy_unique_idx' => array(
                'col' => 'taxonomy,term_id,parent_id',
                'type' => 'unique', // normal, unique, fulltext, spatial
                'index_type' => '', // hash, btree
                'index_option' => '',
                'algorithm_option' => '',
                'lock_option' => ''
            )
        );
    }
}

