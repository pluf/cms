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
namespace Pluf\Test\Term;

use Pluf\Test\Client;
use Pluf\Test\TestCase;
use CMS_Term;
use CMS_TermMeta;
use CMS_TermTaxonomy;
use Exception;
use Pluf;
use Pluf_Migration;
use User_Account;
use User_Credential;
use User_Role;

class Term_RestTest extends TestCase
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
        $form = array(
            'name' => 'term-' . rand(),
            'slug' => 'slug-' . rand()
        );
        $response = $this->client->post('/cms/terms', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     *
     * @test
     */
    public function getRestTest()
    {
        $item = new CMS_Term();
        $item->name = 'term-' . rand();
        $item->slug = 'slug-' . rand();
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create CMS_Term');
        // Get item
        $response = $this->client->get('/cms/terms/' . $item->id);
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
        $item = new CMS_Term();
        $item->name = 'term-' . rand();
        $item->slug = 'slug-' . rand();
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create CMS_Term');
        // Update item
        $form = array(
            'name' => 'new name' . rand(),
            'slug' => 'new slug' . rand()
        );
        $response = $this->client->post('/cms/terms/' . $item->id, $form);
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
        $item = new CMS_Term();
        $item->name = 'term-' . rand();
        $item->slug = 'slug-' . rand();
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create CMS_Term');

        // delete
        $response = $this->client->delete('/cms/terms/' . $item->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     *
     * @test
     */
    public function findRestTest()
    {
        $response = $this->client->get('/cms/terms');
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
    }

    /**
     * Getting a term by its slug
     *
     * @test
     */
    public function getTermBySlugTest(){
        $item = new CMS_Term();
        $item->name = 'term-' . rand();
        $item->slug = 'slug-' . rand();
        $item->create();
        $this->assertFalse($item->isAnonymous(), 'Could not create CMS_Term');
        // Get item
        $response = $this->client->get('/cms/terms/' . $item->slug);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $actual = json_decode($response->content, true);
        $this->assertEquals($actual['id'], $item->id);
        $this->assertEquals($actual['name'], $item->name);
        $this->assertEquals($actual['slug'], $item->slug);
    }

    /**
     *
     * CRUD (create,read,update,delete) on metas of term
     *
     * @test
     */
    public function crudOnMetaOfTermTest()
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

        // Adding term-meta
        $key = 'meta-' . rand();
        $value = 'meta value';
        $form = array(
            'key' => $key,
            'value' => $value
        );
        $response = $this->client->post('/cms/terms/' . $term->id . '/metas', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $this->assertResponseAsModel($response);
        $actual = json_decode($response->content, true);
        $this->assertEquals($actual['key'], $key);
        $this->assertEquals($actual['value'], $value);

        $tm = new CMS_TermMeta($actual['id']);
        $this->assertFalse($tm->isAnonymous(), 'TermMeta is not created!');

        // Getting the term-meta
        $response = $this->client->get('/cms/terms/' . $term->id . '/metas/' . $tm->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $this->assertResponseAsModel($response);

        // Getting list of metas
        $response = $this->client->get('/cms/terms/' . $term->id . '/metas');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNonEmptyPaginateList($response, 'The list is empty');

        // Deleting meta from the term
        $response = $this->client->delete('/cms/terms/' . $term->id . '/metas/' . $tm->id);
        $this->assertResponseNotNull($response, 'Result of delete request is empty');
        $this->assertResponseStatusCode($response, 200, 'Delete status code is not 200');

        // Getting list of metas
        $response = $this->client->get('/cms/terms/' . $term->id . '/metas');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseEmptyPaginateList($response, 'The list is not empty');
    }

    /**
     *
     * CRUD (create,read,update,delete) on taxonomies of term
     *
     * @test
     */
    public function crudOnTermTaxonomyOfTermTest()
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

        // Adding term-meta
        $key = 'meta-' . rand();
        $value = 'meta value';
        $form = array(
            'taxonomy' => 'test',
            'value' => $value
        );
        $response = $this->client->post('/cms/terms/' . $term->id . '/term-taxonomies', $form);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $this->assertResponseAsModel($response);
        $actual = json_decode($response->content, true);
        $this->assertEquals($actual['taxonomy'], $form['taxonomy']);

        $tm = new CMS_TermTaxonomy($actual['id']);
        $this->assertFalse($tm->isAnonymous(), 'TermTaxonomy is not created!');

        // Getting the term-taxonomies
        $response = $this->client->get('/cms/terms/' . $term->id . '/term-taxonomies/' . $tm->id);
        $this->assertNotNull($response);
        $this->assertEquals($response->status_code, 200);
        $this->assertResponseAsModel($response);

        // Getting list of taxonomies
        $response = $this->client->get('/cms/terms/' . $term->id . '/term-taxonomies');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseNonEmptyPaginateList($response, 'The list is empty');

        // Deleting taxonomies from the term
        $response = $this->client->delete('/cms/terms/' . $term->id . '/term-taxonomies/' . $tm->id);
        $this->assertResponseNotNull($response, 'Result of delete request is empty');
        $this->assertResponseStatusCode($response, 200, 'Delete status code is not 200');

        // Getting list of taxonomies
        $response = $this->client->get('/cms/terms/' . $term->id . '/term-taxonomies');
        $this->assertResponseNotNull($response, 'Find result is empty');
        $this->assertResponseStatusCode($response, 200, 'Find status code is not 200');
        $this->assertResponseEmptyPaginateList($response, 'The list is not empty');
    }
}



