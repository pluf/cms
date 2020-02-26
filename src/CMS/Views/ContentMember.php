<?php
Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');

class CMS_Views_ContentMember extends Pluf_Views
{
    public function members($request, $match)
    {
        $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $match['parentId']);
        $user = new User_Account();
        $userTable = $user->_con->pfx . $user->_a['table'];
        Pluf::loadFunction('Pluf_Shortcuts_GetAssociationTableName');
        $assocTable = $user->_con->pfx . Pluf_Shortcuts_GetAssociationTableName('User_Account', 'CMS_Content');
        Pluf::loadFunction('Pluf_Shortcuts_GetForeignKeyName');
        $userFk = Pluf_Shortcuts_GetForeignKeyName('User_Account');
        $user->_a['views']['myView'] = array(
            'select' => $user->getSelect(),
            'join' => 'LEFT JOIN ' . $assocTable . ' ON ' . $userTable . '.id=' . $assocTable . '.' . $userFk
        );
        
        $contentFk = Pluf_Shortcuts_GetForeignKeyName('CMS_Content');
        $builder = new Pluf_Paginator_Builder($user);
        return $builder->setWhereClause(new Pluf_SQL($contentFk.'=%s', array(
            $content->id
        )))
        ->setView('myView')
        ->setRequest($request)
        ->build();
    }
    
    public static function addMember($request, $match)
    {
        $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $match['parentId']);
        if (isset($match['modelId'])) {
            $userId = $match['modelId'];
        } else {
            $userId = isset($request->REQUEST['id']) ? $request->REQUEST['id'] : $request->REQUEST['modelId'];
        }
        $user = Pluf_Shortcuts_GetObjectOr404('User_Account', $userId);
        $content->setAssoc($user);
        return $user;
    }
    
    public static function removeMember($request, $match)
    {
        $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $match['parentId']);
        if (isset($match['modelId'])) {
            $userId = $match['modelId'];
        } else {
            $userId = isset($request->REQUEST['id']) ? $request->REQUEST['id'] : $request->REQUEST['modelId'];
        }
        $user = Pluf_Shortcuts_GetObjectOr404('User_Account', $userId);
        $content->delAssoc($user);
        return $user;
    }
    
    /**
     * Returns information of a member of a content.
     *
     * @param Pluf_HTTP_Request $request
     * @param array $match
     */
    public static function getMember($request, $match)
    {
        $content = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $match['parentId']);
        $user = new User_Account();
        $userTable = $user->_con->pfx . $user->_a['table'];
        Pluf::loadFunction('Pluf_Shortcuts_GetAssociationTableName');
        $assocTable = $user->_con->pfx . Pluf_Shortcuts_GetAssociationTableName('User_Account', 'CMS_Content');
        Pluf::loadFunction('Pluf_Shortcuts_GetForeignKeyName');
        $userFk = Pluf_Shortcuts_GetForeignKeyName('User_Account');
        $user->_a['views']['myView'] = array(
            'select' => $user->getSelect(),
            'join' => 'LEFT JOIN ' . $assocTable . ' ON ' . $userTable . '.id=' . $assocTable . '.' . $userFk
        );
        
        $contentFk = Pluf_Shortcuts_GetForeignKeyName('CMS_Content');
        $param = array(
            'view' => 'myView',
            'filter' => array(
                'id=' . $match['modelId'],
                $contentFk . '=' . $content->id
            )
        );
        $users = $user->getList($param);
        if ($users->count() == 0) {
            throw new Pluf_Exception_DoesNotExist('Content has not such member');
        }
        return $users[0];
    }
    
}