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
Pluf::loadFunction('CMS_Shortcuts_CleanName');

/**
 * ایجاد یک محتوای جدید
 *
 * با استفاده از این فرم می‌توان یک محتوای جدید را ایجاد کرد.
 *
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 *        
 */
class CMS_Form_ContentCreate extends Pluf_Form_Model
{

    public $tenant = null;

    public $user = null;

    public function initFields($extra = array())
    {
        $this->tenant = Pluf_Tenant::current();
        $this->user = $extra['user'];
        parent::initFields($extra);
    }

    public function clean_name()
    {
        $name = $this->cleaned_data['name'];
        if (empty($name))
            return null;
        return CMS_Shortcuts_CleanName($name);
    }

    function save($commit = true)
    {
        if (! $this->isValid()) {
            throw new \Pluf\Exception_Form('cannot save the content from an invalid form', $this);
        }
        // Create the content
        $content = new CMS_Content();
        $content->setFromFormData($this->cleaned_data);
        $cmsPath = Pluf_Tenant::storagePath() . '/cms';
        if (! is_dir($cmsPath)) {
            if (false == @mkdir($cmsPath, 0777, true)) {
                throw new Pluf_Form_Invalid('An error occured when creating the upload path. Please try to send the file again.');
            }
        }
        $content->author_id = $this->user;
        $content->tenant = $this->tenant;
        if ($commit) {
            $content->create();
            $content->file_path = Pluf::f('upload_path') . '/' . $this->tenant->id . '/cms/' . $content->id;
            $content->update();
        }
        return $content;
    }
}
