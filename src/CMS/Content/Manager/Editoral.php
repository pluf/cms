<?php
Pluf::loadFunction('Pluf_Shortcuts_GetFormForModel');
Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
Pluf::loadFunction('Pluf_Shortcuts_GetRequestParamOr403');
Pluf::loadFunction('Pluf_Shortcuts_GetRequestParam');

/**
 * Editoral Content manager
 *
 * @author hadi<mohammad.hadi.mansouri@dpq.co.ir>
 * @author maso<mostafa.barmshory@dpq.co.ir>
 */
class CMS_Content_Manager_Editoral extends CMS_Content_Manager_Abstract
{

    /**
     * State machine of the manager
     */
    private static $STATE_MACHINE = array(
        Pluf\Workflow\Machine::STATE_UNDEFINED => array(
            'next' => 'planning'
        ),
        // States
        'planning' => array(
            'research' => array(
                'next' => 'researching',
                'visible' => true,
                'title' => 'Research',
                'description' => 'The content needs researching',
                'properties' => CMS_Content_Event::COMMON_PROPERTIES,
                'action' => CMS_Content_Event::ADD_COMMENT_ACTION
            )
        ),
        'researching' => array(
            'setUpNext' => array(
                'next' => 'up-next',
                'visible' => true,
                'title' => 'To Up-Next',
                'description' => 'Sends content to up-next phase',
                'properties' => CMS_Content_Event::COMMON_PROPERTIES,
                'action' => CMS_Content_Event::ADD_COMMENT_ACTION
            )
        ),
        'up-next' => array(
            'write' => array(
                'next' => 'writing',
                'visible' => true,
                'title' => 'Write',
                'description' => 'Start to write the content',
                'properties' => CMS_Content_Event::COMMON_PROPERTIES,
                'action' => CMS_Content_Event::ADD_COMMENT_ACTION
            )
        ),
        'writing' => array(
            'edit' => array(
                'next' => 'editing',
                'visible' => true,
                'title' => 'Edit',
                'description' => 'Start to edit the content',
                'properties' => CMS_Content_Event::COMMON_PROPERTIES,
                'action' => CMS_Content_Event::ADD_COMMENT_ACTION
            )
        ),
        'editing' => array(
            'makeGraphics' => array(
                'next' => 'making-graphics',
                'visible' => true,
                'title' => 'Make Graphics',
                'description' => 'Start to make graphics of the content',
                'properties' => CMS_Content_Event::COMMON_PROPERTIES,
                'action' => CMS_Content_Event::ADD_COMMENT_ACTION
            )
        ),
        'making-graphics' => array(
            'setReady' => array(
                'next' => 'ready',
                'visible' => true,
                'title' => 'Set Ready',
                'description' => 'Set content as ready to publish',
                'properties' => CMS_Content_Event::COMMON_PROPERTIES,
                'action' => CMS_Content_Event::ADD_COMMENT_ACTION
            )
        ),
        'ready' => array(
            'publish' => array(
                'next' => 'published',
                'visible' => true,
                'title' => 'Publish',
                'description' => 'Publish the content',
                'properties' => CMS_Content_Event::COMMON_PROPERTIES,
                'action' => CMS_Content_Event::ADD_COMMENT_ACTION
            )
        )
    );

    /**
     * State machine of the manager
     */
    public function getStates()
    {
        return self::$STATE_MACHINE;
    }

    /**
     *
     * {@inheritdoc}
     * @see CMS_Content_Manager::createContentFilter()
     */
    public function createContentFilter($request)
    {
        if (CMS_Precondition::isEditor($request)) {
            return new Pluf_SQL();
        }
        if (CMS_Precondition::isAuthor($request)) {
            return new Pluf_SQL('author_id=%d', array(
                $request->user->id
            ));
        }
        return new Pluf_SQL('false');
    }
}
