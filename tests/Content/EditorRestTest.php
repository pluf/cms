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
use Pluf_HTTP_Error404;
use User_Account;
use User_Credential;
use User_Role;

class EditorRestTest extends TestCase
{

    var $client;

    var $author;
    var $editor;

    /**
     *
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../conf/config.php');
        $m = new Pluf_Migration();
        $m->install();
        $m->init();

        // Create user: author
        $user = new User_Account();
        $user->login = 'author';
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
        
        // Create user: editor
        $user = new User_Account();
        $user->login = 'editor';
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
        $per = User_Role::getFromString('cms.editor');
        $user->setAssoc($per);
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
     * @before
     */
    public function init()
    {
        $this->client = new Client();
        // login
        $response = $this->client->post('/user/login', array(
            'login' => 'editor',
            'password' => '123456'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        
        // Authors
        $this->author = User_Account::getUser('author');
        $this->editor = User_Account::getUser('editor');
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
        // Editor could create page
        Pluf::loadFunction('CMS_Shortcuts_GetNamedContentOr404');
        $content = CMS_Shortcuts_GetNamedContentOr404($name);
        $this->assertEquals($this->editor->id, $content->author_id);
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
        $content->author_id = $this->author;
        $this->assertTrue($content->create(), 'Impossible to create cms content');
        // Editor could change content created by any author
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
    public function deleteTest()
    {
        $content = new CMS_Content();
        $content->name = 'test-content' . rand();
        $content->title = 'Title ' . rand();
        $content->description = 'It is my content description';
        $content->author_id = $this->author;
        $this->assertTrue($content->create(), 'Impossible to create cms content');
        // Author could delete content created by any author
        $response = $this->client->delete('/cms/contents/' . $content->id);
        $this->assertEquals($response->status_code, 200);
        
        // Content should not be existed
        Pluf::loadFunction('Pluf_Shortcuts_GetObjectOr404');
        $this->expectException(Pluf_HTTP_Error404::class);
        Pluf_Shortcuts_GetObjectOr404('CMS_Content', $content->id);
    }

}



