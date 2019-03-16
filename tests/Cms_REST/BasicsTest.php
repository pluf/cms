<?php
/*
 * This file is part of Pluf Framework, a simple PHP Application Framework.
 * Copyright (C) 2010-2020 Phoinex Scholars Co. (http://dpq.co.ir)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\IncompleteTestError;
require_once 'Pluf.php';

/**
 *
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class Cms_REST_BasicsTest extends TestCase
{

    private static $client = null;

    /**
     *
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../conf/config.php');
        $m = new Pluf_Migration(Pluf::f('installed_apps'));
        $m->install();

        // Test user
        $user = new User_Account();
        $user->login = 'test';
        $user->is_active = true;
        if (true !== $user->create()) {
            throw new Exception();
        }
        // Credential of user
        $credit = new User_Credential();
        $credit->setFromFormData(array(
            'account_id' => $user->id
        ));
        $credit->setPassword('test');
        if (true !== $credit->create()) {
            throw new Exception();
        }

        $per = User_Role::getFromString('tenant.owner');
        $user->setAssoc($per);

        self::$client = new Test_Client(array(
            array(
                'app' => 'Cms',
                'regex' => '#^/api/v2/cms#',
                'base' => '',
                'sub' => include 'CMS/urls-v2.php'
            ),
            array(
                'app' => 'User',
                'regex' => '#^/api/v2/user#',
                'base' => '',
                'sub' => include 'User/urls-v2.php'
            )
        ));
    }

    /**
     *
     * @afterClass
     */
    public static function removeDatabses()
    {
        $m = new Pluf_Migration(Pluf::f('installed_apps'));
        $m->unInstall();
    }

    /**
     *
     * @test
     */
    public function listContentsRestTest()
    {
        $client = new Test_Client(array(
            array(
                'app' => 'Cms',
                'regex' => '#^/api/v2/cms#',
                'base' => '',
                'sub' => include 'CMS/urls-v2.php'
            )
        ));
        $response = $client->get('/api/v2/cms/contents');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * TODO: divide the test
     *
     * @test
     */
    public function crudRestTest()
    {
        // login
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // create
        $form = array(
            'name' => 'test content' . rand(),
            'title' => 'test contetn',
            'description' => 'This is a simple content is used int test process',
            'mime_type' => 'application/test'
        );
        $response = self::$client->post('/api/v2/cms/contents', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        $content = new CMS_Content();
        $content->name = 'test content' . rand();
        $content->mime_type = 'application/test';
        $content->create();

        // Get by id
        $response = self::$client->get('/api/v2/cms/contents/' . $content->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // Update by id
        $response = self::$client->post('/api/v2/cms/contents/' . $content->id, array(
            'title' => 'new title'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // delete by id
        $response = self::$client->delete('/api/v2/cms/contents/' . $content->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * Add meta to a content
     *
     * @test
     */
    public function addingMetaToContent()
    {
        // login
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // create
        $form = array(
            'name' => 'test-content' . rand(),
            'title' => 'test contetn',
            'description' => 'This is a simple content is used int test process',
            'mime_type' => 'application/test'
        );
        $response = self::$client->post('/api/v2/cms/contents', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // load content by name
        Pluf::loadFunction('CMS_Shortcuts_GetNamedContentOr404');
        $content = CMS_Shortcuts_GetNamedContentOr404($form['name']);
        $this->assertNotNull($content);
        $this->assertEquals($content->name, $form['name']);

        // Adding new metadate to the content
        $metaForm = array(
            'key' => 'meta key' . rand(),
            'value' => 'THis is a SEIMple texte long'
        );
        $response = self::$client->post('/api/v2/cms/contents/' . $content->id . '/metas', $metaForm);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponseNotAnonymousModel($response, 'Meta data is not generated');

        // Getting list of metas
        $metaList = $content->get_metas_list();
        Test_Assert::assertNotNull($metaList, 'There is not meta data for the content');
        Test_Assert::assertTrue($metaList->count() > 0, 'There is not meta data for the content');
    }

    /**
     * Getting list of metas
     *
     * @test
     */
    public function gettingMetasOfContent()
    {
        // login
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // create
        $form = array(
            'name' => 'test-content' . rand(),
            'title' => 'test contetn',
            'description' => 'This is a simple content is used int test process',
            'mime_type' => 'application/test'
        );
        $response = self::$client->post('/api/v2/cms/contents', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // load content by name
        Pluf::loadFunction('CMS_Shortcuts_GetNamedContentOr404');
        $content = CMS_Shortcuts_GetNamedContentOr404($form['name']);
        $this->assertNotNull($content);
        $this->assertEquals($content->name, $form['name']);

        // Adding new metadate to the content
        $metaForm = array(
            'key' => 'meta key' . rand(),
            'value' => 'THis is a SEIMple texte long'
        );
        $response = self::$client->post('/api/v2/cms/contents/' . $content->id . '/metas', $metaForm);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponseNotAnonymousModel($response, 'Meta data is not generated');

        // Getting list of metas
        $response = self::$client->get('/api/v2/cms/contents/' . $content->id . '/metas');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponseNonEmptyPaginateList($response, 'The list is empty');
    }

    /**
     * Get a metas
     *
     * @test
     */
    public function deleteMetaOfContent()
    {
        // login
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // create
        $form = array(
            'name' => 'test-content' . rand(),
            'title' => 'test contetn',
            'description' => 'This is a simple content is used int test process',
            'mime_type' => 'application/test'
        );
        $response = self::$client->post('/api/v2/cms/contents', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // load content by name
        Pluf::loadFunction('CMS_Shortcuts_GetNamedContentOr404');
        $content = CMS_Shortcuts_GetNamedContentOr404($form['name']);
        $this->assertNotNull($content);
        $this->assertEquals($content->name, $form['name']);

        // Adding new metadate to the content
        $metaForm = array(
            'key' => 'meta key' . rand(),
            'value' => 'THis is a SEIMple texte long'
        );
        $response = self::$client->post('/api/v2/cms/contents/' . $content->id . '/metas', $metaForm);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponseNotAnonymousModel($response, 'Meta data is not generated');

        $mlist = $content->get_metas_list();
        $meta = $mlist[0];

        // Delete list of metas
        $response = self::$client->get('/api/v2/cms/contents/' . $content->id . '/metas/' . $meta->id);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponseNotAnonymousModel($response, 'Meta is not accessable');
    }

    /**
     *
     * Delete a metas
     *
     * @test
     */
    public function deleteMetaOfContent()
    {
        // login
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // create
        $form = array(
            'name' => 'test-content' . rand(),
            'title' => 'test contetn',
            'description' => 'This is a simple content is used int test process',
            'mime_type' => 'application/test'
        );
        $response = self::$client->post('/api/v2/cms/contents', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // load content by name
        Pluf::loadFunction('CMS_Shortcuts_GetNamedContentOr404');
        $content = CMS_Shortcuts_GetNamedContentOr404($form['name']);
        $this->assertNotNull($content);
        $this->assertEquals($content->name, $form['name']);

        // Adding new metadate to the content
        $metaForm = array(
            'key' => 'meta key' . rand(),
            'value' => 'THis is a SEIMple texte long'
        );
        $response = self::$client->post('/api/v2/cms/contents/' . $content->id . '/metas', $metaForm);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponseNotAnonymousModel($response, 'Meta data is not generated');

        $mlist = $content->get_metas_list();
        $meta = $mlist[0];

        // Delete list of metas
        $response = self::$client->delete('/api/v2/cms/contents/' . $content->id . '/metas/' . $meta->id);
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');

        // Getting list of metas
        $response = self::$client->get('/api/v2/cms/contents/' . $content->id . '/metas');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponseEmptyPaginateList($response, 'The list is empty');
    }
}



