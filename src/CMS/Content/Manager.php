<?php

/**
 * Manages orders in different states and handles events on orders.
 * 
 * The order manager should manage orders in different states and handle events on orders.
 * Each implementation could define its own states and events for orders. 
 * However all implementations should handle the following events:
 * <ul>
 * <li>create: to create a new order this event will be occured</li>
 * <li>update: to update an order this event will be occured</li>
 * <li>delete: to delete an order this event will be occured</li>
 * </ul>
 * 
 * Note: If 'secureId' is sets in the REQUEST parameters, then access MUST not be checked.
 *
 * @author maso<mostafa.barmshory@dpq.co.ir>
 * @author hadi<mohammad.hadi.mansouri@dpq.co.ir>
 */
interface CMS_Content_Manager
{

    /**
     * Creates a content filter
     * 
     * This filter is used to list contents based on states and the request. For
     * example, all contents will be displayed to the owner of the system.
     *
     * @param Pluf_HTTP_Request $request
     * @return Pluf_SQL
     */
    public function createContentFilter ($request);

    /**
     * Apply action on content
     *
     * Each content must follow CRUD actions in life cycle. Here is default action
     * list:
     *
     * <ul>
     * <li>create</li>
     * <li>read</li>
     * <li>update</li>
     * <li>delete</li>
     * </ul>
     *
     * @param CMS_Content $content
     * @param String $action
     * @param Boolean $save to save or not the content
     * @return CMS_Content
     */
    public function apply ($order, $action, $save = false);

    /**
     * Returns possible transitions for given content
     * 
     * Returns possible transitions respect to currecnt state of given content.
     *
     * @param CMS_Content $order
     * @return array array of transitions
     */
    public function transitions ($order);
}