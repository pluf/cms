<?php

class CMS_Content_Event
{

    /*
     * Properties
     */
    public const PROPERTY_COMMENT = array(
        'name' => 'description',
        'type' => 'String',
        'unit' => 'none',
        'title' => 'Description',
        'description' => 'A description text to put to the history',
        'editable' => true,
        'visible' => true,
        'priority' => 5,
        'defaultValue' => '',
        'validators' => []
    );
    public const PROPERTY_STATE = array(
        'name' => 'state',
        'type' => 'String',
        'unit' => 'none',
        'title' => 'State',
        'description' => 'The new state of the content',
        'editable' => true,
        'visible' => true,
        'priority' => 4,
        'defaultValue' => '',
        'validators' => []
    );
    // End of properties

    public const COMMON_PROPERTIES = array(
        self::PROPERTY_COMMENT
    );
    
    /*
     * Actions
     */
    public const ADD_COMMENT_ACTION = array(
        'CMS_Content_Event',
        'addComment'
    );

    // End of actions

    /**
     * Adds comment into the order
     *
     * @param Pluf_HTTP_Request $request
     * @param CMS_Content $object
     */
    public static function addComment($request, $object)
    {
        // Note: hadi, 98-08-04: there is no need to do any action.
        // A history will be added by propagating an state change signal.
    }

    /*
     * Preconditions
     */
    public static function isAuthor($request){
        return CMS_Precondition::isAuthor($request);
    }
    
    public static function isEditor($request){
        return CMS_Precondition::isEditor($request);
    }
    
}