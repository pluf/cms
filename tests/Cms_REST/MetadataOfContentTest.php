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
class Cms_REST_MetadataOfContentTest extends TestCase
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
     * Add meta to a content
     *
     * @test
     */
    public function addingTermTaxonomyToContent()
    {
        // login
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // Create content
        $content = new CMS_Content();
        $content->name = 'test-content' . rand();
        $content->title = 'test content';
        $content->description = 'This is a simple content is used in the test process';
        $content->mime_type = 'application/test';
        $content->create();

        // Adding new term-taxonomy to the content
        $form = array(
            'key' => 'meta.key.test' . rand(),
            'value' => 'meta.random.value' . rand()
        );
        $response = self::$client->post('/api/v2/cms/contents/' . $content->id . '/metas', $form);
        Test_Assert::assertResponseNotNull($response, 'Result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Status code is not 200');

        // Getting list of meta of content
        $ttList = $content->get_metas_list();
        Test_Assert::assertNotNull($ttList, 'There is no term-taxonomy for the content');
        Test_Assert::assertTrue($ttList->count() > 0, 'There is no term-taxonomy related to the content');
    }

    /**
     * Getting list of metas of content
     *
     * @test
     */
    public function gettingMetadataOfContent()
    {
        // login
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // Create content
        $content = new CMS_Content();
        $content->name = 'test-content' . rand();
        $content->title = 'test content';
        $content->description = 'This is a simple content is used in the test process';
        $content->mime_type = 'application/test';
        $content->create();
        // Create meta
        $tt = new CMS_ContentMeta();
        $tt->key = 'test-' . rand();
        $tt->vlaue = 'A meta used in the test process';
        $tt->content_id = $content;
        $tt->create();

        // Getting list of meta
        $response = self::$client->get('/api/v2/cms/contents/' . $content->id . '/metas');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponseNonEmptyPaginateList($response, 'The list is empty');
    }

    /**
     *
     * Delete a meta from a content
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

        // Create content
        $content = new CMS_Content();
        $content->name = 'test-content' . rand();
        $content->title = 'test content';
        $content->description = 'This is a simple content is used in the test process';
        $content->mime_type = 'application/test';
        $content->create();

        // Create meta
        $tt = new CMS_ContentMeta();
        $tt->key = 'test-' . rand();
        $tt->vlaue = 'A meta used in the test process';
        $tt->content_id = $content;
        $tt->create();

        // Getting list of meta
        $response = self::$client->get('/api/v2/cms/contents/' . $content->id . '/metas');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponseNonEmptyPaginateList($response, 'The list is empty');

        // Delete term-taxonomoy from the content
        $response = self::$client->delete('/api/v2/cms/contents/' . $content->id . '/metas/' . $tt->id);
        Test_Assert::assertResponseNotNull($response, 'Result of delete request is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Delete status code is not 200');

        // Getting list of metas
        $response = self::$client->get('/api/v2/cms/contents/' . $content->id . '/metas');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponseEmptyPaginateList($response, 'The list is not empty');
    }

    /**
     *
     * Update a meta from a content
     *
     * @test
     */
    public function updateMetaOfContent()
    {
        // login
        $response = self::$client->post('/api/v2/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // Create content
        $content = new CMS_Content();
        $content->name = 'test-content' . rand();
        $content->title = 'test content';
        $content->description = 'This is a simple content is used in the test process';
        $content->mime_type = 'application/test';
        $content->create();

        // Create meta
        $tt = new CMS_ContentMeta();
        $tt->key = 'test-' . rand();
        $tt->vlaue = 'A meta used in the test process';
        $tt->content_id = $content;
        $tt->create();

        // Getting list of meta
        $response = self::$client->get('/api/v2/cms/contents/' . $content->id . '/metas');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponseNonEmptyPaginateList($response, 'The list is empty');

        // Delete term-taxonomoy from the content
        $response = self::$client->post('/api/v2/cms/contents/' . $content->id . '/metas/' . $tt->id, array(
            'key' => 'test-' . rand(),
            'value' => 'test-' . rand()
        ));
        Test_Assert::assertResponseNotNull($response, 'Result of delete request is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Delete status code is not 200');

        // Getting list of metas
        $response = self::$client->get('/api/v2/cms/contents/' . $content->id . '/metas');
        Test_Assert::assertResponseNotNull($response, 'Find result is empty');
        Test_Assert::assertResponseStatusCode($response, 200, 'Find status code is not 200');
        Test_Assert::assertResponseNonEmptyPaginateList($response, 'The list is not empty');
    }
}



