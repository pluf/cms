package ir.co.dpq.pluf.user;

import java.util.Map;

import retrofit.Callback;
import retrofit.http.FieldMap;
import retrofit.http.FormUrlEncoded;
import retrofit.http.GET;
import retrofit.http.POST;
import retrofit.http.Path;

/**
 * سیستم مدیریت پروفایل‌ها
 * 
 * 
 * @author maso
 *
 */
public interface IPProfileAdministrator {

	/**
	 * پروفایل کاربر تعیین شده را می‌دهد.
	 * 
	 * @param id
	 * @param callback
	 */
	@GET("/api/user/{id}/profile")
	void getProfile(@Path("id") long id, Callback<PProfile> callback);

	/**
	 * پروفایل کاربر تعیین شده را می‌دهد
	 * 
	 * @param l
	 * @return
	 */
	@GET("/api/user/{id}/profile")
	PProfile getProfile(@Path("id") long l);

	/**
	 * پروفایل کاربر تعیین شده را به روز می‌کند.
	 * 
	 * در صورتی که کاربر تعیین شده خود کاربر جاری باشد این فراخوانی معادل با به
	 * روز کردن اطلاعات پروفایل کاربری است.
	 * 
	 * برای دسترسی به پروفایل کاربر باید دسترسی مدیریت سیستم را داشت.
	 * 
	 * @param id
	 * @param params
	 * @param callback
	 */
	@FormUrlEncoded
	@POST("/api/user/{id}/profile")
	void updateProfile(@Path("id") long id, @FieldMap Map<String, Object> params, Callback<PProfile> callback);

	/**
	 * اطلاعات پروفایل کاربری را به روز می‌کند.
	 * 
	 * @param id
	 * @param params
	 * @param callback
	 */
	@FormUrlEncoded
	@POST("/api/user/{id}/profile")
	PProfile updateProfile(@Path("id") long id, @FieldMap Map<String, Object> params);
}
