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
 * Content data model
 *
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 * @author maso <mostafa.barmshory@dpq.co.ir>
 *
 */
class CMS_Content extends Pluf_Model
{

    protected $_internal = false;
    
    /**
     * مدل داده‌ای را بارگذاری می‌کند.
     *
     * @see Pluf_Model::init()
     */
    function init()
    {
        $this->_a['table'] = 'cms_contents';
        $this->_a['cols'] = array(
            // Identifier
            'id' => array(
                'type' => 'Sequence',
                'is_null' => false,
                'editable' => false
            ),
            // Fields
            'name' => array(
                'type' => 'Varchar',
                'is_null' => false,
                'size' => 64,
                'unique' => true,
                'editable' => true
            ),
            'title' => array(
                'type' => 'Varchar',
                'is_null' => true,
                'size' => 250,
                'default' => '',
                'editable' => true
            ),
            'description' => array(
                'type' => 'Varchar',
                'is_null' => true,
                'size' => 2048,
                'default' => 'auto created content',
                'editable' => true
            ),
            'mime_type' => array(
                'type' => 'Varchar',
                'is_null' => true,
                'size' => 64,
                'default' => 'application/octet-stream',
                'editable' => true
            ),
            'media_type' => array(
                'type' => 'Varchar',
                'is_null' => true,
                'size' => 64,
                'default' => 'application/octet-stream',
                'verbose' => 'Media type',
                'help_text' => 'This types allow you to category contents',
                'editable' => true
            ),
            'file_path' => array(
                'type' => 'File',
                'is_null' => false,
                'default' => '',
                'size' => 250,
                'verbose' => 'File path',
                'help_text' => 'Content file path',
                'editable' => false,
                'readable' => false
            ),
            'file_name' => array(
                'type' => 'Varchar',
                'is_null' => false,
                'size' => 250,
                'default' => 'unknown',
                'verbose' => 'file name',
                'help_text' => 'Content file name',
                'editable' => true
            ),
            'file_size' => array(
                'type' => 'Integer',
                'is_null' => false,
                'default' => 'no title',
                'verbose' => 'file size',
                'help_text' => 'content file size',
                'editable' => false
            ),
            'downloads' => array(
                'type' => 'Integer',
                'is_null' => false,
                'default' => 0,
                'help_text' => 'content downloads number',
                'editable' => false
            ),
            'state' => array(
                'type' => 'Varchar',
                'is_null' => true,
                'size' => 64,
                'default' => '',
                'editable' => true
            ),
//             'manager' => array(
//                 'type' => 'Varchar',
//                 'blank' => true,
//                 'size' => 100,
//                 'editable' => false,
//                 'readable' => true
//             ),
            'password' => array(
                'type' => 'Password',
                'is_null' => true,
                'size' => 150,
                'help_text' => 'Format: [algo]:[salt]:[hash]',
                'editable' => false,
                'readable' => false
            ),
            'comment_status' => array(
                'type' => 'Varchar',
                'is_null' => true,
                'size' => 64,
                'default' => NULL,
                'editable' => false
            ),
            'comment_count' => array(
                'type' => 'Integer',
                'is_null' => false,
                'default' => 0,
                'help_text' => 'number of comments on the content',
                'editable' => false
            ),
            'cache_policy' => array(
                'type' => 'Varchar',
                'is_null' => true,
                'size' => 512,
                'default' => 'max-age=21600', // can be cached by browser and any intermediary caches for up to 6 hour
                'editable' => true
            ),
            'creation_dtime' => array(
                'type' => 'Datetime',
                'blank' => true,
                'verbose' => 'creation',
                'help_text' => 'content creation time',
                'editable' => false
            ),
            'modif_dtime' => array(
                'type' => 'Datetime',
                'blank' => true,
                'verbose' => 'modification',
                'help_text' => 'content modification time',
                'editable' => false
            ),
            /*
             * Foreign keys
             */
            'author_id' => array(
                'type' => 'Foreignkey',
                'model' => 'User_Account',
                'is_null' => false,
                'name' => 'author',
                'relate_name' => 'authored_contents',
                'graphql_name' => 'author',
                'editable' => false
            ),
            'parent_id' => array(
                'type' => 'Foreignkey',
                'model' => 'CMS_Content',
                'is_null' => true,
                'name' => 'parent',
                'graphql_name' => 'parent',
                'relate_name' => 'children',
                'editable' => true,
                'readable' => true
            ),
            'members' => array(
                'type' => 'Manytomany',
                'model' => 'User_Account',
                'name' => 'members',
                'graphql_name' => 'members',
                'relate_name' => 'member_contents',
                'is_null' => true,
                'editable' => false
            )
        );

        $this->_a['idx'] = array(
            // @Note: hadi - 1396-10: when define an attribute as 'unique => true', pluf automatically
            // create an unique index for it (for example 'name' field here).
            'content_mime_filter_idx' => array(
                'col' => 'mime_type',
                'type' => 'normal', // normal, unique, fulltext, spatial
                'index_type' => '', // hash, btree
                'index_option' => '',
                'algorithm_option' => '',
                'lock_option' => ''
            )
        );
    }

    /**
     * پیش ذخیره را انجام می‌دهد
     *
     * @param boolean $create
     *            حالت
     *            ساخت یا به روز رسانی را تعیین می‌کند
     */
    function preSave($create = false)
    {
        if($this->_internal){
            // this function do nothing in the internal state.
            return;
        }
        if ($this->id == '') {
            $this->creation_dtime = gmdate('Y-m-d H:i:s');
        }
        $this->modif_dtime = gmdate('Y-m-d H:i:s');
        // File path
        $path = $this->getAbsloutPath();
        // file size
        if (file_exists($path)) {
            $this->file_size = filesize($path);
        } else {
            $this->file_size = 0;
        }
        // mime type (based on file name)
        $mime_type = $this->mime_type;
        if (! isset($mime_type) || $mime_type === 'application/octet-stream') {
            $fileInfo = Pluf_FileUtil::getMimeType($this->file_name);
            $this->mime_type = $fileInfo[0];
        }
    }
    
    function internalUpdate($where = ''){
        $this->_internal = true;
        try{
            $this->update($where);
        }catch ( \Pluf\Exception $e){
        }
        $this->_internal = false;
    }

    /**
     * حالت کار ایجاد شده را به روز می‌کند
     *
     * @see Pluf_Model::postSave()
     */
    function postSave($create = false)
    {
        //
    }

    /**
     * \brief عملیاتی که قبل از پاک شدن است انجام می‌شود
     *
     * عملیاتی که قبل از پاک شدن است انجام می‌شود
     * در این متد فایل مربوط به است حذف می شود. این عملیات قابل بازگشت نیست
     */
    function preDelete()
    {
        // remove related file
        $filename = $this->getAbsloutPath();
        if (is_file($filename)) {
            unlink($filename);
        }
    }

    /**
     * مسیر کامل محتوی را تعیین می‌کند. این مسیر حاوی اسم فایل هم هست.
     *
     * @return string
     */
    public function getAbsloutPath()
    {
        return $this->file_path;
    }
    
//     /**
//      * Returns an object which manages the content. This function find the manager from the setting of the tenant.
//      * The setting key which this function looks to find the manager of the content is named 'Cms.Content.Manager'.
//      * If there is no setting in the tenant with this key, this function uses the class 'Editoral'.
//      *
//      * @return CMS_Content_Manager
//      */
//     function getManager()
//     {
//         $managerClassName = $this->manager;
//         if (! isset($managerClassName) || empty($managerClassName)){
//             $managerClassName = Tenant_Service::setting('Cms.Content.Manager', 'Editoral');
//             $this->manager = $managerClassName;
//         }
//         $managerClassName = 'CMS_Content_Manager_'.$managerClassName;
//         return new $managerClassName();
//     }
    function getManager()
    {
        return new CMS_Content_Manager_Editoral();
    }
    
}
