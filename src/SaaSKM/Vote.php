<?php

/**
 * ساختار داده‌ای رای را تعیین می‌کند.
 * 
 * @author maso <mostafa.barmshory@dpq.co.ir>
 *
 */
class SaaSKM_Vote extends Pluf_Model
{

    /**
     * @brief مدل داده‌ای را بارگذاری می‌کند.
     *
     * @see Pluf_Model::init()
     */
    function init ()
    {
        $this->_a['table'] = 'saaskm_vote';
        $this->_a['model'] = 'SaaSKM_Vote';
        $this->_model = 'SaaSKM_Vote';
        $this->_a['cols'] = array(
                'id' => array(
                        'type' => 'Pluf_DB_Field_Sequence',
                        'blank' => true
                ),
                'owner_id' => array(
                        'type' => 'Pluf_DB_Field_Integer',
                        'blank' => false,
                        'verbose' => __('owner ID')
                ),
                'owner_class' => array(
                        'type' => 'Pluf_DB_Field_Varchar',
                        'blank' => false,
                        'size' => 50,
                        'verbose' => __('owner class'),
                        'help_text' => __(
                                'For example Pluf_User or Pluf_Group.')
                ),
                'vote_comment' => array(
                        'type' => 'Pluf_DB_Field_Varchar',
                        'verbose' => __('comment'),
                        'help_text' => __('id of a location')
                ),
                'vote_value' => array(
                        'type' => 'Pluf_DB_Field_Integer',
                        'blank' => false,
                        'verbose' => __('like'),
                        'help_text' => __('like or dislike filed of a location')
                ),
                'creation_dtime' => array(
                        'type' => 'Pluf_DB_Field_Datetime',
                        'blank' => true,
                        'verbose' => __('creation date')
                ),
                'modif_dtime' => array(
                        'type' => 'Pluf_DB_Field_Datetime',
                        'blank' => true,
                        'verbose' => __('modification date')
                ),
                /*
                 * relations
                 */
                'voter' => array(
                        'type' => 'Pluf_DB_Field_Foreignkey',
                        'model' => 'Pluf_User',
                        'blank' => false,
                        'verbose' => __('user'),
                        'help_text' => __('id of a user')
                ),
                'tenant' => array(
                        'type' => 'Pluf_DB_Field_Foreignkey',
                        'model' => 'SaaS_Application',
                        'blank' => false,
                        'verbose' => __('tenant'),
                        'help_text' => __('Related tenant.')
                )
        );
        
        $this->_a['idx'] = array(
                'voter_class_idx' => array(
                        'type' => 'unique',
                        'col' => 'tenant, owner_class, owner_id, voter'
                )
        );
    }

    /**
     * \brief پیش ذخیره را انجام می‌دهد
     *
     * @param $create حالت
     *            ساخت یا به روز رسانی را تعیین می‌کند
     */
    function preSave ($create = false)
    {
        if ($this->id == '') {
            $this->creation_dtime = gmdate('Y-m-d H:i:s');
        }
        $this->modif_dtime = gmdate('Y-m-d H:i:s');
    }

    /**
     * حالت کار ایجاد شده را به روز می‌کند
     *
     * @see Pluf_Model::postSave()
     */
    function postSave ($create = false)
    {
        //
    }
}