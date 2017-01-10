<?php
Pluf::loadFunction('SDP_Shortcuts_GetLinkOr404');

class SDP_Views_Link
{

    public static function create($request, $match)
    {
        $asset = SDP_Shortcuts_GetAssetOr404($match['asset_id']);
        
        // initial link data
        $extra = array(
            'user' => $request->user,
            'tenant' => $request->tenant,
            'asset' => $asset
        );
        
        // Create link and get its ID
        $form = new SDP_Form_LinkCreate($request->REQUEST, $extra);
        $link = $form->save();
        return new Pluf_HTTP_Response_Json($link);
    }

    public static function get($request, $match)
    {
        $link = new SDP_Link($match['id']);
        return new Pluf_HTTP_Response_Json($link);
    }

    public static function find($request, $match)
    {
        $links = new Pluf_Paginator(new SDP_Link());
        $sql = new Pluf_SQL('tenant=%s', array(
            $request->tenant->id
        ));
        $links->forced_where = $sql;
        $links->list_filters = array(
            'id',
            'secure_link',
            'expiry',
            'download',
            'asset'
        );
        $search_fields = array(
            'id',
            'secure_link',
            'expiry',
            'download',
            'asset'
        );
        $sort_fields = array(
            'id',
            'secure_link',
            'expiry',
            'download',
            'asset'
        );
        $links->configure(array(), $search_fields, $sort_fields);
        $links->items_per_page = 30;
        $links->setFromRequest($request);
        return new Pluf_HTTP_Response_Json($links->render_object());
    }

    public static function download($request, $match)
    {
        $link = SDP_Shortcuts_GetLinkBySecureIdOr404($match['secure_link']);
        if ($link->tenant != $request->tenant->id) {
            // Error 404
        }
        // Check that asset has price or not
        if ($link->get_asset()->price != null && $link->get_asset()->price > 0) {
            if ($link->active != 1)
                throw new SDP_Exception_ObjectNotFound("Link is not activated.");
        }
        
        // Check link expiry
        
        if (date("Y-m-d H:i:s") > $link->expiry) {
            // Error: Link Expiry
            throw new SDP_Exception_ObjectNotFound("Link has been expired.");
        }
        
        $asset = $link->get_asset();
        
        $user = $link->get_user();
        
        // Do Download
        $httpRange = isset($request->SERVER['HTTP_RANGE']) ? $request->SERVER['HTTP_RANGE'] : null;
        $response = new Pluf_HTTP_Response_ResumableFile($asset->path . '/' . $asset->id, $httpRange, $asset->name, $asset->mime_type);
        // TODO: do buz.
        $size = $response->computeSize();
        $link->download ++;
        $link->update();
        return $response;
        // throw new SDP_Exception_ObjectNotFound ( "SDP plan does not have enough priviledges." );
    }

    /**
     *
     * @param Pluf_HTTP_Request $request            
     * @param array $match            
     */
    public static function payment($request, $match)
    {
        $link = SaaSDM_Shortcuts_GetLinkOr404($match['linkId']);
        
        $url = $request->REQUEST['callback'];
        $user = $request->user;
        $backend = $request->REQUEST['backend'];
        $price = $link->get_asset()->price;
        
        $payment = SaaSBank_Service::create($request, array(
            'amount' => $price, // مقدار پرداخت به ریال
            'title' => 'خرید پلن  ' . $link->id,
            'description' => 'description',
            'email' => $user->email,
            // 'phone' => $user->phone,
            'phone' => '',
            'callbackURL' => $url,
            'backend' => $backend
        ), $link);
        
        $link->payment = $payment;
        $link->update();
        return new Pluf_HTTP_Response_Json($payment);
    }

    /**
     *
     * @param Pluf_HTTP_Request $request            
     * @param array $match            
     */
    public static function activate($request, $match)
    {
        $link = SaaSDM_Shortcuts_GetLinkOr404($match['linkId']);
        
        SaaSBank_Service::update($link->get_payment());
        
        if ($link->get_payment()->isPayed())
            $link->activate();
        return new Pluf_HTTP_Response_Json($link);
    }
}