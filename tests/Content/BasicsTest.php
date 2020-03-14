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
use Exception;
use Pluf;
use Pluf_Migration;
use User_Account;
use User_Credential;
use User_Role;

class BasicsTest extends TestCase
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
     *
     * @test
     */
    public function listContentsRestTest()
    {
        $client = new Client();
        $response = $client->get('/cms/contents');
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
        $response = self::$client->post('/user/login', array(
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
        $response = self::$client->post('/cms/contents', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        $content = new CMS_Content();
        $content->name = 'test content' . rand();
        $content->mime_type = 'application/test';
        $content->create();

        // Get by id
        $response = self::$client->get('/cms/contents/' . $content->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // Update by id
        $response = self::$client->post('/cms/contents/' . $content->id, array(
            'title' => 'new title'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // delete by id
        $response = self::$client->delete('/cms/contents/' . $content->id);
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
        $response = self::$client->post('/user/login', array(
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
        $response = self::$client->post('/cms/contents', $form);
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
        $response = self::$client->post('/cms/contents/' . $content->id . '/metas', $metaForm);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNotAnonymousModel($response, 'Meta data is not generated');

        // Getting list of metas
        $metaList = $content->get_metas_list();
        $this->assertNotNull($metaList, 'There is not meta data for the content');
        $this->assertTrue($metaList->count() > 0, 'There is not meta data for the content');
    }

    /**
     * Getting list of metas
     *
     * @test
     */
    public function gettingMetasOfContent()
    {
        // login
        $response = self::$client->post('/user/login', array(
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
        $response = self::$client->post('/cms/contents', $form);
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
        $response = self::$client->post('/cms/contents/' . $content->id . '/metas', $metaForm);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNotAnonymousModel($response, 'Meta data is not generated');

        // Getting list of metas
        $response = self::$client->get('/cms/contents/' . $content->id . '/metas');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNonEmptyPaginateList($response, 'The list is empty');
    }

    /**
     * Get a metas
     *
     * @test
     */
    public function getMetaOfContent()
    {
        // login
        $response = self::$client->post('/user/login', array(
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
        $response = self::$client->post('/cms/contents', $form);
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
        $response = self::$client->post('/cms/contents/' . $content->id . '/metas', $metaForm);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNotAnonymousModel($response, 'Meta data is not generated');

        $mlist = $content->get_metas_list();
        $meta = $mlist[0];

        // Delete list of metas
        $response = self::$client->get('/cms/contents/' . $content->id . '/metas/' . $meta->id);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNotAnonymousModel($response, 'Meta is not accessable');
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
        $response = self::$client->post('/user/login', array(
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
        $response = self::$client->post('/cms/contents', $form);
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
        $response = self::$client->post('/cms/contents/' . $content->id . '/metas', $metaForm);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNotAnonymousModel($response, 'Meta data is not generated');

        $mlist = $content->get_metas_list();
        $meta = $mlist[0];

        // Delete list of metas
        $response = self::$client->delete('/cms/contents/' . $content->id . '/metas/' . $meta->id);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');

        // Getting list of metas
        $response = self::$client->get('/cms/contents/' . $content->id . '/metas');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseEmptyPaginateList($response, 'The list is empty');
    }
    
    /**
     * Add member to a content
     *
     * @test
     */
    public function addingMemberToContent()
    {
        // login
        $response = self::$client->post('/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $user = new User_Account();
        $user = $user->getUser('test');
        
        // create
        $form = array(
            'name' => 'test-content' . rand(),
            'title' => 'test contetn',
            'description' => 'This is a simple content is used int test process',
            'mime_type' => 'application/test'
        );
        $response = self::$client->post('/cms/contents', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // load content by name
        Pluf::loadFunction('CMS_Shortcuts_GetNamedContentOr404');
        $content = CMS_Shortcuts_GetNamedContentOr404($form['name']);
        $this->assertNotNull($content);
        $this->assertEquals($content->name, $form['name']);
        
        // Adding new member to the content
        $data = array(
            'id' => $user->id
        );
        $response = self::$client->post('/cms/contents/' . $content->id . '/members', $data);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNotAnonymousModel($response, 'Meta data is not generated');
        
        // Getting list of members
        $mList = $content->get_members_list();
        $this->assertNotNull($mList, 'There is not meta data for the content');
        $this->assertTrue($mList->count() > 0, 'There is not meta data for the content');
    }
    
    /**
     * Getting list of members
     *
     * @test
     */
    public function gettingMembersOfContent()
    {
        // login
        $response = self::$client->post('/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        $user = new User_Account();
        $user = $user->getUser('test');
        
        // create
        $form = array(
            'name' => 'test-content' . rand(),
            'title' => 'test contetn',
            'description' => 'This is a simple content is used int test process',
            'mime_type' => 'application/test'
        );
        $response = self::$client->post('/cms/contents', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // load content by name
        Pluf::loadFunction('CMS_Shortcuts_GetNamedContentOr404');
        $content = CMS_Shortcuts_GetNamedContentOr404($form['name']);
        $this->assertNotNull($content);
        $this->assertEquals($content->name, $form['name']);
        
        // Adding new member to the content
        $mForm = array(
            'id' => $user->id
        );
        $response = self::$client->post('/cms/contents/' . $content->id . '/members', $mForm);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNotAnonymousModel($response, 'Meta data is not generated');
        
        // Getting list of metas
        $response = self::$client->get('/cms/contents/' . $content->id . '/members');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNonEmptyPaginateList($response, 'The list is empty');
    }
    
    /**
     * Get a member
     *
     * @test
     */
    public function getMemberOfContent()
    {
        // login
        $response = self::$client->post('/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        $user = new User_Account();
        $user = $user->getUser('test');
        
        // create
        $form = array(
            'name' => 'test-content' . rand(),
            'title' => 'test contetn',
            'description' => 'This is a simple content is used int test process',
            'mime_type' => 'application/test'
        );
        $response = self::$client->post('/cms/contents', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // load content by name
        Pluf::loadFunction('CMS_Shortcuts_GetNamedContentOr404');
        $content = CMS_Shortcuts_GetNamedContentOr404($form['name']);
        $this->assertNotNull($content);
        $this->assertEquals($content->name, $form['name']);
        
        // Adding new member to the content
        $metaForm = array(
            'id' => $user->id
        );
        $response = self::$client->post('/cms/contents/' . $content->id . '/members', $metaForm);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNotAnonymousModel($response, 'Meta data is not generated');
        
        $mlist = $content->get_members_list();
        $meta = $mlist[0];
        
        // Delete list of metas
        $response = self::$client->get('/cms/contents/' . $content->id . '/members/' . $meta->id);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNotAnonymousModel($response, 'Meta is not accessable');
    }
    
    /**
     *
     * Delete a member
     *
     * @test
     */
    public function deleteMemberOfContent()
    {
        // login
        $response = self::$client->post('/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        $user = new User_Account();
        $user = $user->getUser('test');
        
        // create
        $form = array(
            'name' => 'test-content' . rand(),
            'title' => 'test contetn',
            'description' => 'This is a simple content is used int test process',
            'mime_type' => 'application/test'
        );
        $response = self::$client->post('/cms/contents', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // load content by name
        Pluf::loadFunction('CMS_Shortcuts_GetNamedContentOr404');
        $content = CMS_Shortcuts_GetNamedContentOr404($form['name']);
        $this->assertNotNull($content);
        $this->assertEquals($content->name, $form['name']);
        
        // Adding new member to the content
        $metaForm = array(
            'id' => $user->id
        );
        $response = self::$client->post('/cms/contents/' . $content->id . '/members', $metaForm);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNotAnonymousModel($response, 'Meta data is not generated');
        
        $mlist = $content->get_members_list();
        $meta = $mlist[0];
        
        // Delete list of members
        $response = self::$client->delete('/cms/contents/' . $content->id . '/members/' . $meta->id);
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        
        // Getting list of members
        $response = self::$client->get('/cms/contents/' . $content->id . '/members');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseEmptyPaginateList($response, 'The list is empty');
    }
    
    
}



