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
 * Holds the basic information about single term.
 * <ul>
 *   <li>id: is a unique ID for the term.</li>
 *   <li>name: is simply the name of the term.</li>
 *   <li>slug: is unique and is the name reduced to a URL friendly form.</li>
 *   <li>term_group: is a means of grouping together similar terms.</li>
 * </ul>
 * 
 * Base on https://wordpress.stackexchange.com/questions/23169/what-is-term-group-for-order-by-in-get-terms the field 
 * term_group practically never used. (So we are not added this field).
 *
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 *        
 */
class CMS_Term extends Pluf_Model
{

    /**
     * مدل داده‌ای را بارگذاری می‌کند.
     *
     * @see Pluf_Model::init()
     */
    function init()
    {
        $this->_a['table'] = 'cms_terms';
        $this->_a['cols'] = array(
            // ID
            'id' => array(
                'type' => 'Sequence',
                'blank' => false,
                'verbose' => 'first name',
                'help_text' => 'id',
                'editable' => false
            ),
            // Fields
            'name' => array(
                'type' => 'Varchar',
                'is_null' => false,
                'size' => 200,
                'default' => '',
                'editable' => true
            ),
            'slug' => array(
                'type' => 'Varchar',
                'is_null' => true,
                'unique' => true,
                'size' => 256,
                'editable' => true
            ),
            /*
             * Foreign keys
             */
        );
    }
}

