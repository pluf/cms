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
namespace Pluf\Test\TermTaxonomy;

use Pluf\Test\Client;
use Pluf\Test\TestCase;
use CMS_Content;
use CMS_Term;
use CMS_TermTaxonomy;
use Exception;
use Pluf;
use Pluf_Migration;
use User_Account;
use User_Credential;
use User_Role;

class RestTest extends TestCase
{

    var $client = null;

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
    }

    /**
     *
     * @test
     */
    public function createRestTest()
    {
        // login
        $response = $this->client->post('/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));

        $term = new CMS_Term();
        $term->name = 'term-' . rand();
        $term->slug = 'slug-' . rand();
        $term->create();

        $form = array(
            'taxonomy' => 'taxonomy-' . rand(),
            'description' => 'description',
            'term_id' => $term->id
        );
        $response = $this->client->post('/cms/term-taxonomies', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     *
     * @test
     */
    public function getRestTest()
    {
        $term = new CMS_Term();
        $term->name = 'term-' . rand();
        $term->slug = 'slug-' . rand();
        $term->create();
        $this->assertFalse($term->isAnonymous(), 'Could not create CMS_Term');

        $item = new CMS_TermTaxonomy();
        $item->taxonomy = 'taxonomy-' . rand();
        $item->description = 'It is a test term-taxonomy';
        $item->term_id = $term;
        $item->create();

        $this->assertFalse($item->isAnonymous(), 'Could not create CMS_TermTaxonomy');
        // Get item
        $response = $this->client->get('/cms/term-taxonomies/' . $item->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     *
     * @test
     */
    public function updateRestTest()
    {
        // login
        $response = $this->client->post('/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));

        $term = new CMS_Term();
        $term->name = 'term-' . rand();
        $term->slug = 'slug-' . rand();
        $term->create();
        $this->assertFalse($term->isAnonymous(), 'Could not create CMS_Term');

        $item = new CMS_TermTaxonomy();
        $item->taxonomy = 'taxonomy-' . rand();
        $item->description = 'It is a test term-taxonomy';
        $item->term_id = $term;
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create CMS_TermTaxonomy');
        // Update item
        $form = array(
            'taxonomy' => 'new-taxonomy' . rand(),
            'description' => 'updated description'
        );
        $response = $this->client->post('/cms/term-taxonomies/' . $item->id, $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     *
     * @test
     */
    public function deleteRestTest()
    {
        // login
        $response = $this->client->post('/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $term = new CMS_Term();
        $term->name = 'term-' . rand();
        $term->slug = 'slug-' . rand();
        $term->create();
        $this->assertFalse($term->isAnonymous(), 'Could not create CMS_Term');
        
        $item = new CMS_TermTaxonomy();
        $item->taxonomy = 'taxonomy-' . rand();
        $item->description = 'It is a test term-taxonomy';
        $item->term_id = $term;
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create CMS_TermTaxonomy');

        // delete
        $response = $this->client->delete('/cms/term-taxonomies/' . $item->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     *
     * @test
     */
    public function findRestTest()
    {
        $response = $this->client->get('/cms/term-taxonomies');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * Getting children of term-taxonomy
     *
     * @test
     */
    public function getChildren()
    {
        $term = new CMS_Term();
        $term->name = 'term-' . rand();
        $term->slug = 'slug-' . rand();
        $term->create();
        $this->assertFalse($term->isAnonymous(), 'Could not create CMS_Term');
        
        $item = new CMS_TermTaxonomy();
        $item->taxonomy = 'taxonomy-' . rand();
        $item->description = 'It is a test term-taxonomy';
        $item->term_id = $term;
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create CMS_TermTaxonomy');
        // Get children (empty list)
        $response = $this->client->get('/cms/term-taxonomies/' . $item->id . '/children');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseEmptyPaginateList($response, 'The list is not empty');
        
        $item2 = new CMS_TermTaxonomy();
        $item2->taxonomy = 'taxonomy-' . rand();
        $item2->description = 'It is a test term-taxonomy';
        $item2->term_id = $term;
        $item2->parent_id = $item;
        $item2->create();
        $this->assertFalse($item2->isAnonymous(), 'Could not create CMS_TermTaxonomy');
        // Get item
        $response = $this->client->get('/cms/term-taxonomies/' . $item->id . '/children');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNonEmptyPaginateList($response, 'The list is not empty');
    }

    /**
     *
     * CRUD (create,read,update,delete) on metas of term
     *
     * @test
     */
    public function crudOnContentsOfTermTaxonomyTest()
    {

        // login
        $response = $this->client->post('/user/login', array(
            'login' => 'test',
            'password' => 'test'
        ));
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);

        // Create term
        $term = new CMS_Term();
        $term->name = 'test-term' . rand();
        $term->slug = 'slug-' . rand();
        $term->create();

        // Create term-taxonomy
        $tt = new CMS_TermTaxonomy();
        $tt->taxonomy = 'taxonomy-' . rand();
        $tt->description = 'It is a test term-taxonomy';
        $tt->term_id = $term;
        $tt->create();
        
        // Get user
        $user = User_Account::getUser('test');
        $this->assertFalse($user->isAnonymous(), 'User could not be found');
        
        // Create content
        $content = new CMS_Content();
        $content->name = 'test-content' . rand();
        $content->title = 'Title ' . rand();
        $content->description = 'It is my content description';
        $content->author_id = $user;
        $this->assertTrue($content->create(), 'Impossible to create cms content');
        
        // Adding content
        $form = array(
            'id' => $content->id
        );
        $response = $this->client->post('/cms/term-taxonomies/' . $tt->id . '/contents', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $this->assertResponseAsModel($response);
        $actual = json_decode($response->content, true);
        $this->assertEquals($actual['id'], $content->id);
        $this->assertEquals($actual['name'], $content->name);

        // Getting list of contents
        $response = $this->client->get('/cms/term-taxonomies/' . $tt->id . '/contents');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNonEmptyPaginateList($response, 'The list is empty');

        // Deleting content from the term-taxonomy
        $response = $this->client->delete('/cms/term-taxonomies/' . $tt->id . '/contents/' . $content->id);
        $this->assertResponseNotNull($response, 'Result of delete request is empty');
        $this->assertResponseStatusCode($response, 200, 'Delete status code is not 200');

        // Getting list of contents
        $response = $this->client->get('/cms/term-taxonomies/' . $tt->id . '/contents');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseEmptyPaginateList($response, 'The list is not empty');
    }
}



