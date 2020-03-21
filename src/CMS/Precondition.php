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
 * Content management system conditions.
 *
 * در بسیاری از موارد لایه نمایش تنها با در نظر گرفتن برخی پیش شرط‌ها قابل دسترسی است
 * در این کلاس پیش شرطهای استاندارد تعریف شده است.
 */
class CMS_Precondition
{
    /**
     * Check if the user is an author. 
     * 
     * A user is author if he/she has one of the following permissions:
     * <ul>
     * <li>tenant.owner</li>
     * <li>cms.editor</li>
     * <li>cms.author</li>
     * </ul>
     *
     * @param Pluf_HTTP_Request
     * @return boolean|\Pluf\Exception_PermissionDenied
     */
    static public function authorRequired($request)
    {
        $res = User_Precondition::loginRequired($request);
        if (true !== $res) {
            return $res;
        }
        if ($request->user->hasPerm('tenant.owner') || //
        $request->user->hasPerm('cms.editor') || //
        $request->user->hasPerm('cms.author')) {
            return true;
        }
        throw new \Pluf\Exception_PermissionDenied();
    }
    /**
     * Check if the user is an author.
     * 
     * User is author if he/she has one of the following permissions:
     * <ul>
     * <li>tenant.owner</li>
     * <li>cms.editor</li>
     * <li>cms.author</li>
     * </ul>
     *
     * @param Pluf_HTTP_Request
     * @return boolean
     */
    static public function isAuthor($request)
    {
        if (! User_Precondition::isLogedIn($request)){
            return false;
        }
        if ($request->user->hasPerm('tenant.owner') || //
        $request->user->hasPerm('cms.editor') || //
        $request->user->hasPerm('cms.author')) {
            return true;
        }
        return false;
    }
    /**
     * Check if the user is an editor.
     * 
     * A user is editor if he/she has one of the following permissions:
     * <ul>
     * <li>tenant.owner</li>
     * <li>cms.editor</li>
     * </ul>
     *
     * @param Pluf_HTTP_Request
     * @return boolean|\Pluf\Exception_PermissionDenied
     */
    static public function editorRequired($request)
    {
        $res = User_Precondition::loginRequired($request);
        if (true !== $res) {
            return $res;
        }
        if ($request->user->hasPerm('tenant.owner') || $request->user->hasPerm('cms.editor')) {
            return true;
        }
        return false;
    }
    /**
     * Check if the user is an editor.
     * User is editor if he/she has one of the following permissions:
     * <ul>
     * <li>tenant.owner</li>
     * <li>cms.editor</li>
     * </ul>
     *
     * @param Pluf_HTTP_Request
     * @return boolean
     */
    static public function isEditor($request)
    {
        if (! User_Precondition::isLogedIn($request)){
            return false;
        }
        if ($request->user->hasPerm('tenant.owner') || $request->user->hasPerm('cms.editor')) {
            return true;
        }
        return false;
    }

}

