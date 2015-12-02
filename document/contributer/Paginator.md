# صفحه بندی

در بسیاری از موارد با فهرست بزرگی از داده‌ها روبرو هستیم که برای کار با آنها نیز به صفحه بندی و دسته بندی آنها داریم. در این سکو امکانات مناسبی برای صفحه بندی داده‌ها در نظر گرفته شده است. تمام این امکانات در کلاس Pluf_Paginator ایجاد شده است.


## یک نمونه ساده

یک نمونه بسیار ابتدایی از این کلاس به صورت زیر تعریف می‌شود:

	$garticle = new YourApp_Article();
	$pag = new Pluf_Paginator($garticle);
	$pag->action = array('YourApp_Views::listArticles');
	$pag->summary = __('This table shows a list of the articles.');
	$list_display = array(
	       array('id', 'Pluf_Paginator_ToString', __('title')),
	       array('modif_dtime', 'Pluf_Paginator_DateYMD', __('modified')),
	       array('status', 'Pluf_Paginator_DisplayVal', __('status')), 
	                     );
	$pag->items_per_page = 50;
	$pag->no_results_text = __('No articles were found.');
	$pag->configure($list_display, 
	                array('content'), 
	                array('status', 'modif_dtime')
	               );
	$pag->setFromRequest($request);

### تشریح نمونه 

یکی از گزینه‌هایی که در تنظیم صفحه بندی می‌تواند استفاده شود، تعداد گزینه‌ها در یک صفحه است. برای این کار از خصورت زیر استفاده می‌شود (که در این نمونه ۵۰ در نظر گرفته شده است):

	$pag->items_per_page = 50;

دسته‌ای از خصوصیت‌ها برای تنظیم نمایش‌ها است که در زیر آورده شده است. این تنظیم‌ها شامل خصوصیت‌هایی که باید استفاده شود، روش نمایش و عنوان آن آورده شده است:

	$list_display = array(
	       array('id', 'Pluf_Paginator_ToString', __('title')),
	       array('modif_dtime', 'Pluf_Paginator_DateYMD', __('modified')),
	       array('status', 'Pluf_Paginator_DisplayVal', __('status')), 
	                     );

در نمونه بالا شناسه، تاریخ تغییر داده و حالت آن به عنوان خصوصیت‌هایی آورده شده که در نمایش به کار گرفته می‌شود. در این نمونه id به این معنی است که فیلد id از مدل داده‌های باید به نمایش ارسال شود و برای نمایش آن باید از تابع Pluf_Paginator_ToString‌ برای ایجاد داده قابل نمایش استفاده شود. در نهای اخرین خصوصیت عنوانی را تعیین می‌کند که باید برای این داده به کار گرفته شود.

علاوه بر این امکاناتی نیز در نظر گرفته شده که با استفاده از آن می‌توان داده‌های ایجاد شده را مرتب کرد. در زیر یک آرایه اضافه شده که از خصوصیت‌های status و modif_dtime برای مرتب کردن دادها استفاده شده است:

	$pag->configure($list_display, 
	                array('content'), 
	                array('status', 'modif_dtime')
	               );

# تنظیم‌های صفحه بندی


## items = null

An ArrayObject of items to list. Only used if not using directly a model.

## item_extra_props = array()

Extra property/value for the items.

This can be practical if you want some values for the edit action which are not available in the model data.

## عبارت جستجوی اجباری

همانگونه که گفته شد، صفحه بندی راهکارهایی خاص را برای اجرای جستجوها در نظر می‌گیرد. اما گاهی نیاز است علاوه بر اجرای عبارت‌های ورودی یک عبارت اجباری نیز به جستجو اضافه کردم. 

برای تعیین یک عبارت خاص در فرآیند جستجو از این خصوصیت استفاده می‌شود. برای نمونه در زیر یک عبارت برای فهرست کردن و دسته بندی موجودیت‌هایی اورده شده که در آنها نرم‌افزار یک شناسه خاص دارد.

	$paginator->forced_where = new Pluf_SQL('application=%s', 
                array(
                        $request->application->id
                ));

به صورت پیش فرض این عبارت تهی بوده و به صورت زیر تعیین می‌شود:

	forced_where = null

## نمایش خاص داده

هر مدل می‌تواند از نمایش‌های متفاوت پایگاه داده استفاده کند. این نمایش‌ها ساختارهای داده‌ای پیچیده‌تری را ایجاد می‌کنند و یا حتی برخی از خصوصیت‌های موجودیت‌ها را حذف می‌کنند. با استفاده از این خصوصیت می‌توان تعیین کرد که کدام نمایش پایگاه داده در جستجو استفاده شود:

	$paginator->model_view='view name';

به صورت پیش فرض این مقدار به صورت زیر تعریف می‌شود:

	model_view = null

## items_per_page = 50

Maximum number of items per page.

## no_results_text = 'No items found'

Text to display when no results are found.

## sort_fields = array()

Which fields of the model can be used to sort the dataset. To be useable these fields must be in the $list_display so that the sort links can be shown for the user to click on them and sort the list.

## sort_order = array()

Current sort order. An array with first value the field and second the order of the sort.

## edit_action = ''

Edit action, if you set it, the first column data will be linked to to view you give here.

You can give a simple view like : YourApp_Views::editItem, the id of the item will be given as argument to the view. You can also decide what arguments you pass to the view, for example: array('YourApp_Views::edit-normal', 'id') will the view YourApp_Views::edit-normal with the id as first argument.

## action = ''

Action for search/next/previous. The action is either the simple Model::views like YourApp_Views::listItems or you can give a fully defined view like array('YourApp_Views::listItems', array('value1', 'value2'))

## id = ''

Id of the generated table.

## extra = null

Extra parameters for the modification function call. These parameters are given as third argument to the call back functions when displaying the data.

## summary = ''

Summary for the table.

## nb_items = 0

Total number of items. Available only after the rendering of the paginator.

# استفاده در الگوهای خروجی

داده‌های که با استفاده از صفحه بندی ایجاد می‌شود را می‌توان به دو مدل استفاد کرد که عبارتند از:

- فهرست اشیا
- ارایه خصوصیت‌ها

در فهرست اشیا تمام خصوصیت‌های یک موجودیت ارسال می‌شود. برای تولید این نوع خروجی از فراخوانی زیر استفاده می‌شود.

	$paginator->render_object();

یک نمونه از خروجی این فراخوانی در زیر آورده شده است:

	{
	    "0": {
	        "id": 1,
	        "receipts": "",
	        "verified": false,
	        "deleted": false,
	        "part": 1,
	        "amount": 1000,
	        "title": "example of payment",
	        "description": "",
	        "creation_dtime": "2015-06-25 00:52:23",
	        "modif_dtime": "2015-06-25 00:52:23"
	    },
	    "counts": 1,
	    "current_page": 1,
	    "items_per_page": 20,
	    "page_number": 1
	}

در مدل ارایه خصوصیت‌ها، تمام خصوصیت‌های صفحه بر اساس تنظیم‌های انجام شده فیلتر شده و در نهایت یک آرایه از خصوصیت‌ها به عنوان خروجی ایجاد می‌شود. این خروجی با روش زیر تولید می‌شود:

	$paginator->render_array();

یک نمونه از خروجی این فراخوانی در زیر آورده شده است:

	[
	    {
	        "id": 1,
	        "title": "example of payment",
	        "amount": 1000,
	        "creation_dtime": "2015-06-25 00:52:23",
	        "modif_dtime": "2015-06-25 00:52:23"
	    }
	]

دو نمونه‌ای که در بالا آورده شده است دقیقا خروجی یک صفحه بندی هستند که در اولی تمام اشیا به عنوان خروجی در نظر گرفته شده است در حالی که در نمونه دوم تنها خصوصیت‌های محدودی از آن در خرجی قرار گرفته است.