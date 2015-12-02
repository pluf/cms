# مدیریت خطا‌ها

همواره باید به این نکته توجه داشت که مدیریت خطا در طراحی هر سیستم REST} مهم
است و باید به دقت انجام شود.

در این بخش یک روش مناسب برای مدیریت خطاها ارائه شده است که قابل به کارگری در
سایر سیستم‌های REST} نیز است.
در فرآیند تشریح روش مدیریت خطا کدهایی نیز به زبان جاوا و جاواسکریپت ارائه شده
است که می‌تواند در پیاده سازی برنامه‌های کاربردی به کار گرفته شوند.

## مدل کلی

در ساختارهایی کارخواه-کارساز دو دسته از خطاها وجود دارد: خطاهایی که در داخل
کارخواه ایجاد شده و یا خطاهایی که به دلیلی فراخوانی نا مناسب واسطه‌ها ایجاد شده
است.
در دو حالت یک نوع داده به عنوان نتیجه ارسال می‌شود که در آن اطلاعات کاملی برا
توسعه دهندگان برنامه‌های کاربری و خود کاربران وجود دارد.
در این واسط توسعه دهنگان و کاربرانی که کارکرد سیستم را دنبال می‌کنند در نظر
گرفته شده‌اند چرا که این افراد خطاهای سیستم را کشف می‌کنند و منجر به بهبود آن
خواهند شد.

این معماری و ساختار مدیریت خطا‌ها بر اساس پروژه‌های معروفی ایجاد شده و از
تجربیات توسعه‌دهندگان بهره برده است.
به تمام توسعه‌دهندگان توسیعه می‌شود که اصول مطرح شده در این بخش را به دقت در
پیاده‌سازی‌های خود به کار گیرند.

اولین گام برای تعیین نوع خطا استفاده از کد خطا در پاسخ‌هایی است که توسط کارخواه
تولید می‌شود.
با یک دسته بندی مناسب می‌توان خطاهای تولید شده را به سادگی به دو دسته تقسیم کرد
که عبارتند از:


- 4xx خطاهای کارساز را نشان می‌دهند خطاهایی که تعیین می‌کند واسطه‌های
نامناسبی فراخوانی شده است و یا داده‌های ارسانی اشتباه است. کارساز در مقابل این
خطاها نباید دوبار داده‌ها را ارسال کند مگر اینکه داده‌ها و واسطه‌ها را به درستی
بازبینی کرده باشد.
- 5xx خطاهای داخلی کارگزار را تعیین می‌کند. در این حال کارساز می‌تواند
تقاضای خود را بدون تغییر تکرار کند.


در گام بعد باید دسته‌ای از اطلاعات برای کاربران ارسال شود و آنها را با خطای رخ
داده آشنا کند.
این اطلاعات را می‌توان به دو دسته تقسیم کرد که دسته اول برای کاربران و دسته دوم
برای توسعه‌گرهای سیستم است.
بدیهی است که دسته دوم اطلاعات می‌تواند جامع باشد.

بر این اساس ساختار یک پیام خطا به صورت زیر خواهد بود:

	{
	   "status": 400,
	   "code": 4000,
	   "message": "Provided data not sufficient for insertion",
	   "link": "http://dpq.co.ir/error",
	   "developerMessage": "Please verify that the feed is properly generated/set"
	}

در ادامه تمام بخش‌هایی که در پیام آورده شده است، به صورت کامل تشریح شده:

### حالت

حالت کد خطای قرارداد HTTP} را تعیین می‌کند.
گرچه این کد خطا در سرآیند بسته‌های HTTP} نیز وجود دارد اما به صورت تکراری در
بدنه پیام نیز آورده می‌شود تا توسعه‌گر بدون نیاز به تحلیل سرآیند بتواند از کد
خطای HTTP} نیز مطلع شود.
این خصوصیت با استفاده از کلید status} در بدنه پیام ایجاد می‌شود.

### کد

خطا حقیقی سیستم با استفاده از یک کد تعیین می‌شود که یک عدد مثبت است.
معنای دقیق این کد بر اساس پیاده سازی سیستم تعیین می‌شود و یک عدد بین 4000 تا
۵۰۰۰ است.
کد خطا با استفاده از متغیر code} در بدنه پیام نمایش داده می‌شود.

### پیام

یک توصیف کوتا از خطای رخ داده، که معمولا دلیل رویداد در سیستم و یا ارائه
راهکارهای برای رفع خطا است، نیز به بدنه پیام اضافه می‌شود.
این پیام با استفاده از کلید message} در پیام آورده می‌شود.

### پیوند

یک پیوند به منابع تحت وب نیز در پیام در نظر گرفته می‌شود تا کاربران و توسعه‌گرها
بتوانند به منابع مفید درسترسی پیدا کنند.


همواره در سایت اصلی پروژه یک مسیر برای دسترسی به مطالب و مستندها در نظر گرفته
می‌شود که در آن تمام مطالب مورد نیاز کاربران قرار می‌گیرد.
به صورت پیش فرض مسیر تعیین شده یکی از مستندهای پایگاه را تعیین می‌کند.
در فصل [wiki] و بخش [api/wiki] مبانی استفاده از دانش‌نامه و واسطه‌های
برنامه نویسی آن آورده شده است.


### پیام برای توسعه‌گر

در مقابل با یک خطا، توسعه‌گرهای برنامه‌های کاربردی نیاز به اطلاعات بیشتری برای
کشف و رفع خطاهای احتمالی دارند.
از ین رو یک داده اضافه برای تشریح کامل خطای رخ داده اضافه می‌شود تا به عنوان یک
راهنمایی بیشتری برای توسعه‌گرها باشد.
برای نمونه پشته فراخوانی سیستم و پیام اصلی که در خود سیستم تولید شده است
می‌تواند به این پیام الحاق شود.

این پیام تنها در حالتی فعال خواهد بود که سیستم در حالت رفع خطا اجرا شده باشد و
در سایر موارد از بدنه پیام حذف خواهد شد.


## پیاده سازی

پیاده سازی مدیریت خطا شامل دو بخش کارخواه و کار ساز می‌شود.
در اینجا تنها روش‌های به کار گرفته شده در کارخواه آورده شده و سایر موارد از
دایره این مستند خارج است.

در بخش کارساز نیز به شکل مختصر اشاره شده است تا به عنوان یک راهنما برای
توسعه‌گرهای برنامه‌های کاربردی باشد.

در اولین مرحله، لایه نمایش درخواست‌های کاربر را بررسی می‌کند و در صورتی که خطایی
در درخواست مشاهده کند، یک استثنا صادر می‌کند.
بنابر این می‌توان گفت که دسته مهمی از خطاهای نوع کاربر در لایه نمایش ایجاد
می‌شود جایی که کاربران تلاش دارند با سیستم در رابطه باشند.

در سطح بعد خطا از سمت لایه سرویس ایجاد شده و به لایه نمایش انتقال می‌یابد.
در این حالت دو نوع خطا سطح کاربر و کارخواه می‌تواند ایجاد شود.
به بیان دیگر ممکن است سرویس‌ها تشخیص دهند که خطای ایجاد شده به دلیل داده‌های
نامناسب کاربر است در حالی که لایه نمایش به این نکته پی نبرده است.

پیاده سازی مدیریت خطا در این بخش بر اساس ساختارهای معرفی شده در Jersey است
که می‌تواند در ساختارهای دیگر نیز پیاده سازی شود.


## checked exception

یکی از مهم‌ترین خطاهایی که در سیستم ایجاد می‌شود، checked exception است.
این خطاها، دسته‌ای از رویدادها هستند که ریداد آنها را می‌دانیم اما نمی‌توانیم از
بروز آنها پیش گیری کنیم.
برای نمونه در فرآیند خواندن از یک پرونده ممکن است، پرونده موجود نباشد، از این
رو خواندن با خطا روبرو می‌شود و ما این مطلب را می‌دانیم.

تمام خطاهایی که در لایه bisiness و یا لایه نمایش ایجاد می‌شود، با
استفاده از یک نوع خاص خطا به نام AdvisorException گروه‌بندی می‌شوند.
از این رو اگر خطای رویداد در لایه business و یا لایه view باشد به
سادگی مدیریت شده و پیام مناسب برای کاربر فرستاده می‌شود.

برای نمونه در Jersey تمام خطاهایی که در لایه مدیریت ایجاد شده است با
استفاده از ExceptionMapper به پیام‌های مناسب تبدیل می‌شود.
در زیر کد مناسب برای ایجاد پیام آورده شده است.

	package ir.co.dpq.advisor.net.netty.errorhandling;
	
	import ir.co.dpq.advisor.net.AdvisorNetException;
	
	import javax.ws.rs.core.MediaType;
	import javax.ws.rs.core.Response;
	import javax.ws.rs.ext.ExceptionMapper;
	import javax.ws.rs.ext.Provider;
	
	@Provider
	public class AdvisorNetExceptionMapper implements
			ExceptionMapper<AdvisorNetException> {
		public Response toResponse(AdvisorNetException ex) {
			return Response.status(ex.getStatus()).entity(new ExceptionMessage(ex))
					.type(MediaType.APPLICATION_JSON).build();
		}
	}

همانگونه که در کد بالا قابل مشاهده است، تمام خطاهایی که از نوع
AdvisorException باشند، با استفاده از این مبدل، به پیام‌های مناسب برای
کاربر تبدیل می‌شوند.
بنابر این business layer و view layer بدون نگرانی در مورد نحوه
انتقال خطا به کار بر می‌توانند استثنا مورد نظر خود را ایجاد کرده و آن را انتشار
دهند.

برای نمونه فرض کنید که یک درخواست به صورت زیر آمده و تلاش دارد که به یک منبع نا
مشخص دست پیدا کند.

	GET http://user.advisor.dpq.co.ir/unknown-resource/22 HTTP/1.1
	Accept-Encoding: gzip,deflate
	Accept: application/json
	Host: localhost:8888
	Connection: Keep-Alive
	User-Agent: Apache-HttpClient/4.1.1 (java 1.5)

در پاسخ به این پیام یک خطا از نوع AdvisorException منتشر می‌شود که بیانگر
عدم وجود منبع مورد نظر است.
بر اساس کدی که در بالا آورده شده است، استثنا به صورت زیر برای کاربر ارسال خواهد
شد.

	HTTP/1.1 404 Not Found
	Content-Type: application/json
	Access-Control-Allow-Origin: *
	Access-Control-Allow-Methods: GET, POST, DELETE, PUT
	Access-Control-Allow-Headers: X-Requested-With, Content-Type, X-Codingpedia
	Content-Length: 231
	Server: Jetty(9.0.7.v20131107)
	
	{
	   "status": 404,
	   "code": 404,
	   "message": "The podcast you requested with id 22 was not found",
	   "link": "http://dpq.co.ir/error",
	   "developerMessage": "Verify the existence of the podcast with the id 22"
	}

همانگونه که در نمونه بالا قابل مشاهده است، نتیجه به صورت مناسب در ساختارهای
JSON سازماندهی شده و برای کاربر ارسال می‌شود.


بدیهی است که بعد از دریافت این پیام برای کاربر کاملا مشخص خواهد بود که منبع مورد
نظرش موجود نیست و باید در مقابل آن کار مناسبی را انجام دهد.


## unchecked exception

دسته‌ای از خطاها که در سیستم منتشر می‌شوند، خطاهایی هستند که ما از وقوع آنها اطلاعای نداری. این خطاها را اصطلاحا unchecked exception می‌گوییم.

برای مدیریت unchecked exception یک کلاس کلی ایجاد شده است تا تمام خطاهای
رخ داده را به ساختارهای مناسب برای کاربران نگاشت کند.
در زیر پیاده سازی پیش فرض این کلاس آورده شده است.


	package org.codingpedia.demo.rest.errorhandling;
	import org.codingpedia.demo.rest.filters.AppConstants;
	public class GenericExceptionMapper implements ExceptionMapper<Throwable> {
		
		@Override 
		public Response toResponse(Throwable ex) {
	
			ErrorMessage errorMessage = new ErrorMessage();		
			setHttpStatus(ex, errorMessage);
			errorMessage.setCode(AppConstants.GENERIC_APP_ERROR_CODE);
			errorMessage.setMessage(ex.getMessage());
			StringWriter errorStackTrace = new StringWriter();
			ex.printStackTrace(new PrintWriter(errorStackTrace));
			errorMessage.setDeveloperMessage(errorStackTrace.toString());
			errorMessage.setLink(AppConstants.BLOG_POST_URL);
	
			return Response.status(errorMessage.getStatus())
					.entity(errorMessage)
					.type(MediaType.APPLICATION_JSON)
					.build();	
		}
	
		private void setHttpStatus(Throwable ex, ErrorMessage errorMessage) {
			if(ex instanceof WebApplicationException ) {
				errorMessage.setStatus(((WebApplicationException)ex).getResponse().getStatus());
			} else {
				//defaults to internal server error 500
				errorMessage.setStatus(Response.Status.INTERNAL_SERVER_ERROR.getStatusCode()); 
			}
		}
	}
	
در رابطه با پیاده سازی موجود از مدیریت خطاها باید به نکته‌های زیر توجه داشت:

- در حالت کلی نمی‌توان این روش را یک روش مناسب برای مدیریت خطاهای کلی سیستم
	در نظر گرفت. در صورت بروز یک خطا تنها کاری که انجام می‌شود ارسال آنها بدون دسته
	بندی و نظارت برای کاربران است.
- پیام ارسال شده شامل پشته فراخوانی است که از نظر امنیتی بسیار مهم است. از
	این رو تنها در حالت رفع خطا این پیام باید ایجاد شود و در سایر موارد نه.
- روش ارتباط با مدیریت سایت، ایمیل، شماره تماس و یا هر راهی دیگر باید در یک
	مسیر در منابع اینترنتی جای داده شود. مسیر تعیین شده برای کاربر باید به نحوی
	دسترسی به این منبع را نیز فراهم کند.

