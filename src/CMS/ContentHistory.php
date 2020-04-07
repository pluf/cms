<?php

/**
 * Defines structure of the history of the actions on a cms-content
 * 
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 *
 */
class CMS_ContentHistory extends Pluf_Model
{

    /**
     * @brief مدل داده‌ای را بارگذاری می‌کند.
     *
     * @see Pluf_Model::init()
     */
    function init()
    {
        $this->_a['table'] = 'cms_content_histories';
        $this->_a['verbose'] = 'CMS Content History';
        $this->_a['cols'] = array(
            'id' => array(
                'type' => 'Sequence',
                'blank' => false,
                'is_null' => false,
                'editable' => false,
                'readable' => true
            ),
            'action' => array(
                'type' => 'Varchar',
                'blank' => false,
                'is_null' => false,
                'size' => 100,
                'editable' => false,
                'readable' => true
            ),
            'workflow' => array(
                'type' => 'Varchar',
                'is_null' => true,
                'default' => '',
                'size' => 100,
                'editable' => true,
                'readable' => true
            ),
            'state' => array(
                'type' => 'Varchar',
                'blank' => true,
                'size' => 50,
                'editable' => true,
                'readable' => true
            ),
            'description' => array(
                'type' => 'Varchar',
                'blank' => true,
                'is_null' => true,
                'size' => 250,
                'editable' => false,
                'readable' => true
            ),
            'creation_dtime' => array(
                'type' => 'Datetime',
                'blank' => true,
                'editable' => false,
                'readable' => true
            ),
            /*
             * Relations
             */
            'actor_id' => array(
                'type' => 'Foreignkey',
                'model' => 'User_Account',
                'blank' => true,
                'is_null' => true,
                'name' => 'actor',
                'graphql_name' => 'actor',
                'relate_name' => 'managed_contents',
                'editable' => false,
                'readable' => true
            ),
            'content_id' => array(
                'type' => 'Foreignkey',
                'model' => 'CMS_Content',
                'blank' => false,
                'is_null' => false,
                'name' => 'content',
                'graphql_name' => 'content',
                'relate_name' => 'histories',
                'editable' => false,
                'readable' => true
            )
        );
        
        $this->_a['idx'] = array(
            'content_history_idx' => array(
                'col' => 'content_id',
                'type' => 'normal', // normal, unique, fulltext, spatial
                'index_type' => '', // hash, btree
                'index_option' => '',
                'algorithm_option' => '',
                'lock_option' => ''
            ),
            'actor_of_content_history_idx' => array(
                'col' => 'actor_id',
                'type' => 'normal', // normal, unique, fulltext, spatial
                'index_type' => '', // hash, btree
                'index_option' => '',
                'algorithm_option' => '',
                'lock_option' => ''
            )
        );
    }

    /**
     * \brief پیش ذخیره را انجام می‌دهد
     *
     * @param $create boolean
     *            ساخت یا به روز رسانی را تعیین می‌کند
     */
    function preSave($create = false)
    {
        if ($this->id == '') {
            $this->creation_dtime = gmdate('Y-m-d H:i:s');
        }
    }
}