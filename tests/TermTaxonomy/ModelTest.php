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
 * @backupGlobals disabled
 * @backupStaticAttributes disabled
 */
class TermTaxonomy_ModelTest extends TestCase
{
    /**
     * @beforeClass
     */
    public static function createDataBase()
    {
        Pluf::start(__DIR__ . '/../conf/config.php');
        $m = new Pluf_Migration(Pluf::f('installed_apps'));
        $m->install();
        $m->init();
        
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
     * @afterClass
     */
    public static function removeDatabses()
    {
        $m = new Pluf_Migration(Pluf::f('installed_apps'));
        $m->unInstall();
    }

    /**
     * @test
     */
    public function createModelTest()
    {
        $term = new CMS_Term();
        $term->name = 'term-' . rand();
        $term->slug = 'slug-' . rand();
        Test_Assert::assertTrue($term->create(), 'Impossible to create CMS_Term');
        
        // TermTaxonomy without term
        $item = new CMS_TermTaxonomy();
        $item->taxonomy = 'term-taxonomy-' . rand();
        $item->description = 'description about term taxonomy';
        Test_Assert::assertTrue($item->create(), 'Impossible to create CMS_TermTaxonomy');

        // TermTaxonomy with a term
        $item = new CMS_TermTaxonomy();
        $item->taxonomy = 'term-taxonomy-' . rand();
        $item->description = 'description about term taxonomy';
        $item->term_id = $term;
        Test_Assert::assertTrue($item->create(), 'Impossible to create CMS_TermTaxonomy');
    }

    /**
     * @test
     */
    public function getTermTaxonomiesOfTermTest()
    {
        $term = new CMS_Term();
        $term->name = 'term-' . rand();
        $term->slug = 'slug-' . rand();
        Test_Assert::assertTrue($term->create(), 'Impossible to create CMS_Term');
        
        $term = new CMS_Term($term->id);
        $ttList = $term->get_term_taxonomies_list();
        Test_Assert::assertEquals(0, $ttList->count());
        
        // TermTaxonomy with a term
        $item = new CMS_TermTaxonomy();
        $item->taxonomy = 'term-taxonomy-' . rand();
        $item->description = 'description about term taxonomy';
        $item->term_id = $term;
        Test_Assert::assertTrue($item->create(), 'Impossible to create CMS_TermTaxonomy');
        
        // Get term taxonomies after adding one to the term
        $ttList = $term->get_term_taxonomies_list();
        Test_Assert::assertEquals(1, $ttList->count());
    }
    
    /**
     * @test
     */
    public function getTermTaxonomiesOfContentTest()
    {
        $content = new CMS_Content();
        $content->name = 'content-' . rand();
        $content->titme = 'title-' . rand();
        $content->titme = 'description of content';
        Test_Assert::assertTrue($content->create(), 'Impossible to create CMS_Content');
        
        $content = new CMS_Content($content->id);
        $ttList = $content->get_term_taxonomies_list();
        Test_Assert::assertEquals(0, $ttList->count());
        
        // Add relation between a TermTaxonomy and a Conetnt 
        $item = new CMS_TermTaxonomy();
        $item->taxonomy = 'term-taxonomy-' . rand();
        $item->description = 'description about term taxonomy';
        Test_Assert::assertTrue($item->create(), 'Impossible to create CMS_TermTaxonomy');
        
        $item->setAssoc($content);
        
        // Get term taxonomies after adding one to the term
        $ttList = $content->get_term_taxonomies_list();
        Test_Assert::assertEquals(1, $ttList->count());
    }
    
    /**
     * @test
     */
    public function getContentsOfTermTaxomomyTest()
    {
        $content = new CMS_Content();
        $content->name = 'content-' . rand();
        $content->titme = 'title-' . rand();
        $content->titme = 'description of content';
        Test_Assert::assertTrue($content->create(), 'Impossible to create CMS_Content');
        
        // Add relation between a TermTaxonomy and a Content
        $item = new CMS_TermTaxonomy();
        $item->taxonomy = 'term-taxonomy-' . rand();
        $item->description = 'description about term taxonomy';
        Test_Assert::assertTrue($item->create(), 'Impossible to create CMS_TermTaxonomy');
        
        // Get contents before adding one to the term
        $contentList = $item->get_contents_list();
        Test_Assert::assertEquals(0, $contentList->count());
        
        $item->setAssoc($content);
        
        // Get contents after adding one to the term
        $contentList = $item->get_contents_list();
        Test_Assert::assertEquals(1, $contentList->count());
    }

}


