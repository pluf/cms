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
namespace Pluf\Test\Content;

use Pluf\Test\Client;
use Pluf\Test\TestCase;
use CMS_Content;
use CMS_ContentMeta;
use Exception;
use Pluf;
use Pluf_Migration;
use User_Account;
use User_Credential;
use User_Role;

class MetadataOfContentTest extends TestCase
{

    private static $client = null;

    /**
     *
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../conf/config.php');
        $m = new Pluf_Migration();
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

        self::$client = new Client();
    }

    /**
     *
     * @afterClass
     */
    public static function removeDatabses()
    {
        $m = new Pluf_Migration();
        $m->unInstall();
    }

    /**
     * Add meta to a content
     *
     * @test
     */
    public function addingMetaToContent()
    {
        // login
        $response = self::$client->post('/user/login', array(
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

        // Adding new content-meta to the content
        $form = array(
            'key' => 'meta.key.test' . rand(),
            'value' => 'meta.random.value' . rand()
        );
        $response = self::$client->post('/cms/contents/' . $content->id . '/metas', $form);
        $this->assertResponseNotNull($response, 'Result is empty');
        $this->assertResponseStatusCode($response, 200, 'Status code is not 200');

        // Getting list of meta of content
        $ttList = $content->get_metas_list();
        $this->assertNotNull($ttList, 'There is no content-meta for the content');
        $this->assertTrue($ttList->count() > 0, 'There is no content-meta related to the content');
    }

    /**
     * Getting list of metas of content
     *
     * @test
     */
    public function gettingMetadataOfContent()
    {
        // login
        $response = self::$client->post('/user/login', array(
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
        $response = self::$client->get('/cms/contents/' . $content->id . '/metas');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNonEmptyPaginateList($response, 'The list is empty');
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
        $response = self::$client->post('/user/login', array(
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
        $response = self::$client->get('/cms/contents/' . $content->id . '/metas');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNonEmptyPaginateList($response, 'The list is empty');

        // Delete content-meta from the content
        $response = self::$client->delete('/cms/contents/' . $content->id . '/metas/' . $tt->id);
        $this->assertResponseNotNull($response, 'Result of delete request is empty');
        $this->assertResponseStatusCode($response, 200, 'Delete status code is not 200');

        // Getting list of metas
        $response = self::$client->get('/cms/contents/' . $content->id . '/metas');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseEmptyPaginateList($response, 'The list is not empty');
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
        $response = self::$client->post('/user/login', array(
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
        $response = self::$client->get('/cms/contents/' . $content->id . '/metas');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNonEmptyPaginateList($response, 'The list is empty');

        // Delete content-meta from the content
        $response = self::$client->post('/cms/contents/' . $content->id . '/metas/' . $tt->id, array(
            'key' => 'test-' . rand(),
            'value' => 'test-' . rand()
        ));
        $this->assertResponseNotNull($response, 'Result of delete request is empty');
        $this->assertResponseStatusCode($response, 200, 'Delete status code is not 200');

        // Getting list of metas
        $response = self::$client->get('/cms/contents/' . $content->id . '/metas');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNonEmptyPaginateList($response, 'The list is not empty');
    }
}



