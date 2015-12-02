# واحد


## فهرست واحدهای ساختمان

با استفاده از فراخوانی زیر می‌توانید فهرست تمامی واحدهای یک ساختمان را به دست آورید:

	/{application id}/part/list

برای اطلاعا بیشتر در مورد واسط برنامه سازی صفحه بندی به مستند [paginator](https://github.com/phoenix-scholars/Pluf/blob/master/document/rest/paginator.md) مراجعه کنید.


تنها فراخوانی قابل قبول متد GET است که پارامترهای زیر را نیز به صورت اختیاری دریافت می‌کند:

- \_px\_count

پارامترهایی که برای جستجو در نظر گرفته شده‌اند عبارتند از:

- id
- part_number
- title

پارامترهای در نظر گرفته شده برای فیلتر کردن نتایج جستجو عبارتند از:

- id
- part_number
- title

پارامترهای در نظر گرفته شده برای مرتب سازی نیز عبارتند از:

- id
- title
- part_number
- creation_date
- modif_dtime

نتیجه این فراخوانی یک فهرست است که با استفاده از JSON‌ نمایش داده می‌شود.


نتیجه فراخوانی یک فهرست است که به صورت موجودیت‌های JSON ایجاد شده است. نمونه‌ای از خروجی این فراخوانی در زیر آورده شده است:

	{
	  "0": {
	    "id": 1,
	    "title": "maso",
	    "count": 1,
	    "part_number": 1,
	    "apartment": 1,
	    "creation_date": "2015-05-06 18:10:37"
	  },
	  "1": {
	    "id": 2,
	    "title": "maso",
	    "count": 1,
	    "part_number": 2,
	    "apartment": 1,
	    "creation_date": "2015-05-06 18:10:41"
	  },
      "current_page": 1,
      "items_per_page": 20,
      "page_number": 1
	}

## ایجاد یک واحد

یک واحد با استفاده از فراخوانی زیر ایجاد می‌شود:

	/api/apartment/part/

این فراخوانی با متد POST انجام می‌شود که نتیجه آن ایجاد یک واحد جدید در آپارتمان تعیین شده است. برای فراخوانی باید پارامترهای زیر را برای آن تعیین کرده باشیم:

- title (Name of owner)
- part_number
- count (People count)

در نتیجه ایجاد این واحد اطلاعات کامل آن ایجاد شده و به صورت یک JSON برگردانده می‌شود. یک نمونه از نتیجه فراخوانی این تابع در زیر آورده شده است.

	{
	  "id": 2,
	  "title": "maso",
	  "count": 1,
	  "part_number": "A2",
	  "apartment": 1,
	  "creation_date": "2015-05-06 18:10:41"
	}

## اطلاعات یک واحد


اطلاعات یک واحد با استفاده از فراخوانی زیر به دست می‌آید:

	/api/apartment/part/{part id}

این فراخوانی باید با استفاده از متد GET انجام شود که در پاسخ آن تمام اطلاعات واحد به صورت یک JSON‌ برگردانده می‌شود. یک نمونه از خروجی این فراخوانی در زیر اورده شده است

	{
	  "id": 2,
	  "title": "maso",
	  "count": 1,
	  "part_number": 2,
	  "apartment": 1,
	  "creation_date": "2015-05-06 18:10:41"
	}

## جستجوی یک واحد

در فهرست واحدهای موجود یک واحد یافت شده و بعنوان نتیجه برگردانده می‌شود. در حال حاظر تنها شناسه یک واحد به عنوان پاامتر جستجو به کار گرفته می‌شود. فراخوانی جستجوی واحد به صورت زیر انجام می‌شود:

	/api/apartment/part/find

این فراخوانی تنها با استفاده از متد GET انجام می‌شود و پارامترهای وروی آن به صورت زیر است:

- part_number

یک نمونه از خروجی این جستجو در زیر آورده شده است:

	{
	  "id": 2,
	  "title": "maso",
	  "count": 1,
	  "part_number": 2,
	  "apartment": 1,
	  "creation_date": "2015-05-06 18:10:41"
	}

## به روز رسانی اطلاعات یک واحد

اطلاعات یک واحد با استفاده از فراخونی زیر به روز می‌شود

	/api/apartment/part/{part id}

این فراخوانی باید با استفاده از متد POST انجام شود که در آن می‌توان هر یک از مدلهای داده‌ای زیر را به صورت دلخاه تعیین کرد:

- title (Name of owner)
- number
- count (People count)

در نتیجه فراخوی این تابع اطلاعات به روز شده به صورتی یک داده JSON برگردانده می‌شود.

	{
	  "id": 11,
	  "title": "Maso Title",
	  "count": "",
	  "part_number": "",
	  "apartment": 1,
	  "creation_date": "2015-05-06 18:21:40"
	}

## تعیین واحد فعال

در بسیاری از موارد نیاز است که یک واحد را به عنوان واحد فعال انتخاب کرد. برای فعال کردن یک واحد به عنوان واحد فعال فراخوانی زیر در نظر گرفته شده است:

	/hm/part/active/{part id}

این فراخوانی باید با متد POST به کار گرفته شود. در نتیجه این فراخوانی تمام اطلاعات واحد به عنوان واحد فعال برای کاربر ارسال خواهد شد.

## تعیین واحد فعال

فراخوانی زیر برای تعیین واحد فعال در نظر گرفته شده است. 

	/hm/part/active
	
این فراخوانی با متد GET باید فراخوانی شود. در نتجه این فراخوانی تمام اطلاعات واحد به عنوان نتیجه برگردانده می‌شود. در صورتی که واحد تعیین نشده باشد یک خطا به عنوان نتیحه اراسال خواهد شد.