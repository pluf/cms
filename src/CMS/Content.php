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
 * ساختار داده‌ای یک دستگاه را تعیین می‌کند.
 *
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 *        
 */
class CMS_Content extends Pluf_Model
{

    /**
     * مدل داده‌ای را بارگذاری می‌کند.
     *
     * @see Pluf_Model::init()
     */
    function init()
    {
        $this->_a['table'] = 'cms_content';
        $this->_a['cols'] = array(
            // شناسه‌ها
            'id' => array(
                'type' => 'Pluf_DB_Field_Sequence',
                'blank' => false,
                'verbose' => __('first name'),
                'help_text' => __('id'),
                'editable' => false
            ),
            // فیلدها
            'name' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => true,
                'size' => 64,
                'unique' => true,
                'verbose' => __('name'),
                'help_text' => __('content name'),
                'editable' => true
            ),
            'title' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => true,
                'size' => 250,
                'default' => 'no title',
                'verbose' => __('title'),
                'help_text' => __('content title'),
                'editable' => true
            ),
            'description' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => true,
                'size' => 250,
                'default' => 'auto created content',
                'verbose' => __('description'),
                'help_text' => __('content description'),
                'editable' => true
            ),
            'mime_type' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => false,
                'size' => 64,
                'default' => 'application/octet-stream',
                'verbose' => __('mime type'),
                'help_text' => __('content mime type'),
                'editable' => true
            ),
            'file_path' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => false,
                'size' => 250,
                'verbose' => __('file path'),
                'help_text' => __('content file path'),
                'editable' => false,
                'readable' => false
            ),
            'file_name' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => false,
                'size' => 250,
                'default' => 'unknown',
                'verbose' => __('file name'),
                'help_text' => __('content file name'),
                'editable' => false
            ),
            'file_size' => array(
                'type' => 'Pluf_DB_Field_Integer',
                'blank' => false,
                'default' => 'no title',
                'verbose' => __('file size'),
                'help_text' => __('content file size'),
                'editable' => false
            ),
            'downloads' => array(
                'type' => 'Pluf_DB_Field_Integer',
                'blank' => false,
                'default' => 0,
                'default' => 'no title',
                'verbose' => __('downloads'),
                'help_text' => __('content downloads number'),
                'editable' => false
            ),
            'creation_dtime' => array(
                'type' => 'Pluf_DB_Field_Datetime',
                'blank' => true,
                'verbose' => __('creation'),
                'help_text' => __('content creation time'),
                'editable' => false
            ),
            'modif_dtime' => array(
                'type' => 'Pluf_DB_Field_Datetime',
                'blank' => true,
                'verbose' => __('modification'),
                'help_text' => __('content modification time'),
                'editable' => false
            )
        );
        
        $this->_a['idx'] = array(
        // @Note: hadi - 1396-10: when define an attribute as 'unique => true', pluf automatically
        // create an unique index for it (for example 'name' field here). So following codes are extra.
//             'content_idx' => array(
//                 'col' => 'name',
//                 'type' => 'unique', // normal, unique, fulltext, spatial
//                 'index_type' => '', // hash, btree
//                 'index_option' => '',
//                 'algorithm_option' => '',
//                 'lock_option' => ''
//             ),
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
        if(!isset($mime_type)){
            $fileInfo = Pluf_FileUtil::getMimeType($this->file_name);
            $this->mime_type = $fileInfo[0];
        }
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
        $filename = $this->file_path . '/' . $this->id;
        if (is_file($filename)) {
            unlink($filename);
        }
    }

    /**
     * مسیر کامل محتوی را تعیین می‌کند.
     *
     * @return string
     */
    public function getAbsloutPath()
    {
        return $this->file_path . '/' . $this->id;
    }
}