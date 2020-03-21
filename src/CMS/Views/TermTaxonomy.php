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
Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');

class CMS_Views_TermTaxonomy extends Pluf_Views
{

    public static function addContent($request, $match)
    {
        $tt = Pluf_Shortcuts_GetObjectOr404('CMS_TermTaxonomy', $match['parentId']);
        $content = NULL;
        if (array_key_exists('modelId', $match)) {
            $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $match['modelId']);
        } elseif (array_key_exists('id', $request->REQUEST)) {
            $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $request->REQUEST['id']);
        }
        if (! isset($content)) {
            throw new \Pluf\Exception_BadRequest('Content is not determined');
        }
        // بررسی دسترسی‌ها
        if (! CMS_Precondition::isAuthor($request)) {
            throw new \Pluf\Exception_PermissionDenied('You are not an author');
        }
        if (! CMS_Precondition::isEditor($request) && $request->user->id !== $content->author_id) {
            throw new \Pluf\Exception_PermissionDenied('You can not change content created by another author');
        }
        // Check if association is existed already
        $relatedContents = $tt->get_contents_ids_list(array(
            'filter' => 'id=' . $content->id
        ));
        if ($relatedContents->count() === 0) {
            $tt->setAssoc($content);
            $tt->count += 1;
            $tt->update();
        }
        return $content;
    }

    /**
     * Finds contents related to a term-taxonomy
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return Pluf_HTTP_Response_Json
     */
    public function findContents($request, $match)
    {
        $tt = Pluf_Shortcuts_GetObjectOr404('CMS_TermTaxonomy', $match['parentId']);
        $content = new CMS_Content();
        Pluf::loadFunction('Pluf_Shortcuts_GetAssociationTableName');
        $assoc = $content->_con->pfx . Pluf_Shortcuts_GetAssociationTableName('CMS_Content', 'CMS_TermTaxonomy');
        $t_content = $content->_con->pfx . $content->_a['table'];
        Pluf::loadFunction('Pluf_Shortcuts_GetForeignKeyName');
        $content_fk = Pluf_Shortcuts_GetForeignKeyName('CMS_Content');
        $tt_fk = Pluf_Shortcuts_GetForeignKeyName('CMS_TermTaxonomy');
        $content->_a['views'] = array(
            'join_tt' => array(
                'join' => 'LEFT JOIN ' . $assoc . ' ON ' . $t_content . '.id=' . $content_fk
            )
        );
        $sql = new Pluf_SQL('`' . $tt_fk . '`=%s', array(
            $tt->id
        ));
        $builder = new Pluf_Paginator_Builder($content);
        return $builder->setRequest($request)
            ->setView('join_tt')
            ->setWhereClause($sql)
            ->build();
    }

    public static function removeContent($request, $match)
    {
        $tt = Pluf_Shortcuts_GetObjectOr404('CMS_TermTaxonomy', $match['parentId']);
        $content = NULL;
        if (array_key_exists('modelId', $match)) {
            $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $match['modelId']);
        } elseif (array_key_exists('id', $request->REQUEST)) {
            $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $request->REQUEST['id']);
        }
        if (! isset($content)) {
            throw new \Pluf\Exception_BadRequest('Content is not determined');
        }
        // بررسی دسترسی‌ها
        if (! CMS_Precondition::isAuthor($request)) {
            throw new \Pluf\Exception_PermissionDenied('You are not an author');
        }
        if (! CMS_Precondition::isEditor($request) && $request->user->id !== $content->author_id) {
            throw new \Pluf\Exception_PermissionDenied('You can not change content created by another author');
        }
        // Check if association is existed
        $relatedContents = $tt->get_contents_ids_list(array(
            'filter' => 'id=' . $content->id
        ));
        if ($relatedContents->count() > 0) {
            $tt->delAssoc($content);
            $tt->count -= 1;
            $tt->update();
        }
        return $content;
    }

    public static function taxonomies($request, $match)
    {
        $p = array(
            'select' => '`id`,`taxonomy`,count(`term_id`) as `count`',
            'group' => '`taxonomy`'
        );
        $tt = new CMS_TermTaxonomy();
        $items = (array) $tt->getList($p);
        $page = array(
            'items' => $items,
            'counts' => count($items),
            'current_page' => 0,
            'items_per_page' => count($items),
            'page_number' => 1
        );
        return $page;
    }
}


