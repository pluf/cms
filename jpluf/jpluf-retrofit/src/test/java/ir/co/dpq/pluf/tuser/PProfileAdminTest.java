package ir.co.dpq.pluf.tuser;

import static ir.co.dpq.pluf.TestConstant.ADMIN_LOGIN;
import static ir.co.dpq.pluf.TestConstant.ADMIN_PASSWORD;
import static ir.co.dpq.pluf.TestConstant.API_URL;

import java.net.CookieHandler;
import java.net.CookieManager;
import java.net.CookiePolicy;

import org.junit.Assert;
import org.junit.Before;
import org.junit.Test;

import com.google.gson.Gson;
import com.google.gson.GsonBuilder;

import ir.co.dpq.pluf.retrofit.PErrorHandler;
import ir.co.dpq.pluf.retrofit.user.IPProfileAdministrator;
import ir.co.dpq.pluf.retrofit.user.IPUserService;
import ir.co.dpq.pluf.retrofit.user.PUser;
import ir.co.dpq.pluf.user.PProfile;
import retrofit.RestAdapter;
import retrofit.converter.GsonConverter;

public class PProfileAdminTest {
	private IPProfileAdministrator profileAdmin;
	private IPUserService usr;

	@Before
	public void createService() {
		CookieManager cookieManager = new CookieManager();
		cookieManager.setCookiePolicy(CookiePolicy.ACCEPT_ALL);
		CookieHandler.setDefault(cookieManager);

		GsonBuilder gsonBuilder = new GsonBuilder();
		gsonBuilder//
				.setDateFormat("yyyy-MM-dd HH:mm:ss");//
		Gson gson = gsonBuilder.create();

		RestAdapter restAdapter = new RestAdapter.Builder()//
				.setConverter(new GsonConverter(gson))//
				// تعیین کنترل کننده خطا
				.setErrorHandler(new PErrorHandler())
				// تعیین آدرس سایت مورد نظر
				.setEndpoint(API_URL)
				// ایجاد یک نمونه
				.build();
		this.profileAdmin = restAdapter.create(IPProfileAdministrator.class);
		this.usr = restAdapter.create(IPUserService.class);
	}

	@Test
	public void getUserProfile() {
		PUser user = usr.login(ADMIN_LOGIN, ADMIN_PASSWORD);
		Assert.assertNotNull(user);

		PProfile profile = profileAdmin.getProfile(user.getId());
		Assert.assertNotNull(profile);
	}
}
