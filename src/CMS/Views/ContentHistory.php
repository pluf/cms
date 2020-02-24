<?php
Pluf::loadFunction('Pluf_Shortcuts_GetFormForModel');
Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
Pluf::loadFunction('Pluf_Shortcuts_GetListCount');

class CMS_Views_ContentHistory extends Pluf_Views
{

    /**
     * Returns history of content
     *
     * @param Pluf_HTTP_Request $request            
     * @param array $match            
     * @return Pluf_Paginator
     */
    public static function find($request, $match)
    {
        if (isset($match['name'])) {
            $content = CMS_Shortcuts_GetNamedContentOr404($match['name']);
        } else {
            $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $match['contentId']);
        }
        CMS_Views::checkAccess($request, $content);
        $pag = new Pluf_Paginator(new CMS_ContentHistory());
        $pag->forced_where = new Pluf_SQL('`content_id`=' . $content->id);
        $pag->list_filters = array(
            'id',
            'action',
            'state',
            'actor_id',
            'content_id'
        );
        $search_fields = array(
            'action',
            'state',
            'description',
            'creation_dtime'
        );
        $sort_fields = array(
            'id',
            'action',
            'state',
            'creation_dtime',
            'actor_id',
            'content_id'
        );
        $pag->items_per_page = Pluf_Shortcuts_GetListCount($request);
        $pag->configure(array(), $search_fields, $sort_fields);
        $pag->setFromRequest($request);
        return $pag;
    }

    /**
     *
     * @param Pluf_HTTP_Request $request            
     * @param array $match            
     * @return CMS_ContentHistory
     */
    public static function get($request, $match)
    {
        if (isset($match['name'])) {
            $content = CMS_Shortcuts_GetNamedContentOr404($match['name']);
        } else {
            $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $match['contentId']);
        }
        CMS_Views::checkAccess($request, $content);
        /**
         *
         * @var CMS_ContentHistory $contentHistory
         */
        $contentHistory = Pluf_Shortcuts_GetObjectOr404('CMS_ContentHistory', $match['historyId']);
        if ($contentHistory->content_id !== $content->id) {
            throw new Pluf_HTTP_Error404('Content with id ' . $content->id . ' has no history with id ' . $contentHistory->id);
        }
        return $contentHistory;
    }

}

