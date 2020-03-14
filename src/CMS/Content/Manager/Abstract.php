<?php
Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
Pluf::loadFunction('Pluf_Shortcuts_GetRequestParamOr403');
Pluf::loadFunction('Pluf_Shortcuts_GetRequestParam');

/**
 * Abstract Order manager
 *
 * @author hadi<mohammad.hadi.mansouri@dpq.co.ir>
 * @author maso<mostafa.barmshory@dpq.co.ir>
 */
abstract class CMS_Content_Manager_Abstract implements CMS_Content_Manager
{

    /**
     *
     * {@inheritdoc}
     * @see CMS_Content_Manager::apply()
     */
    public function apply($content, $action, $save = false)
    {
        $machine = new Pluf\Workflow\Machine();
        $machine->setStates($this->getStates())
            ->setSignals(array('CMS_Content::stateChanged'))
            ->setProperty('state')
            ->apply($content, $action);
        if ($save) {
            return $content->update();
        }
        return true;
    }

    /**
     *
     * {@inheritdoc}
     * @see CMS_Content_Manager::transitions()
     */
    public function transitions($content)
    {
        $states = $this->getStates();
        $transtions = array();
        if (! array_key_exists($content->state, $states) || (! is_array($states[$content->state]) && ! is_object($states[$content->state]))) {
            return $transtions;
        }
        foreach ($states[$content->state] as $id => $trans) {
            $trans['id'] = $id;
            // TODO: check preconditions and return only verified transitions
            unset($trans['preconditions']);
            unset($trans['action']);
            $transtions[] = $trans;
        }
        return $transtions;
    }

    /**
     * Gets list of states
     */
    abstract function getStates();

    /**
     *
     * @param string $signal
     * @param $event
     */
    public static function addHistory($signal, $event)
    {
        // Converts event name from camel case to underscored
        $underscored = ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $event->event)), '_');

        $history = new CMS_ContentHistory();
        $history->content_id = $event->object;
        if (isset($event->request->user)) {
            $history->actor_id = $event->request->user;
        }
        $history->action = $underscored;
        $history->state = '' . Pluf_Shortcuts_GetRequestParam($event->request, 'state');
        $history->description = '' . Pluf_Shortcuts_GetRequestParam($event->request, 'description');
        $history->create();
    }
}
