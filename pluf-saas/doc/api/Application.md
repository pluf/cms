# فهرست نرم‌افزارها

هر کسی می‌تواند این فراخوانی را انجام دهد

این فراخوانی به صورت زیر است:

	/app/list

این کار باید با متد GET انجام شود.

یک نمونه از خروجی این فراخوانی به صورت زیر است:

	{  
	   "0":{  
	      "id":1,
	      "level":0,
	      "access_count":0,
	      "validate":false,
	      "title":"Admin demo apartment",
	      "description":"Auto generated application",
	      "creation_dtime":"2015-06-19 18:44:07",
	      "modif_dtime":"2015-06-19 18:44:07"
	   }
	}

# فهرست اعضا

تنها افرادی که در سیستم ثبت شده‌اند قادرند از این فراخوانی استفاده کنند.

فراخوانی زیر برای این کار در نظر گرفته شده است:

	/app/{application id}/memeber/list

یک نمونه خروجی این فراخوانی در زیر آورده شده است.

	{
	    "members": {},
	    "owners": {
	        "0": "admin"
	    },
	    "authorized": {}
	}

# در یافت اطلاعات یک نرم‌افزار

دریافت اطلاعات نرم‌افزار در دو حالت کلی زیر کاربرد دارد:

- نرم‌افزارهای سمت کاربر در کاوشگر اینترنتی کار می‌کند از این رو می‌خواهد به اطلاعات نرم‌افزار فعال دسترسی پیدا کند. 
- یک برنامه کاربردی با زبان‌های برنامه سازی متفاوت نوشته شده و شناسه نرم‌افزار را دارد اما اطالاعات کلی آن را می‌خواهد.

برای هردو این حالت‌ها واسطه‌هایی در نظر گرفته شده است.

## اطلاعات نرم‌افزار جاری

زمانی که کاربران به صفحه اصلی یک نرم‌افزار وارد می‌شوند اطلاعات آن به صورت کوکی در کاوشگر ذخیره می‌شود تا همواره آخرین نرم‌افزاری که به آن وارد شده‌اید مشخص باشد. در این حالت برنامه‌های داخلی نرم‌افزار می‌توانند اطلاعات نرم‌افزار جاری را دریافت کنند.

برای دریافت اطلاعات نرم‌افزار جاری فراخوانی زیر در نظر گرفته شده است:

	/app

این فراخوانی با متد GET باید فراخوانی شود. نمونه‌ای از خروجی این فراخوانی در زیر آورده شده است:

	{
	    "id": 1,
	    "level": 0,
	    "access_count": 0,
	    "validate": false,
	    "title": "Admin demo apartment",
	    "description": "Auto generated application",
	    "creation_dtime": "2015-06-21 08:07:14",
	    "modif_dtime": "2015-06-21 08:07:14"
	}

برای دریافت اطلاعات یک نرم‌افزار خاص که شناسه آن در دسترس است، فراخوانی زیر در نظر گرفته شده است:

	/app/{application id}

این فراخوانی نیز باید با متد GET استفاده شود. خروجی این فراخوانی نیز مانند نمونه‌ای است که در بالا آورده شده است.
