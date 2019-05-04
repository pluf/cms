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
class Content_AuthorRestTest extends TestCase
{

    var $client;

    var $author1;
    var $author2;

    /**
     *
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../conf/config.php');
        $m = new Pluf_Migration(Pluf::f('installed_apps'));
        $m->install();
        $m->init();

        // Create user: author1
        $user = new User_Account();
        $user->login = 'author1';
        $user->is_active = true;
        if (true !== $user->create()) {
            throw new Exception();
        }
        // Credential of user
        $credit = new User_Credential();
        $credit->setFromFormData(array(
            'account_id' => $user->id
        ));
        $credit->setPassword('123456');
        if (true !== $credit->create()) {
            throw new Exception();
        }
        $per = User_Role::getFromString('cms.author');
        $user->setAssoc($per);
        
        // Create user: author2
        $user = new User_Account();
        $user->login = 'author2';
        $user->is_active = true;
        if (true !== $user->create()) {
            throw new Exception();
        }
        // Credential of user
        $credit = new User_Credential();
        $credit->setFromFormData(array(
            'account_id' => $user->id
        ));
        $credit->setPassword('123456');
        if (true !== $credit->create()) {
            throw new Exception();
        }
        $per = User_Role::getFromString('cms.author');
        $user->setAssoc($per);
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
     * @before
     */
    public function init()
    {
        $this->client = new Test_Client(array(
            array(
                'app' => 'Cms',
                'regex' => '#^/cms#',
                'base' => '',
                'sub' => include 'CMS/urls-v2.php'
            ),
            array(
                'app' => 'User',
                'regex' => '#^/user#',
                'base' => '',
                'sub' => include 'User/urls-v2.php'
            )
        ));
        // login
        $response = $this->client->post('/user/login', array(
            'login' => 'author1',
            'password' => '123456'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // Authors
        $this->author1 = User_Account::getUser('author1');
        $this->author2 = User_Account::getUser('author2');
    }

    /**
     *
     * @test
     */
    public function createTest()
    {
        // create
        $name = 'test-content' . rand();
        $form = array(
            'name' => $name,
            'title' => 'test contetn',
            'description' => 'This is a simple content is used int test process',
            'mime_type' => 'application/test'
        );
        $response = $this->client->post('/cms/contents', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        // Check author
        Pluf::loadFunction('CMS_Shortcuts_GetNamedContentOr404');
        $content = CMS_Shortcuts_GetNamedContentOr404($name);
        $this->assertEquals($this->author1->id, $content->author_id);
    }

    /**
     *
     * @test
     */
    public function updateTest()
    {
        $content = new CMS_Content();
        $content->name = 'test-content' . rand();
        $content->title = 'Title ' . rand();
        $content->description = 'It is my content description';
        $content->author_id = $this->author1;
        Test_Assert::assertTrue($content->create(), 'Impossible to create cms content');
        // Author could change content created by himself
        $form = array(
            'name' => 'updated name',
            'title' => 'updated title',
            'description' => 'updated description'
        );
        $response = $this->client->post('/cms/contents/' . $content->id, $form);
        $this->assertEquals($response->status_code, 200);
        
        // Content should be changed
        $afterContent = new CMS_Content($content->id);
        $this->assertEquals('updated name', $afterContent->name);
        $this->assertEquals('updated title', $afterContent->title);
        $this->assertEquals('updated description', $afterContent->description);
    }
    
    /**
     *
     * @test
     */
    public function anotherAuthorUpdateTest()
    {
        $content = new CMS_Content();
        $content->name = 'test-content' . rand();
        $content->title = 'Title ' . rand();
        $content->description = 'It is my content description';
        $content->author_id = $this->author2;
        Test_Assert::assertTrue($content->create(), 'Impossible to create cms content');
        // Author could not change content created by another author
        $this->expectException(Pluf_Exception_PermissionDenied::class);
        $form = array(
            'name' => 'updated name',
            'title' => 'updated title',
            'description' => 'updated description'
        );
        $response = $this->client->post('/cms/contents/' . $content->id, $form);
        $this->assertEquals($response->status_code, 403);
        
        // Content should remain unchanged
        $afterContent = CMS_Content($content->id);
        $this->assertEquals($content->name, $afterContent->name);
        $this->assertEquals($content->title, $afterContent->title);
        $this->assertEquals($content->description, $afterContent->description);        
    }

    /**
     *
     * @test
     */
    public function deleteTest()
    {
        $content = new CMS_Content();
        $content->name = 'test-content' . rand();
        $content->title = 'Title ' . rand();
        $content->description = 'It is my content description';
        $content->author_id = $this->author1;
        Test_Assert::assertTrue($content->create(), 'Impossible to create cms content');
        // Author could delete content created by himself
        $response = $this->client->delete('/cms/contents/' . $content->id);
        $this->assertEquals($response->status_code, 200);
        
        // Content should not be existed
        Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
        $this->expectException(Pluf_HTTP_Error404::class);
        Pluf_Shortcuts_GetObjectOr404('CMS_Content', $content->id);
    }
    
    /**
     *
     * @test
     */
    public function anotherAuthorDeleteTest()
    {
        $content = new CMS_Content();
        $content->name = 'test-content' . rand();
        $content->title = 'Title ' . rand();
        $content->description = 'It is my content description';
        $content->author_id = $this->author2;
        Test_Assert::assertTrue($content->create(), 'Impossible to create cms content');
        // Author could not delete content created by another author
        $this->expectException(Pluf_Exception_PermissionDenied::class);
        $response = $this->client->delete('/cms/contents/' . $content->id);
        $this->assertEquals($response->status_code, 403);
        
        // Content should remain unchanged
        $this->expectException(Pluf_HTTP_Error404::class);
        $afterContent = Pluf_Shortcuts_GetObjectOr404('CMS_Content', $content->id);
        $this->assertEquals($content->name, $afterContent->name);
        $this->assertEquals($content->title, $afterContent->title);
        $this->assertEquals($content->description, $afterContent->description);        
    }

}



