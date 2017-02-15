<?php

/**
 * به روزرسانی یک دارایی
 *
 * با استفاده از این فرم می‌توان اطلاعات یک دارایی را به روزرسانی کرد.
 *
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 *
 */
class SDP_Form_AssetUpdate extends Pluf_Form
{
    
    private $userRequest = null;
    public $asset = null;

    public function initFields($extra = array())
    {
        $this->userRequest = $extra['request'];
        $this->asset = $extra['asset'];
        
        $this->fields['name'] = new Pluf_Form_Field_Varchar(array(
            'required' => false,
            'label' => 'Name of Asset',
            'initial' => $this->asset->name,
            'help_text' => 'Name of Asset'
        ));
        $this->fields['path'] = new Pluf_Form_Field_Varchar(array(
            'required' => false,
            'label' => 'Path of Asset',
            'initial' => $this->asset->path,
            'help_text' => 'Path of Asset'
        ));
        $this->fields['size'] = new Pluf_Form_Field_Varchar(array(
            'required' => false,
            'label' => 'Size of Asset',
            'initial' => $this->asset->size,
            'help_text' => 'Size of Asset'
        ));
        $this->fields['download'] = new Pluf_Form_Field_Varchar(array(
            'required' => false,
            'label' => 'download of Asset',
            'initial' => $this->asset->download,
            'help_text' => 'download of Asset'
        ));
        $this->fields['driver_type'] = new Pluf_Form_Field_Varchar(array(
            'required' => false,
            'label' => 'driver_type of Asset',
            'initial' => $this->asset->driver_type,
            'help_text' => 'driver_type of Asset'
        ));
        $this->fields['driver_id'] = new Pluf_Form_Field_Varchar(array(
            'required' => false,
            'label' => 'driver_id of Asset',
            'initial' => $this->asset->driver_id,
            'help_text' => 'driver_id of Asset'
        ));
        $this->fields['type'] = new Pluf_Form_Field_Varchar(array(
            'required' => false,
            'label' => 'type of Asset',
            'initial' => $this->asset->type,
            'help_text' => 'type of Asset'
        ));
        $this->fields['description'] = new Pluf_Form_Field_Varchar(array(
            'required' => false,
            'label' => 'description of Asset',
            'initial' => $this->asset->description,
            'help_text' => 'description of Asset'
        ));
        
        $this->fields['parent'] = new Pluf_Form_Field_Varchar(array(
            'required' => false,
            'label' => 'Parent',
            'initial' => $this->asset->parent,
            'help_text' => 'Parent of asset'
        ));
        $this->fields['price'] = new Pluf_Form_Field_Varchar(array(
            'required' => false,
            'label' => 'Price',
            'initial' => $this->asset->price,
            'help_text' => 'Price of asset'
        ));
        $this->fields['content'] = new Pluf_Form_Field_Integer(array(
            'required' => false,
            'label' => 'content id of Asset',
            'initial' => $this->asset->content,
            'help_text' => 'content of Asset'
        ));
        $this->fields['thumbnail'] = new Pluf_Form_Field_Integer(array(
            'required' => false,
            'label' => 'thumbnail of Asset',
            'initial' => $this->asset->thumbnail,
            'help_text' => 'thumbnail of Asset'
        ));
        $this->fields['file'] = new Pluf_Form_Field_File(array(
            'required' => false,
            'max_size' => Pluf::f('upload_max_size', 2097152),
            'move_function_params' => array(
                'upload_path' => Pluf::f('upload_path') . '/' . Pluf_Tenant::current()->id . '/sdp',
                'file_name' => $this->asset->id,
                'upload_path_create' => true,
                'upload_overwrite' => true
            )
        ));
    }

    function update($commit = true)
    {
        if (! $this->isValid()) {
            throw new Pluf_Exception('cannot save the content from an invalid form');
        }
        // update the asset
        $this->asset->setFromFormData($this->cleaned_data);
        
        if (array_key_exists('file', $this->userRequest->FILES)) {
            // Extract information of file
            $myFile = $this->userRequest->FILES['file'];
            $this->asset->mime_type = $myFile['type'];
            $this->asset->size = filesize($this->asset->path . '/' . $this->asset->id);
        }
        
        if ($commit) {
            $this->asset->update();
        }
        return $this->asset;
    }

    function clean_name()
    {
        $fileName = $this->cleaned_data['name'];
        if (! $fileName){
            return array_key_exists('file', $this->data) ? $this->data['file']['name'] : $this->asset->name;
        }
        return $fileName;
    }
}