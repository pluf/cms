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
 * Meta data for Terms
 *
 * @author hadi
 *
 */
class CMS_TermMeta extends Pluf_Model
{

    /**
     * Initial term meta data
     *
     * @see Pluf_Model::init()
     */
    function init()
    {
        $this->_a['table'] = 'cms_term_metas';
        $this->_a['cols'] = array(
            // Identifier
            'id' => array(
                'type' => 'Pluf_DB_Field_Sequence',
                'is_null' => false,
                'editable' => false
            ),
            // Fields
            'key' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'is_null' => false,
                'size' => 256,
                'unique' => true,
                'editable' => true
            ),
            'value' => array(
                'type' => 'Pluf_DB_Field_Text',
                'is_null' => true,
                'default' => '',
                'editable' => true
            ),
            // Foreign keys
            'term_id' => array(
                'type' => 'Pluf_DB_Field_Foreignkey',
                'model' => 'CMS_Term',
                'name' => 'term',
                'graphql_name' => 'term',
                'graphql_field' => true,
                'relate_name' => 'metas',
                'is_null' => true,
                'editable' => true
            ),
        );
    }
}