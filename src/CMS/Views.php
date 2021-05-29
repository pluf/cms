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
Pluf::loadFunction('CMS_Shortcuts_GetNamedContentOr404');

/**
 * Content model
 *
 * @author maso<mostafa.barmshory@dpq.co.ir>
 */
class CMS_Views extends Pluf_Views
{

    /**
     * Creates new content
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @throws \Pluf\Exception
     * @return Pluf_HTTP_Response_Json
     */
    public function create($request, $match)
    {
        // initial content data
        $extra = array(
            'user' => $request->user,
            'model' => new CMS_Content()
        );

        // Create content and get its ID
        $form = new CMS_Form_ContentCreate($request->REQUEST, $extra);

        // Upload content file and extract information about it (by updating
        // content)
        $extra['model'] = $form->save();
        $form = new CMS_Form_ContentUpdate(array_merge($request->REQUEST, $request->FILES), $extra);
        try {
            $content = $form->save();
        } catch ( \Pluf\Exception $e) {
            $content = $extra['model'];
            $content->delete();
            throw $e;
        }
        if(!array_key_exists('state', $request->REQUEST)){
            $manager = $content->getManager();
            $manager->apply($content, 'create');
        }
        return $content;
    }

    /**
     * Gets content meta information
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return Pluf_HTTP_Response_Json
     */
    public function get($request, $match)
    {
        // تعیین داده‌ها
        if (array_key_exists('id', $match)) {
            $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $match['id']);
        } else {
            $content = CMS_Shortcuts_GetNamedContentOr404($match['name']);
        }
        // اجرای درخواست
        return $content;
    }

    /**
     * Updates meta information of content
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @throws Pluf_Exception_PermissionDenied
     * @return Pluf_HTTP_Response
     */
    public function update($request, $match)
    {
        // تعیین داده‌ها
        $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $match['modelId']);
        // بررسی دسترسی‌ها
        if (! CMS_Precondition::isAuthor($request)) {
            throw new Pluf_Exception_PermissionDenied('You are not an author');
        }
        if (! CMS_Precondition::isEditor($request) && $request->user->id !== $content->author_id) {
            throw new Pluf_Exception_PermissionDenied('You can not change content created by another author');
        }
        return parent::updateObject($request, $match, array(
            'model' => 'CMS_Content'
        ));
    }

    /**
     * Deletes a content
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @throws Pluf_Exception_PermissionDenied
     * @return Pluf_HTTP_Response
     */
    public function delete($request, $match)
    {
        // تعیین داده‌ها
        $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $match['modelId']);
        // بررسی دسترسی‌ها
        if (! CMS_Precondition::isAuthor($request)) {
            throw new Pluf_Exception_PermissionDenied('You are not an author');
        }
        if (! CMS_Precondition::isEditor($request) && $request->user->id !== $content->author_id) {
            throw new Pluf_Exception_PermissionDenied('You can not delete content created by another author');
        }
        return parent::deleteObject($request, $match, array(
            'model' => 'CMS_Content'
        ));
    }

    /**
     * Finds contents
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return Pluf_HTTP_Response_Json
     */
    public function find($request, $match)
    {
        $builder = new Pluf_Paginator_Builder(new CMS_Content());
        return $builder->setRequest($request)->build();
    }

    /**
     * Download a content
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return Pluf_HTTP_Response_File
     */
    public function download($request, $match)
    {
        // GET data
        $content = null;
        if (array_key_exists('modelId', $match)) {
            $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $match['modelId']);
        } else {
            $content = CMS_Shortcuts_GetNamedContentOr404($match['name']);
        }
        // Do
        try {
            $response = new Pluf_HTTP_Response_File($content->getAbsloutPath(), $content->mime_type);
            // $response->headers['Content-Disposition'] = sprintf('attachment; filename="%s"', $content->file_name);
            return $response;
        } finally {
            $content->downloads += 1;
            $content->internalUpdate();
        }
    }

    /**
     * Upload a file as content
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return Pluf_HTTP_Response_Json|object
     */
    public function updateFile($request, $match)
    {
        // Get data
        $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $match['modelId']);
        // Check accesss
        if (! CMS_Precondition::isAuthor($request)) {
            throw new Pluf_Exception_PermissionDenied('You are not an author');
        }
        if (! CMS_Precondition::isEditor($request) && $request->user->id !== $content->author_id) {
            throw new Pluf_Exception_PermissionDenied('You can not change content created by another author');
        }
        // Do action
        if (array_key_exists('file', $request->FILES)) {
            $extra = array(
                'model' => $content
            );
            $form = new CMS_Form_ContentUpdate(array_merge($request->REQUEST, $request->FILES), $extra);
            $content = $form->save();
            return $content;
        } else {
            $myfile = fopen($content->getAbsloutPath(), "w") or die("Unable to open file!");
            $entityBody = file_get_contents('php://input', 'r');
            fwrite($myfile, $entityBody);
            fclose($myfile);
            $content->update();
        }
        return $content;
    }

    /**
     * Upload a file as thumbnail
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return Pluf_HTTP_Response_Json|object
     */
    public function updateThumbnail($request, $match)
    {
        throw new  \Pluf\Exception('Not implemented yet!');
    }

    public static function addTermTaxonomy($request, $match)
    {
        $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $match['parentId']);
        $tt = NULL;
        if (array_key_exists('modelId', $match)) {
            $tt = Pluf_Shortcuts_GetObjectOr404('CMS_TermTaxonomy', $match['modelId']);
        } elseif (array_key_exists('id', $request->REQUEST)) {
            $tt = Pluf_Shortcuts_GetObjectOr404('CMS_TermTaxonomy', $request->REQUEST['id']);
        }
        if (! isset($tt)) {
            throw new Pluf_Exception_BadRequest('TermTaxonomy is not determined');
        }
        // بررسی دسترسی‌ها
        if (! CMS_Precondition::isAuthor($request)) {
            throw new Pluf_Exception_PermissionDenied('You are not an author');
        }
        if (! CMS_Precondition::isEditor($request) && $request->user->id !== $content->author_id) {
            throw new Pluf_Exception_PermissionDenied('You can not change content created by another author');
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
        return $tt;
    }

    /**
     * Finds term-taxonomies related to a content
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return Pluf_HTTP_Response_Json
     */
    public function findTermTaxonomies($request, $match)
    {
        $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $match['parentId']);
        $tt = new CMS_TermTaxonomy();
        
        $engine = $content->getEngine();
        $schema = $engine->getSchema();
        
        $assoc = $schema->getRelationTable($tt, $content);
        $tt_table = $schema->getTableName($tt);
        $tt_fk = $schema->getAssocField($tt);
        $content_fk = $schema->getAssocField($content);
        
        
        $tt->setView('join_content',  array(
            'join' => 'LEFT JOIN ' . $assoc . ' ON ' . $tt_table . '.id=' . $tt_fk
        ));
        $sql = new Pluf_SQL($content_fk . '=%s', array(
            $content->id
        ));
        $builder = new Pluf_Paginator_Builder($tt);
        return $builder->setRequest($request)
            ->setView('join_content')
            ->setWhereClause($sql)
            ->build();
    }

    public static function removeTermTaxonomy($request, $match)
    {
        $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $match['parentId']);
        $tt = NULL;
        if (array_key_exists('modelId', $match)) {
            $tt = Pluf_Shortcuts_GetObjectOr404('CMS_TermTaxonomy', $match['modelId']);
        } elseif (array_key_exists('id', $request->REQUEST)) {
            $tt = Pluf_Shortcuts_GetObjectOr404('CMS_TermTaxonomy', $request->REQUEST['id']);
        }
        if (! isset($tt)) {
            throw new Pluf_Exception_BadRequest('Term-taxonomy is not determined');
        }
        // بررسی دسترسی‌ها
        if (! CMS_Precondition::isAuthor($request)) {
            throw new Pluf_Exception_PermissionDenied('You are not an author');
        }
        if (! CMS_Precondition::isEditor($request) && $request->user->id !== $content->author_id) {
            throw new Pluf_Exception_PermissionDenied('You can not change content created by another author');
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
        return $tt;
    }

    // ***********************************************************
    // Meta
    // **********************************************************
    
    public function createOrUpdateMeta($request, $match)
    {
        // Extract order id
        $contentId = self::extractContentId($match);
        $match['parentId'] = $contentId;
        $p = array(
            'parent' => 'CMS_Content',
            'parentKey' => 'content_id',
            'model' => 'CMS_ContentMeta'
            // 'precond' => function($request, $object, $parent) -> {false, true} | throw exception
        );
        // Check if metafield exist
        $metafield = CMS_ContentMeta::getMeta($request->REQUEST['key'], $contentId);
        if (!$metafield) { // Should be created
            return $this->createManyToOne($request, $match, $p);
        } else { // Should be updated
            $match['modelId'] = $metafield->id;
            return $this->updateManyToOne($request, $match, $p);
        }
    }
    
    private static function extractContentId($match)
    {
        if (array_key_exists('parentId', $match)) {
            return $match['parentId'];
        }
        if (array_key_exists('name', $match)) {
            $content = CMS_Shortcuts_GetNamedContentOr404($match['name']);
            return $content->id;
        }
        throw new Pluf_Exception_BadRequest('Not parentId nor name of content is defined');
    }
    
    public static function updateMetaByKey($request, $match)
    {
        $metaField = self::getMetaByKey($request, $match);
        $match['modelId'] = $metaField->id;
        $match['parentId'] = $metaField->content_id;
        $p = array(
            'model' => 'CMS_ContentMeta',
            'parent' => 'CMS_Content',
            'parentKey' => 'content_id'
        );
        $view = new Pluf_Views();
        return $view->updateManyToOne($request, $match, $p);
    }
    
    /**
     * Extract Key of metafield from $match and returns related Metafield
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @throws Pluf_Exception_DoesNotExist if Id is given and Metafield with given id does not exist or is not blong to given Product
     * @return NULL|CMS_ContentMeta
     */
    public static function getMetaByKey($request, $match)
    {
        if (! isset($match['modelKey'])) {
            throw new Pluf_Exception_BadRequest('The modelKey is not set');
        }
        $contentId = self::extractContentId($match);
        $metafield = CMS_ContentMeta::getMeta($match['modelKey'], $contentId);
        if ($metafield === null) {
            throw new Pluf_HTTP_Error404('Object not found (CMS_ContentMeta,' . $match['modelKey'] . ')');
        }
        return $metafield;
    }
    
    public function findByContentName($request, $match)
    {
        $contentId = self::extractContentId($match);
        $match['parentId'] = $contentId;
        $p = array(
            'model' => 'CMS_ContentMeta',
            'parent' => 'CMS_Content',
            'parentKey' => 'content_id'
        );
        return $this->findManyToOne($request, $match, $p);
    }
    
    public function getByContentName($request, $match)
    {
        $contentId = self::extractContentId($match);
        $match['parentId'] = $contentId;
        $p = array(
            'model' => 'CMS_ContentMeta',
            'parent' => 'CMS_Content',
            'parentKey' => 'content_id'
        );
        return $this->getManyToOne($request, $match, $p);
    }
    
    public function updateByContentName($request, $match)
    {
        $contentId = self::extractContentId($match);
        $match['parentId'] = $contentId;
        $p = array(
            'model' => 'CMS_ContentMeta',
            'parent' => 'CMS_Content',
            'parentKey' => 'content_id'
        );
        return $this->updateManyToOne($request, $match, $p);
    }
    
    public function deleteByContentName($request, $match){
        $contentId = self::extractContentId($match);
        $match['parentId'] = $contentId;
        $p = array(
            'model' => 'CMS_ContentMeta',
            'parent' => 'CMS_Content',
            'parentKey' => 'content_id'
        );
        return $this->deleteManyToOne($request, $match, $p);
    }
    
    // ***********************************************************
    // Workflow
    // **********************************************************

    /**
     * Checks access to given content.
     * If request has not access to content it throws an exception.
     *
     * @param Pluf_HTTP_Request $request
     * @param CMS_Content $content
     * @throws \Pluf\Exception
     * @return boolean
     */
    public static function checkAccess($request, $content)
    {
        $manager = $content->getManager();
        $sql = $manager->createContentFilter($request)->SAnd(new Pluf_SQL('id=%s', array(
            $content->id
        )));
        if (0 == $content->getCount(array(
            'filter' => $sql->gen()
        ))) {
            throw new \Pluf\Exception("You are not allowed to access to this content.");
        }
        return true;
    }

    /**
     * Gets lit of possible actions
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     * @return array an array of transitions
     */
    public function actions($request, $match)
    {
        if (isset($match['name'])) {
            $content = CMS_Shortcuts_GetNamedContentOr404($match['name']);
        } else {
            $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $match['contentId']);
        }
        self::checkAccess($request, $content);
        $items = $content->getManager()->transitions($content);
        $page = array(
            'items' => $items,
            'counts' => count($items),
            'current_page' => 1,
            'items_per_page' => count($items),
            'page_number' => 1
        );

        return $page;
    }

    public static function doAction($request, $match)
    {
        if (isset($match['name'])) {
            $content = CMS_Shortcuts_GetNamedContentOr404($match['name']);
        } else {
            $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $match['contentId']);
        }
        self::checkAccess($request, $content);
        $action = $request->REQUEST['action'];
        if($action == 'touch'){
            // Add canonical link of the content to the sitemap links
            // find canonical link of the content
            $canonical = CMS_ContentMeta::getMeta('link.canonical', $content->id);
            if($canonical){
                $mainDomain = Pluf_Tenant::getCurrent()->domain;
                $loc = 'http://' . $mainDomain . $canonical->value;
                $link = Seo_SitemapLink::getLinkByLoc($loc);
                if(!$link){
                    $request->REQUEST['loc'] = $loc;
                    $request->REQUEST['lastmod'] = gmdate('Y-m-d H:i:s');
                    $p = array(
                        'model' => 'Seo_SitemapLink'
                    );
                    parent::createObject($request, array(), $p);
                }else{
                    // Update last_modif of the sitemap link
                    $link->lastmod = gmdate('Y-m-d H:i:s');
                    $link->update();
                }
            }
            return $content;
        }
        $manager = $content->getManager();
        if ($manager->apply($content, $action, true)) {
            $updatedContent = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $content->id);
            return $updatedContent;
        }
        return new \Pluf\Exception('An error is occurred while processing content');
    }
}

